<?php

echo "<form method=\"POST\" name=\"form1\" >\n";

echo "<table border=\"0\" width=\"1000px\">

    <tr>
        <td colspan=\"5\"><span style=\"font-size: 18px; font-weight: bold; \" >
	Průvodce tiskem smlouvy - nový typ:
	</span></td>
    </tr>

        <tr>
                <td colspan=\"5\"><br></td>
        </tr>

        <tr>
                <td colspan=\"1\" align=\"center\" class=\"label-font\" ><label>Evidenční číslo smlouvy: </label></td>
                <td><input type=\"text\" name=\"ec\" size=\"30\" class=\"input1\" value=\"".$ec."\"></td>
    		<td colspan=\"3\"><br></td>
	</tr>

	<tr>
	    <td colspan=\"5\">&nbsp;</td>
	</tr>
	
	<tr>
	    <td colspan=\"5\" ><div style=\"padding-left: 20px; font-weight: bold; \">Oprávněný Zákazník</div></td>
	    
	</tr>
	
	<tr>
	    <td colspan=\"5\"><br></td>
	</tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Jméno a příjmení:  </label></td>
                <td><input type=\"text\" name=\"jmeno\" class=\"input-size-big\" size=\"25\" value=\"".$jmeno."\" ></td>
        
		<td width=\"50px\" ><br></td>
		
		<td align=\"center\" class=\"label-font\" ><label>Název společnosti: </label></td>
                <td><input type=\"text\" name=\"nazev_spol\" class=\"input-size-big\" size=\"20\" value=\"".$nazev_spol."\" ></td>
        </tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Adresa trvalého bydliště, č.p.: </label></td>
                <td><input type=\"text\" name=\"adresa\" class=\"input-size-big\" size=\"25\" value=\"".$adresa."\" ></td>
        
		<td><br></td>
	        
		<td align=\"center\" class=\"label-font\" ><label>Fakturační adresa: </label></td>
                <td><input type=\"text\" name=\"f_adresa\" size=\"20\" class=\"input-size-big\" value=\"".$f_adresa."\" ></td>
        </tr>

        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Město a PSČ: </label></td>
                <td><input type=\"text\" name=\"mesto\" class=\"input-size-big\" size=\"25\" value=\"".$mesto."\" ></td>
    	
		<td><br></td>
	        
		<td align=\"center\" class=\"label-font\" ><label>Fakturační Město, PSČ:</label></td>
                <td><input type=\"text\" name=\"f_mesto\" size=\"20\" class=\"input-size-big\" value=\"".$f_mesto."\" ></td>
	</tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Číslo OP/Rodné Číslo:</label></td>
                <td><input type=\"text\" name=\"cislo_op\" class=\"input-size-big\" size=\"25\" value=\"".$cislo_op."\" ></td>
    	
		<td><br></td>

		<td align=\"center\" class=\"label-font\" ><label>IČO, DIČ:</label></td>
                <td>
            	    <input type=\"text\" name=\"ico\" size=\"10\" class=\"input-size-big\" value=\"".$ico."\" >
            	    
            	    <span style=\"weight: 10px;\" ></span>
            	    
            	    <input type=\"text\" name=\"dic\" size=\"10\" class=\"input-size-big\" value=\"".$dic."\" >
            	    
            	</td>
		
	</tr>
	        
	<tr><td colspan=\"5\" ><br></td></tr>
	
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Korespondenční adresa: </label></td>
                <td><input type=\"text\" name=\"kor_adresa\" class=\"input-size-big\" size=\"25\" value=\"".$kor_adresa."\" ></td>
    	
		<td ><br></td>

                <td align=\"center\" class=\"label-font\" ><label>Telefon: </label></td>
                <td><input type=\"text\" name=\"telefon\" class=\"input-size-big\" size=\"25\" value=\"".$telefon."\" ></td>
		
	</tr>
        
        <tr>
                <td align=\"center\" class=\"label-font\" ><label>Kor. město, PSČ: </label></td>
                <td><input type=\"text\" name=\"kor_mesto\" class=\"input-size-big\" size=\"25\" value=\"".$kor_mesto."\" ></td>
    	
		<td ><br></td>
	
	        <td align=\"center\" class=\"label-font\" ><label>E-Mail:</label></td>
                <td><input type=\"text\" name=\"email\" class=\"input-size-big\" size=\"25\" value=\"".$email."\" ></td>
    		
	</tr>\n";
    	
	echo "<tr><td colspan=\"5\"><br></td></tr>\n\n";

	echo "<tr>
		<td colspan=\"5\" >
		    <div style=\"padding-left: 20px; font-weight: bold; border-bottom: 1px grey solid; \">
		    Tarify a ceny:</div>
		</td>
	     </tr>\n";
	
	//internet
	echo "<tr>
	
		<td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >Internet:</td>
	        
	        <td>
		    <select name=\"internet_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			
			<option value=\"0\" "; 
			    if( $internet_sluzba == 0 or !isset($internet_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $internet_sluzba == 1 ){ echo " selected "; } echo " >Ano - jeden tarif</option>
			
			<option value=\"2\" "; if( $internet_sluzba == 2 ){ echo " selected "; } echo " >Ano - dva tarify</option>
			
		    </select>
		</td>\n";
		
		echo "<td>&nbsp;</td>\n";
		 
		echo "<td colspan=\"2\">&nbsp;</td>\n";
	
		echo "</tr>\n";
	
	if( $internet_sluzba > 0 )
	{
	    
	  echo "<tr>
		    <td colspan=\"5\" style=\"padding-top: 15px;\" >";
	  
	    
	    echo "<table border=\"0\" width=\"\" >";
	    
	    echo "<tr>
	    
		    <td class=\"label-font\" style=\"width: 50px; text-align: center; font-weight: bold;\" >Výběr</td>
		
		    <td class=\"label-font\" style=\"width: 250px; text-align: center; font-weight: bold;\" >Název tarifu</td>
		
		    <td class=\"label-font\" style=\"width: 100px; text-align: center; font-weight: bold;\" >Rychlost</td>
		    
		    <td class=\"label-font\" style=\"width: 100px; text-align: center; font-weight: bold;\" >Cena s DPH</td>
		
		    <td style=\"width: 20px;\" >&nbsp;</td>
		    
		    <td class=\"label-font\" style=\"width: 100px; text-align: center; font-weight: bold;\" >Veřejná IP</td>
		
		    <td class=\"label-font\" style=\"width: 100px; text-align: center; font-weight: bold;\" >Cena s DPH</td>
		
		    <td class=\"label-font\" style=\"width: 250px; text-align: center; font-weight: bold;\" >Adresa odběrného místa</td>
		
		    \n";
	    	    	    
	    echo "</tr>\n";
	
	
	}
	
	if( $internet_sluzba >= 1 )
	{
	
	    echo "<tr>
		    
		    <td class=\"label-font\" style=\"width: 50px;\" >
			<select name=\"int_select_1\" size=\"1\" style=\"width: 50px;\" onChange=\"self.document.forms.form1.submit()\" >";
			
			    $dotaz_int_1 = mysql_query("SELECT zkratka_tarifu, id_tarifu, jmeno_tarifu, cena_bez_dph, cena_s_dph FROM tarify_int_prodej ORDER BY id_tarifu");
			    $dotaz_int_1_radku = mysql_num_rows($dotaz_int_1);
			    
			    if( $dotaz_int_1_radku > 0)
			    {
				echo "<option value=\"0\" >není vybráno</option>";

				while( $data1 = mysql_fetch_array($dotaz_int_1) )
				{ 
				    echo "<option value=\"".$data1["id_tarifu"]."\" ";
					if($int_select_1 == $data1["id_tarifu"] ){ echo " selected "; }
				    echo " >".$data1["zkratka_tarifu"]." - ".$data1["jmeno_tarifu"]."</option>\n"; }
			    }
			    
			    
		    echo "</select>\n";

	    	    if( $int_select_1 > 0 )
	    	    {
	        	    $dotaz_int_11 = mysql_query("SELECT jmeno_tarifu, cena_s_dph, speed FROM tarify_int_prodej WHERE id_tarifu = '".intval($int_select_1)."' ");
		
			    while( $data_int_11 = mysql_fetch_array($dotaz_int_11) )
			    {
				if( strlen($int_1_nazev) == 0 ){
				    $int_1_nazev = $data_int_11["jmeno_tarifu"];				
				}
				
				if( strlen($int_1_rychlost) == 0 ){
				    $int_1_rychlost = $data_int_11["speed"];				
				}
				
				if( strlen($int_1_cena_1) == 0 ){
				    $int_1_cena_1 = $data_int_11["cena_s_dph"];				
				}
				
			    }
	    	    }
			
		    echo "</td>
		     
		    <td class=\"label-font\" style=\"height: 40px;\" ><input type=\"text\" name=\"int_1_nazev\" size=\"30\" value=\"".$int_1_nazev."\" ></td>
		
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_1_rychlost\" size=\"8\" value=\"".$int_1_rychlost."\" ></td>
		    
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_1_cena_1\" size=\"8\" value=\"".$int_1_cena_1."\" ></td>
		
		    <td style=\"\" >&nbsp;</td>
		    
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_1_vip\" size=\"8\" value=\"".$int_1_vip."\" ></td>
		
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_1_cena_2\" size=\"8\" value=\"".$int_1_cena_2."\" ></td>
		
		    <td class=\"label-font\" style=\"\" ><input type=\"text\" name=\"int_1_adresa\" size=\"33\" value=\"".$int_1_adresa."\" ></td>
		
		    \n";
	    	    	    
	    echo "</tr>\n";
		    
	}
	
	//2. tarif
	if($internet_sluzba == 2)
	{
	
	    echo "<tr>
	
		    <td class=\"label-font\" style=\"width: 50px;\" >
			<select name=\"int_select_2\" size=\"1\" style=\"width: 50px;\" onChange=\"self.document.forms.form1.submit()\" >";
			
			    $dotaz_int_1 = mysql_query("SELECT zkratka_tarifu, id_tarifu, jmeno_tarifu, cena_bez_dph, cena_s_dph FROM tarify_int_prodej ORDER BY id_tarifu");
			    $dotaz_int_1_radku = mysql_num_rows($dotaz_int_1);
			    
			    if( $dotaz_int_1_radku > 0)
			    {
				echo "<option value=\"0\">není vybráno</option>";
				
				while( $data1 = mysql_fetch_array($dotaz_int_1) )
				{ 
				    echo "<option value=\"".$data1["id_tarifu"]."\" ";
					if($int_select_2 == $data1["id_tarifu"] )
					{ echo " selected "; }
						    
				    echo " >".$data1["zkratka_tarifu"]." - ".$data1["jmeno_tarifu"]."</option>\n"; }
			    }
			    
		    echo "</select>\n";
	    
	      
	      if( $int_select_2 > 0 )
	      {
	        	    $dotaz_int_22 = mysql_query("SELECT jmeno_tarifu, cena_s_dph, speed FROM tarify_int_prodej WHERE id_tarifu = '".intval($int_select_2)."' ");
		
			    while( $data_int_22 = mysql_fetch_array($dotaz_int_22) )
			    {
				if( strlen($int_2_nazev) == 0 ){
				    $int_2_nazev = $data_int_22["jmeno_tarifu"];				
				}
				
				if( strlen($int_2_rychlost) == 0 ){
				    $int_2_rychlost = $data_int_22["speed"];				
				}
				
				if( strlen($int_2_cena_1) == 0 ){
				    $int_2_cena_1 = $data_int_22["cena_s_dph"];				
				}
				
			    }
	      }
	      
	      echo "<td class=\"label-font\" style=\"height: 40px;\" ><input type=\"text\" name=\"int_2_nazev\" size=\"30\" value=\"".$int_2_nazev."\" ></td>
		
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_2_rychlost\" size=\"8\" value=\"".$int_2_rychlost."\" ></td>
		    
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_2_cena_1\" size=\"8\" value=\"".$int_2_cena_1."\" ></td>
		
		    <td style=\"\" >&nbsp;</td>
		    
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_2_vip\" size=\"8\" value=\"".$int_2_vip."\" ></td>
		
		    <td class=\"label-font\" style=\"text-align: center;\" ><input type=\"text\" name=\"int_2_cena_2\" size=\"8\" value=\"".$int_2_cena_2."\" ></td>
		
		    <td class=\"label-font\" style=\"\" ><input type=\"text\" name=\"int_2_adresa\" size=\"33\" value=\"".$int_2_adresa."\" ></td>
		
		    \n";
	    	    	    
	    echo "</tr>\n";
	
	}
	
	
	//konec vnitri tabulky a bunky
	if($internet_sluzba > 0)
	{
	    echo "</table>\n";
	    
	    echo "</td>
		</tr>\n";
	}
	
	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>\n";
		
	//iptv 
	echo "<tr>\n".
	
		"<td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >IPTV:</td>
	        
	        <td>
		    <select name=\"iptv_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" ";
			    if( $iptv_sluzba == 0 or !isset($iptv_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $iptv_sluzba == 1){ echo " selected "; } echo " >Ano</option>
		    </select>
		</td>\n";
	
	if( $iptv_sluzba == 1 )
	{

	    echo "<td>&nbsp;</td>\n";
	    echo "<td class=\"label-font\" align=\"center\" >Vyberte tarif:</td>\n";
	
	    echo "<td>
		    <select size=\"1\" name=\"iptv_sluzba_id_tarifu\" onChange=\"self.document.forms.form1.submit()\" >\n";
	
	    $iptv_se = mysql_query("SELECT id_tarifu, jmeno_tarifu, zkratka_tarifu FROM tarify_iptv ORDER BY zkratka_tarifu");

		echo "<option value=\"0\" >Není vybráno</option>\n";
		
	    while( $data_iptv_se = mysql_fetch_array($iptv_se))
	    { 
		echo "<option value=\"".$data_iptv_se["id_tarifu"]."\" ";
		if( $iptv_sluzba_id_tarifu == $data_iptv_se["id_tarifu"] )
		{ echo " selected "; }
		
		echo " >";
		echo $data_iptv_se["jmeno_tarifu"]." (".$data_iptv_se["zkratka_tarifu"].")</option>\n";
	    }
	    
	    echo "</option>\n".
		"</td>\n";
	    
	    echo "</tr>\n";
	    	
	    //
	    echo "<tr>
		    <td colspan=\"5\" >\n";
		
		echo "<table border=\"0\" width=\"\" >\n";
		    
		    echo "<tr>".
			    "<td>&nbsp;</td>".
			    "<td style=\"width: 300px; text-align: center; font-weight: bold;\" >Název tarifu</td>
			     <td style=\"width: 100px; text-align: center; font-weight: bold;\" >počet kanálů</td>
			     <td style=\"width: 100px; text-align: center; font-weight: bold;\" >cena s dph</td>
			  </tr>\n";

		    echo "<tr>".
			    "<td>tarif</td>".
			    "<td style=\"text-align: center; font-weight: bold;\" ><input type=\"text\" name=\"iptv_tarif_nazev\" value=\"".$iptv_tarif_nazev."\" ></td>
			     <td style=\"text-align: center; font-weight: bold;\" ><input type=\"text\" name=\"iptv_tarif_kanaly\" value=\"".$iptv_tarif_kanaly."\" size=\"8\" ></td>
			     <td style=\"text-align: center; font-weight: bold;\" ><input type=\"text\" name=\"iptv_tarif_cena\" value=\"".$iptv_tarif_cena."\" size=\"8\" ></td>
			  </tr>\n";

		    echo "<tr>".
			    "<td>tématický</td>".
			    "<td style=\"text-align: center; font-weight: bold;\" ><input type=\"text\" name=\"iptv_tema_nazev\" value=\"".$iptv_tema_nazev."\" ></td>
			     <td style=\"text-align: center; font-weight: bold;\" ><input type=\"text\" name=\"iptv_tema_kanaly\" value=\"".$iptv_tema_kanaly."\" size=\"8\" ></td>
			     <td style=\"text-align: center; font-weight: bold;\" ><input type=\"text\" name=\"iptv_tema_cena\" value=\"".$iptv_tema_cena."\" size=\"8\" ></td>
			  </tr>\n";

		echo "</table>\n";
		
	    echo "</td>
		    </tr>\n";

	    echo "<tr>
		    <td colspan=\"5\" >&nbsp;</td>
		  </tr>\n";

	    echo "<tr>
		    <td class=\"label-font\" align=\"center\">Set-Top-Box</td>
		    <td><input type=\"text\" name=\"stb\" value=\"".$stb."\" ></td>	    
		    <td colspan=\"3\" >&nbsp;</td>
		  </tr>\n";

	    echo "<tr>
		    <td class=\"label-font\" align=\"center\">Sériové č.</td>
		    <td><input type=\"text\" name=\"stb_sn\" value=\"".$stb_sn."\" ></td>	    
		    <td>&nbsp;</td>
		    <td class=\"label-font\" align=\"center\">Vratná kauce</td>
		    <td><input type=\"text\" name=\"stb_kauce\" value=\"".$stb_kauce."\" ></td>	    
		    
		  </tr>\n";
		  	    
	}
	else
	{ echo "<td colspan=\"5\" ><br></td>\n"; }
	
	echo "</tr>";

	echo "<tr>
		  <td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";
	
	echo "<tr>
		  <td class=\"label-font\" style=\"text-align: center;\" >Ostatní</td>
		  <td><input type=\"text\" name=\"ostatni_nazev\" value=\"".$ostatni_nazev."\" ></td>
		  <td>&nbsp;</td>
		  
		  <td class=\"label-font\" style=\"text-align: center;\">Ostatní - Cena s DPH</td>
		  <td><input type=\"text\" name=\"ostatni_cena\" value=\"".$ostatni_cena."\" ></td>
		  
	      </tr>";
	
	echo "<tr>
		  <td colspan=\"5\" height=\"20px\">
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
			<option value=\"1\" "; if( $voip_sluzba == 1 ){ echo " selected "; } echo " >Ano - jedno číslo</option>
			<option value=\"2\" "; if( $voip_sluzba == 2 ){ echo " selected "; } echo " >Ano - dvě čísla</option>
		    </select>
		</td>
	
		<td colspan=\"3\" ><br></td>
	       </tr>";
	       	
	if($voip_sluzba >= 1)
	{
	
	 echo "<tr>
		<td class=\"label-font\" align=\"center\" >Číslo 1.:</td>
		<td><input type=\"text\" name=\"voip_1_cislo\" value=\"".$voip_1_cislo."\" ></td>
		<td>&nbsp;</td>

		<td colspan=\"2\" >
		  <span style=\"margin-left: 20px;\" ></span>
		  
		  <input type=\"radio\" name=\"voip_1_typ\" value=\"1\" ";
		  if( $voip_1_typ == 1 or !isset($voip_1_typ) ){ echo " checked=\"checked\" "; } echo " >
		  <span style=\"margin-left: 20px;\" >
		    Paušál (postpaid)
		  </span>
		  <span style=\"margin-left: 10px; margin-right: 10px;\" >|</span>
		  
		  <input type=\"radio\" name=\"voip_1_typ\" value=\"2\" ";
		  if( $voip_1_typ == 2 ){ echo " checked=\"checked\" "; }
		  echo " >
		  <span style=\"margin-left: 20px;\" >Kredit (prepaid)</span>
		</td>	
	       </tr>";

	}

	if($voip_sluzba == 2)
	{

	 echo "<tr>
		<td class=\"label-font\" align=\"center\" >Číslo 2.:</td>
		<td><input type=\"text\" name=\"voip_2_cislo\" value=\"".$voip_2_cislo."\" ></td>
		<td>&nbsp;</td>

		<td colspan=\"2\" >
		  <span style=\"margin-left: 20px;\" ></span>
		  
		  <input type=\"radio\" name=\"voip_2_typ\" value=\"1\" ";
		  if( $voip_2_typ == 1 or !isset($voip_2_typ) ){ echo " checked=\"checked\" "; } echo " >
		  <span style=\"margin-left: 20px;\" >
		    Paušál (postpaid)
		  </span>
		  <span style=\"margin-left: 10px; margin-right: 10px;\" >|</span>
		  
		  <input type=\"radio\" name=\"voip_2_typ\" value=\"2\" ";
		  if( $voip_2_typ == 2 ){ echo " checked=\"checked\" "; }
		  echo " >
		  <span style=\"margin-left: 20px;\" >Kredit (prepaid)</span>
		</td>	
	       </tr>";
	
	}
	
	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px black solid;\"></div>
		  </td>
	      </tr>";
		
	//zde zacina sleva/bonusy
	echo "<tr>
		<td style=\"text-align: center; font-weight: bold;\" >Bonusy:</td>
		<td>
		  <div style=\"float: left; \" >
		    <select size=\"1\" name=\"sleva_select\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" "; 
			    if( $sleva_select == 0 or !isset($sleva_select) ){ echo " selected "; } 
			echo " >Ne</option>
			<option value=\"1\" "; if($sleva_select == 1){ echo " selected "; } echo " >Ano</option>
		    </select>
		  </div>\n";
		  
	echo " </td>
	       <td>&nbsp;</td>\n";
	
	echo " <td colspan=\"2\" >&nbsp;</td>
	      </tr>";

	echo "<tr><td colspan=\"5\"><br></td></tr>";

	if($sleva_select == 1)
	{
	
    	    echo "<tr>
    		    <td style=\"text-align: center; \" >Zákazník</td>
    		    <td colspan=\"5\" >
			<select name=\"bonus_select_1\" size=\"1\" style=\"margin-left: 30px; margin-right: 30px;\" >
			    <option value=\"0\" ";
				if( $bonus_select_1 == 0 or !isset($bonus_select_1) ){ echo " selected "; } 
			    echo " >nemá</option>
			    <option value=\"1\" "; if( $bonus_select_1 == 1 ){ echo " selected "; } echo ">má</option>
			</select>
			nárok na bonusovou slevu z tarifu.
		    </td>
	    	   </tr>\n";

    	    echo "<tr><td colspan=\"5\"><br></td></tr>";

	    echo "<tr>
    		    <td>&nbsp;</td>
    		    <td>název tarifu</td>
    		    <td>&nbsp;</td>
    		    <td>původní cena</td>
    		    <td>nová cena</td>
	    	  </tr>\n";
	
	
	    echo "<tr>
    		    <td style=\"text-align: center; \" >Bonus č.1</td>
    		    <td><input type=\"text\" name=\"bonus_1_tarif\" value=\"".$bonus_1_tarif."\" size=\"20\" ></td>
    		    <td>&nbsp;</td>
	    	    <td><input type=\"text\" name=\"bonus_1_cena1\" value=\"".$bonus_1_cena1."\" size=\"10\" ></td>
    		    <td><input type=\"text\" name=\"bonus_1_cena2\" value=\"".$bonus_1_cena2."\" size=\"10\" ></td>
	    	  </tr>\n";
	    	  
	    echo "<tr>
    		    <td style=\"text-align: center; \" >Bonus č.2</td>
    		    <td><input type=\"text\" name=\"bonus_2_tarif\" value=\"".$bonus_2_tarif."\" size=\"20\" ></td>
    		    <td>&nbsp;</td>
	    	    <td><input type=\"text\" name=\"bonus_2_cena1\" value=\"".$bonus_2_cena1."\" size=\"10\" ></td>
    		    <td><input type=\"text\" name=\"bonus_2_cena2\" value=\"".$bonus_2_cena2."\" size=\"10\" ></td>
	    	  </tr>\n";
	    	   	
	}
	
	//platební předpis
	echo "<tr><td colspan=\"5\"><br></td></tr>\n";

	echo "<tr>
		<td align=\"center\" >Platební předpis</td>
		<td colspan=\"5\">&nbsp;</td>
	      </tr>\n";

	echo "<tr><td colspan=\"5\"><br></td></tr>\n";

	echo "<tr>
    	        <td colspan=\"5\" >\n";
    	        
    	    echo "<table border=\"0\" width=\"\" style=\"padding-left: 150px;\" >\n";
    	    
    	    echo "<tr>
    		    <td width=\"100px\" >od</td>
    	    	    <td width=\"100px\" >do</td>
    	    	    <td width=\"100px\" >cena s dph</td>
    	    	    <td width=\"200px\" >poznámka</td>
	    	  </tr>\n";
	
	    echo "<tr>
    		    <td><input type=\"text\" name=\"platba_1_od\" value=\"".$platba_1_od."\" size=\"8\" class=tcal ></td>
    	    	    <td><input type=\"text\" name=\"platba_1_do\" value=\"".$platba_1_do."\" size=\"8\" class=tcal ></td>
    	    	    <td><input type=\"text\" name=\"platba_1_cena\" value=\"".$platba_1_cena."\" size=\"8\" ></td>
    	    	    <td><input type=\"text\" name=\"platba_1_pozn\" value=\"".$platba_1_pozn."\" ></td>
	    	  </tr>\n";
	
	    echo "<tr>
    		    <td><input type=\"text\" name=\"platba_2_od\" value=\"".$platba_2_od."\" size=\"8\" class=tcal ></td>
    	    	    <td><input type=\"text\" name=\"platba_2_do\" value=\"".$platba_2_do."\" size=\"8\" class=tcal ></td>
    	    	    <td><input type=\"text\" name=\"platba_2_cena\" value=\"".$platba_2_cena."\" size=\"8\" ></td>
    	    	    <td><input type=\"text\" name=\"platba_2_pozn\" value=\"".$platba_2_pozn."\" ></td>
	    	  </tr>\n";
	
	    echo "<tr>
    		    <td><input type=\"text\" name=\"platba_3_od\" value=\"".$platba_3_od."\" size=\"8\" class=tcal ></td>
    	    	    <td><input type=\"text\" name=\"platba_3_do\" value=\"".$platba_3_do."\" size=\"8\" class=tcal ></td>
    	    	    <td><input type=\"text\" name=\"platba_3_cena\" value=\"".$platba_3_cena."\" size=\"8\" ></td>
    	    	    <td><input type=\"text\" name=\"platba_3_pozn\" value=\"".$platba_3_pozn."\" ></td>
	    	  </tr>\n";
	
	    echo "</td>
		    </tr>
		</table>\n";
	
	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";

	
	//platební podmínky
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
		<td align=\"center\">Platba (fakturace):</td>
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
		<td colspan=\"2\" >&nbsp;</td>
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
		
	echo "<td colspan=\"2\" >&nbsp;</td>";
	
	echo "</tr>";
	
	
	if( $min_plneni == 2 )
	{
	
	      echo "<tr>";
	      
	      echo "<td>Doba min. plnění(v měsících): </td>
	    	    <td class=\"label-font\" >
			<input type=\"text\" size=\"6\" name=\"min_plneni_doba\" value=\"".$min_plneni_doba."\" >
		    </td>
		    ";
	
	      echo "</tr>";
	      
	      echo "<tr>";
	      
	      echo "<td>Automatické prodloužení (v měsících): </td>
	    	    <td class=\"label-font\" >
			<input type=\"text\" size=\"6\" name=\"aut_prodlouzeni\" value=\"".$aut_prodlouzeni."\" >
		    </td>
		    ";
	
	      echo "</tr>";
	      
	}
	    
	    
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
