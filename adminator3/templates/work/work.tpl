{include file="base.tpl"}

<div style="padding-left: 5px;" >

{if $p_bs_alerts|default:'0' }
    <div style="padding-top: 5px">
    {include file="partials/bootstrap-alert-with-columns-array.tpl"}
    </div>
{/if}

{include file="work/work-queue-table.tpl"}

</div>

{include file="base-end.tpl"}
