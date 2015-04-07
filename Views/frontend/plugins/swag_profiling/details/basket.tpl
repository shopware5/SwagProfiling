<div class="basket-wrapper">
    <table class="table">
        <thead>
        <tr>
            <th style="width: 200px">Property name</th>
            <th style="width: 2000px;">Property value</th>
        </tr>
        </thead>
        <tbody>
        {foreach $profiling.basket as $key => $value}
            <tr>
                <td>{$key}</td>
                <td>{include file="frontend/plugins/swag_profiling/details/array.tpl" array=$value}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div class="spacer"></div>
    <h2>Display</h2>
    <div class="grid_16 {if $sUserLoggedIn}push_2{/if} last" id="basket">
        <div class="table grid_16 cart">
            {block name='frontend_checkout_cart_cart_head'}
                {include file="frontend/checkout/cart_header.tpl"}
            {/block}

            {foreach $profiling.basket.content as $key => $value}
                {include file="frontend/plugins/swag_profiling/details/cart_item.tpl" sBasketItem=$value}
            {/foreach}
        </div>
    </div>
    <div class="space">&nbsp;</div>
</div>