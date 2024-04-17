<?php

class partner_servis {

    //
    // variables
    //
    
    var	$jmeno_klienta;
    var $bydliste;
    var $email;
    var $tel;
    var $pozn;
    
    var $odeslat;
    
    var $klient_hledat;
    var $klient_id;
    
    var $fail;
    var $error;
    
    var $fill_form;
    
    var $vyrizeni;
    var $update;
    
    //
    //  function
    //
    
    function show_insert_form(){
    
    echo "  <div style=\"padding-left: 40px; padding-bottom: 10px; padding-top: 10px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >
                   Vložení servisního zásahu
                 </span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 30px; \">Stávající klient: </span>
                 <span style=\"padding-left: 195px; \">hledání</span>
                 <span style=\"padding-left: 45px; \" ><input type=\"text\" name=\"klient_hledat\" size=\"25\" value=\"".$this->klient_hledat."\" ></span>
                
                 <span style=\"padding-left: 195px; \"><input type=\"submit\" name=\"filtrovat\" value=\"HLEDAT\" ></span>
                
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 365px; \">výběr</span>
                 <span style=\"padding-left: 52px; \" >";
            
        	 if( (strlen($this->klient_hledat) == 0) ){
        	 
        	    echo "<span style=\"padding-right: 70px; font-weight: bold;\">Zadejte výraz pro hledání</span>\n";
        	 }
        	 else
        	 {
        	 
        	   $vlastnici = $this->find_clients($this->klient_hledat);
        	
        	   if( (count($vlastnici) == 0) ){       
        	       echo "Žádné výsledky dle hledaného výrazu \n";
        	   }
        	   elseif((count($vlastnici) > 200 )){
        	       
        	       echo "<span>více nalezených klientů, prosím specifikujte hledání</span>\n";
        	   } 
        	   elseif( is_array($vlastnici) ){
        	    
        		echo "<select size=\"1\" name=\"klient_id\">\n";
        		   echo "<option value=\"0\" class=\"select-nevybrano\">není vybráno</option>\n";
        		   
        		   $klient_id = intval($this->klient_id);
        		   
        		    foreach($vlastnici as $key => $value){
        			echo "\t\t<option value=\"".intval($value["id_cloveka"])."\" ";
        			
        			if( $value["id_cloveka"] == $klient_id){ echo " selected "; }
        			
        			echo " >".$value["prijmeni"]." ".$value["jmeno"];
        			echo " -- ".$value["ulice"].", ".$value["mesto"]."";
        			echo "</option>\n";
        		    }
        		    
        		echo "</select>\n";
    
        	   }
        	   else{
        		echo "<span style=\"color: red;\"> error: select from vlastnici \"failed\" </span>";
        	   }
        	 
        	 }
        	   
                 echo "</span>";
                 
                 echo "<span style=\"padding-left: 90px;\" >
                	    <input type=\"submit\" name=\"fill_form\" value=\"PŘENÉST DO FORMULÁŘE\">
                       </span>";
    
    echo "      </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 30px; \">Jméno a příjmení klienta: </span>
                 <span style=\"padding-left: 140px; \" > <input type=\"text\" name=\"jmeno_klienta\" size=\"45\" value=\"".$this->jmeno_klienta."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 30px; \">Bydliště/přípojné místo: </span>
                 <span style=\"padding-left: 148px; \" > <input type=\"text\" name=\"bydliste\" size=\"45\" value=\"".$this->bydliste."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 0px; \" >Emailová adresa: </span>
                 <span style=\"padding-left: 218px; \" > <input type=\"text\" name=\"email\" size=\"30\" value=\"".$this->email."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 0px; \">Telefon: </span>
                 <span style=\"padding-left: 273px; \" > <input type=\"text\" name=\"tel\" size=\"30\" value=\"".$this->tel."\" ></span>
                </div>

                <div style=\"padding-bottom: 20px; margin-left: 45px;  \">
                 <span style=\"position: absolute; padding-top: 35px; \">
                 Poznámka / servisní zásah:
                 </span>
                 <span style=\"padding-left: 325px; \" >

                    <textarea name=\"pozn\" cols=\"35\" rows=\"5\" >".$this->pozn."</textarea>
                 </span>
                </div>

                <div style=\"padding-bottom: 20px; \">
                 <span style=\"padding-left: 45px; padding-right: 0px; \">Priorita: </span>
                 <span style=\"padding-left: 273px; \" >
                  <select size=\"3\" name=\"prio\">";
                  
                echo "<option value=\"1\" "; if($this->prio == 1) echo "selected"; echo " >Vysoká</option>";
                echo "<option value=\"2\" "; if($this->prio == 2 or !isset($this->prio)) echo "selected"; echo " >Normal</option>";
                echo "<option value=\"3\" "; if($this->prio == 3) echo "selected"; echo " >Nízká</option>";
                    
            echo "</select>
                </div>

                <div style=\" padding-top: 40px; \">
                 <span style=\"padding-left: 100px; \" > <input type=\"submit\" name=\"odeslat\" value=\"ULOŽIT\" >

                 <input type=\"hidden\" name=\"user\" value=\"".$this->user."\" >
                 <input type=\"hidden\" name=\"mod\" value=\"".$this->mod."\" >

                 </span>
                </div>";
	
    } //end of function

