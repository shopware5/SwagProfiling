<div class="session-wrapper">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Property name</th>
            <th style="width: 2000px;">Property value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.session as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div class="spacer"></div>
    <h2>Cookies</h2>
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Cookie name</th>
            <th style="width: 2000px;">Cookie value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.cookies as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>

</div>
