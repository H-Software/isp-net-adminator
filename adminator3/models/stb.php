<?php

class stb
{

    var $conn_mysql;

    var $find_id_nodu;		//promenne pro hledani
    var $find_search_string;
    var $find_var_vlastnik;
    
    var $id_stb; 			//pro vypis konkretniho stb, z archivu zmen atd
    
    var $order;			//razeni
       
    var $vypis_pocet_sloupcu;	//pocet sloupcu v tabulce
    
    var $debug = 0; 		//vypis sekudarnich informaci (sql dotazy atd)
    
    var $enable_modify_action = false;

    var $enable_unpair_action = false;

    var $sql_query;
    
    //var $sql_query_listing;
     
    var $listing_mod; 		// v jakym modu bude vypis /vlastnici -- dle id_cloveka, objekty -- beznej vypis
       
    var $id_cloveka; 		//pokud se vypisou STB dle ic_cloveka //u vlastniku//, tak zde prislusny clovek

    var $find_par_vlastnik;

	function __construct($conn_mysql)
    {
		$this->conn_mysql = $conn_mysql;
	}

    public function stbListGetBodyContent()
    {
        $output = "";

        $odeslano = $_GET["odeslano"];
        $par_vlastnik = intval($_GET["par_vlastnik"]);
        $id_nodu = intval($_GET["id_nodu"]);
        $search = $_GET["search"];
        $order = intval($_GET["order"]);
        
        if( (strlen($_GET["list"]) > 0) )
        { $list = intval($_GET["list"]); }
        
        if( (strlen($_GET["id_stb"]) > 0) )
        { $id_stb = intval($_GET["id_stb"]); }
        
        $get_odkazy = "".urlencode("&par_vlastnik")."=".urlencode($par_vlastnik).
                "".urlencode("&id_nodu")."=".urlencode($id_nodu).
                "".urlencode("&search")."=".urlencode($search).
                "".urlencode("&list")."=".urlencode($list).
                "".urlencode("&odeslano")."=".urlencode($odeslano).
                "".urlencode("&id_stb")."=".urlencode($id_stb);
                
        if( $order > 0 ){
            $this->order = $order;
        }
        
        if($id_nodu > 0){
            $this->find_id_nodu = $id_nodu;
        }
        
        if( $par_vlastnik > 0 ){
            $this->find_par_vlastnik = $par_vlastnik;
        }
        
        if( (strlen($search) > 0) ){
            $this->find_search_string = $search;
        }
        
        if( $id_stb > 0 ){
            $this->id_stb = $id_stb;
        }
        
         $this->vypis_pocet_sloupcu = 8;
         
         $output .= "";
        
        $rs_select_nod = $this->filter_select_nods();
        
        if( isset($rs_select_nod["error"]) ){
            
            $output .= "<div style=\"padding: 10px; color: red; font-size: 14px;\">".
                "Chyba! Funkce \"filter_select_nod\" hlásí chybu: ";
            
            foreach ($rs_select_nod["error"] as $key => $val) {
                $output .= "#".$key.":<br> ".$val;
            }
            
            $output .= "</div>\n";
        }
        
        if( !is_array($rs_select_nod["data"]) ){
        
            $output .= "<div style=\"padding: 10px; color: red; font-size: 14px;\">".
                "Chyba! Funkce \"filter_select_nod\" nevrací žádné relevatní data</div>\n";
        }
        
        $output .= "<form method=\"GET\" action=\"" . $_SERVER['SCRIPT_URL']. "\" >";
        
        //filtr - hlavni okno
        if( $_GET["odeslano"] == "OK" ){
            $display = "visible";
        }
        else{
            $display = "none";
        }
        
         $this->generate_sql_query();
        
         $paging_url = "?".urlencode("order")."=".$this->order.$get_odkazy;
            
         $paging = new paging_global($this->conn_mysql, $paging_url, 20, $list, "<div class=\"text-listing2\" style=\"width: 1000px; text-align: center; padding-top: 10px; padding-bottom: 10px;\">", "</div>\n", $this->sql_query);
                         
         $bude_chybet = ( (($list == "")||($list == "1")) ? 0 : ((($list-1) * $paging->interval)) );
         
         $interval = $paging->interval;
          
         $this->sql_query = $this->sql_query . " LIMIT ".$interval." OFFSET ".$bude_chybet." "; 
        
        $output .= "<div id=\"objekty_stb_filter\" style=\"width: 1000px; margin: 10px; display: ".$display."; padding: 10px; border: 1px solid gray; \" >";
        
        //vlastnik - bez
        $output .= "<div style=\"width: 150px; float: left;\" >".
                "přiřazeno k vlastníkovi: </div>";
        
        $output .= "<div style=\"float: left; \">".    
                "<select size=\"1\" name=\"par_vlastnik\" style=\"width: 70px;\" >".
                "<option value=\"0\" style=\"color: gray;\" >obojí</option>".
                "<option value=\"1\" "; if($par_vlastnik == 1) $output .= " selected "; $output .= ">Ano (spárované)</option>".
                "<option value=\"2\" "; if($par_vlastnik == 2) $output .= " selected "; $output .= ">Ne (nespárované)</option>".
                "</select>".    
               "</div>";
        
        //pripojnej bod
        $output .= "<div style=\"width: 100px; float: left; padding-left: 10px; \" >".
                "Přípojný bod: </div>\n";
        
        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".    
                "<select size=\"1\" name=\"id_nodu\" >\n".
                "<option value=\"0\" style=\"color: gray;\" >nevybráno (všechny)</option>\n";
                
            foreach ($rs_select_nod["data"] as $nod_id => $nod_name) {
                    $output .= "<option value=\"".$nod_id."\" ";
                    
                    if($nod_id == $id_nodu) $output .= " selected ";
                    
                    $output .= " >".$nod_name."</option>\n";
            }
                    
            $output .= "</select>".
              "</div>\n";
        
        //tarif 
        $output .= "<div style=\"width: 50px; float: left; padding-left: 10px; \" >".
                "Tarif: </div>\n";
        
        $output .= "<div style=\"float: left; padding-left: 10px; \">\n".    
                "<select size=\"1\" name=\"id_tarifu\" >\n".
                "<option value=\"0\" style=\"color: gray;\" >nevybráno (všechny)</option>\n".
                "</select>\n".
              "</div>\n";
        
        //tlacitko
        $output .= "<div style=\"float: left; padding-left: 100%; width: 250px; text-align: right; padding-left: 10px; \" >".
                "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";
        
        //oddelovac
        $output .= "<div style=\"clear: both; height: 5px; \"></div>\n";
        
        //druha radka
        $output .= "<div style=\"float: left; \" >Hledání: </div>\n";
        
        $output .= "<div style=\"float: left; padding-left: 20px; \" >".
            "<input type=\"text\" name=\"search\" value=\"".htmlspecialchars($search)."\" ></div>\n";
        
        $output .= "<div style=\"float: left; padding-left: 20px; \" >Id Stb: </div>\n";
        
        $output .= "<div style=\"float: left; padding-left: 20px; \" >".
            "<input type=\"text\" name=\"id_stb\" size=\"3\" value=\"".htmlspecialchars($id_stb)."\" ></div>\n";
        
        //tlacitko
        $output .= "<div style=\"float: left; padding-left: 10px; \" >".
                "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";
        
        //oddelovac
        $output .= "<div style=\"clear: both; \"></div>\n";
        
        $output .= "</div>\n";
        
        $output .= "</form>\n";
        
        //listovani
        // TODO: fix paging for STB
        // $output .= $paging->listInterval();
         
        //zacatek tabulky ... popis
        
        $output .= "<table border=\"0\" width=\"1000px\" style=\"padding-left: 0px; \" >";
        
        $output .= "
            <tr>\n";
            
             //popis
             $output .= "<td width=\"200px\" style=\"border-bottom: 1px dashed gray; \" >\n";
             $output .= "\t<div style=\"font-weight: bold; float: left; \">popis</div>\n";
             
             $output .= "\t<div style=\"float: left; padding-left: 55%; \">".
                    "<a href=\"?".urlencode("order")."=1".$get_odkazy."\">";
                
                if($order == 1){
                    $output .= "<img src=\"//img2/sorting_a-z_hot.jpg\" width=\"20px\" alt=\"sorting_a-z-hot\" >";
                    }
                    else{
                    $output .= "<img src=\"/img2/sorting_a-z_normal.jpg\" width=\"20px\" alt=\"sorting_a-z-normal\" >";        
                    }
               $output .= "</a>".
                    "</div>\n";
             
             $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                    "<a href=\"?".urlencode("order")."=2".$get_odkazy."\">";
                    
                    if($order == 2){
                    $output .= "<img src=\"/img2/sorting_z-a_hot.jpg\" width=\"20px\" alt=\"sorting_z-a_hot\" >";
                    }
                    else{
                    $output .= "<img src=\"/img2/sorting_z-a_normal.jpg\" width=\"20px\" alt=\"sorting_z-a_normal\" >";        
                    }
                
                $output .= "</a>".
                    "</div>\n";
             
             $output .= "</td>\n";
             
             //ip adresa
             $output .= "<td style=\"border-bottom: 1px dashed gray;\" >\n";
             $output .= "\t<div style=\"font-weight: bold; float: left; \">IP adresa</div>\n";
             
             $output .= "\t<div style=\"float: left; padding-left: 20%; \">".
                    "<a href=\"?".urlencode("order=")."3".$get_odkazy."\">";
                     
                    if($order == 3){
                     $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >"; 	    
                     }
                     else{	    
                     $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >"; 	    
                     }
                     
                     $output .= "</a>".
                    "</div>\n";
             
             $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                     "<a href=\"?".urlencode("order")."=4".$get_odkazy."\">";
                    
                    if($order == 4){	    
                     $output .= "<img src=\"/img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
                     }
                     else{
                     $output .= "<img src=\"/img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
                     }
                     
               $output .= "</a>".
                    "</div>\n";
             
             $output .= "</td>\n";
             
             //poznamka
             $output .= "<td style=\"border-bottom: 1px dashed gray;\" ><b>poznámka</b></td>\n";
             
             //mac adresa
             $output .= "<td style=\"border-bottom: 1px dashed gray; width: 168px; \" >";
                $output .= "\t<div style=\"font-weight: bold; float: left; \">MAC adresa</div>\n";
        
                $output .= "\t<div style=\"float: left; padding-left: 20%; \">".
                    "<a href=\"?".urlencode("order")."=5".$get_odkazy."\">";
                
                if($order == 5){
                    $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
                }
                else{
                     $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";        
                }
                
                $output .= "</a>".
                    "</div>\n";
                      
                 $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                     "<a href=\"?".urlencode("order")."=6".$get_odkazy."\">";
                    
                if($order == 6){ 
                     $output .= "<img src=\"/img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
                 }
                 else{
                     $output .= "<img src=\"/img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
                 }
                     
                $output .= "</a>".
                    "</div>\n";
        
             $output .= "</td>\n";
        
             //uprava
             $output .= "<td style=\"border-bottom: 1px dashed gray;\" ><b>úprava</b></td>
            
             <td style=\"border-bottom: 1px dashed gray;\" ><b>smazat</b></td>
        
             <td style=\"border-bottom: 1px dashed gray;\" ><b>test</b></td>
        
             <td style=\"border-bottom: 1px dashed gray;\" ><b>tarif</b></td>
            
            </tr>\n\n";
            
            //2. radka
            $output .= "<tr>
             <td style=\"border-bottom: 1px solid black;\" >\n";
             $output .= "\t<div style=\"font-weight: bold; float: left; \">přípojný nod</div>\n";
            
             $output .= "\t<div style=\"float: left; padding-left: 32%; \">".
                    "<a href=\"?".urlencode("order")."=9".$get_odkazy."\">";
                     
                    if($order == 9){
                     $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >"; 	    
                     }
                     else{	    
                     $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >"; 	    
                     }
                     
                     $output .= "</a>".
                    "</div>\n";
        
             $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                    "<a href=\"?".urlencode("order=")."10".$get_odkazy."\">";
                    
                    if($order == 10){
                    $output .= "<img src=\"/img2/sorting_z-a_hot.jpg\" width=\"20px\" alt=\"sorting_z-a_hot\" >";
                    }
                    else{
                    $output .= "<img src=\"/img2/sorting_z-a_normal.jpg\" width=\"20px\" alt=\"sorting_z-a_normal\" >";        
                    }
                
                $output .= "</a>".
                    "</div>\n";
             
             $output .= "</td>\n";
            
             //PUK
             $output .= "<td style=\"border-bottom: 1px solid black;\" >".
                   "\t<div style=\"font-weight: bold; float: left; \">PUK</div>\n";
        
             $output .= "\t<div style=\"float: left; padding-left: 43%; \">".
                    "<a href=\"?".urlencode("order")."=7".$get_odkazy."\" >";
                
             if($order == 7){ 
                $output .= "<img src=\"/img2/sorting_1-9_hot.jpg\" width=\"20px\" alt=\"sorting_1-9_hot\" >";
             }
             else{
                $output .= "<img src=\"/img2/sorting_1-9_normal.jpg\" width=\"20px\" alt=\"sorting_1-9_normal\" >";
             }
        
             $output .= "</a>".
                "</div>\n";
                     
             $output .= "\t<div style=\"float: left; padding-left: 5px; padding-right: 2px; \">".
                     "<a href=\"?".urlencode("order")."=8".$get_odkazy."\" >";
                    
             if($order == 8){ 
                 $output .= "<img src=\"/img2/sorting_9-1_hot.jpg\" width=\"20px\" alt=\"sorting_9-1_hot\" >";
             }
             else{
                $output .= "<img src=\"/img2/sorting_9-1_normal.jpg\" width=\"20px\" alt=\"sorting_9-1_normal\" >";
             }
             
             $output .= "</a>".
                    "</div>\n";
        
             $output .= "</td>\n";
                  
             $output .= "<td style=\"border-bottom: 1px solid black;\" ><b>id stb (historie)</b></td>
            
             <td style=\"border-bottom: 1px solid black;\" ><b>id človeka</b></td>
        
             <td style=\"border-bottom: 1px solid black;\" ><b>switch port</b></td>
        
             <td colspan=\"2\" style=\"border-bottom: 1px solid black;\" ><b>datum vytvoření</b></td>
        
             <td style=\"border-bottom: 1px solid black;\" ><b>reg. form</b></td>
                  
            </tr>\n";
        
         $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>\n";
         
         $output .= $this->vypis();
          
         $output .= "</table>\n";
        
         // TODO: fix paging for STB
         // $output .= $paging->listInterval();
        
         $ret = array($output);

         return $ret;
    }