    function find_clients($find_string) {
    
	$fs = "%".mysql_real_escape_string($find_string)."%";
	
        $select = " WHERE (nick LIKE '$fs' OR jmeno LIKE '$fs' OR prijmeni LIKE '$fs' ";
        $select .= " OR ulice LIKE '$fs' OR mesto LIKE '$fs' OR poznamka LIKE '$fs' )";                                            
                                                    
	$rs_vlastnici = pg_query("SELECT id_cloveka, jmeno, prijmeni, ulice, mesto FROM vlastnici ".$select."");
	
	while($array = pg_fetch_array($rs_vlastnici)){
	    
	    $row = array();
	    
	    $row["id_cloveka"] = $array["id_cloveka"];
	    $row["jmeno"] = $array["jmeno"];
	    $row["prijmeni"] = $array["prijmeni"];
	    $row["ulice"] = $array["ulice"];
	    $row["mesto"] = $array["mesto"];
	    
	    $RetArray[] =$row;
	
	}
	
	
	return $RetArray;
    } //end of function
    
    function form_copy_values(){
    
	$rs_v = pg_query("SELECT id_cloveka, jmeno, prijmeni, ulice, mesto, mail, telefon FROM vlastnici WHERE id_cloveka = '".intval($this->klient_id)."' ");
	
	while($array = pg_fetch_array($rs_v)){
	    
	    $this->jmeno_klienta = $array["jmeno"]." ".$array["prijmeni"];
	    $this->bydliste = $array["ulice"].", ".$array["mesto"];
	    $this->email = $array["mail"];
	    $this->tel = $array["telefon"];
	    
	}
		
    } //end of function
    
