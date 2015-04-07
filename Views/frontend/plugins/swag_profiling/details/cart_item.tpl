{extends file="parent:frontend/checkout/cart_item.tpl"}

{block name='frontend_checkout_cart_item_delete_article'}
    <div class="action">
        {if $sBasketItem.modus == 0}
            <a href="{url controller='checkout' action='deleteArticle' sDelete=$sBasketItem.id sTargetAction='cart'}" class="del" title="{s namespace='frontend/checkout/cart_item' name='CartItemLinkDelete '}{/s}">
                &nbsp;
            </a>
            &nbsp;
        {/if}
    </div>
{/block}

{block name='frontend_checkout_cart_item_voucher_delete'}
    <div class="action">
        <a href="{url controller='checkout' action='deleteArticle' sDelete=voucher sTargetAction='cart'}" class="del" title="{s name='CartItemLinkDelete'}{/s}">&nbsp;</a>
    </div>
{/block}

{block name='frontend_checkout_cart_item_premium_delete'}
    <div class="action">
        <a href="{url controller='checkout' action='deleteArticle' sDelete=$sBasketItem.id sTargetAction='cart'}" class="del" title="{s name='CartItemLinkDelete'}{/s}">&nbsp;</a>
    </div>
{/block}