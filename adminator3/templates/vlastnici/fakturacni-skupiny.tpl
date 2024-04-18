{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

<div class="row mt-3 mb-3 ml-3">
    <div class="col " style="font-size: 18px;">
        Výpis fakturačních skupin
    </div>
    <div class="col">
        <a href="">Přidání</a>
    </div>
    <div class="col-6"></div>
</div>

<div class="row">
    <div class="col">
    {if strlen($message_no_items) gt 0}
        <div class="alert alert-warning" role="alert">
            {$message_no_items}
        </div>
    {/if}
    </div>
</div>

<div class="row">
    <div class="col">
        {* main table *}
        {if $fs_items|@count gt 0}
        {$fs_items}
        {/if}
        {* end of main table *}
    </div>
</div>
{* konecny soubor *}
{include file="base-end.tpl"}
