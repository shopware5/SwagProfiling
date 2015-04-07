<div class="array-wrapper {$expanded}">
{foreach $array as $key => $value}
    {if $value|is_array}
        <div class="item array">
        <button class="toggle btn btn-mini btn-super-mini" type="button">+</button>
        <span class="item-key">{$key}</span><span class="array-string"> => Array (</span>
            {include file="frontend/plugins/swag_profiling/details/array.tpl" array=$value expanded="collapsed"}
        <span class="array-string">)</span></div>
    {elseif $value|is_object}
    <div class="item">
        <span class="item-key">{$key}</span>
        =>
        <span class="item-value">
            {$value|get_class}
        </span>
    </div>
    {else}
        <div class="item">
            <span class="item-key">{$key}</span>
            =>
            <span class="item-value">
                {if $value===null}
                    null
                {elseif $value===false}
                    false
                {else}
                    {$value}
                {/if}
            </span>
        </div>
    {/if}
{/foreach}
</div>
