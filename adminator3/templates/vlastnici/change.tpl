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

<div style="padding-right: 5px;" >
{$body}
</div>

{* konecny soubor *}
{include file="base-end.tpl"}
