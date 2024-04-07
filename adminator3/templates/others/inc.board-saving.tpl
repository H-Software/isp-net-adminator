
<div class="tableheading typ-zprav" >{$mod_hlaska}</div>

<div style="width: 600px; background-color: #eaead7; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; " >

{if $rs == 1}
 <div style="color: #339966; font-weight: bold; padding: 10px; " >Zpráva úspěšně uložena.</div>
{else}
 <div style="color: red; padding: 10px;" >Zprávu se nepodařilo uložit.</div>

{/if}

<div>{$body}</div>

</div>
