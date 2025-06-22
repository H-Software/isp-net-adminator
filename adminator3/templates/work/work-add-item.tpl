<div style="padding-left: 5px;" >

<form method="POST" >
 {$csrf_html}

 <div style="width: 30%; float: left; font-weight: bold;" >Manuální přidání akce pro restart: </div>

 <div style="float: left; padding-left: 20px;" >

    <select size="1" name="single_action" >
        <option value="0" class="select-nevybrano" >Nevybráno</option>
        {foreach $items_list_select as $v}
            <option value="{$v.id}" >{$v.name}</option>
        {/foreach}
    </select>

 </div>

 <div style="float: left; padding-left: 30px;" >
    <input type="hidden" value="true" name="akce" >
    <input type="submit" value="OK" name="odeslat" >
 </div>
 </form>
 <div style="clear: both; padding-bottom: 10px;" ></div>
 
</div>
