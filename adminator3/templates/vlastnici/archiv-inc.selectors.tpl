<form method="GET" >

<input type="radio" name="select" value="1" ><label>Všichni</label> |

<input type="radio" name="select" value="2" ><label>Fakturační</label> |

<input type="radio" name="select" value="3" ><label>Nefakturační</label> |

<input type="radio" name="select" value="4" ><label> Neplatí(free) </label> |

<input type="radio" name="select" value="5" ><label> Platí </label> |

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

<div>
<input type="submit" value="NAJDI" name="najdi"> 

<label>Hledání : </label><input type="text" name="find" >
</div>

{* oddelovaci cara *}
<div style="border-bottom: 2px solid black; padding-top: 5px; margin-bottom: 5px; height: 2px; width: 20%; " ></div>

</form>
