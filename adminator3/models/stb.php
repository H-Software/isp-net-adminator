<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

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

    var $action_form;

    var $action_form_validation_errors = "";

    var $action_form_validation_errors_wrapper_start = '<div class="alert alert-danger" role="alert">';
    var $action_form_validation_errors_wrapper_end = '</div>';

	function __construct($conn_mysql, $logger)
    {
		$this->conn_mysql = $conn_mysql;
        $this->logger = $logger;
	}

    function formInit()
    {
        // bootstrap -> bootstrap.js
        // hush -> no echoing stuff -> https://github.com/formr/formr/issues/87#issuecomment-769374921
        $this->action_form = new Formr\Formr('bootstrap5', 'hush');
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

    function stbActionValidateFormData($data)
    {
        $popisValidatorMaxLenght = 15;
        $popisValidator = v::noWhitespace()->notEmpty()->alnum("-")->length(1,$popisValidatorMaxLenght);

        if($popisValidator->validate($data['popis']) === false)
        {
            $this->action_form_validation_errors .= 
                                    $this->action_form_validation_errors_wrapper_start
                                    . "\"Popis\" musi obsahovat pouze cisla ci pismena a musi byt maximalne " . $popisValidatorMaxLenght . " znaku dlouhy."
                                    . $this->action_form_validation_errors_wrapper_end
                                    ;
        }

         //kontrola vlozenych udaju ( kontrolujou se i vygenerovana data ... )
        $this->checkip($data['ip']); 
        $this->checkmac($data['mac']); 
        $this->checkcislo($data['puk']);
        $this->checkcislo($data['pin1']);
        $this->checkcislo($data['pin2']);
        $this->checkcislo($data['id_nodu']);
        $this->checkcislo($data['port_id']);

        //zjisti jestli neni duplicitni dns, ip adresa, mac ...
        $MSQ_POPIS = $this->conn_mysql->query("SELECT * FROM objekty_stb WHERE popis LIKE '" . $data['popis'] . "' ");
        $MSQ_IP = $this->conn_mysql->query("SELECT * FROM objekty_stb WHERE ip_adresa LIKE '". $data['ip'] . "' ");
        $MSQ_MAC = $this->conn_mysql->query("SELECT * FROM objekty_stb WHERE mac_adresa LIKE '" . $data['mac']. "' ");
        
        if( $MSQ_POPIS->num_rows > 0 )
        { 
            $error .= "<div class=\"alert alert-danger\" role=\"alert\">Popis (".$data['popis']." ) již existuje!!!</div>"; 
        }
        if( $MSQ_IP->mysql_num_rows > 0 )
        { 
            $error .= "<div class=\"alert alert-danger\" role=\"alert\">IP adresa ( ".$data['ip']." ) již existuje!!!</div>"; 
        }
        if( $MSQ_MAC->num_rows > 0 )
        { 
            $error .= "<div class=\"alert alert-danger\" role=\"alert\">MAC adresa ( ".$data['mac']." ) již existuje!!!</div>"; 
        }
 
        $this->action_form_validation_errors .= $error;
        
        if(empty($this->action_form_validation_errors))
        {
            return true;
        }
        else
        {
            $this->logger->addInfo("stb\\stbActionValidateFormData: data validation failed. dump action_form_validation_errors: ".var_export($this->action_form_validation_errors, true));
            return false;
        }
    }

    function stbActionSaveIntoDatabase($data)
    {
            $sql = "INSERT INTO objekty_stb "
            . " (mac_adresa, ip_adresa, puk, popis, id_nodu, sw_port, pozn, vlozil_kdo, id_tarifu)" 
            . " VALUES ('" . $data['mac'] ."','" . $data['ip'] . "','" . $data['puk'] . "','" 
            . $data['popis'] . "','" . $data['id_nodu'] . "','" . $data['port_id'] . "','" . $data['pozn'] . "','"
            . \App\Auth\Auth::getUserEmail() . "', '" . $data['id_tarifu'] . "') ";

            $this->logger->addInfo("stb\\stbActionSaveIntoDatabase: sql dump: ".var_export($sql, true));

            $res = $this->conn_mysql->query($sql);

            $id_stb = $this->conn_mysql->insert_id;

            if($res)
            { $output .= "<H3><div style=\"color: green;\" >Data úspěšně uloženy do databáze.</div></H3>\n"; } 
            else
            { 
                $output .= "<H3><div style=\"color: red;\" >Chyba! Data do databáze nelze uložit. </div></H3>\n"; 
                $output .= "res: $res \n";
            }	

            // pridame to do archivu zmen
            $pole="<b> akce: pridani stb objektu ; </b><br>";

            $pole .= "[id_stb]=> ".$id_stb.", ";
            $pole .= "[mac_adresa]=> ".$data['mac'].", [ip_adresa]=> ".$data['ip'].", [puk]=> ".$data['puk'].", [popis]=> ".$data['popis'];
            $pole .= ", [id_nodu]=> ".$data['id_nodu'].", [sw_port]=> ".$data['port_id']." [pozn]=> ".$data['pozn'].", [id_tarifu]=> ".$data['id_tarifu'];

            if( $res == 1 ){ $vysledek_write="1"; }

            $this->conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) ".
                "VALUES ('".$this->conn_mysql->real_escape_string($pole)."',".
                "'".$this->conn_mysql->real_escape_string(\App\Auth\Auth::getUserEmail())."',".
                "'" . $vysledek_write . "')");

            // $writed = "true"; 
            return $output;
    }

    function stbAction(ServerRequestInterface $request, ResponseInterface $response, $csrf)
    {
        // 0 field -> html code for smarty
        // 1 field -> name (and path) of smarty template
        $ret = array();

        $this->logger->addInfo("stb\\stbAction called ");

        $a = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $this->formInit();

        $data = $this->action_form->validate('popis, ip, mac, id_nodu, nod_find, puk, pin1, pin2, port_id, id_tarifu, pozn, odeslano, FormrID, g1, g2');
        $this->logger->addDebug("stb\\stbAction: form data: ".var_export($data, true));

        $this->action_form->required = 'popis,ip,mac,id_nodu,puk,port_id,id_tarifu';
        // $this->action_form->required = 'nod_find, pozn, pin1, pin2';

        if(!empty($data['odeslano']))
        {
            // go for final

            if($this->action_form->ok())
            {
                // go for validate data
                $rs_v = $this->stbActionValidateFormData($data);
                $this->logger->addInfo("stb\\stbAction: form data validation result: ".var_export($rs_v, true));

                if( $rs_v === true){
                    // go for save into databze
    
                    $rs_s = $this->stbActionSaveIntoDatabase($data);
                    $rs .= $rs_s;

                    // TODO: improve showing data from form
                        $rs .= "<br>
                        STB Objekt byl přidán/upraven, zadané údaje:<br><br>
                        <b>Popis objektu</b>: " . $data['popis'] . "<br>
                        <b>IP adresa</b>: " . $data['ip'] . "<br>
                        <b>MAC adresa</b>: " . $data['mac'] . "<br><br>
                        
                        <b>Puk</b>: " . $data['puk'] . "<br>
                        <b>Číslo portu switche</b>: " . $data['$port_id'] . "<br>
                        
                        <b>Přípojný bod</b>: ";

                        $vysledek3=$this->conn_mysql->query("select jmeno, id from nod_list WHERE id='".intval($data['id_nodu'])."' ");
                        $radku3=$vysledek3->num_rows;
                        
                        if($radku3==0) $rs .= " Nelze zjistit ";
                        else 
                        {
                            while( $zaznam3=$vysledek3->fetch_array() )
                              { $rs .= $zaznam3["jmeno"]." (id: ".$zaznam3["id"].") ".''; }
                        }
                    
                        $rs .= "<br><br>";
                        
                        $rs .= "<b>Poznámka</b>:".htmlspecialchars($data['pozn'])."<br>";
                        
                        $ms_tarif = $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($data['id_tarifu'])."'");
                        
                        $ms_tarif->data_seek(0);
                        $ms_tarif_r = $ms_tarif->fetch_row();
                        
                        $rs .= "<b>Tarif</b>: ".$ms_tarif_r[0]."<br><br>";

                    $ret[0] = $rs;
                    return $ret;
                }
                else
                {
                    // ship validation results to form
                    $this->logger->addWarning("stb\\stbAction: form data validatation failed");
                }
            }
        }

        //
        // prepare data
        //

        $topology = new \App\Core\Topology($this->conn_mysql, $this->smarty, $this->logger);
        
        $node_list = $topology->getNodeListForForm($data['nod_find']);
        $this->logger->addDebug("stb\\stbAction: node_list data: " . var_export($node_list, true));

        $tarifs_iptv = $a->getTarifIptvListForForm();
        $this->logger->addDebug("stb\\stbAction: tarifs iptv list data: " . var_export($tarifs_iptv, true));

        $form_data = $this->stbActionRenderForm($request, $response, $csrf, $data, $node_list, $tarifs_iptv);

        // $this->logger->addDebug("stb\\stbAction: form_data: " . var_export($form_data, true));

        $ret[0] = $form_data;
        $ret[1] = "objekty/stb-action-form.tpl";

        return $ret;

    }

    function stbActionRenderForm (ServerRequestInterface $request, ResponseInterface $response, $csrf, $data, $node_list, $tarifs_iptv_list)
    {
        $form_csrf = array(
            $csrf[1] => $csrf[3],
            $csrf[2] => $csrf[4],
        );
        
        for ($x = 1; $x <= 48; $x++) {
            $form_port_id[$x] = $x;
        }

        $uri = $request->getUri();

        // if()
        // {

        // }
        // else
        // {

        // }

        $form_id = "stb-action-add";

        $form_data['f_open'] = $this->action_form->open($form_id,$form_id, $uri->getPath(), '','',$form_csrf);
        $form_data['f_close'] = $this->action_form->close();
        $form_data['f_submit_button'] = $this->action_form->input_submit('odeslano', '', 'OK / Odeslat / Uložit');

        $form_data['f_input_popis'] = $this->action_form->text('popis','Popis objektu', $data['popis']);

        $form_data['f_input_nod_find'] = $this->action_form->text('nod_find','Přípojný bod - filtr', $data['nod_find']);

        $form_data['f_input_nod_find_button'] = $this->action_form->input_submit(
                                                                        'g1',
                                                                        '',
                                                                        'Hledat (nody)',
                                                                        '',
                                                                        'class="btn btn-secondary" ');

        $form_data['f_input_ip'] = $this->action_form->text('ip','IP adresa',$data['ip']);
        $form_data['f_input_id_nodu'] = $this->action_form->select('id_nodu','', $data['id_nodu'], '', 'class="form-select-inline form-select-sm"','','', $node_list);

        $form_data['f_input_mac'] = $this->action_form->text('mac','mac adresa', $data['mac_adresa']);
        $form_data['f_input_gen_button'] = $this->action_form->input_submit(
                                                                'g2',
                                                                '',
                                                                'Generovat údaje',
                                                                '',
                                                                'class="btn btn-secondary" ');

        $form_data['f_input_puk'] = $this->action_form->text('puk','puk', $data['puk']);
        $form_data['f_input_pin1'] = $this->action_form->text('pin1','pin1', $data['pin1']);
        $form_data['f_input_pin2'] = $this->action_form->text('pin2','pin2', $data['pin2']);


        $form_data['f_input_port_id'] = $this->action_form->select('port_id','Číslo portu (ve switchi)', $data['port_id'], '', 'class="form-select form-select-sm"', '', '', $form_port_id);

        $form_data['f_input_pozn'] = $this->action_form->textarea('pozn','poznámka', $data['pozn'], 'rows="5" wrap="soft"');

        $form_data['f_input_id_tarifu'] = $this->action_form->select('id_tarifu', 'Tarif', $data['id_tarifu'], '', 'class="form-select form-select-sm"','','', $tarifs_iptv_list);


        // print messages, formatted using Bootstrap alerts
        $form_data['f_messages'] = $this->action_form->messages();
        $form_data['f_messages_validation'] = $this->action_form_validation_errors;

        return $form_data;
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
            $this->action_form_validation_errors .= 
                            $this->action_form_validation_errors_wrapper_start
                            . "IP adresa ( ".$ip." ) není ve správném formátu !!!"
                            . $this->action_form_validation_errors_wrapper_end
                            ;
       }
       
    } //konec funkce check-ip			 
   
    function checkmac($mac) 
    {
       $mac_check=ereg('^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$',$mac);
       
       if( !($mac_check) )
       {
            $this->action_form_validation_errors .= 
                    $this->action_form_validation_errors_wrapper_start
                    . "MAC adresa ( ".$mac." ) není ve správném formátu !!! (Správný formát je: 00:00:64:65:73:74)"
                    . $this->action_form_validation_errors_wrapper_end
                    ;
       }
       
     } //konec funkce check-mac
   
     function checkcislo($cislo)
     {
        $rra_check=ereg('^([[:digit:]]+)$',$cislo);
        
        if( !($rra_check) )
        {
            $this->action_form_validation_errors .= 
                    $this->action_form_validation_errors_wrapper_start
                    . "Zadaný číselný údaj ( ".$cislo." ) není ve  správném formátu!"
                    . $this->action_form_validation_errors_wrapper_end
                    ;
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