    function check_insert_value() {

	// zde kontrola, popr. naplneni promenne error

        $this->fail = false;

        //diakriticka predloha: příliš žluťoučký kůň pěl ďábelské ódy

        //kontrola jmena
        if(strlen($this->jmeno_klienta) > 50)
        {
          $this->fail = true;
          $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Jméno a příjmení\" může obsahovat maximálně 50 znaků.</H4></div>";
        }
        elseif((strlen($this->jmeno_klienta) < 2) and ($this->odeslat == "ULOŽIT"))
        {
          $this->fail = true;
          $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Jméno a příjmení\" musí být vyplněno.</H4></div>";
        
        }

	//kontrola bydliste / pripojneho mista
        if(strlen($this->bydliste) > 50)
        {
    	  $this->fail = true;
          $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Bydliště\" může obsahovat maximálně 50 znaků.</H4></div>";
        }
        elseif( (strlen($this->bydliste) < 5) and ($this->odeslat == "ULOŽIT"))
        {
    	  $this->fail = true;
          $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Bydliště\" musí být vyplněno.</H4></div>";
        }
        
	//kontrola emailu
        if( (strlen($this->email) > 0) and ($this->odeslat == "ULOŽIT") )
        {
    	    if( !(Aglobal::check_email($this->email)) )
    	    {
        	$this->fail = true;
        	$this->error .= "<div style=\"color: red; padding-left: 10px;\" >".
        		    "<H4>Pole \"Email\" neodpovídá tvaru emailu.</H4></div>";
    	    }
        }
        
        //kontrola telefonu, resp. tel. cisla
        if(strlen($this->tel) > 0)
        {
        
            if( !(ereg('^([[:digit:]])+$',$this->tel)) )
    	    {
                 $this->fail = true;
                 $this->error .= "<div style=\"color: red; padding-left: 10px;\">".
                	    "<H4>Pole \"Telefon\" není ve správnem formátu. (pouze číslice)</H4></div>";
            }
                                                         
            if( strlen($this->tel) <> 9 ){
                                                     
    		$this->fail = true;
		$this->error .= "<div style=\"color: red; padding-left: 10px; \">".
				"<H4>Pole \"Telefon\" musí obsahovat 9 číslic. </H4></div>";
    	    }
    
        }
        elseif( (strlen($this->tel) == 0) and ($this->odeslat == "ULOŽIT") ){
    	    $this->fail = true;
    	    $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Telefon\" musí být vyplněno.</H4></div>";
        }
        
        //kontrola poznamky
        if(strlen($this->pozn) > 0)
        {
            if( strlen($this->pozn) > 500 ){
                                                     
    		$this->fail = true;
		$this->error .= "<div style=\"color: red; padding-left: 10px;\">".
				"<H4>Pole \"Poznámka\" musí obsahovat 9 číslic.</H4></div>";
    	    }
    	        
        }
        elseif( $this->odeslat == "ULOŽIT" )
        {
            $this->fail = true;
    	    $this->error .= "<div style=\"color: red; padding-left: 10px;\" ><H4>Pole \"Poznámka\" musí být vyplněno.</H4></div>";
        
        }
        
        
/*
        if( !( ereg('^([[:digit:]])+$',$this->tel) ) )
        {
         $this->fail = true;
         $this->error .= "<div style=\"color: red; \" ><H4>Pole \"Telefon\" musí obsahovat pouze číslice! </H4></div>";
        }

        if ( !( ereg('^([[:digit:]])+$',$this->typ_balicku) ) )
        {
         $this->fail = true;
         $this->error .= "<div style=\"color: red; \" ><H4>Pole \"Typ instalačního balíčku\" je ve špatném formátu! </H4></div>";
        }

        if ( !( ereg('^([[:digit:]])+$',$this->typ_linky) ) )
        {
         $this->fail = true;
         $this->error .= "<div style=\"color: red; \" ><H4>Pole \"Linka\" je ve špatném formátu! </H4></div>";
        }

        if( (strlen($this->pozn) > 0) )
        {
    	    if ( !( ereg('^([[:alnum:]]|ř|í|š|ž|ť|č|ý|ů|ň|ě|ď|á|é|ó|ú|Ř|Í|Š|Ž|Ť|Č|Ý|Ů|Ň|Ě|Ď|Á|É|Ó| |-|\.|_|@|,|\(|\)|\?)+$',$this->pozn) ) )
    	    {
        	$this->fail = true;
        	$this->error .= "<div style=\"color: red; \" ><H4>Poznámka obsahuje nepovolené znaky ( povolené: Písmena, čísla,  - , ., _, @, ,(,),? ) ! </H4></div>";
    	    }
        }
*/

    } //end of function 