    function generujdata()
    {
      
        // promenne ktere potrebujem, a ktere budeme ovlivnovat
        global $ip;
       
        //skusime ip vygenerovat   
        $vysl_nod = $this->conn_mysql->query("SELECT * FROM nod_list WHERE id = '370' ");
        $radku_nod = $vysl_nod->num_rows;
   
        if( $radku_nod <> 1 ) 
        {
          $gen_ip = "E1"; //echo "chybnej vyber nodu";
        }
        else	
        {
               
       while ($data_nod = $vysl_nod->fetch_array() )
       { $ip_rozsah=$data_nod["ip_rozsah"]; }  
        
       list($a,$b,$c,$d) =split("[.]",$ip_rozsah);
                 
       // c-ckova ip	
       $gen_ip_find = $a.".".$b.".".$c.".".$d."/24";
           
       $msq_check_ip = $this->conn_mysql->query("SELECT * FROM objekty_stb ORDER BY ip_adresa ASC");
       $msq_check_ip_radku = $msq_check_ip->num_rows;
       
       if( $msq_check_ip_radku == 0 ) //nic v db, takze prvni adresa ...
       { 
         $d=16; 
         $gen_ip = $a.".".$b.".".$c.".".$d; 
       }
       else
       {
         while( $data_check_ip = $msq_check_ip->fetch_array() )
         { $gen_ip = $data_check_ip["ip_adresa"]; }
                
         list($a,$b,$c,$d) = split("[.]",$gen_ip);
                
         if( $d >= "250") //jsme u stropu, vracime rozsah ...
         { $gen_ip = $a.".".$b.".".$c.".0"; }
         else
         {
           $d = $d + 2;
           $gen_ip = $a.".".$b.".".$c.".".$d;
         }
        } // konec else radku == 0
           
           
       // vysledek predame
       if( ( strlen($ip) <= 0) ){ $ip = $gen_ip; }
            
         }
      
    } //konec funkce generujdata
   
