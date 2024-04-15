{include file="base.tpl"}

{include file="archiv-zmen/inc.cat.archiv-zmen.tpl"}

<div style="padding-left: 5px;" >
    <div class="zmeny-ucetni-banner" >Změny pro účetní</div>

    <div style="padding-top: 10px;" ><a href="{$link_add}" >Ruční přidání změny</a></div>

    <div style="clear: both;" ></div>
    
    <div class="zmeny-ucetni-popis-sloupcu2" style="text-align: center; width: 60px; " >id</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu2" style="width: 150px;" >typ změny</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu2" style="width: 445px;" >text změny</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu2" style="width: 130px;" >Akceptováno / kým</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu2" style="width: 105px;" >datum vložení</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div style="clear: both;" ></div>
    
    <div class="zmeny-ucetni-popis-sloupcu" style="text-align: center; width: 60px; " >&nbsp;</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu" style="width: 150px;" >id typu změny</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu" style="width: 445px;" >akceptováno poznámka</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu" style="width: 130px;" >datum akceptování</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div class="zmeny-ucetni-popis-sloupcu" style="width: 105px;" >vložil  /  úprava</div>
    <div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
    <div style="clear: both; height: 50xp;" >&nbsp;</div>
    	
    {section name="prvek2" loop=$zmeny}
	<div class="zu-radky zu-cara1" style="text-align: center; width: 60px; ">{$zmeny[prvek2].zu_id}&nbsp;</div>
	<div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
	<div class="zu-radky zu-cara1" style="width: 150px; ">{$zmeny[prvek2].typ_nazev}&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>
    
	<div class="zu-radky zu-cara1" style="width: 445px;" >{$zmeny[prvek2].zu_text}&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>

	<div class="zu-radky zu-cara1" style="width: 130px;" >
	    {if $zmeny[prvek2].zu_akceptovano == 1}
		<span class="zu-a-yes"> Ano </span>/ {$zmeny[prvek2].zu_akceptovano_kym}
	    {else}
		<span class="zu-a-no"> Ne </span>/ <a href="{$link_accept}{$zmeny[prvek2].zu_id}">akceptovat</a>
	    {/if}
	    
	</div>
	
	<div class="zmeny-ucetni-cara1">&nbsp;</div>
    
	<div class="zu-radky zu-cara1" style="width: 105px;" >{$zmeny[prvek2].zu_vlozeno_kdy2}&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>
	
	<div style="clear: both; height: 2px;" ></div>
	
	<div class="zu-radky2 zu-cara2" style="text-align: center; width: 60px;" >&nbsp;</div>
	<div class="zmeny-ucetni-cara1" >&nbsp;</div>
    
	<div class="zu-radky2 zu-cara2" style="width: 150px; ">{$zmeny[prvek2].zu_typ}&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>
    
	<div class="zu-radky2 zu-cara2" style="width: 445px;" >{$zmeny[prvek2].zu_akceptovano_pozn}&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>

	<div class="zu-radky2 zu-cara2" style="width: 130px;" >{$zmeny[prvek2].zu_akceptovano_kdy2}&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>
        
	<div class="zu-radky2 zu-cara2" style="width: 105px;" >
	    {$zmeny[prvek2].zu_vlozeno_kym}
	     /<span style="margin-left: 10px;" >{$zmeny[prvek2].uprava}</span>&nbsp;</div>
	<div class="zmeny-ucetni-cara1">&nbsp;</div>
	
    {sectionelse}
	<div style="padding-top: 10px; padding-bottom: 10px; font-size: 18px;" >Žádné změny v systému</div>
    {/section}

    <div style="clear: both;" ></div>

</div>

{include file="base-end.tpl"}
