{include file="base.tpl"}

{include file="objekty/inc.subcat.tpl"}

{if $p_bs_alerts|default:'0' }
    {include file="partials/bootstrap-alert-with-columns-array.tpl"}
{/if}

{$body}

{include file="base-end.tpl"}