    function checkip($ip)
    {
       $ip_check=ereg('^([[:digit:]]{1,3})\.([[:digit:]]{1,3})\.([[:digit:]]{1,3})\.([[:digit:]]{1,3})$',$ip);
       
       if( !($ip_check) )
       {
         global $fail;  
         $fail="true";
         
         global $error; 
         $error .= "<div class=\"objekty-add-fail-ip\"><H4>IP adresa ( ".$ip." ) není ve správném formátu !!!</H4></div>";
       }
       
    } //konec funkce check-ip			 
   
    function checkmac($mac) 
    {
       $mac_check=ereg('^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$',$mac);
       
       if( !($mac_check) )
       {
         global $fail;	
         $fail="true";
         
         global $error;  
         $error .= "<div class=\"objekty-add-fail-mac\"><H4>MAC adresa ( ".$mac." ) není ve správném formátu !!! ( Správný formát je: 00:00:64:65:73:74 ) </H4></div>";
       }
       
     } //konec funkce check-mac
   
     function checkcislo($cislo)
     {
        $rra_check=ereg('^([[:digit:]]+)$',$cislo);
        
        if( !($rra_check) )
        {
         global $fail;	$fail="true";
         
         global $error;	
         $error .= "<div class=\"objekty-add-fail-cislo\"><H4>Zadaný číselný údaj ( ".$cislo." ) není ve  správném formátu !!! </H4></div>";
        }		    
       
     } // konec funkce check cislo
   
