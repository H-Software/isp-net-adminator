<form method="GET" action="{$action_1}" >

  <div style="padding-top: 4px; " class="listing-selectors-line" >
  
    <input type="radio" name="select" value="1" {if $select == 1 } checked {/if} >
    <label class="listing-selector-label" >Všichni</label>|
	    
    <input type="radio" name="select" value="2" {if $select == 2 } checked {/if} >
    <label class="listing-selector-label" > Fakturační </label> |
			
    <input type="radio" name="select" value="3" {if $select == 3 } checked {/if} >
    <label class="listing-selector-label" > Nefakturační </label> |
				    
    <input type="radio" name="select" value="4" {if $select == 4 } checked {/if} >
    <label class="listing-selector-label" > Neplatí (free) </label> |
						
    <input type="radio" name="select" value="5" {if $select == 5 } checked {/if} >
    <label class="listing-selector-label" > Platí </label> |
					    
  {* razeni *}
  <span style="padding-left: 5px; ">|</span>
    
  <span style="padding-left: 5px; padding-right: 5px; ">Řadit dle:</span>

  <span style="">
    <select name="razeni" size="1" >
      <option value="1" {if $razeni == 1 } selected {/if} > id klienta  </option>
	    <option value="3" {if $razeni == 3 } selected {/if} > jména  </option>
	    <option value="4" {if $razeni == 4 } selected {/if} > Příjmení  </option>
	    <option value="5" {if $razeni == 5 } selected {/if} > Ulice  </option>
	    <option value="6" {if $razeni == 6 } selected {/if} > Město  </option>
	    <option value="14" {if $razeni == 14 } selected {/if} > Var. symbol  </option>
	    <option value="15" {if $razeni == 15 } selected {/if} > K platbě  </option>
    </select> 
  </span>
      
  <span style="padding-left: 7px; ">

      <select name="razeni2" size="1" >
        <option value="1" {if $razeni2 == 1 } selected {/if} > vzestupně </option>
        <option value="2" {if $razeni2 == 2 } selected {/if} > sestupně </option>
      </select>
  </span>

  </div>
 
  <div style="padding-top: 4px; border-bottom: 1px gray solid; width: 20%; "></div>

  {* second line *}
  <div style="padding-top: 4px; width: 100%;" class="listing-selectors-line" >

    <span style="padding-right: 20px;" >
      <input type="submit" value="NAJDI" name="najdi">
    </span>
    <span >
      <label style="padding-right: 20px;" >Hledání: </label>
      <input style="margin-left: 20px;" type="text" name="find" value="{$form_find}" >
    </span>
  </div>

  {* oddelovaci cara *}
  <div style="border-bottom: 2px solid black; padding-top: 5px; margin-bottom: 5px; height: 2px; width: 20%; " ></div>

</form>
