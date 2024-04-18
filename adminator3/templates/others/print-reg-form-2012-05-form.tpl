{include file="base.tpl"}

 {include file="inc.cat.others.tpl"}

 <div style="padding-left: 5px; padding-top: 10px; height: 100%" >
   <form method="POST" action="{$form_action}" >

     <div style="font-size: 24px; font-weight: bold; padding-bottom: 30px;" >Průvodce tiskem registračního formuláře 2012-05</div>
    
     {* ram s podklad. obr *}
     <div style="background-image: url('/img2/print/2012-05-form-background.jpg'); width: 960px; height: 1400px;" >
     
       {* 
          //
          //zde jednotlive form. pole
          //
       *}
       
       {* ev. cislo *}
       <div style="padding-left: 670px; padding-top: 35px;">
          <input type="text" name="input_ec" value="{$input_ec}" size="28" class="print-reg-form-2012-05-input" >
       </div>
     
       {* 
         //
         //   sekce zakaznik 
         //
       *}
       
       <div style="padding-left: 220px; padding-top: 315px; float: left;" >
          <input type="text" name="input_jmeno_a_prijmeni" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
        
       <div style="padding-left: 660px; padding-top: 315px; ">
          <input type="text" name="input_adresa_odber" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
     
       {* sekce zakaznik, 2.radka *}
     
       <div style="padding-left: 220px; padding-top: 6px; float: left;" >
          <input type="text" name="input_adresa_tr_byd" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
     
       <div style="padding-left: 660px; padding-top: 6px; ">
          <input type="text" name="input_pozadovany_tarif" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
          
      {* sekce zakaznik, 3.radka *}
            
       <div style="padding-left: 220px; padding-top: 6px; float: left; " >
          <input type="text" name="input_mesto_psc" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
     
       <div style="padding-left: 212px; padding-top: 6px; width: 80px; float: left;" >
          <input type="text" name="input_cena_tarifu" value="" size="6" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
       
       <div style="padding-left: 90px; padding-top: 6px; width: 80px; ">
          <input type="text" name="input_uvazek" value="" size="6" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>

      {* sekce zakaznik, 4. radka *}

       <div style="padding-left: 220px; padding-top: 6px; float: left; " >
          <input type="text" name="input_tel" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
	
       <div style="padding-left: 165px; padding-top: 3px; width: 20px; float: left; " >
          <input type="radio" name="input_prip_tech" value="1" class="print-reg-form-2012-05-input " >
       </div>
       
       <div style="padding-left: 110px; padding-top: 3px; width: 10px; float: left; " >
          <input type="radio" name="input_prip_tech" value="2" class="print-reg-form-2012-05-input " >
       </div>
    
       <div style="padding-left: 95px; padding-top: 3px; width: 10px; " >
          <input type="radio" name="input_prip_tech" value="3" class="print-reg-form-2012-05-input " >
       </div>
    
       <div style="clear: both;"></div>
       
       {* 
         //
         //   sekce "predmet predani, ip konfigurace" 
         //
       *}
    
       <div style="padding-left: 220px; padding-top: 50px; float: left;" >
          <input type="text" name="input_klient_zarizeni" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
    
       <div style="padding-left: 210px; padding-top: 50px; width: 200px;" >
          <input type="text" name="input_ip_adresa" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
       
      {* 2. radka  *}
      
       <div style="padding-left: 220px; padding-top: 5px; float: left; border: 1px;" >
          <input type="text" name="input_antena" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
      
       <div style="padding-left: 210px; padding-top: 5px; width: 200px;" >
          <input type="text" name="input_maska" value="" size="30" class="print-reg-form-2012-05-input print-reg-form-2012-05-customer" >
       </div>
      
      
      
      {* zbytek poli - TODO *}
    
    
      {**}
      <div style="clear: both;"></div>
       
    
      <div style="padding-top: 800px;">
    	<input type="submit" name="send" value="OK - VYGENEROVAT" >
    	
    	<input type="submit" name="reg" value="PŘEPOČÍTAT FORMULÁŘ" >
      </div>
        
     {* konec ramu s podklad. obr *}
     </div>
     
   </form>
 </div>

{include file="base-end.tpl"}

