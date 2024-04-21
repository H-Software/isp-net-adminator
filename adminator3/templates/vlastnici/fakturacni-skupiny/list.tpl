{include file="base.tpl"}

{* zobrazeni subkategorie *}
{include file="vlastnici/inc.cat.vlastnici.tpl"}

<div class="row mt-3 mb-3 ml-3">
    <div class="col " style="font-size: 18px;">
        Výpis fakturačních skupin
    </div>
    <div class="col">
        <a href="/vlastnici2/fakturacni-skupiny/action">Přidání</a>
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
            <table class="fakturacni-skupiny-table" >
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
         
                <tr class=fs-border">
                    <td><span style="color: gray;" >H</span></td>
                    <td colspan="3" style="color: grey;" >Fakturační text</td>
                    
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

                            <td>
                            {if $v.typ eq '1'}
                                DÚ
                            {elseif $v.typ eq '2' }
                                FÚ
                            {else}
                                N/A ({$v.typ})
                            {/if}
                            </td>

                            {if $v.typ_sluzby eq '0'}
                                <td align="center" class="" bgcolor="#99FF99" >wifi</td>
                            {elseif $v.typ_sluzby eq '1' }
                                <td align="center" class="" bgcolor="#fbbc86" >optika</td>
                            {else}
                                <td>N/A ({$v.typ_sluzby})</td>
                            {/if}

                            <td><span>&nbsp;</span></td>
			                <td>&nbsp;</td>

                            <td>{include file="global/fs.convert-sluzba-number.tpl" tpl_sluzba_number = {$v.sluzba_int} }</td>
                            <td>{include file="global/fs.convert-sluzba-number.tpl" tpl_sluzba_number = {$v.sluzba_iptv} }</td>
                            <td>{include file="global/fs.convert-sluzba-number.tpl" tpl_sluzba_number = {$v.sluzba_voip} }</td>

                            <td><span>&nbsp;</span></td>
			                <td>&nbsp;</td>

                            <td>
                                <a href="/vlastnici2/fakturacni-skupiny/action?update_id={$v.id|escape:'url'}" >upravit</a>
                            </td>
                            <td>
                                <a href="/vlastnici2/fakturacni-skupiny/delete?erase_id={$v.id|escape:'url'}" >smazat</a>
                            </td>

                            <td><span>&nbsp;</span></td>
			                <td>&nbsp;</td>
                        </tr>
                        <tr class=fs-border">
                            <td><span style="color: grey; " ><a href="/archiv-zmen?id_fs={$v.id|escape:'url'}">H</a></span></td>

                            <td colspan="3" ><span style="color: grey;" >{$v.fakturacni_text}</span></td>

                            <td><span>&nbsp;</span></td>
			                <td>&nbsp;</td>

                            <td>
                                <span style="color: grey; " >
                                {if $v.sluzba_int_id_tarifu gt '0'}
                                    <a href="/admin/tarify?id_tarifu={$v.sluzba_int_id_tarifu|escape:'url'}">ID {$v.sluzba_int_id_tarifu}</a>
                                {/if}
                                </span>
                            </td>
                            <td>
                                <span style="color: grey; " >
                                {if $v.sluzba_iptv_id_tarifu gt '0'}
                                    ID {$v.sluzba_iptv_id_tarifu}
                                {/if}
                                </span>
                            </td>
                            
                            <td>&nbsp;</td>

                            <td><span style="" >&nbsp;</span></td>
                            <td>&nbsp;</td>
                        
                            <td colspan="2" >&nbsp;</td>
                            
                            <td><span style="" >&nbsp;</span></td>
                            <td>&nbsp;</td>
                        </tr>
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
