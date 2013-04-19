<div class="mail-wrapper">
    {foreach $profiling.mails as $mail}
        <table class="table">
            <thead>
            <tr>
                <th style="width: 200px">Property name</th>
                <th style="width: 2000px;">Property value</th>
            </tr>
            </thead>
            <tbody>
                {foreach $mail.information as $key => $value}
                <tr>
                    <td>{$key}</td>
                    <td>{include file="frontend/plugins/profiling/details/array.tpl" array=$value}</td>
                </tr>
                {/foreach}
                <div class="spacer"></div>
                <tr class="mail-content">
                    <td colspan="2">
                        {$mail.content}
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="spacer"></div>
    {/foreach}
</div>