    function save_form(){
    
    	  if( isset($this->klient_id) )
	  { $this->jmeno_klienta .= ",  V:".$this->klient_id; }
	 
    echo "  <div style=\"padding-bottom: 20px; padding-top: 20px; padding-left: 20px; font-size: 18px; font-weight: bold; \">
             <span style=\"border-bottom: 1px solid grey; \" >
                Vložené informace:
             </span>
            </div>

            <div style=\"padding-left: 20px; \" >
                <span style=\"font-weight: bold; \" >Jméno, příjmení klienta: </span>
                <span style=\"padding-left: 80px; \" > ".htmlspecialchars($this->jmeno_klienta)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Bydliště: </span>
                <span style=\"padding-left: 181px; \" > ".htmlspecialchars($this->bydliste)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Emailová adresa: </span>
                <span style=\"padding-left: 125px; \" > ".htmlspecialchars($this->email)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Telefon: </span>
                <span style=\"padding-left: 184px; \" > ".htmlspecialchars($this->tel)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Poznámka / servisní zásah: </span>
                <span style=\"padding-left: 63px; \" > ".htmlspecialchars($this->pozn)."</span>
            </div>

            <div style=\"padding-left: 20px; padding-top: 5px; \" >
                <span style=\"font-weight: bold; \" >Priorita: </span>
                <span style=\"padding-left: 184px; \" > ";
            	    if($this->prio == 1) echo "Vysoká";
            	    elseif($this->prio == 2) echo "Normální";
            	    elseif($this->prio == 3) echo "Nízká";
            	    else echo "Nejze zjistit (".intval($this->prio).")";
            echo "</span>
            </div>

          <div style=\"padding-top: 5px; \" ></div>";

          if( isset($user) )
          { $user_plaint= $_SESSION["user_plaint"]; }
          else
          { $user_plaint = $_SESSION["db_nick"]; }

	  $tel = mysql_real_escape_string($this->tel);
	  $email = mysql_real_escape_string($this->email);
	  
	  $jmeno_klienta = mysql_real_escape_string($this->jmeno_klienta);
	  $bydliste = mysql_real_escape_string($this->bydliste);
	  $pozn = mysql_real_escape_string($this->pozn);

	  $prio = intval($this->prio);

	  $user_plaint = mysql_real_escape_string($user_plaint);   
	  
          $add = mysql_query("INSERT INTO partner_klienti_servis (tel, jmeno, adresa, email, poznamky, prio, vlozil)
                            VALUES ('$tel','$jmeno_klienta','$bydliste','$email','$pozn', '$prio', '$user_plaint') ");

    	echo "<div style=\"padding-left: 20px; padding-top: 15px; padding-bottom: 10px;\" >";

          if($add == 1)
          {
            echo "<div style=\"color: green; font-size: 18px; font-weight: bold;\" >Záznam úspěšně uložen.</div>";
          }
          else
          {
            echo "<div style=\"color: red; font-weight: bold; font-size: 16px; \">Záznam nelze vložit do databáze. </div>";
            echo "<div style=\"color: grey; \">debug: ".mysql_error()."</div>";
          }

        echo "</div>";

    
    } //end of function

    function list_show_legend($vyrizeni,$update)
    {
        if($vyrizeni == true)
        {

           echo "  <div style=\"padding-left: 40px; padding-top: 20px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >Akceptování žádostí o připojení</span>
                </div>";
        }
        elseif($update == true)
        {
    	   echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >Změna poznámky</span>
                </div>";
        }
        else
        {
           echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 10px; font-weight: bold; font-size: 18px; \">
                 <span style=\"border-bottom: 1px solid grey; \" >Výpis vložených položek</span>
                </div>";
        }

    } //end of function list show legend

    function list_show_items($filtr_akceptovano,$filtr_prio,$dotaz_sql)
    {
        echo "<div style=\"padding-left: 45px; \">
                <table border=\"0\" width=\"90%\" cellpadding=\"5\" >\n";

             echo "<form action=\"\" method=\"GET\" >\n";

             echo "<tr><td colspan=\"8\" >
                    <span style=\"font-weight: bold; \" >Filtrování:</span>

                    <span style=\"font-weight: bold; padding-left: 20px; color: gray; \" >Akceptováno technikem:</span>
                    <span style=\"padding-left: 20px; \">
                        <select name=\"filtr_akceptovano\">
                        <option value=\"0\" "; if ( $filtr_akceptovano == 0 or !isset($filtr_akceptovano) ){ echo " selected "; }
                            echo "class=\"select-nevybrano\">Nevybráno</option>
                         <option value=\"1\" "; if ( $filtr_akceptovano == 1 ){ echo " selected "; } echo ">Ano</option>
                         <option value=\"2\" "; if ( $filtr_akceptovano == 2 ){ echo " selected "; } echo ">Ne</option>
                        </select>
                    </span>

                    <span style=\"font-weight: bold; padding-left: 20px; color: gray; \" >Priorita:</span>
                    <span style=\"padding-left: 20px; \">
                        <select name=\"filtr_prio\" size=\"1\">
                         <option value=\"0\" "; if ( $filtr_prio == 0 or !isset($filtr_prio )){ echo " selected "; }
                                echo " class=\"select-nevybrano\">Nevybráno</option>
                         <option value=\"1\" "; if ( $filtr_prio == 1 ){ echo " selected "; } echo " >Vysoká</option>
                         <option value=\"2\" "; if ( $filtr_prio == 2 ){ echo " selected "; } echo " >Normální</option>
                         <option value=\"3\" "; if ( $filtr_prio == 3 ){ echo " selected "; } echo " >Nízká</option>
                         
                        </select>
                    </span>

                    <span style=\"padding-left: 60px; \" ><input type=\"submit\" name=\"filtr\" value=\"FILTRUJ\" ></span>

                   </td></tr>";
    
    	     echo "<input type=\"hidden\" name=\"user\" value=\"".htmlspecialchars($user)."\" >
                   <input type=\"hidden\" name=\"mod\" value=\"".htmlspecialchars($mod)."\" >";

             echo "</form>";

             echo "<tr><td colspan=\"8\" ><br></td></tr>";

            $filtr="";

           //prvne dotaz

           $dotaz = mysql_query($dotaz_sql);

	   //if( !$dotaz )
	   //{ echo "error: mysql_query: ".mysql_error().": sql: ".$dotaz_sql."\n"; }
	   
           $dotaz_radku = mysql_num_rows($dotaz);

           if ( $dotaz_radku > 0)
           {
             echo "<tr><td colspan=\"8\" >
                    <span style=\"font-weight: bold;\" >Počet zákazníků:</span> ".$dotaz_radku."
                   </td></tr>";

             echo "<tr><td colspan=\"8\" ><br></td></tr>";

              // popis sloupcu
              echo "<tr>
                        <td class=\"table-vypis-1-line2\"><span style=\"font-weight: bold; \">Jméno klienta: </span></td>
                        <td class=\"table-vypis-1-line2\"><span style=\"font-weight: bold; \">Bydliště: </span></td>

                        <td class=\"table-vypis-1-line2\"><span style=\"font-weight: bold; \">Email: </span></td>
                        <td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">Telefon: </span></td>

                        <td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold;\">Akceptováno technikem: </span></td>
                        
                        <td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">&nbsp;</span></td>\n";

                        if ( $this->vyrizeni == true)
                        {
                          echo "<td class=\"table-vypis-1-line2\" colspan=\"2\" ><span style=\"font-weight: bold; \">Akceptovat</span></td>\n";
                        }
                        elseif( $this->update == true)
                        {
                          echo "<td class=\"table-vypis-1-line2\" colspan=\"2\" ><span style=\"font-weight: bold; \">Upravit</span></td>\n";
                        }
                        else
                        {
                          echo "<td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">&nbsp;</span></td>
				<td class=\"table-vypis-1-line2\" ><span style=\"font-weight: bold; \">&nbsp;</span></td>\n";
                        }

                if ( !($this->vyrizeni == true) and !($this->update == true) )
                {

                echo "</tr>
                    <tr>
                      <td colspan=\"2\" class=\"table-vypis-1-line\">
                        <span style=\"font-weight: bold; \">Poznámka/servis. úkol: </span>
                      </td>
                      <td colspan=\"1\" class=\"table-vypis-1-line\">
                        <span style=\"font-weight: bold; \">Vložil: </span>
                      </td>
                      <td colspan=\"1\" class=\"table-vypis-1-line\">
                       <span style=\"font-weight: bold; \">Priorita: </span>
                      </td>
                      <td colspan=\"2\" class=\"table-vypis-1-line\">
                       <span style=\"font-weight: bold; \">Poznámka technika: </span>

                      </td>
                      <td colspan=\"2\" class=\"table-vypis-1-line\" >
                        <span style=\"font-weight: bold; \">Datum vložení: </span>
                      </td>
                    </tr>";
                }

              echo "<tr><td colspan=\"8\" ><br></td></tr>";

              while( $data=mysql_fetch_array($dotaz) )
              {
                $jmeno = htmlspecialchars($data["jmeno"]);
		
		//nahrazeni id vlasntníka odkazem
	        if( preg_match("/V:\d/", $jmeno) )
        	{
        	    $id_cloveka_res = "";
        	    $pomocne = explode("V:", $jmeno);
        	    $id_cloveka_pomocne = $pomocne[1];
        	    
            	    $dotaz_vlastnik_pom = pg_query("SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");

            	    while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
            	    { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }

            	    if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"/adminator2/vlastnici-archiv.php"; }
            	    elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"/adminator2/vlastnici2.php"; }
            	    else{ $id_cloveka_res .= "<a href=\"/adminator2/vlastnici.php"; }

        	    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >V:".$id_cloveka_pomocne."</a>";

        	    $jmeno = ereg_replace("V:".$id_cloveka_pomocne, $id_cloveka_res, $jmeno);
		    
	    	}

                if( ( $this->vyrizeni == true) or ($this->update == true) ){ $class="table-vypis-suda-radka"; }

                echo "<tr>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">".$jmeno."</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";
                   if( ( strlen($data["adresa"]) < 1) ){ echo "&nbsp;"; }
                   else { echo htmlspecialchars($data["adresa"]); }
                echo "</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";
                   if( ( strlen($data["email"]) < 1) ){ echo "&nbsp;"; }
                   else { echo htmlspecialchars($data["email"]); }
                echo "</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";
                   if( ( strlen($data["tel"]) < 1) ){ echo "&nbsp;"; }
                   else{ echo htmlspecialchars($data["tel"]); }
                echo "</span></td>";

                echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">";

    
                  if( $data["akceptovano"] == 1 )
                  {
                    echo "<span style=\"color: green; font-weight: bold; \">Ano </span>";
                    echo "<span style=\"\">(".htmlspecialchars($data["akceptovano_kym"]).")</span>";
                  }
                  else
                  { echo "<span style=\"color: orange; font-weight: bold; \" >Ne</span>"; }
                
                  echo "</span></td>";
		
		echo "<td class=\"".$class."\" >&nbsp;</td>";
		echo "<td class=\"".$class."\" >&nbsp;</td>";
		
                if( $this->vyrizeni == true)
                {
                 echo "<td colspan=\"2\" class=\"".$class."\"><a href=\"".$_SERVER["PHP_SELF"]."?accept=1&id=".intval($data["id"])."\">akceptovat</a></td>";
                }
                elseif( $this->update == true)
                {
                 echo "<td colspan=\"2\" class=\"".$class."\"><a href=\"".$_SERVER["PHP_SELF"]."?edit=1&id=".intval($data["id"])."\">upravit</a></td>";
                }
                else
                {

            	  echo "<td class=\"".$class."\" ><span style=\"font-size: 13px; \">&nbsp;</span></td>";

                }

                echo "</tr>";

                if ( !( $this->vyrizeni == true) and !($this->update == true) )
                {
                // druha radka

                echo "<tr>";

                echo "<td colspan=\"2\" class=\"table-vypis-suda-radka\" ><span style=\"font-size: 12px; color: #555555; \">";
                   if( ( strlen($data["poznamky"]) < 1) ){ echo "poznámka nevložena"; }
                   else { echo htmlspecialchars($data["poznamky"]); }
                echo "</span></td>";

                echo "<td colspan=\"1\" class=\"table-vypis-suda-radka\" ><span style=\"font-size: 12px; color: #555555; \">";
                   if( ( strlen($data["vlozil"]) < 1) ){ echo "vložil"; }
                   else { echo htmlspecialchars($data["vlozil"]); }
                echo "</span></td>";

                echo "<td class=\"table-vypis-suda-radka\" ><span style=\"font-size: 13px; color: gray; \">";
                 
                    if($data["prio"] == 1){ echo "<span style=\"color: #990033;\">Vysoká</span>"; }
                    elseif($data["prio"] == 2){ echo "Normální"; }
                    elseif($data["prio"] == 3){ echo "Nízká"; }
                    else{ echo "Nelze zjistit"; }
                
                echo "</span></td>";

                echo "<td colspan=\"2\" class=\"table-vypis-suda-radka\" ><span style=\"font-size: 12px; color: #555555; \">";
                   if( ( strlen($data["akceptovano_pozn"]) < 1) ){ echo "poznámka nevložena"; }
                   else { echo $data["akceptovano_pozn"]; }
                echo "</span></td>";

                echo "<td colspan=\"2\" class=\"table-vypis-suda-radka\" >
                        <span style=\"font-size: 12px; color: #555555; \">";
                   if( $data["datum_vlozeni2"] == "00.00.0000 00:00:00" ){ echo "není dostupné"; }
                   else { echo $data["datum_vlozeni2"]; }
                echo "</span>
                      </td>";

                echo "</tr>";

                } // konec if ! vyrizeno == true

              } // konec while

            } // konec if dotaz_radku vetsi > 0
            else
            {
              echo "<tr><td colspan=\"\" ><span style=\"font-size: 16px;\">Žádný zákazník v databázi neuložen.</span></td></tr>";
            }

