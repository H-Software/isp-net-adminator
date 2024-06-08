{include file="base.tpl"}

{include file="objekty/inc.subcat.tpl"}

<div style="font-weight: bold; padding-top: 5px; border-bottom: 1px gray solid; width: 20%; " >
</div>

{* hledaci a filtrovaci prvky *}
{include file="objekty/inc.selectors.tpl"}

{if $p_bs_alerts|default: '' }
    <div style="padding-top: 20px;">
    {include file="partials/bootstrap-alert-with-columns-array.tpl"}
    </div>
{/if}

{$body}

{include file="base-end.tpl"}
