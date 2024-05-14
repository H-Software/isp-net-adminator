{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

<div style="padding-left: 4px; padding-bottom: 5px; padding-top: 5px; ">
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
</div>

<div style="font-weight: bold; padding-top: 5px; border-bottom: 1px gray solid; width: 20%; " >
</div>

{* hledaci a filtrovaci prvky *}
{include file="vlastnici/archiv-inc.selectors.tpl"}

{if $listing|count_characters > 1}
    <div style="border: 1px solid black; " >{$listing}</div>
{/if}

<div class="vlastnici-archiv-table" style="padding-right: 5px;" >
{$body}
</div>

{if $listing|count_characters > 1}
    <div class="text-center" >{$listing}</div>
{/if}

{* konecny soubor *}
{include file="base-end.tpl"}