            //konec vnitrni kabulky
            echo "</table></div>";

    
    } //end of function
                                                             
} //end of class

//coded by Warden - http://warden.dharma.cz

/*
priklad vytvareni instance:

$listing = new c_Listing("aktivni link pro strankovani", "pocet zaznamu v jednom listu",
    "list pro zobrazeni", "formatovani zacatku odkazu strankovani",
    "formatovani konce odkazu strankovani", "sql dotaz pro vyber vsech zazkamu k vylistovani");
*/

//definice tridy c_Listing

class c_listing_partner_servis {

    var $url;
    var $interval;
    var $sql;
    var $list;
    var $before;
    var $after;
    var $numLists;
    var $numRecords;
    var $errName;
    var $befError = "<div align=\"center\" style=\"color: maroon;\">";
    var $aftError = "</div>";

    //konstruktor...naplni promenne
    function c_listing_partner_servis($conUrl = "./partner-servis.php?", $conInterval = 10, $conList = 1, $conBefore = "", $conAfter = "", $conSql = ""){
        $this->errName[1] = "Při volání konstruktotu nebyl zadán SQL dotaz!<br>\n";
        $this->errName[2] = "Nelze zobrazit listování, chyba databáze(Query)!<br>\n";
        // $this->errName[3] = "Nelze zobrazit listov▒n▒, chyba datab▒ze(Num_Rows)!<br>\n";
        $this->url = $conUrl;
        $this->interval = $conInterval;
        $this->list = $conList;
        $this->before = $conBefore;
        $this->after = $conAfter;

        if (empty($conSql)){
            $this->error(1);
        }
        else {
            $this->sql = $conSql;
        }
    }
    
