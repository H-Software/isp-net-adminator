<?php

echo "<form method=\"POST\" action=\"\" name=\"form1\" >";

echo "<table border=\"0\" width=\"1000px\">

    <tr>
        <td colspan=\"5\"><span style=\"font-size: 18px; font-weight: bold; \" >
	Průvodce tiskem smlouvy:
	</span></td>

    </tr>

        <tr>
                <td align=\"center\" colspan=\"5\"><br></td>
        </tr>

        <tr>
                <td colspan=\"1\" align=\"center\" class=\"label-font\" ><label>Evidenční číslo smlouvy: </label></td>
                <td><input type=\"text\" name=\"ec\" size=\"30\" class=\"input1\" value=\"".$ec."\"></td>
    		<td colspan=\"3\"><br></td>
	</tr>

	<tr>
	    <td><br></td>
	</tr>
	
	<tr>
	    <td colspan=\"4\" ><div style=\"padding-left: 20px; font-weight: bold; \">Oprávněný Zákazník</div></td>
	    
	</tr>
	
	<tr>
	    <td colspan=\"5\"><br></td>
	</tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Jméno a příjmení:  </label></td>
                <td><input type=\"text\" name=\"jmeno\" class=\"input-size-big\" size=\"25\" value=\"".$jmeno."\" ></td>
        
		<td><br></td>
		
		<td align=\"center\" class=\"label-font\" ><label>Název společnosti: </label></td>
                <td><input type=\"text\" name=\"nazev_spol\" class=\"input-size-big\" size=\"20\" value=\"".$nazev_spol."\" ></td>
        </tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Adresa trvalého bydliště, č.p.: </label></td>
                <td><input type=\"text\" name=\"adresa\" class=\"input-size-big\" size=\"25\" value=\"".$adresa."\" ></td>
        
		<td><br></td>
	        
		<td align=\"center\" class=\"label-font\" ><label>IČ / DIČ: </label></td>
                <td><input type=\"text\" name=\"ico_dic\" size=\"20\" class=\"input-size-big\" value=\"".$ico_dic."\" ></td>
        </tr>
	</tr>

        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Město a PSČ: </label></td>
                <td><input type=\"text\" name=\"mesto\" class=\"input-size-big\" size=\"25\" value=\"".$mesto."\" ></td>
    	
		<td><br></td>
	        
		<td align=\"center\" class=\"label-font\" ><label>E-mail: </label></td>
                <td><input type=\"text\" name=\"email\" size=\"20\" class=\"input-size-big\" value=\"".$email."\" ></td>
	</tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Telefon: </label></td>
                <td><input type=\"text\" name=\"telefon\" class=\"input-size-big\" size=\"25\" value=\"".$telefon."\" ></td>
    	
		<td colspan=\"3\" ><br></td>
	</tr>
	        
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Korespondenční adresa: </label></td>
                <td><input type=\"text\" name=\"kor_adresa\" class=\"input-size-big\" size=\"25\" value=\"".$kor_adresa."\" ></td>
    	
		<td colspan=\"3\" ><br></td>
		
	</tr>
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Kor. město, PSČ: </label></td>
                <td><input type=\"text\" name=\"kor_mesto\" class=\"input-size-big\" size=\"25\" value=\"".$kor_mesto."\" ></td>
    	
		<td colspan=\"3\" ><br></td>
		
	</tr>
	
	<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\" ></div>
		  </td>
	      </tr>
	
	<tr>
	    <td colspan=\"4\" >
		<div style=\"padding-left: 20px; font-weight: bold; \">Specifikace přípojného místa</div>
	    </td>
	</tr>
	
	<tr>
	    <td colspan=\"5\"><br></td>
	</tr>

        <tr>
                <td colspan=\"1\"><br></td>
                <td colspan=\"4\">";
		
                    echo "<select size=\"1\" name=\"spec_prip_mista\" onChange=\"self.document.forms.form1.submit()\"  >";
                      echo "<option value=\"1\" ";
		        if( ($spec_prip_mista == 1) or (!isset($spec_prip_mista)) ){ echo " selected "; }
		      echo ">Přípojné místo stejné jako jako trvalé bydliště Zákazníka</option>";
		      
                      echo "<option value=\"2\" ";
		        if ( $spec_prip_mista == 2 ){ echo " selected "; }
		      echo " >jiné...</option>";
		      
                    echo "</select>";
    
    echo "      </td>
	  </tr>
	  
	  <tr>
	    <td colspan=\"5\"><br></td>
	  </tr>

	
	";
		

        if($spec_prip_mista == 2)
        {
         echo "<tr>
                <td align=\"center\" class=\"label-font\" ><label>Adresa připojeného místa: </label></td>
                <td><input type=\"text\" name=\"prip_misto_adresa\" class=\"input-size-big\" size=\"25\" value=\"".$prip_misto_adresa."\"></td>
        	<td><br></td> 
	        <td align=\"center\" class=\"label-font\" ><label>č.p: </label></td>
                <td><input type=\"text\" name=\"prip_misto_cp\" size=\"20\" value=\"".$prip_misto_cp."\"></td>
    	      </tr>";

         echo "<tr>
                <td align=\"center\" class=\"label-font\" ><label>Město: </label></td>
                <td><input type=\"text\" name=\"prip_misto_mesto\" class=\"input-size-big\" size=\"25\" value=\"".$prip_misto_mesto."\"></td>
		<td><br></td>
	        <td align=\"center\" class=\"label-font\" ><label>PSČ: </label></td>
                <td><input type=\"text\" name=\"prip_misto_psc\"  size=\"20\" value=\"".$prip_misto_psc."\" ></td>
	      </tr>";

	  echo "<tr><td colspan=\"5\"><br></td></tr>";
	
	  echo "<tr>
	            <td colspan=\"2\" align=\"center\" >Použí tuto adresu jako korespondenční:</td>
		    
		    <td><br></td>
		    
		    <td colspan=\"2\">
		     <select name=\"adr_prip_jako_kor\" size=\"1\" >
		     
		        <option value=\"1\" "; if( $adr_prip_jako_kor == 1 or !isset($adr_prip_jako_kor)) echo " select "; echo " >Ne</option>	
			<option value=\"2\" "; if( $adr_prip_jako_kor == 2) echo " selected "; echo " >Ano</option>
			
		     </select>
		    
		    </td>
		</tr>";
	
	} // konec if spec. pripojneho mista


	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px black solid;\"></div>
		  </td>
	      </tr>";
	      	
	echo "<tr>
                <td align=\"center\" class=\"label-font\" ><label>Připojená technologie: </label></td>
                <td colspan=\"4\" >
		    <span style=\"margin-left: 10px; \" ></span>
		        
		    <input type=\"radio\" name=\"prip_tech\" value=\"1\" onChange=\"self.document.forms.form1.submit()\" ";
			 { echo " checked=\"checked\" "; } echo " >
		    <span style=\"margin-left: 10px; \" >Optiká síť</span>
		    |
		    <input type=\"radio\" name=\"prip_tech\" value=\"2\" onChange=\"self.document.forms.form1.submit()\" ";
			if( $prip_tech == 2 ){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"margin-left: 10px; \" >Metalický okruh</span>
		    |
		    <input type=\"radio\" name=\"prip_tech\" value=\"3\" onChange=\"self.document.forms.form1.submit()\" ";
			if( $prip_tech == 3 ){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"margin-left: 10px; \" >Bezdrátová síť</span>
		</td>
          </tr>";
	
        echo "</tr>";
    	
	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td colspan=\"5\" >
		    <div style=\"padding-left: 20px; font-weight: bold; border-bottom: 1px grey solid; \">
		    Tarify a ceny:</div>
		</td>
	     </tr>";
	
	//internet
	echo "<tr><td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >Internet:</td>
	        <td>
		    <select name=\"internet_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			
			<option value=\"0\" "; 
			    if( $internet_sluzba == 0 or !isset($internet_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $internet_sluzba == 1 ){ echo " selected "; } echo " >Ano</option>
			
		    </select>
		</td>";
		
		echo "<td>&nbsp;</td>";
		
		if( $internet_sluzba == 1 )
		{
	
		  echo "
		    <td class=\"label-font\" align=\"center\" >Vyberte tarif:</td>
		    <td colspan=\"4\">";
		    
		    if( $prip_tech == 1 )
		    { $sql_int = " WHERE typ_tarifu = '1' "; }
		    elseif( $prip_tech == 2 or $prip_tech == 3 )
		    { $sql_int = " WHERE typ_tarifu = '0' ";}
		    else
		    { $sql_int = ""; }
		    
		    $dotaz_int_tarify = mysql_query("SELECT * FROM tarify_int ".$sql_int." ORDER BY id_tarifu");
		    
		    echo "<select size=\"1\" name=\"int_tarify_id_tarifu\" onChange=\"self.document.forms.form1.submit()\" >";
		    
		    while( $data_int = mysql_fetch_array($dotaz_int_tarify))
		    {
			echo "<option value=\"".$data_int["id_tarifu"]."\" ";
			
			if( $int_tarify_id_tarifu == $data_int["id_tarifu"] )
			{ echo " selected "; }
			
			echo " >".$data_int["jmeno_tarifu"];
			echo " (".$data_int["zkratka_tarifu"].")</option>";
		    
		    }
		    
		    echo "</select>\n";
		    
		    echo "</td>";
	
		  }
		  else
		  { echo "<td colspan=\"2\">&nbsp;</td>"; }	
	
		echo "</tr>";
	
	if( $internet_sluzba == 1 )
	{
	
	    $int_se = mysql_query("SELECT * FROM tarify_int WHERE id_tarifu = '$int_tarify_id_tarifu' ");

	    while( $data_int_se = mysql_fetch_array($int_se))
	    { 
		$int_sluzba_tarif_text_db = $data_int_se["jmeno_tarifu"];
		$typ_tarifu_db = $data_int_se["typ_tarifu"];
		$garant_db = $data_int_se["garant"];
	    
		$int_sluzba_tarif_cena_db = $data_int_se["cena_bez_dph"];
		$int_sluzba_tarif_cena_s_dph_db = $data_int_se["cena_s_dph"];
	    
		$speed_dwn_db = $data_int_se["speed_dwn"]; 

		$int_sluzba_tarif_agr_db = $data_int_se["agregace_smlouva"];
		
	    }
		
	    if( strlen($int_sluzba_tarif_cena) < 1 )
	    { 
		$int_sluzba_tarif_cena = $int_sluzba_tarif_cena_db; 
	    }
	    
	    if( strlen($int_sluzba_tarif_cena_s_dph) < 1 )
	    { 
		$int_sluzba_tarif_cena_s_dph = $int_sluzba_tarif_cena_s_dph_db;
	    }

	    if( strlen($int_sluzba_tarif_agr) < 1 )
	    {  //detailni agregaci dodelat
		$int_sluzba_tarif_agr = $int_sluzba_tarif_agr_db;
	    }
	    	    
	    echo "<tr>
		    <td class=\"label-font\" align=\"center\" >Internet - tarif / Max. agregace:</td>
		    <td>";
		    
		    if( $int_tarify_id_tarifu >= 0 and ( strlen($int_sluzba_tarif_text) < 1 ) )
		    { $int_sluzba_tarif_text = $int_sluzba_tarif_text_db; } 

		    echo "<input type=\"text\" name=\"int_sluzba_tarif_text\" value=\"".$int_sluzba_tarif_text."\" >";
		    		    
		    echo "<span class=\"label-font\" style=\"margin-left: 10px;\" >
			    1: <input type=\"text\" size=\"4\" name=\"int_sluzba_tarif_agr\" value=\"".$int_sluzba_tarif_agr."\" >
			  </span>";
		    
		    echo "</td>
		    <td>&nbsp;</td>
		    <td class=\"label-font\" align=\"center\" >CENA TARIFU (bez DPH / s DPH): </td>
		    <td>
		    			
			<input type=\"text\" name=\"int_sluzba_tarif_cena\" value=\"".$int_sluzba_tarif_cena."\" size=\"5\" >,-
			<span style=\"margin-left: 10px; \" >
			 <input type=\"text\" name=\"int_sluzba_tarif_cena_s_dph\" value=\"".$int_sluzba_tarif_cena_s_dph."\" size=\"5\" >
			,-</span>
			
			</td>
		  </tr>";
	
	    echo "<tr>
		    <td class=\"label-font\" align=\"center\">Max. rychlost (Mb/s):</td>";
		    
		if( (strlen($int_sluzba_rychlost) < 1) )
		{ $int_sluzba_rychlost = round($speed_dwn_db/1024); }
		
		echo "<td><input type=\"text\" name=\"int_sluzba_rychlost\" value=\"".$int_sluzba_rychlost."\" ></td>
		    
		    <td colspan=\"3\" >&nbsp;</td>
		  </tr>";
	}
	
	echo "<tr><td colspan=\"5\" ><br></td></tr>";
	
	echo "<tr>
		<td class=\"label-font\" align=\"center\">Veřejná IP adresa: </td>
		<td colspan=\"\">
		    <select name=\"int_verejna_ip\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" "; 
			    if( $int_verejna_ip == 0 or !isset($int_verejna_ip)){ echo " selected "; }
		    echo " >Ne</option>
			<option value=\"1\" "; if($int_verejna_ip == 1){ echo " selected "; } echo " >Ano</option>    
		    </select>
		</td>";
		
	echo "<td>&nbsp;</td>";
	
	
	
	if( $int_verejna_ip == 1 )
	{
	    echo "<td class=\"label-font\" align=\"center\" >CENA, bez DPH / s DPH:</td>
		  <td>
		    <input type=\"text\" name=\"int_verejna_ip_cena\" value=\"".$int_verejna_ip_cena."\" size=\"5\" >,-
			
		    <span style=\"margin-left: 10px; \" >
		      <input type=\"text\" name=\"int_verejna_ip_cena_s_dph\" value=\"".$int_verejna_ip_cena_s_dph."\" size=\"5\" >
		    ,-</span>
		  
		  </td>";
	}
	else
	{
	  echo "<td colspan=\"2\" >&nbsp;</td>";
	}
		
	echo "</tr>";
	
	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";

	//iptv 
	echo "<tr><td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >
	IPTV:</td>
	        <td>
		    <select name=\"iptv_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" ";
			    if( $iptv_sluzba == 0 or !isset($iptv_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $iptv_sluzba == 1){ echo " selected "; } echo " >Ano</option>
		    </select>
		</td>";
	
	if( $iptv_sluzba == 1 )
	{
	    $iptv_se = mysql_query("SELECT * FROM tarify_iptv WHERE id_tarifu = '$iptv_sluzba_id_tarifu' ");

	    while( $data_iptv_se = mysql_fetch_array($iptv_se) )
	    {
		$iptv_sluzba_cena_bez_dph_db = $data_iptv_se["cena_bez_dph"];
		$iptv_sluzba_cena_s_dph_db = $data_iptv_se["cena_s_dph"];
	    }

	    if( (strlen($iptv_sluzba_cena) < 1 ) )
	    { $iptv_sluzba_cena = $iptv_sluzba_cena_bez_dph_db; }
	    
	    if( (strlen($iptv_sluzba_cena_s_dph) < 1 ) )
	    { $iptv_sluzba_cena_s_dph = $iptv_sluzba_cena_s_dph_db; }
	    
	     
	    echo "<td>&nbsp;</td>";
	    echo "<td class=\"label-font\" align=\"center\" >Vyberte tarif:</td>";
	
	    echo "<td>
		    <select size=\"1\" name=\"iptv_sluzba_id_tarifu\" onChange=\"self.document.forms.form1.submit()\" >";
	
	    $iptv_se = mysql_query("SELECT * FROM tarify_iptv ORDER BY zkratka_tarifu ");

	    while( $data_iptv_se = mysql_fetch_array($iptv_se))
	    { 
		echo "<option value=\"".$data_iptv_se["id_tarifu"]."\" ";
		if( $iptv_sluzba_id_tarifu == $data_iptv_se["id_tarifu"] )
		{ echo " selected "; }
		
		echo " >";
		echo $data_iptv_se["jmeno_tarifu"]." (".$data_iptv_se["zkratka_tarifu"].")</option>";
	
	    }
	    
	    echo "</option></td>";
	
	    //dalsi radka :: cena tarifu
	    echo "<tr>
		    <td colspan=\"2\" >&nbsp;</td>
		    <td>&nbsp;</td>
		    
		    <td class=\"label-font\" align=\"center\" >CENA, bez DPH / s DPH:</td>
		    <td>
			<input type=\"text\" name=\"iptv_sluzba_cena\" size=\"5\" value=\"".$iptv_sluzba_cena."\" >
			,-
			<span style=\"margin-left: 8px; \" >
			  <input type=\"text\" name=\"iptv_sluzba_cena_s_dph\" size=\"5\" value=\"".$iptv_sluzba_cena_s_dph."\" >
			,-</span>
		    
		    </td>";
	
	    //treti radek IPTV - tematicke balicky
	    echo "<tr>
		    <td class=\"label-font\" align=\"center\" >Počet tématických balíčků: </td>
		    <td>
			<select size=\"1\" name=\"pocet_tb\" onChange=\"self.document.forms.form1.submit()\" >
			  <option value=\"0\" style=\"color: gray; \" ";
			  if( $pocet_tb == 0 or !isset($pocet_tb) ){ echo " selected "; }
			   echo " >0 (žádný)</option>
			 
			  <option value=\"1\" "; if($pocet_tb==1){ echo " selected "; } echo " >1</option>
			  <option value=\"2\" "; if($pocet_tb==2){ echo " selected "; } echo " >2</option>
			</select>
		    </td>
		    <td>&nbsp;</td>
		    <td colspan=\"2\" >&nbsp;</td>
		  </tr>";
	
	    for($i=1; $i<=$pocet_tb; $i++)
	    {

	     $tb = "tb".$i;
    
    	     $tb_cena = "tb_cena_".$i;
    	     $tb_cena_s_dph = "tb_cena_s_dph_".$i;
     	    
	    echo "<tr>	      
	    	    <td class=\"label-font\" align=\"center\" >Tématický balíček č.".$i."</td>
		    <td style=\"\">
		    	<span class=\"label-font\" style=\"padding-right: 10px; \" >Název: </span>
			<input type=\"text\" name=\"".$tb."\" size=\"20\" value=\"".$$tb."\" >
		    </td>
		    <td>&nbsp;</td>
		    <td class=\"label-font\" align=\"center\" >CENA, bez DPH / s DPH:</td>
		    <td>
		        <input type=\"text\" name=\"".$tb_cena."\" size=\"5\" value=\"".$$tb_cena."\" >
		        ,-
		        <span style=\"margin-left: 8px; \" >
		        <input type=\"text\" name=\"".$tb_cena_s_dph."\" size=\"5\" value=\"".$$tb_cena_s_dph."\" >
		        ,-</span>
		    
		    </td>
		  </tr>";
	    }
	    
	}
	else
	{ echo "<td colspan=\"3\" ><br></td>"; }
	
	echo "</tr>";

	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";
	
	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >VoIP:</td>
	        <td>
		    <select name=\"voip_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >	
			<option value=\"0\" "; 
			    if( $voip_sluzba == 0 or !isset($voip_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $voip_sluzba == 1 ){ echo " selected "; } echo " >Ano</option>
		    </select>
		</td>
	
		<td colspan=\"3\" ><br></td>
	       </tr>";
	
	if($voip_sluzba == 1)
	{
	 echo "<tr>
		<td class=\"label-font\" align=\"center\" >Telefonní číslo:</td>
		<td><input type=\"text\" name=\"voip_cislo\" value=\"".$voip_cislo."\" ></td>
		<td>&nbsp;</td>

		<td colspan=\"2\" >
		  <span style=\"margin-left: 20px;\" ></span>
		  
		  <input type=\"radio\" name=\"voip_typ\" value=\"1\" ";
		  if( $voip_typ == 1 or !isset($voip_typ) ){ echo " checked=\"checked\" "; } echo " >
		  <span style=\"margin-left: 20px;\" >
		    Paušál (postpaid)
		  </span>
		  <span style=\"margin-left: 10px; margin-right: 10px;\" >|</span>
		  
		  <input type=\"radio\" name=\"voip_typ\" value=\"2\" ";
		  if( $voip_typ == 2 ){ echo " checked=\"checked\" "; }
		  echo " >
		  <span style=\"margin-left: 20px;\" >Kredit (prepaid)</span>
		</td>	
	       </tr>";

	}

	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px black solid;\"></div>
		  </td>
	      </tr>";
		
//zde zacina sleva	
	echo "<tr>
		<td style=\"text-align: center; font-weight: bold;\" >sleva:</td>
		<td>
		  <div style=\"float: left; \" >
		    <select size=\"1\" name=\"sleva_select\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" "; 
			    if( $sleva_select == 0 or !isset($sleva_select) ){ echo " selected "; } 
			echo " >Ne</option>
			<option value=\"1\" "; if($sleva_select == 1){ echo " selected "; } echo " >Ano</option>
		    </select>
		  </div>";
		  
	if( $sleva_doporucena == 1)
	{ echo "<div style=\"text-align: right; padding-right: 5px; font-weight: bold;\" >sleva doporučena</div>"; }
	else
	{ echo "<div style=\"text-align: right; padding-right: 5px;\" >sleva nedoporučena</div>"; }
	
	echo " </td>
	       <td>&nbsp;</td>";
	
	if( $sleva_select == 1)
	{
	    echo "<td class=\"label-font\" align=\"center\" >Součet před slevou: (netiskne se)</td>";
	    echo "<td style=\"font-weight: bold; font-style: italic; \" >".$soucet_bez_dph.",-
		    <span style=\"padding-left: 20px;\"></span>"
		    .$soucet_s_dph.",-";
	    echo "</td>
	      </tr>";
	
	}
	else
	{
	 echo "<td colspan=\"2\" >&nbsp;</td>
	      </tr>";
	
	}

	if( $sleva_select == 1)
	{
	  echo "<tr>
		 <td class=\"label-font\" align=\"center\" >výše slevy: </td>
		 <td>
		    <input type=\"text\" name=\"sleva_hodnota\" size=\"10\" value=\"".$sleva_hodnota."\" >
		    <span style=\"padding-left: 10px; \" >%</span>
		 </td>
		</tr>";
	}


	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";

//platební podmínky atd
	echo "<tr>
		<td colspan=\"5\" >
		    <div style=\"padding-left: 20px; font-weight: bold; \">Platební podmínky:</div>
		</td>
	     </tr>";
	
	echo "<tr>
		<td class=\"label-font\" align=\"center\" >Způsob placení:</td>
	        <td colspan=\"2\" >
		    <span style=\"padding-left: 10px; \" ></span>
		    <input type=\"radio\" name=\"zpusob_placeni\" value=\"1\" ";
			if( $zpusob_placeni == 1 or !isset($zpusob_placeni) ){ echo " checked=\"checked\" "; }
		    echo " >";
	
		    echo "<span style=\"padding-left: 3px;\" >Bankovní příkaz</span>
		    <span style=\"padding-left: 5px; padding-right: 5px; \" >|</span>";
		    
		/*    
		    echo "<input type=\"radio\" name=\"zpusob_placeni\" value=\"2\" ";
			if($zpusob_placeni == 2){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"padding-left: 3px;\" >Inkaso</span>
		    <span style=\"padding-left: 5px; padding-right: 5px; \" >|</span>
		
		
		    <input type=\"radio\" name=\"zpusob_placeni\" value=\"3\" ";
			if($zpusob_placeni == 3){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"padding-left: 3px;\" >SIPO</span>
		*/
		    
		echo "</td> 
		<td class=\"label-font\" align=\"center\">Variabilní symbol:</td>
		<td><input type=\"text\" name=\"vs\" value=\"".$vs."\" ></td>
		
	      </tr>";
	
	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td align=\"center\">Platba:</td>
		<td>";
		
	echo "<input type=\"radio\" name=\"platba\" value=\"1\" ";
		if(($platba == 1) or !isset($platba) ){ echo " checked=\"checked\" "; } echo " >
		<span style=\"padding-left: 3px;\" >Měsíčně</span>
	        <span style=\"padding-left: 5px; padding-right: 5px; \" >|</span>";

	echo "<input type=\"radio\" name=\"platba\" value=\"2\" ";
		if($platba == 2){ echo " checked=\"checked\" "; } echo " >
		<span style=\"padding-left: 3px;\" >Čtvrtletně</span>
	        <span style=\"padding-left: 5px; padding-right: 5px; \" >|</span>";
		
	echo "  </td>
		<td></td>
	      </tr>";

	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td class=\"label-font\" align=\"center\" >doba splatnosti ke dni:</td>
	        <td ><input type=\"text\" name=\"splatnost_ke_dni\" size=\"10\" value=\"".$splatnost_ke_dni."\" ></td>
	        <td>&nbsp;</td>
		<td style=\"font-weight: bold; text-align: center;\" >
		    CELKOVÁ CENA, <span style=\"font-size: 10px; \" >bez DPH / s DPH:</span></td>
		<td>
		    <input class=\"input-border\" type=\"text\" name=\"celk_cena\" size=\"5\" value=\"".$celk_cena."\" >
		    ,-
		    <span style=\"margin-left: 8px; \" >
		    <input class=\"input-border\" type=\"text\" name=\"celk_cena_s_dph\" size=\"5\" value=\"".$celk_cena_s_dph."\" >
		    ,-</span>
		</td>
	      </tr>";

	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td class=\"label-font\" align=\"center\" >
		  Minimální plnění:
		</td>
		<td>
		  <select size=\"1\" name=\"min_plneni\" onChange=\"self.document.forms.form1.submit()\" >
		    <option value=\"1\" "; 
			if($min_plneni == 1 or !isset($min_plneni)){ echo " selected "; }
			echo " >Ne</option>
		    <option value=\"2\" "; if( $min_plneni == 2){ echo " selected "; }
			 echo " >Ano</option>
		  </select>
		</td>
		<td>&nbsp;</td>";
		
	    if( $min_plneni == 2 )
	    {
	      echo "<td>Doba min. plnění(v měsících): </td>
	    	    <td class=\"label-font\" >
			<input type=\"text\" size=\"6\" name=\"min_plneni_doba\" value=\"".$min_plneni_doba."\" >
		    </td>
		    ";
	    }
	    else
	    { echo "<td colspan=\"2\" >&nbsp;</td>"; }
	    
	    echo "</tr>";
	    
//konec policek
echo "
        <tr>
                <td align=\"center\" colspan=\"5\" ><br></td>
        </tr>

	<tr>
	        <td align=\"center\" colspan=\"2\" ><input type=\"submit\" name=\"odeslano\" value=\"OK -- VYGENEROVAT\" ></td>
		<td>&nbsp;</td>
		<td align=\"center\" colspan=\"2\" ><input type=\"submit\" name=\"reg\" value=\"PŘEPOČÍTAT FORMULÁŘ\" ></td>
	</tr>
							

</table>
</form>";

?>
