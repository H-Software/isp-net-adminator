{include file="base.tpl"}

{include file="objekty/inc.subcat.tpl"}

{if $p_bs_alerts|default:'0' }
    <div style="padding-top: 5px">
    {include file="partials/bootstrap-alert-with-columns-array.tpl"}
    </div>
{/if}

{$body}

{include file="base-end.tpl"}
