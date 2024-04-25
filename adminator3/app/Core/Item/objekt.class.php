<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class objekt extends adminator
{

    var $conn_pqsql;
    var $conn_mysql;

    var $logger;

    var $loggedUserEmail;

    var $adminator; // handler for instance of adminator class

    var $dns_find;

    var $ip_find;

    var $mod_vypisu;

    var $es;

    var $razeni;

    var $list;

    var $nod_find;

    var $update_id;
    var $odeslano;
    var $send;

    var $mod_objektu;

    var $dotaz_source;

    var $listErrors;

    var $listAllowedActionUpdate = false;

    var $listAllowedActionErase = false;

    var $listAllowedActionGarant = false;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->validator;
        $this->conn_mysql = $container->connMysql;   
        $this->logger = $container->logger;

        $i = $container->auth->getIdentity();
        $this->loggedUserEmail = $i['username'];
    }

    public function listGetOrderItems()
    {
        $output = "";

        $output .= "\n<tr>\n";
        $output .= '<td colspan="1">';
        
        $output .= "\n\n <input type=\"radio\" ";
        if ( ($_GET["razeni"]== 1) ){ $output .= " checked "; }
        $output .= "name=\"razeni\" value=\"1\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
            if ( ($_GET["razeni"]== 2) ){ $output .= " checked "; }
        $output .= " name=\"razeni\" value=\"2\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
                    <td colspan="3">';
        
        $output .= "<input type=\"radio\" ";
        if ( ($_GET["razeni"]== 3) ){ $output .= " checked "; }
        $output .= "name=\"razeni\" value=\"3\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
            if ( ($_GET["razeni"]== 4) ){ $output .= " checked "; }
        $output .= " name=\"razeni\" value=\"4\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
                    <td>';
        
        $output .= "<input type=\"radio\" ";
        if ( ($_GET["razeni"]== 9) ){ $output .= " checked "; }
        $output .= "name=\"razeni\" value=\"9\" onClick=\"form1.submit();\" > ";
        $output .= "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";

        $output .= " | <input type=\"radio\" ";
            if ( ($_GET["razeni"]== 10) ){ $output .= " checked "; }
        $output .= " name=\"razeni\" value=\"10\" onClick=\"form1.submit();\"> \n";
        $output .= "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

        $output .= '</td>
        <td></td>';
  
        // $output .= "<td><b>client ap </b></td>";
       
        // $output .= '
        //     <td align="center" ><b>upravit</b></td>
        //     <td align="center" ><b>smazat</b></td>
        //     <td><b>třída </b></td>
        // <td><b>Aktivní</b></td>
        // <td><b>Test obj.</b></td>
        // <td><b>Linka </b></td>
        // <td><b>Omezení </b></td>';
          
        $output .= "\n</tr>\n";

        return array($output);
    }

    private function listPrepareVars()
    {
        $mod_vypisu = $_GET["mod_vypisu"];
    
        if( isset($mod_vypisu) )
        {
            if( !( preg_match('/^([[:digit:]])+$/',$mod_vypisu) ) )
            {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Chyba! Nesouhlasi vstupni data. (mod vypisu) </div>";
            }
            $this->mod_vypisu = $mod_vypisu;
        }

        if( ( strlen($this->dns_find) > 0 ) )
        {
            if( !( preg_match('/^([[:alnum:]]|_|-|\.|\%)+$/',$this->dns_find) ) )
            {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Hledání dle dns\". (Povolené: a-z,A-Z,0-9,-, _,. )</div>";
            }
        }
                
        if( ( strlen($this->ip_find) > 0 ) )
        {
            if( !( preg_match('/^([[:digit:]]|\.|/)+$/',$this->ip_find) ) )
            {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Hledání dle ip adresy\". (Povolené: a-z,A-Z,0-9,-, _,. )</div>";
            }    
        }
        
        $es = $_GET["es"];
        
        if( ( strlen($es) > 0 ) )
        {
            if( !( preg_match('/^([[:digit:]])+$/',$es) ) )
            {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v poli \"Sekundární hledání\". </div>";
            }
            $this->es = $es;
        }
        
        $razeni = $_GET["razeni"];
    
        if( ( strlen($razeni) > 0 ) )
        {
            if( !( preg_match('/^([[:digit:]])+$/',$razeni) ) )
            {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"razeni\". </div>";
            }
            $this->razeni = $razeni;
        }
        
        $list=$_GET["list"];       
       
        if( ( strlen($list) > 0 ) )
        {
            if( !( preg_match('/^([[:digit:]])+$/',$list) ) )
            {
                $this->listErrors .= "<div style=\"color: red; font-weight: bold; \" >Nepovolené znaky v promenné \"list\". </div>";
            }    
            $this->list = $list;
        }

        if(strlen($this->listErrors) > 0 )
        {
            return false;
        }

        return true;
    }

    private function listGenerateSql()
    {
        // detect mode
        //
        if ( ( strlen($this->dns_find) > 0 ) )
        {
            $co=1;
            $sql=$this->dns_find;
        }  

        if ( ( strlen($this->ip_find) > 0  ) )
        {
            $co=2;
            $sql=$this->ip_find;
        }

        list($se,$order) = \objekt_a2::select($this->es,$this->razeni);
       
        $tarif_sql = "";
          
         if( $this->mod_vypisu == 1 )
         { 
           $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");
           
           $i = 0;
           
           while( $data_f = $dotaz_f->fetch_array() )
           {
            if( $i == 0 ){ $tarif_sql .= "AND ( "; }
            if( $i > 0 ){ $tarif_sql .= " OR "; }
            
            $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"].""; 
            
            $i++;
          }
          
          if( $i > 0 ){ $tarif_sql .= " ) "; }
          
         }
         elseif( $this->mod_vypisu == 2 )
         { 
           $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");
           
           $i = 0;
           
           while( $data_f = $dotaz_f->fetch_array() )
           {
            if( $i == 0 ){ $tarif_sql .= "AND ( "; }
            if( $i > 0 ){ $tarif_sql .= " OR "; }
            
            $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." "; 
            
            $i++;
           }
           
           if( $i > 0 ){ $tarif_sql .= " ) "; }
          
         }
         // echo "dotaz_tarif: ".$tarif_sql." /";
          
        if( $co==1)
        {
          $sql="%".$sql."%";
          
          $dotaz_source = "SELECT * FROM objekty WHERE dns_jmeno LIKE '$sql' ".$se.$tarif_sql.$order;
        }
        elseif( $co==2 ){ $dotaz_source = "SELECT * FROM objekty WHERE ip <<= '$sql' ".$se.$tarif_sql.$order; }
        elseif( $co==3 ){ $dotaz_source = "SELECT * FROM objekty WHERE id_cloveka=".$id; }
        else
        {
         echo ""; 
         return false;
        }

        $this->dotaz_source = $dotaz_source;

        return true;
    }
    public function listGetBodyContent()
    {
        $output = "";
        $exportLink = "";
        $error = "";

        $this->adminator = new \App\Core\adminator($this->conn_mysql, $this->smarty, $this->logger);

        $objekt_a2 = new \objekt_a2;
        $objekt_a2->echo = false;
        $objekt_a2->conn_mysql = $this->conn_mysql;
        $objekt_a2->conn_pqsql = $this->container->connPgsql;

        // checking levels for update/erase/..
        if ($this->adminator->checkLevel(29, false) === true) {
            $this->listAllowedActionUpdate = true;
        }
        if ($this->adminator->checkLevel(33, false) === true) {
            $this->listAllowedActionErase = true;
        }
        if ($this->adminator->checkLevel(34, false) === true) {
            $this->listAllowedActionGarant = true;
        }
        if ($this->adminator->checkLevel(59, false) === true) {
            $export_povolen = true;
        }

        $objekt_a2->listAllowedActionUpdate = $this->listAllowedActionUpdate;
        $objekt_a2->listAllowedActionErase = $this->listAllowedActionErase;
        $objekt_a2->listAllowedActionGarant = $this->listAllowedActionGarant;

        if ( $export_povolen === true )
        { 
            $exportLink = $objekt_a2->export_vypis_odkaz(); 
        }	

        // prepare vars
        //
        $prepVarsRs = $this->listPrepareVars();
        if($prepVarsRs === false){
            return array("", $this->listErrors);
        }
        
        // detect mode (again)
        //
        if ( ( strlen($this->dns_find) > 0 ) )
        {
            $co=1;
            $sql=$this->dns_find;
        }  

        if ( ( strlen($this->ip_find) > 0  ) )
        {
            $co=2;
            $sql=$this->ip_find;
        }

        $output .= $objekt_a2->vypis_tab(1);
        
        $output .= $objekt_a2->vypis_tab_first_rows($this->mod_vypisu);

        list($output_razeni) = $this->listGetOrderItems();
        $output .= $output_razeni;

        $output .=  "</form>";

        $generateSqlRes = $this->listGenerateSql();
        if($generateSqlRes === false){
            return array("", '<div class="alert alert-danger" role="alert">Chyba! Nepodarilo se vygenerovat SQL dotaz.</div>');
        }
        // paging
        //
        $poradek = "es=".$this->es."&najdi=".$najdi."&odeslano=".$_GET['odeslano']."&dns_find=".$this->dns_find."&ip_find=".$this->ip_find."&razeni=".$_get['razeni'];
        $poradek .= "&mod_vypisu=".$this->mod_vypisu;
       
        //vytvoreni objektu
        $listovani = new \c_listing_objekty("/objekty?".$poradek."&menu=1", 30, $this->list, "<center><div class=\"text-listing2\">\n", "</div></center>\n", $this->dotaz_source);
        $listovani->echo = false;
        
        if(($this->list == "")||($this->list == "1")){ $bude_chybet = 0;  } //pokud není list zadán nebo je první bude ve výběru sql dotazem chybet 0 záznamů
        else
        { $bude_chybet = (($this->list-1) * $listovani->interval); }   //jinak jich bude chybet podle závislosti na listu a intervalu
     
        //  $interval=$listovani->interval;
     
        if(intval($listovani->interval) > 0 and intval($bude_chybet) > 0) {
            $this->dotaz_source = $this->dotaz_source . " LIMIT ". intval($listovani->interval)." OFFSET ".intval($bude_chybet)." ";
        }
       
        $output .= $listovani->listInterval();
        
        $this->logger->debug("objekt\listGetBodyContent: dump vars: "
                                ."dotaz_source: " . var_export($this->dotaz_source, true)
                                . ", sql: " . var_export($sql, true)
                                . ", co: " . var_export($co, true)
                            );

        $output .= $objekt_a2->vypis($sql,$co,0,$this->dotaz_source);

        $output .= $objekt_a2->vypis_tab(2);  

        // listing
        $output .= $listovani->listInterval(); 

        return array($output, $error, $exportLink);
    }

    public function actionPrepareVars()
    {
        $nod_find = $_POST["nod_find"];

        if( ( strlen($nod_find) < 1 ) ){ $nod_find="%"; }
        else
        {
          // TODO: add validation of nod_find

          if( !(preg_match("/^%.*%$/",$nod_find)) )
          { $nod_find="%".$nod_find."%"; }
        }

        $this->nod_find = $nod_find;

        // TODO: add validation fo control vars

        $this->update_id=$_POST["update_id"];
        $this->odeslano=$_POST["odeslano"];
        $this->send = $_POST["send"];
    }

    public function actionWifi()
    {
        if (  ( $this->update_id > 0 ) )
        { $update_status=1; }

        if( ( $update_status==1 and !( isset($this->send) ) ) )
        {
            //rezim upravy
            $dotaz_upd = pg_query("SELECT * FROM objekty WHERE id_komplu='".intval($update_id)."' ");
            $radku_upd=pg_num_rows($dotaz_upd);
            
            if ( $radku_upd==0 ) echo "Chyba! Požadovaná data nelze načíst! ";
            else
            {
                while($data=pg_fetch_array($dotaz_upd)):
                    // primy promenny 
                    $dns=$data["dns_jmeno"];  
                    $ip=$data["ip"];	 
                    $mac=$data["mac"];
                    $typ=$data["typ"];	$pozn=$data["poznamka"]; 
                    $selected_nod=$data["id_nodu"];

                    $sikana_text=$data["sikana_text"];
                    $client_ap_ip=$data["client_ap_ip"];
                    
                    $id_tarifu=$data["id_tarifu"];

                    // neprimy :) -> musi se zkonvertovat
                    
                    $dov_net_l=$data["dov_net"];	
                    if( $dov_net_l =="a" ){ $dov_net=2; }
                    else{ $dov_net=1; }    
                    
                    $verejna_l=$data["verejna"];	
                    
                    if( $data["tunnelling_ip"] == "1")
                    { //tunelovaná verejka 
                    $typ_ip = "4";
                    
                    $tunnel_user = $data["tunnel_user"];
                    $tunnel_pass = $data["tunnel_pass"];
                    
                    } 
                    elseif( $verejna_l=="99" ) 
                    { $typ_ip="1"; }
                    else { 
                    $typ_ip="2"; 
                    $vip_rozsah=$verejna_l; 
                    }
                    
                    $sikana_status_l=$data["sikana_status"]; 
                    if( preg_match("/a/",$sikana_status_l) ) { $sikana_status=2; } else { $sikana_status=1; }
                    $sikana_cas_l=$data["sikana_cas"];
                    if( strlen($sikana_cas_l) > 0 ) { $sikana_cas=$sikana_cas_l; }
                
                endwhile;
                
            }
            
        }
        else
        {
            // rezim pridani, ukladani
            $dns=$_POST["dns"];		$ip=$_POST["ip"];			$typ=$_POST["typ"];	

            $typ_ip=$_POST["typ_ip"];	$dov_net=$_POST["dov_net"];		$id_tarifu = $_POST["id_tarifu"];
            $mac=$_POST["mac"];		$verejna=$_POST["verejna"];
            $typ_ip=$_POST["typ_ip"];	$vip_rozsah=$_POST["vip_rozsah"];	$pozn=$_POST["pozn"];

            //systémove
            $send=$_POST["send"];	
            $selected_nod=$_POST["selected_nod"];

            // dalsi
            $sikana_status = $_POST["sikana_status"];	 $sikana_cas = $_POST["sikana_cas"];	$sikana_text = $_POST["sikana_text"];

            //$vip_snat_lip = $_POST["vip_snat_lip"];
            $client_ap_ip = $_POST["client_ap_ip"];

            $tunnel_user = $_POST["tunnel_user"];
            $tunnel_pass = $_POST["tunnel_pass"];
        }

        //co mame: v promeny selected_nod mame id nodu kam se to bude pripojovat
        // co chcete: ip adresu , idealne ze spravnyho rozsahu :)

        \objektypridani::generujdata($selected_nod, $typ_ip, $dns, $this->conn_mysql); 

        if( (strlen($ip) > 0) )  { \objektypridani::checkip($ip); }

        if( ( strlen($dns) > 0 ) )  { \objektypridani::checkdns($dns); }
        if( ( strlen($mac) > 0 ) ) { \objektypridani::checkmac($mac); }	
        if( (strlen($sikana_cas) > 0 ) ) { \objektypridani::checkcislo($sikana_cas); }
        if( (strlen($selected_nod) > 0 ) ) { \objektypridani::checkcislo($selected_nod); }

        if( (strlen($client_ap_ip) > 0 ) ) { \objektypridani::checkip($client_ap_ip); }

        if( $sikana_status == 2 ) { 

            \objektypridani::checkSikanaCas($sikana_cas); 
            
            \objektypridani::checkSikanaText($sikana_text); 

        }


        if( $typ_ip == 4 )
        {
            if( (strlen($tunnel_user) > 0 ) ){ objektypridani::check_l2tp_cr($tunnel_user); }
            if( (strlen($tunnel_pass) > 0 ) ){ objektypridani::check_l2tp_cr($tunnel_pass); }
        }

        // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
        if( ( ($dns != "") and ($ip != "") ) and ( $selected_nod > 0 ) and ( ($id_tarifu >= 0) ) ):

            if( ( $update_status!=1 ) )
            {
            $ip_find=$ip."/32";

            //zjisti jestli neni duplicitni dns, ip
            $MSQ_DNS = pg_query("SELECT ip FROM objekty WHERE dns_jmeno LIKE '$dns' ");
            $MSQ_IP = pg_query("SELECT ip FROM objekty WHERE ip <<= '$ip_find' ");
                
            if (pg_num_rows($MSQ_DNS) > 0){ $error .= "<h4>Dns záznam ( ".$dns." ) již existuje!!!</h4>"; $fail = "true"; }
            if (pg_num_rows($MSQ_IP) > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }

            //duplicitni tunnel_pass/user
            if($typ_ip==4)
            {
            $MSQ_TUNNEL_USER = pg_query("SELECT tunnel_user FROM objekty WHERE tunnel_user LIKE '$tunnel_user' ");
            $MSQ_TUNNEL_PASS = pg_query("SELECT tunnel_pass FROM objekty WHERE tunnel_pass LIKE '$tunnel_pass' ");
            
            if(pg_num_rows($MSQ_TUNNEL_USER) > 0)
            { $error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>"; $fail = "true"; }
            if(pg_num_rows($MSQ_TUNNEL_PASS) > 0)
            { $error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>"; $fail = "true"; }  
            }
            
            }

            // check v modu uprava
            if ( ( $update_status==1 and (isset($odeslano)) ) )
            {
            $ip_find=$ip."/32";
            
            //zjisti jestli neni duplicitni dns, ip
            $MSQ_DNS2 = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ( dns_jmeno LIKE '$dns' AND id_komplu != '".intval($update_id)."' ) ");
            $MSQ_IP2 = pg_exec($db_ok2, "SELECT * FROM objekty WHERE ( ip <<= '$ip_find' AND id_komplu != '".intval($update_id)."' ) ");

            if(pg_num_rows($MSQ_DNS2) > 0){ $error .= "<h4>Dns záznam ( ".$dns." ) již existuje!!!</h4>"; $fail = "true"; }
            if(pg_num_rows($MSQ_IP2) > 0){ $error .= "<h4>IP adresa ( ".$ip." ) již existuje!!!</h4>"; $fail = "true"; }


            //duplicitni tunnel_pass/user
            if($typ_ip==4)
            {
            $MSQ_TUNNEL_USER = pg_query("SELECT tunnel_user FROM objekty WHERE ( tunnel_user LIKE '$tunnel_user' AND id_komplu != '".intval($update_id)."' ) ");
            $MSQ_TUNNEL_PASS = pg_query("SELECT tunnel_pass FROM objekty WHERE ( tunnel_pass LIKE '$tunnel_pass' AND id_komplu != '".intval($update_id)."' ) ");
            
            if(pg_num_rows($MSQ_TUNNEL_USER) > 0)
            { $error .= "<h4>Login k tunelovacímu serveru (".$tunnel_user.") již existuje!!!</h4>"; $fail = "true"; }
            if(pg_num_rows($MSQ_TUNNEL_PASS) > 0)
            { $error .= "<h4>Heslo k tunelovacímu serveru (".$tunnel_pass.") již existuje!!!</h4>"; $fail = "true"; }  
            }
            
            }

            // checknem stav vysilace a filtraci
            try {
            $msq_stav_nodu = $conn_mysql->query("SELECT stav, router_id FROM nod_list WHERE id= '".intval($selected_nod)."' ");
            $msq_stav_nodu_radky = $msq_stav_nodu->num_rows;
            } catch (Exception $e) {
            die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
            }

            while ($data=$msq_stav_nodu->fetch_array() )
            { $stav_nodu = $data["stav"]; $router_id = $data["router_id"]; }

            if ( $stav_nodu == 2 )
            { $info .= "<div style=\"color: orange; \" ><h4>UPOZORNĚNÍ: Tento přípojný bod je vytížen, mohou se vyskytovat problémy. </h4></div>"; }
            elseif ( ( $stav_nodu == 3 ) and ( $update_status == 1 ) )
            { $info .= "<div style=\"color: orange; \"><h4>UPOZORNĚNÍ: Tento přípojný bod je přetížen. </h4></div>"; }
            elseif ( $stav_nodu == 3)
            { $fail="true"; $error .= "<div style=\"color: red; \" ><h4>Tento přípojný bod je přetížen, vyberte prosím jiný. </h4></div>";  }

            // kontrola jestli se muze povolit inet / jestli jsou pozatavené fakturace
            $poz_fakt_clovek=pg_query("SELECT id_cloveka, dov_net FROM objekty WHERE id_komplu = '".intval($update_id)."' ");
            $poz_fakt_clovek_radku=pg_num_rows($poz_fakt_clovek);

            while ($data_poz_f_clovek=pg_fetch_array($poz_fakt_clovek))
            { $id_cloveka=$data_poz_f_clovek["id_cloveka"]; 
                $dov_net_puvodni=$data_poz_f_clovek["dov_net"];
            }

            if ( ( ($id_cloveka > 1) and ( $update_status==1 ) ) )
            {

            $pozastavene_fakt=pg_query("SELECT billing_suspend_status FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
            $pozastavene_fakt_radku=pg_num_rows($pozastavene_fakt);

            if ( $pozastavene_fakt_radku == 1)
            {
            while ( $data_poz_fakt=pg_fetch_array($pozastavene_fakt) )
            { $billing_suspend_status = intval($data_poz_fakt["billing_suspend_status"]); }
            }
            else
            { echo "Chyba! nelze vybrat vlastníka."; }

            // echo "debug: id_fakturacni_skupiny: ".$pozastavene_fakturace_id." id_cloveka: $id_cloveka ,dov_net-puvodni: $dov_net_puvodni , povolen inet: $dov_net";

            if( $billing_suspend_status == 1)
            {
            // budeme zli
            // prvne zjisteni predchoziho stavu
            

            if( ( ($dov_net_puvodni == "n") and ($dov_net == 2 ) ) )
            {
                $fail="true"; 
                $error.="<div class=\"objekty-add-mac\" >Klient má pozastavené fakturace. Před povolením internetu je potřeba změnit u vlastníka pole \"Pozastavené fakturace\". </div>"; 
            }
            
            }

            } // konec if jestli id_cloveka > 1 and update == 1

            //checkem jestli se macklo na tlacitko "OK" :)
            if( preg_match("/^OK$/",$odeslano) ) { echo ""; }
            else 
            { 
                $fail="true"; 
                $error.="<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; 
            }

            //ulozeni
            if ( !( isset($fail) ) ) 
            { 
            // priprava promennych
            
            if ( $dov_net == 2 ) 
            { $dov_net_w ="a"; } 
            else { $dov_net_w="n"; }
            
            if ( $typ == 3 ) { $dov_net_w="a"; }
            
            if ($typ_ip == 1)
            { $verejna_w="99"; } 
            elseif( $typ_ip == 3 )
            { 
                $verejna_w=$vip_rozsah;
                //$vip_snat="1";    
            }
            elseif( $typ_ip == 4 )
            {
            //tunelovane ip adresy
            $tunnelling_ip=1; //flag pro selekci tunelovanych ip
            $verejna_w=$vip_rozsah; //flag ze je jedna o verejnou (asi jen pro DNS)
            
            $tunnel_user_w = $tunnel_user;
            $tunnel_pass_w = $tunnel_pass;
                
            }
            else
            {
            //obyc verejka 
                $verejna_w=$vip_rozsah; 
                $tunnelling_ip="0"; 
            }
            
            if( $sikana_status =="2" )
            { $sikana_status_w='a'; } 
            else
            { $sikana_status_w='n'; }
                
            $sikana_cas = intval($sikana_cas);
            
            if($update_status =="1")
            {
                // rezim upravy
                
                if( !(check_level($level,29) ) ) 
                {
                echo "<br><div style=\"color: red; font-size: 18px; \" >Objekty nelze upravovat, není dostatečné oprávnění. </div><br>";
                    exit;
                }
                else
                {
                //prvne stavajici data docasne ulozime 
                $pole2 .= "<b>akce: uprava objektu; </b><br>";
                    
                $sql_rows = "id_komplu, dns_jmeno, ip, mac, client_ap_ip, dov_net, id_tarifu, typ, poznamka, verejna, ";
                $sql_rows .= "sikana_status, sikana_cas, sikana_text, upravil, id_nodu, ";
                $sql_rows .= "tunnelling_ip, tunnel_user, tunnel_pass";
                
                $vysl4=pg_query("SELECT ".$sql_rows." FROM objekty WHERE id_komplu='".intval($update_id)."' ");

                if( ( pg_num_rows($vysl4) <> 1 ) )
                { echo "<div>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </div>"; }
                else  
                { 
                        while ($data4=pg_fetch_array($vysl4) ){
                
                    $pole_puvodni_data["id_komplu"]=$data4["id_komplu"];		
                    $pole_puvodni_data["dns_jmeno"]=$data4["dns_jmeno"];	
                    $pole_puvodni_data["ip"]=$data4["ip"];
                    $pole_puvodni_data["mac"]=$data4["mac"];		
                    $pole_puvodni_data["client_ap_ip"]=$data4["client_ap_ip"];	
                    $pole_puvodni_data["dov_net"]=$data4["dov_net"];	
                    $pole_puvodni_data["id_tarifu"]=$data4["id_tarifu"];
                    $pole_puvodni_data["typ"]=$data4["typ"];
                    $pole_puvodni_data["poznamka"]=$data4["poznamka"];	
                    $pole_puvodni_data["verejna"]=$data4["verejna"];
                    $pole_puvodni_data["sikana_status"]=$data4["sikana_status"];	
                    $pole_puvodni_data["sikana_cas"]=$data4["sikana_cas"];
                    $pole_puvodni_data["sikana_text"]=$data4["sikana_text"];
                    $pole_puvodni_data["upravil"]=trim($data4["upravil"]);	
                    $pole_puvodni_data["id_nodu"]=$data4["id_nodu"];
                
                    $pole_puvodni_data["tunnelling_ip"]=$data4["tunnelling_ip"];	
                    $pole_puvodni_data["tunnel_user"]=$data4["tunnel_user"];
                    $pole_puvodni_data["tunnel_pass"]=$data4["tunnel_pass"];	
                
                        }
                    
                } // konec else if radku <> 1

                    $obj_upd = array( "dns_jmeno" => $dns, "ip" => $ip,
                            "client_ap_ip" => $client_ap_ip, "dov_net" => $dov_net_w,"id_tarifu" => $id_tarifu,
                        "typ" => $typ, "poznamka" => $pozn, "verejna" => $verejna_w,
                        "mac" => $mac, "upravil" => $nick, "sikana_status" => $sikana_status_w,
                    "sikana_cas" => $sikana_cas, "sikana_text" => $sikana_text, "id_nodu" => $selected_nod );
                                
                if( $typ_ip == 4)
                {
                        $obj_upd["tunnelling_ip"] = $tunnelling_ip; 

                        $obj_upd["tunnel_user"] = $tunnel_user_w;
                        $obj_upd["tunnel_pass"] = $tunnel_pass_w;
                }   
                else
                { 
                        $obj_upd["tunnelling_ip"] = "0"; 
                }
                
                $obj_id = array( "id_komplu" => $update_id );
                $res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);

                } // konec else jestli je opravneni
                
                if($res){ echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
                else{ 
                    echo "<br><H3><div style=\"color: red; \">".
                    "Chyba! Data v databázi nelze změnit. </div></h3>\n".pg_last_error($db_ok2); 
                }
                    
                //ted zvlozime do archivu zmen
                require("objekty-add-inc-archiv.php");				     

                $updated="true";
                
            }
            else
            {
                // rezim pridani
                
                $sql_rows = "";
                $sql_values = "";
                
                $obj_add_i = 1;
                
            //    $sql_rows = "dns_jmeno, ip, id_tarifu, dov_net, typ, poznamka, verejna, pridal, id_nodu, ".
            //		    "sikana_status, sikana_cas, sikana_text ";

                $obj_add = array( "dns_jmeno" => $dns, "ip" => $ip, "id_tarifu" => $id_tarifu, "dov_net" => $dov_net_w, 
                        "typ" => $typ, "poznamka" => $pozn, "verejna" => $verejna_w, "pridal" => $nick, "id_nodu" => $selected_nod,
                                "sikana_status" => $sikana_status_w, "sikana_cas" => $sikana_cas, "sikana_text" => $sikana_text );

                if($typ_ip == 4){
                    $obj_add["tunnelling_ip"] = $tunnelling_ip;
                    
                    $obj_add["tunnel_user"] = $tunnel_user_w;
                    $obj_add["tunnel_pass"] = $tunnel_pass_w;
                                                    
                }
                    
                if( (strlen($client_ap_ip) > 0) ){
                $obj_add["client_ap_ip"] = $client_ap_ip;
                }
                
                if( (strlen($mac) > 0) ){
                $obj_add["mac"] = $mac;
                }
                    
                                                                                    
                foreach ($obj_add as $key => $val) {
                
                if($obj_add_i > 1){
                    $sql_rows .= ", ";
                    $sql_values .= ", ";
                }
                $sql_rows .= $conn_mysql->real_escape_string($key);
                
                $sql_values .= "'".$conn_mysql->real_escape_string($val)."'";
                
                $obj_add_i++;	
                }

                $sql = "INSERT INTO objekty (".$sql_rows.") VALUES (".$sql_values.") ";
                    
                $res = pg_query($sql);
                    
                if( !($res === false) ) 
                { 
                echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; 
                } 
                else
                { 
                        echo "<H3><div style=\"color: red; padding-top: 20px; padding-left: 5px; \">".
                            "Chyba! Data do databáze nelze uložit. </div></H3>\n";
                        
                        echo "<div style=\"color: red; padding-bottom: 10px; padding-left: 5px; \" >".
                        pg_last_error($db_ok2).
                            "</div>";
                        
                        echo "<div style=\"padding-left: 5px; \">sql: ".$sql."</div>";
                }
                
                // pridame to do archivu zmen
                require("objekty-add-inc-archiv-wifi-add.php");
                
            } // konec else - rezim pridani

            }
            else {} // konec else ( !(isset(fail) ), muji tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

        elseif ( isset($send) ): 
            $error = "<h4>Chybí povinné údaje !!! (aktuálně jsou povinné:  dns, ip adresa, přípojný bod, tarif) </H4>"; 
        endif; 

        if ($update_status==1)
        { echo '<h3 align="center">Úprava objektu</h3>'; } 
        else 
        { echo '<h3 align="center">Přidání nového objektu</h3>'; }

        // jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
        if( (isset($error)) or (!isset($send)) ): 
            echo $error; 

            echo $info;

            // vlozeni vlastniho formu
            // require("objekty-add-inc.php");
            $this->actionForm();

        elseif ( ( isset($writed) or isset($updated) ) ):

            echo '<table border="0" width="50%" >
                <tr>
                <td align="right">Zpět na objekty </td>
                <td><form action="" method="GET" ><input type="hidden"' . "value=\"".$dns."\"" . ' name="dns_find" >
                <input type="submit" value="ZDE" name="odeslat" > </form></td>
            </table>';

            echo '<br>
            Objekt byl přidán/upraven , zadané údaje:<br><br> 
            <b>Dns záznam</b>: ' . $dns . '<br> 
            <b>IP adresa</b>: ' . $ip . '<br> 
            <b>client ap ip </b>: ' . $client_ap_ip . '<br>'
            . "<br><b>Typ objektu </b>:";
        
            if ($typ == 1) { echo "platiči"; } elseif ($typ == 2) { echo "Free"; } elseif ($typ == 3) { echo "AP"; }
            else { echo "chybný výběr"; }
            
            echo '<br> 
                 <b>Linka</b>: ';

            $vysledek4 = $conn_mysql->query("SELECT jmeno_tarifu, zkratka_tarifu FROM tarify_int WHERE id_tarifu='".intval($id_tarifu)."' ");
            $radku4 = $vysledek4->num_rows;
        
            if($radku4==0) echo "Nelze zjistit tarif";
            else 
            {
                while( $zaznam4=$vysledek4->fetch_array() )
                { echo $zaznam4["jmeno_tarifu"]." (".$zaznam4["zkratka_tarifu"].") "; }
            }
        
            echo '<br>
            <b>Povolet NET</b>: ';
            if ($dov_net == 2 ) { echo "Ano"; } else { echo "Ne"; }
            echo '<br>
            <br>
            <b>MAC </b>: ' . $mac . '<br> 
            <br>
            <b>Poznámka</b>: ' . $pozn . '<br>
            <b>Přípojný bod</b>:';

            $vysledek3 = $conn_mysql->query("SELECT jmeno,id FROM nod_list WHERE id='".intval($selected_nod)."'");
            $radku3 = $vysledek3->num_rows;

            if($radku3==0) echo "Nelze zjistit ";
            else 
            {
                while ($zaznam3=$vysledek3->fetch_array() )
                { echo $zaznam3["jmeno"]." (".$zaznam3["id"].") ".''; }
            }
            
            echo "<br><br><b>Šikana: </b>"; 
            if( $sikana_status==2) 
            { 
            echo "Ano"; 

            echo "<br><b>Šikana - počet dní: </b>".$sikana_cas;
            echo "<br><b>Šikana - text: </b>".$sikana_text;
            } 
            elseif($sikana_status==1){ echo "Ne"; }
            else { echo "Nelze zjistit"; }

        endif; 
    }

    private function actionForm()
    {
        echo '
        <form name="form1" method="post" action="" >
        <input type="hidden" name="send" value="true" >
        <input type="hidden" name="update_id" value="'.intval($this->update_id).'" >';

        echo '<table border="0" width="100%" >
            
            <tr>
            <td><span style="font-weight: bold; font-size: 18px; color: teal;" >Mód:</span></td>
            <td >
            <select size="1" name="mod_objektu" onChange="self.document.forms.form1.submit()" >
                <option value="1" style="color: #CC0033;" ';
                if($this->mod_objektu == 1) echo " selected "; echo ' >Bezdrátová síť</option>
                <option value="2" style="color: #e37d2b; font-weight: bold;" ';
                if($this->mod_objektu == 2) echo " selected "; echo ' >Optická síť</option>
            </select>  
            </td>
            </tr>

            <tr><td colspan="4" ><br></td></td>
            
            <tr>
            <td width="170px" >dns záznam:</td>
            <td width="380px" ><input type="Text" name="dns" size="30" maxlength="50" value="'.$dns.'" ></td>

            <td width="" >Přípojný bod - hledání:</td>
            <td width="" ><input type="Text" name="nod_find" size="30" value="'.$nod_find.'" ></td>

            </tr>

            <tr><td colspan="4" ><br></td></td>

            </tr>

            <tr>
            <td>typ ip adresy:</td>
            <td width="" >
                    <table border="0">
                <tr>
                <td>
                <input type="radio" name="typ_ip" onChange="self.document.forms.form1.submit()" value="1" 
                <?php if ( ( $typ_ip==1 or (!isset($typ_ip)) ) ) { echo "checked"; } ?> >
                <label>Neveřejná </label>
                
                <!--
                <input type="radio" name="typ_ip" onchange="self.document.forms.form1.submit()" value="2" 
                <?php if($typ_ip==2 ) { echo " checked "; } ?> >
                -->
                
                <span style="padding-left: 5px; padding-right: 5px;"> | </span>
                <span style="padding-right: 10px;">Veřejná </span>
                </td>
                <td> 
                <select size="1" name="typ_ip" onchange="self.document.forms.form1.submit()" >';
                    echo '<option value="1" class="select-nevybrano" '; if($typ_ip==1 ) { echo " selected "; } echo ' >vyberte typ</option>
                <option value="2" '; if($typ_ip==2 ) { echo " selected "; } echo ' >default - routovaná</option>';
                
                if( ($update_id > 0) and ($typ_ip==3) )
                {
                echo "<option value=\"3\"";
                if($typ_ip==3 ) { echo " selected "; }
                echo " >překládaná - snat/dnat</option> "; 
                }
                echo '
                <option value="4" '; if($typ_ip==4 ) { echo " selected "; } echo ' >tunelovaná - l2tp tunel</option>
                </select>
                </td>
                </tr>
                </table>
                                    
                <input type="hidden" name="vip_rozsah" value="1" >

            </td>
                
            <td><label> Přípojný bod: </label></td>
                <td>';
            
            $sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$nod_find%' ";
            $sql_nod .= " OR ip_rozsah LIKE '%$nod_find%' OR adresa LIKE '%$nod_find%' ";
            $sql_nod .= " OR pozn LIKE '%$nod_find%' ) AND ( typ_nodu = '1' ) ORDER BY jmeno ASC ";

            $vysledek = $this->conn_mysql->query($sql_nod);
            $radku=$vysledek->num_rows;
            
            print '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

            if($typ_ip==4)
            {
                echo "<option value=\"572\" selected > verejne_ip_tunelovane ( 212.80.82.160 ) </option>"; 
            }	
            elseif( ($radku==0) )
            { 
                echo "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>"; 
            }
            else
            {
                echo '<option value="0" style="color: gray; font-style: bold; "';
                if( ( $_POST["selected"] == 0 ) or ( (!isset($selected_nod)) ) ) { echo "selected"; }
                echo ' > Není vybráno</option>';

                while ($zaznam2=$vysledek->fetch_array() )
                    {
                        echo '<option value="'.$zaznam2["id"].'"';
                        if ( ( $selected_nod == $zaznam2["id"]) ){ echo " selected "; }
                        echo '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
                    } //konec while
                } //konec else
                
            print '</select>';
                                                                                                                                                                
            echo '<input type="button" value="Generovat / hledat (nody)" name="G" onClick="self.document.forms.form1.submit()" >
                    </td>
                    
            </tr>
            
            <tr><td colspan="4" ><br></td></tr>
                                                        
            <tr>
                <td>ip adresa:</td>
                <td><input type="Text" name="ip" size="30" maxlength="20" value="'.$ip.'" >';
                //global $ip_error;
                if($ip_error == 1) 
                { 
                echo "<img title=\"error\" width=\"20px\" src=\"img2/warning.gif\" align=\"middle\" ";
                echo "onclick=\" window.open('objekty-vypis-ip.php?id_rozsah=".$ip_rozsah."'); "."\">";
                } 
                
                echo '</td>
                <td>';

                if($typ_ip == 3)
                {
                echo "<label> Lokální adresa k veřejné: </label>";	
                }
                elseif($typ_ip==4)
                {
                echo "Přihlašovací údaje 
                    <span style=\"font-size: 11px;\">(k tunelovacímu serveru): </span>";
                }
                else
                { echo "<span style=\"color: gray; \" >Není dostupné </span>"; }
                
                echo '
                </td>
                <td>';
                
                /*
                if ( $typ_ip == 3)
                {
                $vysledek2=pg_query("select * from objekty where typ != 3 AND verejna=99 ORDER BY dns_jmeno ASC" );
                        $radku2=pg_num_rows($vysledek2);

                        if ($radku==0) { echo "žádné objekty v databázi "; }
                        else
                        {
                        print '<select size="1" name="vip_snat_lip" onChange="self.document.forms.form1.submit()" >';
                        print '<option value="0" style="color: gray; font-style: bold; "';

                        if ( ( $_POST["vip_snat_lip"] == 0 ) or ( (!isset($vip_snat_lip)) ) ) { echo "selected"; }
                        echo ' > Není vybráno</option>';

                        while ($zaznam3=pg_fetch_array($vysledek2) ):

                            echo '<option value="'.$zaznam3["ip"].'"';
                            if( ( $vip_snat_lip == $zaznam3["ip"]) ){ echo " selected "; }
                            echo '>'." ".$zaznam3["dns_jmeno"]." ( ".$zaznam3["ip"]." )".'</option>'." \n";

                        endwhile;
            
                }
                }
                else
                */
                
                if($typ_ip==4)
                {
                echo "<span style=\"padding-right: 10px; padding-left: 5px;\">login:</span>".
                    "<input type=\"text\" name=\"tunnel_user\" size=\"6\" maxlength=\"4\" value=\"".$tunnel_user."\" >".
                
                "<span style=\"padding-left: 10px; padding-right: 5px\">heslo: </span>".
                
                "<input type=\"text\" name=\"tunnel_pass\" size=\"6\" maxlength=\"4\" value=\"".$tunnel_pass."\" >";
                
                }	
                else
                { echo "<span style=\"color: gray; \" >Není dostupné </span>"; }
                
                echo '
                </td>
            </tr>
                        
            
            <tr><td colspan="4" ><br></td></tr>
            
            <tr>
                <td>mac adresa: <div style="font-size: 12px;">(prouze pro DHCP server/y)</div></td>
                <td>';

                if($typ_ip==4)
                { echo "<span style=\"color: gray; \" >Není dostupné </span>"; }
                else
                { echo "<input type=\"text\" name=\"mac\" maxlength=\"17\" value=\"".$mac."\">"; }
            echo '
                </td>
            
                <td>&nbsp;</td>
                <td>&nbsp;</td>	
            </tr>
                                                        
            <tr><td colspan="4"><br></td></tr>

            <tr>
            <td>ip klientského zařízení: </td>
            <td>';
                if( ($typ_ip <> 3) and ($typ_ip != 4) )
                { echo "<input type=\"text\" name=\"client_ap_ip\" value=\"".$client_ap_ip."\" > "; }
                else
                { echo "<span style=\"color: gray; \">není dostupné</span>"; }
            echo '
            </td>
                <td>Povolen NET:</td>
                <td>';
                        
                if( ($typ==3) or ($typ_ip == 3) )
                { 
                if( $typ_ip ==3){ echo "<input type=\"hidden\" name=\"dov_net\" value=\"2\" >"; }
                echo "<div class=\"objekty-not-allow\">není dostupné</div>"; 
                }
                else
                {
                echo "<input type=\"radio\" name=\"dov_net\" value=\"2\""; if ( ( $dov_net==2 or (!isset($dov_net)) ) ) { echo "checked"; } echo ">";
                echo "<label>Ano | </label>";
                            
                echo "<input type=\"radio\" name=\"dov_net\" value=\"1\""; if ( $dov_net==1 ) { echo "checked"; } echo ">";
                echo "<label> Ne</label>";
                        
                }
                echo "</td>";
                
            echo '   
            </tr>
            <tr><td colspan="4" ><br></td></tr>
            
            <tr>
            <td>Typ:</td>
            <td>
            
            <select name="typ" onChange="self.document.forms.form1.submit()" >
                    <option value="1" '; if ( $typ == 1) { echo " selected "; } echo ' >poc (platici)</option>
                    <option value="2" '; if ( $typ == 2) { echo " selected "; } echo ' >poc (free)</option>
                    <option value="3" '; if ( $typ == 3) { echo " selected "; } echo ' >AP</option>
            </select>
            
            </td>
            <td>Šikana: </td>
            <td>';
            
            if ($typ==3 or $typ_ip==3 )
            { 
                echo "<div class=\"objekty-not-allow\">není dostupné</div>"; 
            }
            else
            {
                echo "<select name=\"sikana_status\" size=\"1\" onChange=\"self.document.forms.form1.submit()\"> \n";
                echo "<option value=\"1\" "; if ( ( $sikana_status==1 or (!isset($sikana_status) ) ) ) { echo " selected "; } echo ">Ne</option> \n";	    
                echo "<option value=\"2\" "; if ( $sikana_status==2 ) { echo " selected "; } echo ">Ano</option> \n";
                echo "</select>";
            }
            
            echo '
                </td>
                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>	
                <td style="" >Tarif:</td>
                <td>';
                
            if( !isset($id_tarifu) )
            {
            if( $typ==3 ){ $find_tarif = "2"; } //ap-cko ...
            elseif( $typ_ip==3 ) //snat/dnat verejka ...
            { $find_tarif = "2"; }
            elseif( $garant == 2 ) //garant linka ...
            { } //.
            elseif( $tarif == 1 )  // asi SmallCity
            {  $find_tarif = "1"; }
            elseif( $tarif == 2 )  // Mp linka
            { $find_tarif = "0"; }
            else
            { $find_tarif = "0"; }
            }
            
            echo "<select name=\"id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

            //echo "<option value=\"\" class=\"select-nevybrano\" >Nevybráno</option>";
            $dotaz_t2 = $this->conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '0' ORDER BY zkratka_tarifu ");
            
            while( $data_t2 = $dotaz_t2->fetch_array() )
            { 
            echo "<option value=\"".$data_t2["id_tarifu"]."\" ";
                    
            if( isset($find_tarif) )
            { if( ( $find_tarif == $data_t2["id_tarifu"] ) ){ echo " SELECTED "; } }
            else
            { 
                if( $id_tarifu == $data_t2["id_tarifu"] ){ echo " SELECTED "; } 
            }
            
            echo " >".$data_t2["zkratka_tarifu"];
            echo " (".$data_t2["jmeno_tarifu"]." :: ".$data_t2["speed_dwn"]."/".$data_t2["speed_upl"]." )</option> \n"; 
            }      
            
            echo "</select>";
            
            echo '</td>';

            echo "<td>Šikana - počet dní: </td>
                <td>";
            
            if( ( $typ==3 or ($sikana_status!=2) ) )
            { 
                echo "<div class=\"objekty-not-allow\" >není dostupné</div>"; 
                echo "<input type=\"hidden\" name=\"sikana_cas\" value=\"".$sikana_cas."\">";
            }
            else
            { echo "<input type=\"text\" name=\"sikana_cas\" size=\"5\" value=\"".$sikana_cas."\" >"; }
            
            echo '	    
            </td>
            </tr>

                <tr><td colspan="4" ><br></td></tr>							       

                <tr>
                    <td><label> poznámka:  </label></td>
                    <td>
                        <textarea name="pozn" cols="30" rows="6" wrap="soft">' . $pozn . '</textarea>
                    </td>
                    
                    <td><label>Šikana - text: </label></td>
                    <td>';

            if( ( $typ ==3 or ($sikana_status!=2) ) ) 
            { 
                echo "<div class=\"objekty-not-allow\" >není dostupné</div>"; 
                echo "<input type=\"hidden\" name=\"sikana_text\" value=\"".$sikana_text."\" >";
            }
            else 
            { echo "<textarea name=\"sikana_text\" cols=\"30\" rows=\"4\" wrap=\"soft\" >".$sikana_text."</textarea>";  }
            
            echo '
                    </td>
                </tr>

                <tr><td colspan="4" ><br></td></tr>

                <tr>
                    <td colspan="2" align="center">	
                    <hr>
                    <input name="odeslano" type="submit" value="OK">
                    </td>
                    <td colspan="2" >
                    <br>
                    </td>
                </tr>
                    
                </table>
                </form>';
    }
}