    //vyber dat z databaze
    function dbSelect(){
        $listRecord = @mysql_query($this->sql);
        if (!$listRecord){
            $this->error(2);
        }
        $allRecords = @mysql_num_rows($listRecord);
        if (!$allRecords){
            $this->error(3);
        }
        $allLists = ceil($allRecords / $this->interval);

        $this->numLists = $allLists;
        $this->numRecords = $allRecords;

    }

    //zobrazi pouze seznam cisel listu
    //napr.:    1 | 2 | 3
    function listNumber(){
        $this->dbSelect();
        echo $this->before;
        for ($i = 1; $i <= $this->numLists; $i++){
            $isLink = 1;
            $spacer = " | ";

            if (empty($this->list)){
                $this->list = 1;
            }
            if ($i == $this->list){
                $isLink = 0;
            }
            if ($i == $this->numLists){
                $spacer = "";
            }
            if ($isLink == 0){
                echo $i." ".$spacer;
            }
            if ($isLink == 1){
                echo "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$i."</a> ".$spacer;
            }
        }
        echo $this->after;
    }

    //zobrazi seznam intervalu v zadanem rozsahu ($interval)
    //napr.:    1-10 | 11-20 | 21-30
    function listInterval(){
        $this->dbSelect();
        echo $this->before;
        for ($i = 1; $i <= $this->numLists; $i++){
            $isLink = 1;
            $spacer = " | ";
            $from = ($i*$this->interval)-($this->interval-1);
            $to = $i*$this->interval;

            if (Empty($this->list)){
                $this->list = 1;
            }
            if ($i == $this->list){
                $isLink = 0;
            }
            if ($i == $this->numLists){
                $to = $this->numRecords;
                $spacer = "";
            }
            if ($isLink == 0){
                echo $from."-".$to." ".$spacer;
            }
            if ($isLink == 1){
                echo "<a href=\"".$this->url."&list=".$i."\" onFocus=\"blur()\">".$from."-".$to."</a> ".$spacer;
            }
        }
        echo $this->after;
    }

