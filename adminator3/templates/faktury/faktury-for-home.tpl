
 <div style="width: 80%; position: relative; margin-left: auto; margin-right: auto; padding-bottom: 30px; padding-top: 20px; font-size: 0.85rem; " >
	    
   <div style="font-weight: bold; padding-bottom: 10px;" >
      Informace z modulu <!--<a href="fn.php" >-->"Neuhrazené faktury"<!--</a>-->
   </div>

   {if $stats_faktury_neuhr_error_messages|default: '' gt 0 }
      <div 
      class="alert alert-danger" 
      role="alert"
      style="width: 80%; "
      >   
         Některé části statistiky nelze zjistit.</br></br>
         {$stats_faktury_neuhr_error_messages|default: ''}
      </div>
   {/if}

   <div style="padding-left: 5px;" >
         <span style="color: #555555; ">Celkový počet neuhrazených faktur:</span> 
               
         <span style="font-weight: bold;" >{$count_total}</span>
         <span style="color: grey;" >( z toho ignorovaných: 
         <span style="font-weight: bold; color: black;" >{$count_ignored}</span> )</span>
   </div>
                              
   <div style="padding-left: 5px;" >
      <span style="color: #555555;" >Počet nespárovaných neuh. faktur:</span>
      <span style="font-weight: bold;" >{$count_unknown}</span>
   </div>

   <div style="padding-left: 5px;" >
         <span style="color: #555555;" >Datum a čas posledního importu faktur: </span>
         <span style="font-weight: bold;" >{$date_last_import}</span>
   </div>  
</div>
  															    