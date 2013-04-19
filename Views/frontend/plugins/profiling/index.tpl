{block name="frontend_index_header_css_screen" append}
    <link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/profiling.css'}" />
{/block}

{block name="frontend_index_header_javascript" append}
    <script src="{link file='frontend/_resources/js/beautify.js'}"></script>
    <script src="{link file='frontend/_resources/js/profiling.js'}"></script>
{/block}


{block name="frontend_index_body_inline" append}
    <div class="clear"></div>

    <div class="developer-bar">
        <div class="config bar-element"><span class="element-content">{$profiling.short.config}</span></div>
        <div class="request bar-element"><span class="element-content">{$profiling.short.request}</span></div>
        <div class="template bar-element"><span class="element-content">{$profiling.short.templates}x Templates</span></div>
        <div class="events bar-element"><span class="element-content">{$profiling.short.eventCount}x Events / {$profiling.short.listenerCount}x Listeners</span></div>
        <div class="queries bar-element"><span class="element-content">{$profiling.short.queryCount} ({$profiling.short.queryTime} ms) > <div class="query-warnings">{$profiling.short.slowQueries}</div></span></div>
        <div class="emails bar-element"><span class="element-content">{$profiling.short.mails} Mails</span></div>
        <div class="cache bar-element"><span class="element-content">{$profiling.short.cacheFiles}x Cached files</span></div>
        <div class="php-info bar-element"><span class="element-content">{$profiling.short.php}</span></div>
        <div class="trace bar-element"><span class="element-content">{$profiling.trace|count}x Function calls</span></div>
        <div class="memory bar-element"><span class="element-content">{$profiling.short.memory} MB</span></div>
        <div class="ajax bar-element"><span class="element-content">0</span>x Ajax requests</div>

        <div class="clear-cache"><span class="element-content">Clear cache</span>
            <input type="hidden" name="clear-cache-url" value="{url controller=detail action=ClearCache}" />
        </div>
    </div>

    <div class="clear"></div>

    {include file="frontend/plugins/profiling/detail.tpl"}

    <div class="clear"></div>

{/block}