{include file="base.tpl"}

 {include file="inc.cat.others.tpl"}

 <div style="padding-top: 10px; padding-left: 10px; font-size: 18px; text-decoration: underline; ">
    Informace z odeslaných formulářů z webu spolecnosti
 </div>
 
 <div style="padding-top: 10px; padding-left: 10px;" >
 :: napište nám ::<br>
 
 <div style="padding-top: 10px; font-weight: bold; ">
    <div style="float: left; width: 50px; ">id</div>
    <div style="float: left; width: 120px; ">jmeno</div>
    <div style="float: left; width: 120px; ">prijmeni</div>
    <div style="float: left; width: 120px; ">telefon</div>
    <div style="float: left; width: 200px; ">email</div>
    <div style="float: left; width: 120px; ">ev. číslo (VS)</div>
    <div style="float: left; width: 150px; ">datum vložení</div>
    
    <div style="clear: both; ">dotaz</div>
    
    <div style="clear: both; width: 900px; border-bottom: 1px solid black; margin-bottom: 10px; "></div>
 </div>
 
   {section name="data" loop=$data_q}
       <div style="float: left; width: 50px;" >{$data_q[data].id_question}&nbsp;</div>       
       <div style="float: left; width: 120px;" >{$data_q[data].jmeno}&nbsp;</div>   
       <div style="float: left; width: 120px;" >{$data_q[data].prijmeni}&nbsp;</div>
       <div style="float: left; width: 120px;" >{$data_q[data].telefon}&nbsp;</div>
       <div style="float: left; width: 200px;" >{$data_q[data].email}&nbsp;</div>
       <div style="float: left; width: 120px;" >{$data_q[data].vs}&nbsp;</div>
       <div style="float: left; width: 150px;" >{$data_q[data].datum_vlozeni}&nbsp;</div>

       <div style="clear: both;" >{$data_q[data].dotaz}&nbsp;</div>
      
       <div style="clear: both; width: 900px; padding-top: 2px; padding-bottom: 5px;
    		    border-top: 1px dashed silver; border-bottom: 1px solid black; color: #666666; " >
        {$data_q[data].text}&nbsp;
       </div>
           
    {sectionelse}
	<div style="padding-top: 10px; color: red; font-size: 16px;" >V databázi nejsou žádné údaje.</div>
    {/section}

  <div style="padding-top: 20px;">:: objednávka ::</div>

  <div style="padding-top: 10px; font-weight: bold; ">
    <div style="float: left; width: 30px; ">id</div>
    <div style="float: left; width: 120px; ">jmeno</div>
    <div style="float: left; width: 120px; ">prijmeni</div>
    <div style="float: left; width: 150px; ">adresa</div>
    <div style="float: left; width: 100px; ">telefon</div>
    <div style="float: left; width: 150px; ">email</div>
    <div style="width: 150px; ">datum vložení</div>
    
    <div style="width: 900px; height: 3px; border-top: 1px dashed silver;" ></div>
       
    <div style="float: left; width: 300px; ">internet tarif</div>
    <div style="float: left; width: 150px; ">internet poznámka</div>

    <div style="width: 900px; height: 3px; border-top: 1px dashed silver;" ></div>

    <div style="float: left; width: 300px; ">IPTV tarif</div>
    <div style="float: left; width: 200px; ">Tém. balíček</div>
    <div style="float: left; width: 150px; ">IPTV poznámka</div>

    <div style="width: 900px; height: 3px; border-top: 1px dashed silver;" ></div>

    <div style="float: left; width: 300px; ">VoIP číslo</div>
    <div style="float: left; width: 200px; ">VoIP platby</div>
    <div style="float: left; width: 150px; ">VoIP poznámka</div>

    <div style="width: 900px; height: 3px; border-top: 1px solid black;" ></div>
    
 </div>

   {section name="data" loop=$data_o}
       <div style="float: left; width: 30px;" >{$data_o[data].id_order}&nbsp;</div>       
       <div style="float: left; width: 120px;" >{$data_o[data].jmeno}&nbsp;</div>   
       <div style="float: left; width: 120px;" >{$data_o[data].prijmeni}&nbsp;</div>
       <div style="float: left; width: 150px;" >{$data_o[data].adresa}&nbsp;</div>
       <div style="float: left; width: 100px;" >{$data_o[data].telefon}&nbsp;</div>
       <div style="float: left; width: 150px;" >{$data_o[data].email}&nbsp;</div>
       <div style="float: left; width: 200px;" >{$data_o[data].datum_vlozeni}&nbsp;</div>

       <div style="width: 900px; height: 3px; border-top: 1px dashed silver;" ></div>
       
       <div style="float: left; width: 300px; "><b>NET</b> {$data_o[data].internet}&nbsp;</div>
       <div style="float: left; ">{$data_o[data].text_internet}&nbsp;</div>

       <div style="width: 900px; height: 3px; border-top: 1px dashed silver;" ></div>
        
       <div style="float: left; width: 300px; "><b>IPTV</b> {$data_o[data].iptv}&nbsp;</div>
       <div style="float: left; width: 200px; ">{$data_o[data].balicek}&nbsp;</div>
       <div style="float: left; width: 150px; ">{$data_o[data].text_iptv}&nbsp;</div>

       <div style="width: 900px; height: 3px; border-top: 1px dashed silver;" ></div>

       <div style="float: left; width: 300px; "><b>VoIP</b> {$data_o[data].voipcislo}&nbsp;</div>
       <div style="float: left; width: 200px; ">{$data_o[data].voip}&nbsp;</div>
       <div style="float: left; width: 150px; ">{$data_o[data].text_voip}&nbsp;</div>
	
       <div style="clear: both; width: 900px; padding-top: 2px; padding-bottom: 5px;
    		    border-top: 1px dashed silver; border-bottom: 1px solid black; color: #666666; " >
        {$data_o[data].poznamka}&nbsp;
       </div>
       
    {sectionelse}
	<div style="clear: both; padding-top: 10px; color: red; font-size: 16px;" >V databázi nejsou žádné údaje.</div>
    {/section}
      	   
 </div>

{include file="base-end.tpl"}
