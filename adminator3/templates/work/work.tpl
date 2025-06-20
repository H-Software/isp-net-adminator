{include file="base.tpl"}

<div style="padding-left: 5px;" >

<form method="POST" action="{$action}" >

 <div style="width: 100px; float: left; font-weight: bold;" >RESTART: </div>

 <div style="float: left; padding-left: 20px;" >
    <input type="checkbox" value="1" name="item1" id="item1" onclick='return !restart_item(1);' >
    <label> :: wifi iptables & shaper  </label>
 </div>

 <div style="float: left; padding-left: 20px;" >
    <input type="checkbox" value="1" name="item2" id="item2" onclick='return !restart_item(2);' >
    <label> :: dns </label>
 </div>

 <div style="float: left; padding-left: 20px;" >
    <input type="checkbox" value="1" name="item3" id="item3" onclick='return !restart_item(3);' >
    <label> :: optika all ( shape, ipt, radius )</label>
 </div>

 <div style="float: left; padding-left: 30px;" >
    <input type="hidden" value="true" name="akce" >
    <input type="submit" value="OK" name="odeslat" >
 </div>
 </form>

 <div style="clear: both; padding-bottom: 10px;" ></div>
 
 <div class="work-main-window" id='restart-stav' ><div id='work-vyberte-akci' >Vyberte požadovanou akci:</div></div>
 
</div>

{include file="base-end.tpl"}
