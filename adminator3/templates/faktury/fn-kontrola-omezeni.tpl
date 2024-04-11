{include file="base.tpl"}

{include file="platby/inc.cat.platby-fn.tpl"}

  <div style="margin-left: 5px; margin-top: 5px;" >
      <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">{$nadpis}</div>
    
      <div style="padding-bottom: 10px;">počet neuhr. faktur: <b>{$faktury_pocet}</b></div>
      
      <div style="padding-bottom: 10px;">počet omezených vlastníků: <b>{$vlastnici_pocet}</b></div>
      
      {section name="prvek" loop=$pole_data}
	    
	  {$pole_data[prvek]}
      				   
      {sectionelse}
	  {* Tato cast se provede v pripade prazdneho pole *}
			 
	  <div class="fn-no-record" >Chyba! Žádné omezené objekty nenalezeny ..</div>
							 
      {/section}
							       
  </div>

{include file="base-end.tpl"}
