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

        <div class="clear-cache"><span class="element-content">Clear cache</span>
            <input type="hidden" name="clear-cache-url" value="{url controller=detail action=ClearCache}" />
        </div>

        <div class="config bar-element"><span class="element-content">{$profiling.short.config}</span></div>
        <span class="spacer"></span>
        <div class="request bar-element"><span class="element-content">{$profiling.short.request}</span></div>
        <span class="spacer"></span>
        <div class="events bar-element"><span class="element-content">{$profiling.short.eventCount}x Events / {$profiling.short.listenerCount}x Listeners</span></div>
        <span class="spacer"></span>
        <div class="queries bar-element"><span class="element-content">{$profiling.short.queryCount} ({$profiling.short.queryTime} ms) > {$profiling.short.slowQueries}</span></div>
        <span class="spacer"></span>
        <div class="emails bar-element"><span class="element-content">{$profiling.short.mails} Mails</span></div>
        <span class="spacer"></span>
        <div class="cache bar-element"><span class="element-content">{$profiling.short.cacheFiles}x Cached files</span></div>
        <span class="spacer"></span>
        <div class="php-info bar-element"><span class="element-content">{$profiling.short.php}</span></div>
        <span class="spacer"></span>
        <div class="memory bar-element"><span class="element-content">{$profiling.short.memory} MB</span></div>
        <span class="spacer"></span>
        <div class="trace bar-element"><span class="element-content">{$profiling.trace|count}x Function calls</span></div>
        <span class="spacer"></span>
        <div class="ajax bar-element"><span class="element-content">0</span>x Ajax requests</div>
        <span class="spacer"></span>
        <div class="template bar-element"><span class="element-content">{$profiling.short.templates}x Templates</span></div>
        <span class="spacer"></span>

    </div>

    <div class="clear"></div>

    {include file="frontend/plugins/swag_profiling/detail.tpl"}

    <div class="clear"></div>

{/block}