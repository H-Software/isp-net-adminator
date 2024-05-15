<form method="GET" >

<div style="padding-top: 4px; " class="listing-selectors-line" >

<input type="radio" name="select" value="1" >
<label class="listing-selector-label" >Všichni</label> |

<input type="radio" name="select" value="2" >
<label class="listing-selector-label" >Fakturační</label> |

<input type="radio" name="select" value="3" >
<label class="listing-selector-label" >Nefakturační</label> |

<input type="radio" name="select" value="4" >
<label class="listing-selector-label" > Neplatí(free) </label> |

<input type="radio" name="select" value="5" >
<label class="listing-selector-label" > Platí </label> |

<span style="padding-left: 5px; padding-right: 5px; ">Řadit dle:</span>

<select name="razeni" size="1" >

        <option value="1" > id klienta  </option>
        <option value="3" > jména  </option>
        <option value="4" > Příjmení  </option>
        <option value="5" > Ulice  </option>
        <option value="6" > Město  </option>
        <option value="14" > Var. symbol  </option>
        <option value="15" > K platbě  </option>

</select>
<span style="padding-left: 7px; "></span>
<select name="razeni2" size="1" >
    <option value="1" > vzestupně  </option>
    <option value="2" > sestupně  </option>
</select>

</div>

<div style="padding-top: 4px; border-bottom: 1px gray solid; width: 20%; "></div>

<div style="padding-top: 4px; width: 100%;" class="listing-selectors-line" >

<span style="padding-right: 20px;" >
  <input type="submit" value="NAJDI" name="najdi"> 
</span>
<label style="padding-right: 20px;" >Hledání: </label>
<input type="text" name="find" value="{$form_find|default: ''}">

</div>

{* oddelovaci cara *}
<div style="border-bottom: 2px solid black; padding-top: 5px; margin-bottom: 5px; height: 2px; width: 20%; " ></div>

</form>
