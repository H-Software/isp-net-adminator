
<form method="GET" action="{$action_1}" >

  <div style="padding-top: 4px; " class="listing-selectors-line" >
  
    <input type="radio" name="select" value="1" {if $select == 1 } checked {/if} >
    <label class="vlastnici-selector-label" >Všichni</label>|
	    
    <input type="radio" name="select" value="2" {if $select == 2 } checked {/if} >
    <label class="vlastnici-selector-label" > FÚ </label> |
			
    <input type="radio" name="select" value="3" {if $select == 3 } checked {/if} >
    <label class="vlastnici-selector-label" > DÚ </label> |
				    
    <input type="radio" name="select" value="4" {if $select == 4 } checked {/if} >
    <label class="vlastnici-selector-label" > Neplatí(free) </label> |
						
    <input type="radio" name="select" value="5" {if $select == 5 } checked {/if} >
    <label class="vlastnici-selector-label" > Platí </label> |
							    
    <span style="padding-left: 5px; padding-right: 5px;" >Fakturační skupina: </span>
								    
    <select name="fakt_skupina" size="1" style="max-width: 190px;" >
      <option value="0" class="select-nevybrano" >Nevybráno</option>
	    
	{section  name="prvek1" loop=$fakt_skupiny }
	    <option value="{$fakt_skupiny[prvek1].id}" >{$fakt_skupiny[prvek1].nazev} 
	    {if $fakt_skupiny[prvek1].typ == 1 } (DÚ) 
	    {elseif $fakt_skupiny[prvek1].typ == 2 } (FÚ) {/if}
	    </option>	
	{sectionelse}
	    <option value="0" class="select-nevybrano">Žádné fakturační skupiny nenalezeny</option>
	{/section}
    </select>
									
  {* razeni *}
  <span style="padding-left: 5px; ">|</span>
    
  <span style="padding-left: 5px; padding-right: 5px; ">Řadit dle:</span>

  <span style="">
    <select name="razeni" size="1" >
            <option value="1" > id klienta  </option>
	    <option value="3"  > jména  </option>
	    <option value="4"  > Příjmení  </option>
	    <option value="5"  > Ulice  </option>
	    <option value="6"  > Město  </option>
	    <option value="14" > Var. symbol  </option>
	    <option value="15" > K platbě  </option>
    </select> 
  </span>
      
  <span style="padding-left: 7px; ">

      <select name="razeni2" size="1" >
					
        <option value="1" > vzestupně </option>
        <option value="2" > sestupně </option>
									
      </select>
  </span>

  </div>
 
  {* second line *}
  <div style="padding-top: 4px; border-bottom: 1px gray solid; width: 20%; "></div>

  <div style="padding-top: 4px; width: 100%;" class="listing-selectors-line" >

    <span style="padding-right: 20px;" ><input type="submit" value="NAJDI" name="najdi"> </span>
    <span ><label>Hledání: </label><input style="margin-left: 20px;" type="text" name="find" value="{$form_search_value}" ></span>
  </div>

  {* oddelovaci cara *}
  <div style="border-bottom: 2px solid black; padding-top: 5px; margin-bottom: 5px; height: 2px; width: 20%; " ></div>

</form>
