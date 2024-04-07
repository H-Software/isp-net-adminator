
<form method="GET" action="{$action_1}" >

  <div style="padding-top: 4px;" >
  
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
								    
    <select name="fakt_skupina" size="1" >
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
									
  </div>
 
</form>