    function zjistipocetobj($id_cloveka)
    {
       $sql_sloupce = " id_stb, id_cloveka, mac_adresa, puk, ip_adresa, popis, id_nodu, sw_port, pozn, datum_vytvoreni ";
      
        $dotaz = $this->conn_mysql->mysql_query("SELECT ".$sql_sloupce." FROM objekty_stb WHERE id_cloveka = '".intval($id_cloveka)."' ORDER BY id_stb");
        $dotaz_radku = $dotaz->num_rows;
   
       return $dotaz_radku;
    }
    
    function generate_sql_query(){
   
       /*
       novej sql doraz
       
       SELECT id_stb, id_cloveka, mac_adresa, puk, ip_adresa, popis, id_nodu, sw_port, objekty_stb.pozn, 
           datum_vytvoreni, DATE_FORMAT(datum_vytvoreni, '%d.%m.%Y %H:%i:%s') as datum_vytvoreni_f, nod_list.jmeno 
       FROM objekty_stb, nod_list 
       WHERE ( (objekty_stb.id_nodu = nod_list.id) ) 
       GROUP BY objekty_stb.id_stb 
       ORDER BY id_stb
       */
       
       $sql_rows = " id_stb, id_cloveka, mac_adresa, puk, ip_adresa, popis, id_nodu, sw_port, objekty_stb.pozn, datum_vytvoreni, ".
               " DATE_FORMAT(datum_vytvoreni, '%d.%m.%Y %H:%i:%s') as datum_vytvoreni_f, nod_list.jmeno AS nod_jmeno ".
               ", jmeno_tarifu ";
     
       
       if($this->listing_mod == 1){
   
        $this->sql_query = "SELECT ".$sql_rows." FROM objekty_stb, nod_list, tarify_iptv ".
                        " WHERE ( (objekty_stb.id_nodu = nod_list.id) ".
                           " AND (objekty_stb.id_tarifu = tarify_iptv.id_tarifu) ".
                           " AND (id_cloveka = '".intval($this->id_cloveka)."') ) ".
                        " GROUP BY objekty_stb.id_stb ".
                        " ORDER BY id_stb";    
       }
       else{
   
           $sql_where = "";
   
           if( $this->find_id_nodu > 0 )
           {
               $sql_where .= " AND (id_nodu = '".intval($this->find_id_nodu)."') ";
           } 
           
           if(isset($this->find_par_vlastnik)){
               
               if($this->find_par_vlastnik == 1)
                   $sql_where .= " AND (id_cloveka > 0) ";
               elseif($this->find_par_vlastnik == 2)
               $sql_where .= " AND (id_cloveka is NULL) ";
               else{
               //chyba :)
               }
           
           }
           
           if( (strlen($this->find_search_string) > 0) ){
           
           $find_search_string = "%".$this->conn_mysql->real_escape_string($this->find_search_string)."%";
           
               $sql_where .= " AND ( (id_stb = '$find_search_string') OR ".
                       " (id_cloveka = '$find_search_string') OR ".
                       " (mac_adresa LIKE '$find_search_string' ) OR ".
                       " (ip_adresa LIKE '$find_search_string') OR ".
                       " (puk LIKE '$find_search_string') OR ".
                       " (popis LIKE '$find_search_string') OR ".
                       " (objekty_stb.pozn LIKE '$find_search_string') OR ".
                       " (nod_list.jmeno LIKE '$find_search_string') ".
               " ) ";
           
           }
       
       if( isset($this->id_stb) ){
       
           $sql_where .= " AND (id_stb = '".intval($this->id_stb)."') ";
       }
       
       if($this->order == 1){
               $sql_order = " ORDER BY popis ASC ";
           }
           elseif($this->order == 2){
               $sql_order = " ORDER BY popis DESC ";
           }
           elseif($this->order == 3){
               $sql_order = " ORDER BY ip_adresa ASC ";
           }
           elseif($this->order == 4){
               $sql_order = " ORDER BY ip_adresa DESC ";
           }
           elseif($this->order == 5){
               $sql_order = " ORDER BY mac_adresa ASC ";
           }
           elseif($this->order == 6){
               $sql_order = " ORDER BY mac_adresa DESC ";
           }
           elseif($this->order == 7){
               $sql_order = " ORDER BY puk ASC ";
           }
           elseif($this->order == 8){
               $sql_order = " ORDER BY puk DESC ";
           }
           elseif($this->order == 9){
               $sql_order = " ORDER BY nod_list.jmeno ASC ";
           }
           elseif($this->order == 10){
               $sql_order = " ORDER BY nod_list.jmeno DESC ";
           }
           
           $this->sql_query = "SELECT ".$sql_rows." FROM objekty_stb, nod_list, tarify_iptv ".
                           " WHERE ( (objekty_stb.id_nodu = nod_list.id) AND (objekty_stb.id_tarifu = tarify_iptv.id_tarifu) ".
                           $sql_where." ) "." GROUP BY objekty_stb.id_stb ".$sql_order; 
           
       
       } //end of else if mod == 1
        
    } //end of function generate_sql_query
    
