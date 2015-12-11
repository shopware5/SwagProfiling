<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

use Shopware\Models\Config\Form;

require_once(__DIR__ . '/Components/SqlFormatter.php');
require_once(__DIR__ . '/Components/EventManager.php');
require_once(__DIR__ . '/Components/QueryProfiler.php');

/**
 * Class Shopware_Plugins_Frontend_Profiling_Bootstrap
 * This is the bootstrap class of the developer toolbar plugin.
 * This class bootstraps the plugin. The install function registers
 * all necessary events to collect the different profiling data and display
 * them in the store front.
 *
 * @category  Shopware
 * @package   Shopware\Plugins\Profiling
 * @copyright Copyright (c) 2012, shopware AG (http://www.shopware.de)
 */
class Shopware_Plugins_Frontend_SwagProfiling_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * Internal array which contains all fired events for a single request.
     *
     * @var array
     */
    public $events = array();

    /**
     * @var array
     */
    public $mails = array();

    /**
     * Total time of all queries
     *
     * @var int
     */
    protected $sqlTime = 0;

    /**
     * Counter of executed event listeners
     *
     * @var int
     */
    protected $listenerCount = 0;

    /**
     * Internal log to prevent the log function
     * of events which fired over this plugin.
     *
     * @var bool
     */
    protected $preventEventLog = false;

    /**
     * Helper property which counts the
     * slow queries of the current request.
     *
     * @var int
     */
    protected $slowQueryCounter = 0;

    /**
     * Returns the displayed label for this plugin.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Shopware Developer Toolbar';
    }

    /**
     * Returns all information about this plugin.
     * This information will be displayed in the plugin manager detail page
     * of a single plugin.
     *
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'description' => file_get_contents(__DIR__ . '/description.txt')
        );
    }

    /**
     * Returns the current plugin version
     *
     * @return string
     * @throws Exception
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }


    /**
     * Registers all necessary events and the plugin configuration.
     *
     * @return bool
     */
    public function install()
    {
        $this->initForm();

        $this->subscribeEvent(
            'Enlight_Controller_Action_Frontend_Detail_GetPhpInfo',
            'onGetPhpInfo'
        );
        $this->subscribeEvent(
            'Enlight_Controller_Action_Frontend_Detail_ClearCache',
            'onClearCache'
        );

        $this->subscribeEvent(
            'Enlight_Components_Mail_Send',
            'onSendMail',
            200
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch',
            'onDisplayProfiling',
            200
        );

        $this->subscribeEvent(
            'Enlight_Controller_Front_StartDispatch',
            'onStartDispatch',
            100
        );

        return true;
    }

    private function initForm()
    {
        $form = $this->Form();
        /* @var Form $parent */
        $parent = $this->Forms()->findOneBy(array('name' => 'Core'));

        $form->setParent($parent);
        $form->setElement(
            'text',
            'ipLimitation',
            array(
                'label' => 'Auf IP beschränken',
                'value' => ''
            )
        );

        $form->setElement(
            'boolean',
            'show_queries',
            array(
                'label' => 'Queries anzeigen',
                'value' => 1,
                'description' => 'Durch die Query-Ausgabe kann ein "memory limit" Fehler ausgelöst werden. Sollte dies der Fall sein, deaktivieren Sie einfach diese Option.',
            )
        );
    }

    /**
     * Event listener function of the Enlight_Components_Mail_Send event
     * which fired on sending each mail in shopware.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onSendMail(Enlight_Event_EventArgs $args)
    {
        $this->mails[] = $args->getMail();
    }

    /**
     * Startet function of the shopware profling
     * This function initials all profiling components.
     *
     * @param Enlight_Event_EventArgs $arguments
     */
    public function onStartDispatch(Enlight_Event_EventArgs $arguments)
    {
        //check ip configuration.
        if (!empty($_SERVER["REMOTE_ADDR"])
            && !empty($this->Config()->ipLimitation)
            && strpos($this->Config()->ipLimitation, $_SERVER["REMOTE_ADDR"]) === false
        ) {
            return;
        }

        //enable zend db logging
        $profiler = new Zend_Db_Profiler(true);
        Shopware()->Db()->setProfiler($profiler);

        $showQueries = $this->Config()->show_queries;
        if ($showQueries) {
            //enable doctrine query logging
            Shopware()->Models();
            $logger = new \Doctrine\DBAL\Logging\DebugStack();
            $logger->enabled = true;
            Shopware()->Models()->getConfiguration()->setSQLLogger($logger);
        }

        //enable event logging
        Shopware()->setEventManager(new EventManager($this->Application()->Events()));
    }

    /**
     * Global post dispatch event.
     * Used to extends the template and display the profiled data.
     *
     * @param Enlight_Event_EventArgs $arguments
     * @return bool
     */
    public function onDisplayProfiling(Enlight_Event_EventArgs $arguments)
    {
        if (!empty($_SERVER["REMOTE_ADDR"])
            && !empty($this->Config()->ipLimitation)
            && strpos($this->Config()->ipLimitation, $_SERVER["REMOTE_ADDR"]) === false
        ) {
            return $arguments->getReturn();
        }

        /**@var $controller Shopware_Controllers_Frontend_Index */
        $controller = $arguments->getSubject();
        $request = $controller->Request();

        /**@var $view Enlight_View_Default */
        $view = $arguments->getSubject()->View();

        if ($request->getModuleName() !== 'frontend' || !$view->hasTemplate()) {
            return $arguments->getReturn();
        }

        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('frontend/plugins/swag_profiling/index.tpl');
        $this->preventEventLog = true;
        $view->assign('profiling', $this->getProfiling($arguments));
        $this->preventEventLog = false;
    }

    /**
     * Internal helper function to get all profiled data.
     * This function is used from the onDisplayProfiling function.
     *
     * @param Enlight_Event_EventArgs $arguments
     * @return array
     */
    private function getProfiling(Enlight_Event_EventArgs $arguments)
    {
        $showQueries = $this->Config()->show_queries;
        $events = $this->getEvents();
        $exceptions = Shopware()->Front()->Response()->getException();

        $data = array(
            'request' => $this->getRequestData(),
            'response' => $this->getResponseData(),
            'session' => $this->getSessionData(),
            'cookies' => $_COOKIE,
            'config' => Shopware()->getOptions(),
            'events' => $events,
            'template' => $this->getTemplateData($arguments),
            'basket' => Shopware()->Modules()->Basket()->sGetBasket(),
            'mails' => $this->getMails(),
            'cache' => $this->getCacheData(),
            'exception' => $this->getExceptionData($exceptions[0]),
            'trace' => debug_backtrace()
        );

        $data['short'] = array(
            'config' => Shopware::VERSION,
            'request' => $data['request']['Controller'] . '::' . $data['request']['Action'],
            'queryTime' => round($this->sqlTime, 6),
            'slowQueries' => $this->slowQueryCounter,
            'sessionId' => Shopware()->SessionID(),
            'eventCount' => count($events),
            'listenerCount' => $this->listenerCount,
            'mails' => count($data['mails']),
            'cacheFiles' => count($data['cache']['metaData']),
            'php' => phpversion(),
            'memory' => memory_get_usage() / 1024 / 1024,
            'templates' => count($data['template']['Loaded templates'])
        );

        if ($showQueries) {
            $queryComponent = new QueryProfiler();

            $sqlQueries = $queryComponent->getSqlQueries();
            $doctrineQueries = $queryComponent->getDoctrineQueries();
            $queries = array_merge($sqlQueries, $doctrineQueries);

            $data['queries'] = $queries;
            $data['short']['queryCount'] = count($queries);
        }

        return $data;
    }

    /**
     * Helper function to get the exception data as array.
     *
     * @param $exception
     * @return array
     */
    private function getExceptionData($exception)
    {
        if (!$exception instanceof Exception) {
            return array();
        }
        $previousData = $this->getExceptionData($exception->getPrevious());

        return array(
            'Code' => $exception->getCode(),
            'Message' => $exception->getMessage(),
            'File' => $exception->getFile(),
            'Line' => $exception->getLine(),
            'Trace' => $exception->getTrace(),
            'Previous' => $previousData,
        );
    }

    /**
     * Internal helper function which returns all relevant cache data
     * of the current request.
     */
    private function getCacheData()
    {
        /**@var $cache Zend_Cache_Core */
        $cache = Shopware()->Cache();
        $data = array(
            'metaData' => array(),
            'options' => array(
                'percentage' => $cache->getFillingPercentage()
            ),
        );
        $options = array(
            'write_control',
            'caching',
            'cache_id_prefix',
            'automatic_serialization',
            'automatic_cleaning_factor',
            'lifetime',
            'logging',
            'logger',
            'ignore_user_abort'
        );
        foreach ($options as $option) {
            $data['options'][$option] = $cache->getOption($option);
        }

        foreach ($cache->getIds() as $id) {
            $metaData = $cache->getMetadatas($id);
            $createdAt = date('Y-m-d H:i:s', $metaData['mtime']);
            $validTo = date('Y-m-d H:i:s', $metaData['expire']);

            $from = new \DateTime($createdAt);
            $diff = $from->diff(new \DateTime($validTo));
            $minutes = $diff->days * 24 * 60;
            $minutes += $diff->h * 60;
            $minutes += $diff->i;

            $data['metaData'][$id] = array(
                'Tags' => $metaData['tags'],
                'Created at' => $createdAt,
                'Valid to' => $validTo,
                'Lifetime' => $minutes . ' minutes'
            );
        }

        return $data;
    }

    /**
     * Internal helper function which returns all data about each mail
     * which has been send in this request.
     *
     * @return array
     */
    private function getMails()
    {
        $data = array();
        /**@var $mail Enlight_Components_Mail */
        foreach ($this->mails as $mail) {
            $data[] = array(
                'information' => array(
                    'From' => $mail->getFrom(),
                    'From name' => $mail->getFromName(),
                    'Default from' => $mail->getDefaultFrom(),
                    'Recipients' => $mail->getRecipients(),
                    'Subject' => $mail->getSubject(),
                    'Subject - plain' => $mail->getPlainSubject(),
                    'To' => $mail->getTo(),
                    'Charset' => $mail->getCharset(),
                    'Date' => $mail->getDate(),
                    'Html body' => $mail->getBodyHtml(),
                    'Text body' => $mail->getBodyText(),
                    'Default reply to' => $mail->getDefaultReplyTo(),
                    'Header encoding' => $mail->getHeaderEncoding(),
                    'Message ID' => $mail->getMessageId(),
                    'Mime' => $mail->getMime(),
                    'Mime boundary' => $mail->getMimeBoundary(),
                    'Part count' => $mail->getPartCount(),
                    'Parts' => $mail->getParts(),
                    'Type' => $mail->getType(),
                ),
                'content' => $mail->getPlainBody()
            );
        }

        return $data;
    }

    /**
     * Internal helper function which returns all template data
     * of the current request.
     *
     * @param Enlight_Event_EventArgs $arguments
     * @return array
     */
    private function getTemplateData(Enlight_Event_EventArgs $arguments)
    {
        $template = Shopware()->Template();
        /**@var $view Enlight_View_Default */
        $view = $arguments->getSubject()->View();
        $viewTemplate = $view->Template();

        $data = array(
            'Loaded templates' => explode('|', $viewTemplate->template_resource),
            'Cache directory' => $template->getCacheDir(),
            'Compile directory' => $template->getCompileDir(),
            'Config directory' => $template->getConfigDir(),
            'Config variables' => $template->getConfigVars(),
            'Debug template' => $template->getDebugTemplate(),
            'Plugin directories' => $template->getPluginsDir(),
            'Template directories' => $template->getTemplateDir(),
            'Assignments' => $view->getAssign(),
        );

        return $data;
    }

    /**
     * Internal helper function which returns all shopware data.
     *
     * @return array
     */
    private function getSessionData()
    {
        $session = Shopware()->Session();
        $sessionData = $session->getIterator()->getArrayCopy();
        $sessionData['id'] = Shopware()->SessionID();

        return $sessionData;
    }

    /**
     * Internal helper function which returns the whole request data
     * as array.
     *
     * @return array
     */
    private function getRequestData()
    {
        $request = Shopware()->Front()->Request();

        return array(
            'Class' => get_class($request),
            'Module' => $request->getModuleName(),
            'Controller' => $request->getControllerName(),
            'Action' => $request->getActionName(),
            'IP' => $request->getClientIp(),
            'Http host' => $request->getHttpHost(),
            'Request uri' => $request->getRequestUri(),
            'Scheme' => $request->getScheme(),
            'Server' => $request->getServer(),
            'Base url' => $request->getBaseUrl(),
            'Base url (raw)' => $request->getBaseUrl(true),
            'Parameters' => $request->getParams(),
            'Path information' => $request->getPathInfo(),
            'Base path' => $request->getBasePath(),
            'Header' => (function_exists('getallheaders')) ? getallheaders() : array(),
        );
    }

    /**
     * Internal helper function which returns the response data as array.
     *
     * @return array
     */
    private function getResponseData()
    {
        $response = Shopware()->Front()->Response();

        return array(
            'Class' => get_class($response),
            'Raw header' => $response->getRawHeaders(),
            'Response code' => $response->getHttpResponseCode(),
            'Exception' => $response->getException()
        );
    }

    /**
     * Helper function to add two new event listeners for the passed
     * event. This is used to log all fired request events.
     *
     * @param $event
     * @return array
     */
    public function getAdditionalListeners($event)
    {
        return array(
            new Enlight_Event_Handler_Default($event, array($this, 'onEvent'), -1000),
            new Enlight_Event_Handler_Default($event, array($this, 'onEvent'), 1000)
        );
    }

    /**
     * Internal listener function of each fired event in shopware.
     *
     * @param Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function onEvent(Enlight_Event_EventArgs $args)
    {
        if ($this->preventEventLog) {
            return $args->getReturn();
        }

        $event = $args->getName();
        $this->events[$event]['returns'][] = $args->getReturn();
        $this->events[$event]['time'][] = microtime(true);

        return $args->getReturn();
    }

    /**
     * Helper function to get all traced events as array data.
     *
     * @return mixed
     */
    private function getEvents()
    {
        foreach ($this->events as &$event) {
            $event['duration'] = round($event['time'][1] - $event['time'][0], 5);
        }

        return $this->events;
    }

    /**
     * Global function to add a fired event to the internal events array
     * which will be displayed in the event tab of the toolbar.
     *
     * @param $event
     * @param string $type
     * @param array $listeners
     * @param null $eventArgs Enlight_Event_EventArgs
     */
    public function addEvent($event, $type = '', $listeners = array(), $eventArgs = null)
    {
        if ($this->preventEventLog) {
            return;
        }
        if (!array_key_exists($event, $this->events)) {
            $data = array();
            /**@var $listener Enlight_Event_Handler_Default */
            foreach ($listeners as $listener) {
                $temp = $listener->getListener();
                if (is_array($temp)) {
                    $class = get_class($temp[0]);
                    $function = $temp[1];
                } else {
                    /**@var $listener Enlight_Event_Handler_Plugin */
                    $class = $listener->Plugin()->getClassName();
                    $function = $listener->getListener();
                }
                if ($class === 'Shopware_Plugins_Frontend_SwagProfiling_Bootstrap') {
                    continue;
                }

                $this->listenerCount++;
                $data[] = array(
                    'class' => $class,
                    'function' => $function,
                    'position' => $listener->getPosition(),

                );
            }

            /**@var $eventArgs Enlight_Event_EventArgs */
            $params = array();
            if ($eventArgs instanceof Enlight_Event_EventArgs) {
                foreach ($eventArgs->getIterator() as $key => $value) {
                    if (is_object($value) || is_array($value)) {
                        $params[$key] = get_class($value);
                    } else {
                        $params[$key] = $value;
                    }
                }
            } elseif (is_array($eventArgs)) {
                foreach ($eventArgs as $key => $value) {
                    if (is_object($value) || is_array($value)) {
                        $params[$key] = get_class($value);
                    } else {
                        $params[$key] = $value;
                    }
                }
            }

            $this->events[$event] = array(
                'type' => $type,
                'listeners' => $data,
                'returns' => array(),
                'params' => $params
            );
        }
    }

    /**
     * Event listener function of the GetPhpInfo controller action of the
     * detail controller.
     *
     * @param Enlight_Event_EventArgs $arguments
     */
    public function onGetPhpInfo(Enlight_Event_EventArgs $arguments)
    {
        $arguments->getSubject()->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!empty($_SERVER["REMOTE_ADDR"])
            && !empty($this->Config()->ipLimitation)
            && strpos($this->Config()->ipLimitation, $_SERVER["REMOTE_ADDR"]) === false
        ) {
            return;
        }

        echo phpinfo();
        exit();
    }

    /**
     * Event listener function of the ClearCache action which called
     * over the profiling plugin if the user clicks on the right toolbar button.
     *
     * @param Enlight_Event_EventArgs $arguments
     */
    public function onClearCache(Enlight_Event_EventArgs $arguments)
    {
        $arguments->getSubject()->Front()->Plugins()->ViewRenderer()->setNoRender();

        if (!empty($_SERVER["REMOTE_ADDR"])
            && !empty($this->Config()->ipLimitation)
            && strpos($this->Config()->ipLimitation, $_SERVER["REMOTE_ADDR"]) === false
        ) {
            return;
        }

        Shopware()->Cache()->clean();
        Shopware()->Template()->clearAllCache();
        Shopware()->Template()->clearCompiledTemplate();
        $this->clearSearchCache();
        $this->clearRewriteCache();
        $this->clearFrontendCache($arguments->getSubject()->Request());
        $this->clearProxyCache();
        $this->clearConfigCache();
        exit();
    }

    /**
     * Clear search cache
     */
    protected function clearConfigCache()
    {
        Shopware()->Cache()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
            array(
                'Shopware_Config',
                'Shopware_Plugin'
            )
        );
    }

    /**
     * Clear proxy cache
     *
     * Clears:
     * - Shopware Proxies
     * - Classmap
     * - Doctrine-Proxies
     * - Doctrine-Anotations
     * - Doctrine-Metadata
     */
    protected function clearProxyCache()
    {
        $configuration = Shopware()->Models()->getConfiguration();
        $metaDataCache = $configuration->getMetadataCacheImpl();
        if (method_exists($metaDataCache, 'deleteAll')) {
            $metaDataCache->deleteAll();
        }

        // Clear Shopware Proxies
        Shopware()->Hooks()->getProxyFactory()->clearCache();

        // Clear classmap
        $classMap = Shopware()->Hooks()->getProxyFactory()->getProxyDir() . 'ClassMap_' . \Shopware::REVISION . '.php';
        @unlink($classMap);

        // Clear Doctrine Proxies
        $files = new GlobIterator(
            $configuration->getProxyDir() . '*.php',
            FilesystemIterator::CURRENT_AS_PATHNAME
        );

        foreach ($files as $filePath) {
            @unlink($filePath);
        }

        // Clear Anotation file cache
        $files = new GlobIterator(
            $configuration->getFileCacheDir() . '*.php',
            FilesystemIterator::CURRENT_AS_PATHNAME
        );

        foreach ($files as $filePath) {
            @unlink($filePath);
        }
    }

    /**
     * Helper function to clear the caches
     */
    private function clearFrontendCache($request)
    {
        if ($request->getHeader('Surrogate-Capability') === false) {
            return true;
        }

        $proxyUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBaseUrl() . '/';

        try {
            $client = new Zend_Http_Client(
                null,
                array(
                    'useragent' => 'Shopware/' . Shopware()->Config()->version,
                    'timeout' => 5,
                )
            );
            $client->setUri($proxyUrl)->request('BAN');
        } catch (Exception $e) {
            return false;
        }

        try {
            Shopware()->Db()->exec('TRUNCATE s_cache_log');
        } catch (\Exeption $e) {
            Shopware()->Db()->exec('DELETE FROM s_cache_log');
        }

        return true;
    }

    /**
     * Helper function to clear the caches
     */
    protected function clearSearchCache()
    {
        $sql = "SELECT `id` FROM `s_core_config_elements` WHERE `name` LIKE 'fuzzysearchlastupdate'";
        $elementId = Shopware()->Db()->fetchOne($sql);

        $sql = 'DELETE FROM s_core_config_values WHERE element_id=?';
        Shopware()->Db()->query($sql, array($elementId));
    }

    /**
     * Helper function to clear the caches
     */
    protected function clearRewriteCache()
    {
        $cache = (int) Shopware()->Config()->routerCache;
        $cache = $cache < 360 ? 86400 : $cache;

        $sql = "SELECT `id` FROM `s_core_config_elements` WHERE `name` LIKE 'routerlastupdate'";
        $elementId = Shopware()->Db()->fetchOne($sql);

        $sql = "
            SELECT v.shop_id, v.value
            FROM s_core_config_values v
            WHERE v.element_id=?
        ";
        $values = Shopware()->Db()->fetchPairs($sql, array($elementId));

        foreach ($values as $shopId => $value) {
            $value = unserialize($value);
            $value = min(strtotime($value), time() - $cache);
            $value = date('Y-m-d H:i:s', $value);
            $value = serialize($value);
            $sql = '
                UPDATE s_core_config_values SET value=?
                WHERE shop_id=? AND element_id=?
            ';
            Shopware()->Db()->query($sql, array($value, $shopId, $elementId));
        }
    }
}
