{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

<div style="padding-left: 4px; padding-bottom: 5px; padding-top: 5px; border-bottom: 1px gray solid; width: 20%; ">
Vlastníci - FO
</div>

{include file="vlastnici/vlastnici-inc.links-panel.tpl"}

<div style="font-weight: bold; padding-top: 5px; border-bottom: 1px gray solid; width: 20%; " >
</div>

{* hledaci a filtrovaci prvky *}
{include file="vlastnici/vlastnici-inc.selectors.tpl"}

<div class="vlastnici-table" style="padding-right: 5px;" >
{$body}
</div>

{* konecny soubor *}
{include file="base-end.tpl"}
