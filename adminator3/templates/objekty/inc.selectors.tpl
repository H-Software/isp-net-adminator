<form method="GET" action="{$action_1}" >

  <div style="padding-top: 4px; " class="listing-selectors-line" >
  
    <input type="radio" name="select" value="1" {if $select == 1 } checked {/if} >
    <label class="listing-selector-label" >Všichni</label>|
	    
    <input type="radio" name="select" value="2" {if $select == 2 } checked {/if} >
    <label class="listing-selector-label" > FÚ </label> |
			
    <input type="radio" name="select" value="3" {if $select == 3 } checked {/if} >
    <label class="listing-selector-label" > DÚ </label> |
				    
    <input type="radio" name="select" value="4" {if $select == 4 } checked {/if} >
    <label class="listing-selector-label" > Neplatí(free) </label> |
						
    <input type="radio" name="select" value="5" {if $select == 5 } checked {/if} >
    <label class="listing-selector-label" > Platí </label> |
							    
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