    function vypis($mod = 0, $id_cloveka = 0)
    {
       
        $output = "";

       $this->listing_mod = $mod;
       $this->id_cloveka  = $id_cloveka;
       
       if(empty($this->sql_query)){
           $this->generate_sql_query();    
       }
       
        $dotaz_vypis = $this->conn_mysql->query($this->sql_query);
        $dotaz_vypis_radku = $dotaz_vypis->num_rows;
   
       if($this->debug == 1){
   
       $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\" >
               <div style=\"color: red; font-weight: bold; \" >debug sql: ".$this->sql_query.
               
               "<br>var search: ".$this->find_search_string.
               "</div>
               </td></tr>\n";
   
       $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>\n";
                   
       }
       
       if(!$dotaz_vypis){
   
       $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\" >
               <div style=\"color: red; font-weight: bold; \" >error in function \"vypis\": mysql: ".
               mysql_errno().": ".mysql_error()."</div>
               </td></tr>";
   
       $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>";
                   
       }
           
       if( ($dotaz_vypis_radku == 0) and ( $mod != 1 ) )
       {
   
       $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\" >
               <div style=\"color: red; font-weight: bold; \" >Žádný set-top-box nenalezen.</div>
               </td></tr>";
   
       $output .= "<tr><td colspan=\"".$this->vypis_pocet_sloupcu."\"><br></td></tr>";
       }
       else
       {
           $class_stb_liche = "border-bottom: 1px dashed gray; font-size: 15px; ";
       $class_stb_sude = "border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px; ";
         
       while($data_vypis = $dotaz_vypis->fetch_array())
       {
         $output .= "
           <tr>
           <td style=\"".$class_stb_liche."\" >".$data_vypis["popis"]."&nbsp;</td>
           <td style=\"".$class_stb_liche."\" >".$data_vypis["ip_adresa"]."&nbsp;</td>\n";
               
               //pozn
               $output .= "<td style=\"".$class_stb_liche."\" ><span class=\"pozn\"><img title=\"poznamka\" src=\"/img2/poznamka3.png\" alt=\"poznamka\" ";
               $output .= " onclick=\"window.alert(' poznámka: ".htmlspecialchars($data_vypis["pozn"])." , Vytvořeno: ".$pridano." ');\" ></span>\n</td>\n";
   
           //mac adresa
           $output .= "<td style=\"".$class_stb_liche."\" >\n";
           
               $output .= "<div style=\"float: left; width: 135px; padding-top: 2px;\" >".htmlspecialchars($data_vypis["mac_adresa"])."</div>";
                       
               $p_link1 = "http://app01.cho01.iptv.local:9080/admin/admin/provisioning/stb-search.html?".
                   "searchText=".urlencode($data_vypis["mac_adresa"])."&amp;type=".urlencode("MAC_ADDRESS")."&amp;submit=OK";
               
               $output .= "<div style=\"float: left;\" >".
                   "<a href=\"".$p_link1."\" target=\"_new\" >".
                      "<img src=\"/img2/Letter-P-icon-small.png\" alt=\"letter-p-small\" width=\"20px\" >".
                   "</a>".
                 "</div>";
               
               $output .= "<div style=\"clear: both;\" ></div>";
               
           //$output .= "</div>";
               
           $output .= "</td>\n";
           
           //uprava
               $output .= "<td style=\"".$class_stb_liche."\" >";
       
               // if( !( check_level($this->level,137) ) )
               if($this->enable_modify_action === true)               
               {
               $output .= "<form method=\"POST\" action=\"" . $_SERVER['SCRIPT_URL'] . "\" >
               <input type=\"hidden\" name=\"update_id\" value=\"".intval($data_vypis["id_stb"])."\" >
               <input class=\"\" type=\"submit\" value=\"update\" >
               </form>\n";
               }
               else
               { $output .= "<div style=\"\" style=\"".$class_stb_liche."\" >úprava</div>\n"; }

               $output .= "</td>\n";
   
           //smazani
           $output .= "<td style=\"".$class_stb_liche."\" >\n";
   
               $output .= "<div style=\"\" ><a href=\"" . fix_link_to_another_adminator("/objekty-stb-erase.php?".
                   urlencode("id_stb")."=".intval($data_vypis["id_stb"]))."\" >smazání</a>".
                 "</div>";
               
               $output .= "</td>\n";
           
           //test
               $output .= "<td style=\"".$class_stb_liche."\" >
            <a href=\"" . fix_link_to_another_adminator("/objekty-test.php?".urlencode("id_stb")."=".intval($data_vypis["id_stb"]))."\" >test</a>
           </td>\n";
               
               //tarif
               $output .= "<td style=\"".$class_stb_liche."\" >".htmlspecialchars($data_vypis["jmeno_tarifu"])."</td>\n";
               
               //druhej radek
               $output .= "</tr>\n".
               "<tr>\n";
                                                              
               //pripojny bod / nod
               $output .= "<td style=\"".$class_stb_sude."\" >\n";
                   
               $output .= "<span class=\"objekty-2radka objekty-odkaz\">".
                                 "<a href=\"". fix_link_to_another_adminator("/topology-nod-list.php?".urlencode("typ_nodu")."=2".urlencode("&find")."=".urlencode($data_vypis["nod_jmeno"])) . "\" >".
                                 $data_vypis["nod_jmeno"]."</a>".
                    "</span>";
               $output .= "</td>\n";
               
               //puk
               $output .= "<td style=\"".$class_stb_sude."\" >".$data_vypis["puk"]."&nbsp;</td>\n";
               
               //id stb (historie)
               $output .= "<td style=\"".$class_stb_sude."\" >H: \n";
               $output .= "<a href=\"" . fix_link_to_another_adminator("/archiv-zmen.php?".urlencode("id_stb")."=".intval($data_vypis["id_stb"])) ."\" >".$data_vypis["id_stb"]."</a>\n";
               $output .= "</td>\n";
               
           //vlastnik - id cloveka
           $id_cloveka = $data_vypis["id_cloveka"];
           
           $rs_create_link = ($id_cloveka > 0 ? Aglobal::create_link_to_owner($id_cloveka) : "");
           
           $odkaz_data = ($rs_create_link === false ? "E_1" : $rs_create_link);
           
               $output .= "<td style=\"".$class_stb_sude."\" >V: ".$rs_create_link."&nbsp;</td>";
               
               $output .= "<td style=\"".$class_stb_sude."\" >".$data_vypis["sw_port"]."&nbsp;</td>";
   
               $output .= "<td colspan=\"2\" style=\"".$class_stb_sude."\" >";
               
               $output .= ($data_vypis["datum_vytvoreni_f"] == 0 ? "nelze zjistit " : $data_vypis["datum_vytvoreni_f"]);
               
               $output .= "</td>";
   
           //generovani Reg. Formu
           if( (intval($data_vypis["id_cloveka"]) > 0) ){
       
           $rs_rf = pg_query("SELECT id_komplu FROM objekty WHERE id_cloveka = '".intval($data_vypis["id_cloveka"])."'");
           
           while($data_rf = pg_fetch_array($rs_rf)){
               $id_komplu = $data_rf["id_komplu"];
           }
           
           if( (intval($id_komplu) > 0) ){
   
                 $output .= "<td style=\"".$class_stb_sude."\" >".
               "<a href=\"/print/reg-form-pdf.php?".urlencode("id_vlastnika")."=".intval($id_komplu)."\">R.F.</a>".
               "</td>";
           
           } 
           else{
               $output .= "<td style=\"".$class_stb_sude."\">E</td>";
           }
           
           }
           else{
               $output .= "<td style=\"".$class_stb_sude."\" >".
               "<a href=\"/print/reg-form-pdf.php?".urlencode("id_stb")."=".intval($data_vypis["id_stb"])."\">R.F.</a>".
               "</td>";
           }
           
           //zbytek	
           if($mod == 1){
                   
               // if( check_level($this->level, 152) )
               if($this->enable_unpair_action === true)
               {
                   $output .= "<td style=\"".$class_stb_sude."\" ><a href=\"objekty-stb-unpairing.php?id=".intval($data_vypis["id_stb"])."\" >odendat</a></td>";
               }
               else{
                   $output .= "<td style=\"".$class_stb_sude."\" ><div style=\"color: gray; \" >odendat</div></td>";
               }
           }
           else
           {
           //$output .= "<td style=\"".$class_stb_sude."\" >&nbsp;</td>";
           }
           
           $output .= "</tr>\n";
   
       } //konec while
   
        } //konec else if $dotaz_vypis_radku == 0
    
        return $output;

      } //konec funkce vypis
   