    //zobrazi aktivni odkaz pouze na dalsi cast intervalu (dopredu, dozadu)
    //napr.:    <<< << 11-20 >> >>>
    function listPart(){
        $this->dbSelect();
        echo $this->before;
        if (Empty($this->list)){
                $this->list = 1;
        }
        $from = ($this->list*$this->interval)-($this->interval-1);
        $to = $this->list*$this->interval;
        $forward = "<a href=\"".$this->url."&list=1\" onFocus=\"blur()\">&lt;&lt;&lt;</a>&nbsp;<a href=\"".$this->url."&list=".($this->list-1)."\" onFocus=\"blur()\">&lt;&lt;</a>&nbsp;";
        $backward = "&nbsp;<a href=\"".$this->url."&list=".($this->list+1)."\" onFocus=\"blur()\">&gt;&gt;</a>&nbsp;<a href=\"".$this->url."&list=".$this->numLists."\" onFocus=\"blur()\">&gt;&gt;&gt;</a>";

        if ($this->list == 1){
            $forward = "";
        }
        if ($this->list == $this->numLists){
            $to = $this->numRecords;
            $backward = "";
        }
        echo $forward.$from."-".$to.$backward;
        echo $this->after;
    }

    //vypisovani chybovych hlasek
    function error($errNum = 0){
        if ($errNum != 0){
            echo $this->befError.$this->errName[$errNum].$this->aftError;
        }
    
    } //konec funkce

} //end of class


?>
