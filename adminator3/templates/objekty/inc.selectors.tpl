<form method="GET" action="{$action_1|default: '' }" >

  <div style="padding-top: 4px; " class="listing-selectors-line" >

    <input type="radio" name="es" value="1" {if $es|default: '' == 1 } checked {/if} >
     <label class="listing-selector-label" >všichni | </label>
    <input type="radio" name="es" value="2" {if $es|default: '' == 2 } checked {/if} >
      <label class="listing-selector-label" >platiči | </label>
    <input type="radio" name="es" value="3" {if $es|default: '' == 3 } checked {/if} >
      <label class="listing-selector-label" >NEplatici | </label>

    <input type="radio" name="es" value="4" {if $es|default: '' == 4 } checked {/if} >
      <label class="listing-selector-label" >apčka | </label>
    <input type="radio" name="es" value="5" {if $es|default: '' == 5 } checked {/if} >
      <label class="listing-selector-label" >garantované | </label>
    <input type="radio" name="es" value="6" {if $es|default: '' == 6 } checked {/if} >
      <label class="listing-selector-label" >veřejné |</label>

    <input type="radio" name="es" value="7" {if $es|default: '' == 7 } checked {/if} >
      <label class="listing-selector-label" >bez vlastníka |</label>
    <input type="radio" name="es" value="8" {if $es|default: '' == 8 } checked {/if} >
      <label class="listing-selector-label" >zakázaný net |</label>
    <input type="radio" name="es" value="9" {if $es|default: '' == 9 } checked {/if} >
      <label class="listing-selector-label" >šikana </label>

  </div>

  {* second line *}
  <div style="padding-top: 4px; border-bottom: 1px gray solid; width: 20%; "></div>

  <div style="padding-top: 4px; width: 100%;" class="listing-selectors-line" >

    {* <span style="padding-right: 20px;" ><input type="submit" value="NAJDI" name="najdi"> </span>
    <span ><label>Hledání: </label><input style="margin-left: 20px;" type="text" name="find" value="{$form_search_value}" ></span> *}

    <span style="padding-right: 15px; padding-left: 10px; ">
      <input type="submit" value="NAJDI" name="najdi">
      <input type="hidden" name="odeslano" value="true">
    </span>

    <span style="padding-right: 10px;" >
	    <span style="padding-right: 10px;">mód objektů:</span>

	    <select size="1" name="mod_vypisu" >
        <option value="1" {if $mod_vypisu|default: '' == 1 } selected {/if} >bezdrátová síť</option>
	      <option value="2" {if $mod_vypisu|default: '' == 2 } selected {/if} >optická síť</option>
	    </select>
	  </span>

    <span>
      <label class="listing-selector-label" >Hledání podle dns: </label><input type="text" name="dns_find" value="{$dns_find|default: '' }" >
      <span style="padding-left: 10px;"></span>
      <label class="listing-selector-label" > Hledání podle ip: </label><input type="text" name="ip_find" value="{$ip_find|default: '' }" >
    </span>

    <span style="padding-left: 15px; padding-right: 5px;" >
      {$export_link|default: '' }
    </span>
  </div>

  {* oddelovaci cara *}
  <div style="border-bottom: 2px solid black; padding-top: 5px; margin-bottom: 5px; height: 2px; width: 20%; " ></div>

{* form tag is ending after sorting buttons (not in smarty templates) *}
{* </form> *}
