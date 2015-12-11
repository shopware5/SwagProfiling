<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class QueryProfiler
{
    private $profiler;

    public function __construct($profiler)
    {
        $this->profiler;
    }

    /**
     * Internal helper function which returns all traced plain sql queries
     * which executed over the Shopware()->Db() object.
     *
     * @return array
     */
    public function getSqlQueries()
    {
        $queries = array();
        /**@var $query Zend_Db_Profiler_Query* */
        foreach ($this->profiler as $query) {
            $explain = $this->getSqlExplain($query->getQuery(), $query->getQueryParams());
            $sql = $this->getQuerySql($query->getQuery());

            if (strpos($sql, '-- IGNORE PROFILING') !== false) {
                continue;
            }
            $this->sqlTime += $query->getElapsedSecs();

            $queries[] = array(
                'sql' => SqlFormatter::format($sql),
                'short' => $this->getShortSql($sql),
                'explain' => $explain,
                'status' => $this->getQueryStatus($explain),
                'params' => $query->getQueryParams(),
                'time' => number_format($query->getElapsedSecs(), 5)
            );
        }

        return $queries;
    }

    /**
     * Internal helper function which returns all traced doctrine queries
     * which executed over the Shopware()->Models() manager.
     *
     * @return array
     */
    public function getDoctrineQueries()
    {
        $queries = array();

        /**@var $logger \Doctrine\DBAL\Logging\DebugStack */
        $logger = Shopware()->Models()->getConfiguration()->getSQLLogger();
        foreach ($logger->queries as $query) {
            $explain = $this->getSqlExplain($query['sql'], $query['params']);
            $sql = $this->getQuerySql($query['sql']);

            $this->sqlTime += $query['executionMS'];

            $queries[] = array(
                'sql' => SqlFormatter::format($sql),
                'short' => $this->getShortSql($sql),
                'explain' => $explain,
                'status' => $this->getQueryStatus($explain),
                'params' => $query['params'],
                'time' => number_format($query['executionMS'], 5)
            );
        }

        return $queries;
    }

    /**
     * Helper function to get the sql explain of the passed sql query.
     *
     * @param $sql
     * @param $params
     * @return array|string
     */
    private function getSqlExplain($sql, $params)
    {
        try {
            $prepared = array();
            foreach ($params as $param) {
                $prepared[] = $param;
            }
            $result = Shopware()->Db()->fetchAll('EXPLAIN ' . $sql, $prepared);

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Converted function to get a small sql path of the original sql statement.
     *
     * @param $sql
     * @return string
     */
    private function getShortSql($sql)
    {
        $fromPos = strrpos($sql, 'FROM');
        if ($fromPos !== false) {
            return substr($sql, 0, 40) . ' ... <br>' . substr($sql, $fromPos, 80);
        } else {
            return substr($sql, 0, 120);
        }
    }

    /**
     * Converted function which prepares a sql string.
     * This functions trims all line feats and replace them with a space.
     *
     * @param $sql
     * @return string
     */
    private function getQuerySql($sql)
    {
        $sql = trim(preg_replace('/\s+/', ' ', $sql));

        return $sql;
    }

    /**
     * Helper function to get a specify sql status for a single
     * query. This function expects the sql explain result of a single query.
     * This function sets the following query status:
     *  - Error   => The explain contains a temporary table or an file sort.
     *  - Warning => The explain selects more than 100 rows for one table or use the select type "ALL"
     *
     * @param $explain
     * @return array
     */
    private function getQueryStatus($explain)
    {
        if (!is_array($explain)) {
            return array(
                'cls' => 'query-error',
                'notices' => array()
            );
        }
        $useTemporary = false;
        $useFileSort = false;
        $useManyRows = false;
        $useAllSelect = false;
        $notices = array();
        $cls = 'query-success';
        foreach ($explain as $row) {
            $extra = $row['Extra'];
            if (strpos($extra, 'filesort') !== false) {
                $useFileSort = true;
                $notices[] = 'Table: ' . $row['table'] . ' using <b>file sort</b>';
            }
            if (strpos($extra, 'temporary') !== false) {
                $useTemporary = true;
                $notices[] = 'Table: ' . $row['table'] . ' using <b>temporary</b> table';
            }
            if ($row['rows'] > 100) {
                $useManyRows = true;
                $notices[] = 'Table: ' . $row['table'] . ' more than <b>100 rows</b>';
            }
            if ($row['type'] == 'ALL') {
                $useAllSelect = true;
                $notices[] = 'Table: ' . $row['table'] . ' select <b>ALL</b>';
            }
        }
        if ($useAllSelect || $useManyRows) {
            $cls = 'query-warning';
        }
        if ($useTemporary || $useFileSort) {
            $cls = 'query-error';
            $this->slowQueryCounter++;
        }

        return array(
            'cls' => $cls,
            'notices' => $notices
        );
    }
}
