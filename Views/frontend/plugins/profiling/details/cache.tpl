<div class="cache-wrapper">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Property name</th>
            <th style="width: 2000px;">Property value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.cache.options as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div class="spacer"></div>

    <h2>Cache - Files</h2>
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Cache id</th>
            <th style="width: 2000px;">Meta data</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.cache.metaData as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>

</div>