      //
      //funkce pro filtraci vypisu
      //
      
      function filter_select_nods(){
          
          $ret = array();
          
          //sql 
          $sql = "SELECT nod_list.id, nod_list.jmeno FROM nod_list, objekty_stb ".
               " WHERE ( (nod_list.id = objekty_stb.id_nodu) AND (nod_list.typ_nodu = 2) ) ".
               " group by nod_list.id";
           try {
               $rs = $this->conn_mysql->query($sql);
           } catch (Exception $e) {
               die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
           }
          
          if(!$rs){    
               
               $text = htmlspecialchars(mysql_errno() . ": " . mysql_error());
               $ret["error"] = array("2" => $text);
       
               return $ret;
          }
          
          $rs_num = $rs->num_rows;
           
          if( $rs_num == 0){
       
               $text = htmlspecialchars("Žádné nody nenalezeny");
               $ret["error"] = array("1" => $text);
               
               return $ret;
          }
          
          while( $data = $rs->fetch_array()){
               
               $id = intval($data["id"]);
               $val = htmlspecialchars($data["jmeno"]);
               
               $ret["data"][$id] = $val;
          }
          
          return $ret;
          
      } //end of function filter_select_nods
           
      function filter_select_tarifs(){
      
       //dodelat :) 
       //TODO: add logic for filter tarifs
   
      } //end of function filter_select_tarifs    
}