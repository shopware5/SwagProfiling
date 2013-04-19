<div class="exception-wrapper">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Property name</th>
            <th style="width: 2000px;">Property value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.exception as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>