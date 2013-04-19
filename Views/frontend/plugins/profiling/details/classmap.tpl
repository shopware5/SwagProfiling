<div class="classmap-wrapper">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Class</th>
            <th style="width: 2000px;">File</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.classMap as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>