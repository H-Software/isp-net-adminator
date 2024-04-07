{include file="base.tpl"}

{include file="inc.cat.platby-fn.tpl"}

  <div style="margin-left: 5px; margin-top: 5px;" >

    <form method="GET" action="{$form_action1}" >
    
      <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px; float: left; margin-right: 80px;">{$nadpis}</div>  
      <div style="float: left; padding-right: 20px;">Vyberte akci:</div>
      <div style="float: left; padding-right: 20px;">
        <input type="checkbox" name="mod" value="2" id="item1" <!-- onclick='return !restart_item(1);' --> >
	<span style="padding-left: 3px; padding-right: 3px;">::</span>Vygenerování logu
      </div>
      <div style="float: left; padding-right: 10px;" >
        <input type="checkbox" name="mod" value="1" >
	<span style="padding-left: 3px; padding-right: 3px;">::</span>Zobrazení logu
      </div>
  
      <div style="margin-right: 10px;" ><input type="submit" name="odeslat" value="OK" ></div>

    </form>
      
      <div style="font-size: 18px; color: red;">{$chyba}</div>
          
      <div id='stav' ></div>
      
    {if $mod == 1 } {* zobrazeni formu pro vyber souboru *}
      
        <form action="{$form_action}" method="GET">
    	  <input type="hidden" name="mod" value="3">
	  
	  <div style="width: 400px; padding-bottom: 20px; padding-top: 15px; float: left;" >
	  
	    <div style="float: left; width: 200px; padding-bottom: 2px;" >Výpis vygenerovaných souborů:</div>
		  
	    <div style="float: left; width: 70px; padding-left: 105px; padding-bottom: 2px;" >
	              <input type="submit" name="od3" value="Zobrazit" >
	    </div>
					      
	    <div>
		
	      <select size="5" name="soubor" style="width: 370px;" >
							       
	       {section name="prvek" loop=$soubory}
	          <option value="{$soubory[prvek]}" >{$soubory[prvek]}</option>
	       {sectionelse}
	  	  <option value="0" class="select-nevybrano" >Žádný soubor pro tento typ dokumentu nenalezen</option>
	       {/section}
	      </select>
	
	    </div>
	  </div>
	</form>

    {elseif $mod == 2} {* vygenerovani logu *}
	
        <div style="padding-top: 5px; padding-bottom: 10px;">počet kontrolovaných vlastníků: <b>{$vlastnici_pocet}</b></div>
      
	<div>Soubor s výsledkem akce vygenerován, zobrazit ho můzete <a href="fn-kontrola-omezeni.php?mod=3&soubor={$nazev_souboru2}">zde</a></div>

    {elseif $mod == 3} {* zobrazeni logu, resp. vybraneho souboru *}
	
         <div style="padding-bottom: 5px;">
	    <span style="font-size: 14px; font-weight: bold;">Zobrazení logu:</span> 
	    <span style="font-size: 12px;">{$nazev_logu}
	    <span style="padding-left: 5px;"><a href="{$nazev_logu}" target="_new">soubor</a></span></span>
	 </div>
	
	 {section name="prvek" loop=$data_z_xml}
	    {$data_z_xml[prvek]}
	 {sectionelse}
	    <div class="fn-no-record" >Žádné záznamy v XML souboru nenalezeny ..</div>
 	 {/section}
	 	
    {/if}
      							       
  </div>

{include file="base-end.tpl"}
