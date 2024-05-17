{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

{* <div style="padding-left: 4px; padding-bottom: 5px; padding-top: 5px; ">
    <span style="">
        Výpis odpojených klientů    
    </span>

    <span style="padding-left: 25px; ">
        {if $vlastnici_archiv_export_povolen eq "true"}
            <a href="\export\vlastnici-archiv.xls" >export dat</a>
        {else}
            export dat (N/A)
        {/if}
    </span>
</div> *}

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

{$body3}
</div>

{/if}



{* konecny soubor *}
{include file="base-end.tpl"}
