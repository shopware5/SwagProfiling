<div class="query-wrapper">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 20px">
                <a class="toggle-all button">Toggle</a>
                {*<button class="toggle-all btn btn-mini" type="button">Toggle</button>*}
            </th>
            <th style="width: 250px;">Params</th>
            <th style="width: 2000px;">Query</th>
            <th style="width: 600px;">Explain</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.queries as $query}
            <tr class="{$query.status.cls} short-query">
                <td style="vertical-align: middle; text-align: center;">
                    <a class="toggle button">Toggle</a>
                    {*<button class="toggle btn btn-mini" type="button">Toggle</button>*}
                </td>
                <td>...</td>
                <td>{$query.short}</td>
                <td>
                    {foreach $query.status.notices as $notice}
                        <span>- {$notice}</span><br>
                    {/foreach}
                </td>
            </tr>
            <tr class="details collapsible">
                <td>{$query.time}</td>
                <td>
                    {foreach $query.params as $param}
                        <pre>{$param|var_dump}</pre>
                    {/foreach}
                </td>

                <td colspan="2"><span class="full-sql-query">{$query.sql}</span></td>
            </tr>
            <tr class="details collapsible">
                <td colspan="4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>select_type</th>
                            <th>table</th>
                            <th>type</th>
                            <th>possible_keys</th>
                            <th>key</th>
                            <th>key_len</th>
                            <th>ref</th>
                            <th>rows</th>
                            <th style="width: 800px;">Extra</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach $query.explain as $explain}
                            <tr>
                                <td>{$explain.id}</td>
                                <td>{$explain.select_type}</td>
                                <td>{$explain.table}</td>
                                <td>{$explain.type}</td>
                                <td>{$explain.possible_keys}</td>
                                <td>{$explain.key}</td>
                                <td>{$explain.key_len}</td>
                                <td>{$explain.ref}</td>
                                <td>{$explain.rows}</td>
                                <td>{$explain.Extra}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </td>
            <tr>
        {/foreach}
        </tbody>
    </table>
</div>