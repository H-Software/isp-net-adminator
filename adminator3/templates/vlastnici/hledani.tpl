{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

<div style="padding-left: 4px; padding-bottom: 5px; padding-top: 5px; ">
    <span style="">
        Vlastníci - globální hledání
    </span>
</div>

<div style="font-weight: bold; padding-top: 5px; border-bottom: 1px gray solid; width: 20%; " >
</div>

{* hledaci a filtrovaci prvky *}
{include file="vlastnici/hledani-inc.selectors.tpl"}

{if $bodyNoData|count_characters > 1}
{$bodyNoData}
{else}

<div class="vlastnici-archiv-table" style="padding-right: 5px;" >

{$body1}

<tr>
    <td colspan="10"><hr></td>
</tr>

<tr>
    <td colspan="10" height="40px" >
        <span style="font-size: 20px; font-weight: bold; color: navy; " >
            Výsledek hledání výrazu: "{$form_select}" v sekci "vlastníci"
        </span>
    </td>
</tr>

{$body2}

<tr>
    <td colspan="10"><hr></td>
</tr>

<tr>
    <td colspan="10" height="40px" >
        <span style="font-size: 20px; font-weight: bold; color: navy; " >
            Výsledek hledání výrazu: "{$form_select}" v sekci "vlastníci2"</span>
    </td>
</tr>

{$body3}

</div>

{/if}



{* konecny soubor *}
{include file="base-end.tpl"}
