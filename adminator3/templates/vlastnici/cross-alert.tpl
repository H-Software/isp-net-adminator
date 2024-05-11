{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

<div style="font-weight: bold; padding-top: 20px;  " >
</div>

{if $alert_type|count_characters > 1}
    {include file="partials/bootstrap-alert-with-columns.tpl"}
 {/if}

{* konecny soubor *}
{include file="base-end.tpl"}
