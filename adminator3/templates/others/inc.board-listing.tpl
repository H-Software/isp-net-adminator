
 <div class="tableheading typ-zprav" >{$mod_hlaska}</div>
 
 {section name="entry" loop=$zpravy}

  <div class="tableheading" style="padding-top: 2px; " >zpráva č. {$zpravy[entry].id}</div>

  <div class="table zprava-main" >
     <a href="mailto:{$zpravy[entry].email}" ><b>{$zpravy[entry].author}</b></a>
     <br>
     <b>{$zpravy[entry].subject}</b> [{$zpravy[entry].from_date} - {$zpravy[entry].to_date}]
     <br><br>{$zpravy[entry].body}
  </div>

  <br>

 {/section}
	
 <div style="width: 590px; text-align: right; border-top: 1px solid #7D7642; padding-top: 5px; " class="table" >
   <b>strana

   {section name="page" loop=$strany}
    | 
	{if $strany[page].i == $strany[page].i_akt}
	  {$strany[page].i2}
	{else}
	 <a href="others-board.php?action=view&what={$strany[page].what}&page={$strany[page].i}">
	  {$strany[page].i2}
         </a>
	{/if}
   {/section}
  
   |</b>

 </div>
