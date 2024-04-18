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
    <div class="col-12">
        {* main table *}
        {if $fs_items|@count gt 0}
            <table class="table fs-6">
                <thead>
                <tr>
                    <td><b><div class="pl-13">id</b></div></td>
                    <td><b>název skupiny</b></td>
                    <td><b>typ</b></td>
                    <td align="center" ><b>typ služby</b></td>
            
                    <td><span style="" >&nbsp;</span></td>
                    <td>&nbsp;</td>
            
                    <td colspan="" ><span style="font-weight: bold; " >Internet</span></td>
                    <td><span style="font-weight: bold; width: 150px; " >IPTV</span></td>
                    <td><span style="font-weight: bold; " >VoIP</span></td>
                    <td><span style="" >&nbsp;</span></td>
                    <td>&nbsp;</td>
                
                    <td><span style="font-weight: bold; " >upravit</td>
                    <td><span style="font-weight: bold; " >smazat</td>
                
                    <td><span style="" >&nbsp;</span></td>
                    <td>&nbsp;</td>
                    
                </tr>
         
                <tr>
                    <td><span style="color: gray;" >lidí</span></td>
                    <td colspan="3" >Fakturační text</td>
                    
                    <td><span style="" >&nbsp;</span></td>
                    <td>&nbsp;</td>
            
                    <td>tarif</td>
                    <td>tarif</td>
                    <td></td>
                
                    <td><span style="" >&nbsp;</span></td>
                    <td>&nbsp;</td>
                    <td colspan="2" >&nbsp;</td>
            
                    <td><span style="" >&nbsp;</span></td>
                    <td>&nbsp;</td>
            
                </tr>
                </thead>

                <tbody>
                    {foreach $fs_items as $v}
                        <tr>
                            <td>{$v.id}</td>
                            <td>{$v.nazev}</td>
                            <td>{$v.typ}</td>
                            <td>{$v.typ_sluzby}</td>
                        </tr>
                        <tr>
                        </td>
                    {/foreach}
                </tbody>
            </table>
        {/if}
        {$fs_items_debug}
        {* end of main table *}
    </div>
</div>
{* konecny soubor *}
{include file="base-end.tpl"}
