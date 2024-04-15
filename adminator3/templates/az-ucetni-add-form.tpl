{include file="base.tpl"}

{include file="archiv-zmen/inc.cat.archiv-zmen.tpl"}

<div style="padding-left: 5px;" >
    <div class="zmeny-ucetni-banner-add" >Archiv změn pro účetní - přidání změny</div>

    <form name="form1" method="post" action="{$action}" >
    <input type="hidden" name="send" value="true" >
    <input type="hidden" name="update_id" value="{$update_id}" >
    {$csrf_html}
    
    <div style="width: 200px; float: left;" >Vyberte typ: </div>
    <div>
      <select size="1" name="typ" >
            <option value="0" class="select-nevybrano" >Nevybráno</option>
	 {section name="prvek" loop=$typ}
	    <option value="{$typ[prvek].id}" 
	    {if $typ[prvek].id == $typ_select} selected {/if} >
	    {$typ[prvek].nazev}</option>
	 {/section}
      </select>
    </div>

    <div style="width: 200px; float: left; padding-top: 20px;" >Zadajte text:</div>
    <div style="padding-top: 10px;" >
	<textarea name="text" cols="40" rows="5">{$text}</textarea>
    </div>

    <div style="padding-left: 40px; padding-top: 20px;" >    
	<input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" class="zu-form-ok-button" >
    </div>

    </form>

    {$error}
    {$info}
    
</div>

{include file="base-end.tpl"}
