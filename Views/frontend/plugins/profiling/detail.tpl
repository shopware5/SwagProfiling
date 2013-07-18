
<div class="developer-bar-details">
    <div class="developer-header">
        <img class="developer-logo" src="{link file='frontend/_resources/img/shopware-dev-toolbar.png'}" width="291" height="28">
        <a class="close-button" href="#"><span class="button-text">Display <strong>storefront</strong></span></a>
    </div>
    <div class="developer-content">
        <div class="navigation">
            <ul class="nav nav-list">
                <li class="config active"><a href="#config">Config</a></li>
                <li class="request"><a href="#request">Request</a></li>
                <li class="session"><a href="#session">Session</a></li>
                <li class="template"><a href="#template">Template</a></li>
                <li class="events"><a href="#events">Events</a></li>
                <li class="queries"><a href="#queries">Queries</a></li>
                <li class="emails"><a href="#emails">Emails</a></li>
                <li class="dev-basket"><a href="#dev-basket">Basket</a></li>
                <li class="cache"><a href="#cache">Cache</a></li>
                <li class="exception"><a href="#exception">Exception</a></li>
                <li class="class-map"><a href="#class-map">Class map</a></li>
                <li class="php-info"><a href="#php-info">PHP</a></li>
                <li class="trace"><a href="#trace">Trace</a></li>
                <li class="ajax"><a href="#ajax">Ajax Requests</a></li>
            </ul>
        </div>

        <div class="inner-content">
            <div class="config active element-content">
                {include file="frontend/plugins/profiling/details/config.tpl"}
            </div>

            <div class="request element-content">
                {include file="frontend/plugins/profiling/details/request.tpl"}
            </div>

            <div class="session element-content">
                {include file="frontend/plugins/profiling/details/session.tpl"}
            </div>

            <div class="template element-content">
                {include file="frontend/plugins/profiling/details/template.tpl"}
            </div>

            <div class="events element-content">
                {include file="frontend/plugins/profiling/details/events.tpl"}
            </div>

            <div class="queries element-content">
                {include file="frontend/plugins/profiling/details/queries.tpl"}
            </div>

            <div class="emails element-content">
                {include file="frontend/plugins/profiling/details/mail.tpl"}
            </div>

            <div class="dev-basket element-content">
                {include file="frontend/plugins/profiling/details/basket.tpl"}
            </div>

            <div class="exception element-content">
                {include file="frontend/plugins/profiling/details/exception.tpl"}
            </div>

            <div class="class-map element-content">
                {include file="frontend/plugins/profiling/details/classmap.tpl"}
            </div>

            <div class="cache element-content">
                {include file="frontend/plugins/profiling/details/cache.tpl"}
            </div>

            <div class="trace element-content">
                {include file="frontend/plugins/profiling/details/trace.tpl"}
            </div>

            <div class="ajax element-content">
                {include file="frontend/plugins/profiling/details/ajax.tpl"}
            </div>

            <div class="php-info element-content">
                <iframe src="{url controller=detail action=getPhpInfo}" width="800" scrolling="no" id="phpFrame"></iframe>
            </div>
        </div>
    </div>

    <div class="clear"></div>
    <div class="developer-footer">

    </div>

</div>
