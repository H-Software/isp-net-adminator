{include file="base.tpl"}

{include file="inc.cat.others.tpl"}

<div style="padding-left: 5px; padding-top: 10px; height: 100%" >

<div style="font-size: 24px; font-weight: bold; padding-bottom: 30px;" >Zobrazení vygenerovaného souboru/div>

{if strlen($alert_type) gt 0}
    {include file="partials/bootstrap-alert-with-columns.tpl"}
{/if}
</div>

{include file="base-end.tpl"}
