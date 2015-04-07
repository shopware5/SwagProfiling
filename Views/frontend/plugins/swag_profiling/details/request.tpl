<div class="request-wrapper">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Property name</th>
            <th style="width: 2000px;">Property value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.request as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/swag_profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="spacer"></div>
    <h2>Response</h2>
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Property name</th>
            <th style="width: 2000px;">Property value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.response as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/swag_profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>