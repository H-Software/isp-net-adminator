<?php

namespace App\Core;

use App\Models\ArchivZmen as Model;
use Psr\Container\ContainerInterface;

class ArchivZmen {

    var $conn_mysql;
    var $smarty;
    var $logger;

    var $db_table_name = 'archiv_zmen';

    public function __construct(ContainerInterface $container, $smarty)
    {
        $this->conn_mysql = $container->connMysql;
        $this->logger = $container->logger;
        $this->smarty = $smarty;
        
        $this->logger->info("archivZmen\__construct called");
    }
    
    function mutateStbParams(array $dataOrig, array $dataUpdated)
    {
        $pole3 = "";

        foreach($dataOrig as $key => $val)
        {
         
         if( !($dataUpdated[$key] == $val) )
         {
           if( !($key == "id_stb") )
           {
               if( $key == "ip_adresa" )
               {
                 $pole3 .= "změna <b>IP adresy</b> z: ";
                 $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>";
                 $pole3 .= ", ";
               } //konec key == ip
               elseif( $key == "mac_adresa" )
               {
                 $pole3 .= "změna <b>MAC adresy</b> z: ";
                 $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>";
                 $pole3 .= ", ";
               } //konec key == mac
               elseif( $key == "sw_port" )
               {
                 $pole3 .= "změna <b>Čísla portu (ve switchi)</b> z: ";
   
                 if( $val == "a"){ $pole3 .= "<span class=\"az-s1\">Ano</span> na: <span class=\"az-s2\">Ne</span>"; }
                 elseif( $val == "n"){ $pole3 .= "<span class=\"az-s1\">Ne</span> na: <span class=\"az-s2\">Ano</span>"; }
                 else{ $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>"; }
   
                 $pole3 .= ", ";
               } //konec key == sw_port
                 elseif($key == "id_nodu")
               {
                 $pole3 .= "změna <b>Přípojného bodu</b> z: ";
   
                 $vysl_t1=$this->conn_mysql->query("SELECT jmeno FROM nod_list WHERE id = '".intval($val)."'");
                 while ($data_t1=$vysl_t1->fetch_array() )
                 { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno"]."</span>"; }
   
                 $pole3 .= " na: ";
   
                 $val2 = $dataUpdated[$key];
   
                 $vysl_t2=$this->conn_mysql->query("select jmeno FROM nod_list WHERE id = '$val2'" );
                 while ($data_t2=$vysl_t2->fetch_array() )
                 { $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno"]."</span>"; }
   
                 $pole3 .= ", ";                                                                                                                 
               } // konec key == id_nodu
               elseif( $key == "id_tarifu" ){
                 $pole3 .= "změna <b>Tarifu</b> z: ";
   
                 $vysl_t1=$this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($val)."'");
                 while ($data_t1=$vysl_t1->fetch_array() )
                 { $pole3 .= "<span class=\"az-s1\">".$data_t1["jmeno_tarifu"]."</span>"; }
   
                 $pole3 .= " na: ";
   
                 $val2 = $dataUpdated[$key];
   
                 $vysl_t2 = $this->conn_mysql->query("SELECT jmeno_tarifu FROM tarify_iptv WHERE id_tarifu = '".intval($val2)."'");
                 while ($data_t2=$vysl_t2->fetch_array() )
                 { $pole3 .= "<span class=\"az-s2\">".$data_t2["jmeno_tarifu"]."</span>"; }
   
                 $pole3 .= ", ";                                                                                                                 
               
               } //konec key == id_tarifu
               elseif( $key == "pozn" )
               {
                    $pole3 .= "změna <b>Poznámky</b> z: ";
                    $pole3 .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>";
                    $pole3 .= ", ";
               } //konec key == pozn
               else
               { // ostatni mody, nerozpoznane
                 $pole3 .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                 $pole3 .= "na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>, ";
               }
   
            } //konec if nejde-li od id_komplu ( to v tom poli neni )
          } // konec if obj == val
        } // konec foreach

        return $pole3;
    }

    function getActionType($actionType, $itemId = NULL)
    {
        if($actionType == 1){
            return "<b> akce: pridani fakt. skupiny; </b><br>";
        }
        elseif($actionType == 2){
            $r .= "<b>akce: uprava fakturacni skupiny; </b><br>";
            $r .= "[id_fs] => " . $itemId;
            $r .= " diferencialni data: ";

            return $r;
        }
        elseif($actionType == 3){
            $r .= "[id_stb]=> " . $itemId . ",";
            $r .= " diferencialni data: ";

            return $r;
        }
        else{
            return false;
        }
    }
    function insertItem(int $actionType, array $actionData, int $actionResult = 0, string $loggedUserEmail = NULL)
    {

        $actionBody = $this->getActionType($actionType);

        foreach ($actionData as $c => $v) {
            $actionBody .= "[$c]=> $v, ";
        }  
         
        $this->logger->info("archivZmen\insertItem: dump actionBody: " . var_export($actionBody, true));

        $item = Model::create([
            'akce' => $actionBody,
            'vysledek' => $actionResult,
            'provedeno_kym' => $loggedUserEmail
        ]);

        return $item;
    }

    function insertItemDiff(int $actionType, array $dataOrig, array $dataUpdated, ...$args)
    {
        $this->logger->info("Archiv-Zmen\insertItemDiff called");
        $this->logger->info("Archiv-Zmen\insertItemDiff: mode: ". $actionType . "");

        $actionBody = $this->getActionType($actionType, $args[0]['itemId']);

        if($actionType == 3)
        {
            $actionBody .= $this->mutateStbParams($dataOrig, $dataUpdated);
        }
        else
        {
            foreach($dataOrig as $key => $val)
            {
                if( !($dataUpdated[$key] == $val) )
                {
                    if( $key == "pozn" )
                    {
                        $actionBody .= "změna <b>Poznámky</b> z: ";
                        $actionBody .= "<span class=\"az-s1\">".$val."</span> na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>";
                        $actionBody .= ", ";
                    } //konec key == pozn
                    else
                    { // ostatni mody, nerozpoznane
                        $actionBody .= "změna pole: <b>".$key."</b> z: <span class=\"az-s1\" >".$val."</span> ";
                        $actionBody .= "na: <span class=\"az-s2\">".$dataUpdated[$key]."</span>, ";
                    } //konec else
                } // konec if key == val
            } // konec foreach
        }

        $this->logger->info("archivZmen\insertItemDiff: dump actionBody: " . var_export($actionBody, true));

        $item = Model::create([
            'akce' => $actionBody,
            'vysledek' => $args[0]['actionResult'],
            'provedeno_kym' => $args[0]['loggedUserEmail']
        ]);

        return $item;
    }

    function archivZmenList()
    {
        $output = "";

        $pocet = intval($_GET["pocet"]);

        if( strlen($_GET["id"]) > 0 ) {
        $id = intval($_GET["id"]);
        }
        
        if( strlen($_GET["id_nodu"]) > 0 ) {
        $id_nodu = intval($_GET["id_nodu"]);
        }

        if( strlen($_GET["id_stb"]) > 0 ) {
        $id_stb = intval($_GET["id_stb"]);
        }
        
        if( strlen($_GET["id_cloveka"]) > 0 ) {
        $id_cloveka = intval($_GET["id_cloveka"]);
        }
        
        if( strlen($_GET["id_routeru"]) > 0 ) {
        $id_routeru = intval($_GET["id_routeru"]);
        }
        
        $typ = intval($_GET["typ"]);

        $id_objektu = $id;
    
        $output .= "<div style=\"padding-left: 5px; padding-top: 10px; \">\n";
        
        $output .= "<div style=\" padding-bottom: 10px; padding-right: 40px; font-size: 18px; font-weight: bold; float: left; \" >\n";
        $output .= " Archiv změn</div>\n";
    
        $output .= "<div style=\" \" ><form method=\"GET\" action=\"\" >\n";
        
        $output .= "<span style=\"margin-right: 20px; \" >
                        <label>Vyberte počet záznamů: </label>
                    </span>
        
                    <select name=\"pocet\" size=\"1\" >
                        <option value=\"50\" "; if ($pocet == "50" or !isset($pocet) ){ $output .= " selected "; } $output .= " >50</option>
                        <option value=\"100\" "; if( $pocet == "100" ){ $output .= " selected "; } $output .= " >100</option>
                        <option value=\"150\""; if( $pocet == "150" ){ $output .= " selected "; } $output .= " >150</option>
                        
                        <option value=\"1000\""; if( $pocet == "500" ){ $output .= " selected "; } $output .= " >500</option>
                        
                    </select>\n";
        
        if( !isset($id_nodu) and !isset($id) and !isset($id_stb) and !isset($id_cloveka) and !isset($id_routeru) )
        {
        
        $output .= "<span style=\"margin-right: 20px; margin-left: 20px; \">Typ záznamů:</span>\n";

        $output .= "<select name=\"typ\" size=\"1\" style=\"max-width: 300px;\">
            <option value=\"0\" "; if ($typ == "0" or !isset($typ) ){ $output .= " selected "; } $output .= " >Vše</option>
        
            <optgroup label=\"objekty\" >
        
            <option value=\"1\" "; if( $typ == "1" ){ $output .= " selected "; } $output .= " >akce: úprava objektu</option>
            <option value=\"2\""; if( $typ == "2" ){ $output .= " selected "; } $output .= " >akce: přidání objektu</option>
            <option value=\"3\""; if( $typ == "3" ){ $output .= " selected "; } $output .= " >akce: smazáni objektu</option>
        
            <option value=\"4\""; if( $typ == "4" ){ $output .= " selected "; } $output .= " >akce: úprava stb objektu</option>
            <option value=\"5\""; if( $typ == "5" ){ $output .= " selected "; } $output .= " >akce: přidání stb objektu</option>
            <option value=\"6\""; if( $typ == "6" ){ $output .= " selected "; } $output .= " >akce: smazaní stb objektu</option>
            
            <optgroup label=\"vlastníci\" >
        
            <option value=\"7\""; if( $typ == "7" ){ $output .= " selected "; } $output .= " >akce: úprava vlastníka</option>
            <option value=\"8\""; if( $typ == "8" ){ $output .= " selected "; } $output .= " >akce: přidáni vlastníka</option>
        
            <option value=\"9\""; if( $typ == "9" ){ $output .= " selected "; } $output .= " >akce: přidáni fakturační adresy</option>
            <option value=\"10\""; if( $typ == "10" ){ $output .= " selected "; } $output .= " >akce: úprava fakturační adresy</option>
            <option value=\"11\""; if( $typ == "11" ){ $output .= " selected "; } $output .= " >akce: smazání fakturační adresy</option>
        
        <!-- akce: poslani emailu z duvodu neplaceni -->
        
        <!-- akce: poslani SMS z duvodu neplacení -->
        
            <optgroup label=\"obojí (objekty i vlastníci)\" >
        
            <option value=\"12\""; if( $typ == "12" ){ $output .= " selected "; } $output .= " >akce: přiřazení objektu k vlastníkovi</option>
            <option value=\"13\""; if( $typ == "13" ){ $output .= " selected "; } $output .= " >akce: prirazeni objektu typu STB k vlastnikovi</option>

            <option value=\"14\""; if( $typ == "14" ){ $output .= " selected "; } $output .= " >akce: odrazeni objektu (od vlastníka)</option>
            <option value=\"15\""; if( $typ == "15" ){ $output .= " selected "; } $output .= " >akce: odparovani stb objektu (od vlastníka)</option>

            <option value=\"25\""; if( $typ == "25" ){ $output .= " selected "; } $output .= " >akce: zakazani netu z duvodu sikany</option>

        <!-- akce: automaticke nastaveni sikany z duvodu neuhr. faktur -->
        
            <optgroup label=\"topologie - routery \" >

            <option value=\"17\""; if( $typ == "17" ){ $output .= " selected "; } $output .= " >akce: přidání routeru</option>
            <option value=\"18\""; if( $typ == "18" ){ $output .= " selected "; } $output .= " >akce: úprava routeru</option>
            <option value=\"19\""; if( $typ == "19" ){ $output .= " selected "; } $output .= " >akce: smazání routeru</option>

            <optgroup label=\"topologie - nody/lokality \" >

            <option value=\"20\""; if( $typ == "20" ){ $output .= " selected "; } $output .= " >akce: přidání nodu</option>
            <option value=\"21\""; if( $typ == "21" ){ $output .= " selected "; } $output .= " >akce: úprava nodu</option>
            <option value=\"22\""; if( $typ == "22" ){ $output .= " selected "; } $output .= " >akce: smazání nodu</option>

            <optgroup label=\"monitoring - grafy \" >

            <option value=\"23\""; if( $typ == "23" ){ $output .= " selected "; } $output .= " >akce: přidání/změna grafu</option>
            <option value=\"24\""; if( $typ == "24" ){ $output .= " selected "; } $output .= " >akce: smazání grafu</option>

            <optgroup label=\"ostatní (prostě zbytek)\" >

            <option value=\"16\""; if( $typ == "16" ){ $output .= " selected "; } $output .= " >akce: požadavek na restart</option>
                
    <!--	    	
            <option value=\"\""; if( $typ == "" ){ $output .= " selected "; } $output .= " >akce: pridani hotovostni platby</option>

            akce: pridani opravy
            
            akce: voip - pridani klienta (customer)
    -->
            
        </select>\n";
        
        }
        
        $output .= "<span style=\"margin-left: 10px; \"><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></span>
        <span style=\"margin-left: 40px; \">";
        $output .=  '<!--<a href="include\export-archiv-zmen.php">-->export dat<!--</a>-->';
        $output .= "</span>
        
        </form></div>\n\n";
    
        $output .= "</div>\n"; //konec hlavni divu
        
        $zaklad_sql = "select *,DATE_FORMAT(provedeno_kdy, '%d.%m.%Y %H:%i:%s') as provedeno_kdy2 from archiv_zmen ";
        
        if($typ > 0)
        {   
        
            if($typ == 0){
                $type_select = "";
            }
            elseif($typ == 1){
                $type_select = "WHERE (akce LIKE '<b>akce: uprava objektu; </b>%') ";
            }
            elseif($typ == 2){
                $type_select = "WHERE (akce LIKE '<b> akce: pridani objektu ; </b>%') ";
            }
            elseif($typ == 3){
                $type_select = "WHERE (akce LIKE '<b>akce: smazani objektu;</b>%') ";
            }
            elseif($typ == 4){
                $type_select = "WHERE (akce LIKE '<b>akce: uprava stb objektu; </b>%') ";
            }
            elseif($typ == 5){
                $type_select = "WHERE (akce like '<b> akce: pridani stb objektu ; </b>%') ";
            }
            elseif($typ == 6){
                $type_select = "WHERE (akce LIKE '<b> akce: smazani stb objektu ; </b>%') ";
            }
            elseif($typ == 7){
                $type_select = "WHERE (akce LIKE '<b>akce: uprava vlastnika; </b>%') ";
            }
            elseif($typ == 8){
                $type_select = "WHERE (akce LIKE '<b>akce: pridani vlastnika ; </b>%') ";
            }
            elseif($typ == 9){
                $type_select = "WHERE (akce LIKE '<b>akce: pridani fakturacni adresy;</b>%') ";
            }
            elseif($typ == 10){
                $type_select = "WHERE ( ".
                        "akce LIKE '<b>akce</b>: uprava fakturacni adresy%' ".
                        " OR ".
                        "akce LIKE ' akce: uprava fakturacni adresy%' ".
                        ")";
            }
            elseif($typ == 11){
                $type_select = "WHERE ( ".
                        "akce LIKE ' akce: smazani fakturacni adresy ;%' ".
                        ")";
            }
            elseif($typ == 12){
                $type_select = "WHERE ( ".
                        "akce LIKE ' prirazeni objektu%' ".
                        " OR ".
                        "akce LIKE '<b>akce: prirazeni objektu k vlastnikovi; </b>%' ".
                        ")";
            }
            elseif($typ == 13){
                $type_select = "WHERE akce LIKE '<b>akce: prirazeni objektu typu STB k vlastnikovi; </b>%' ";
            }
            elseif($typ == 14){
                $type_select = "WHERE ( ".
                            "akce LIKE ' odrazeni objektu%' ".
                            " OR ".
                            "akce LIKE '<b>akce: odrazeni objektu; </b>%' ".
                        ")";
            }
            elseif($typ == 15){
                $type_select = "WHERE (akce LIKE '<b> akce: odparovani stb objektu ; </b>%') ";
            }
            elseif($typ == 16){
                $type_select = "WHERE ( ".
                            "akce LIKE '<b>akce: požadavek na restart;</b>%' ".
                            " OR ".
                            "akce LIKE '<b>akce:</b> požadavek na restart;<br>%' ".
                        " ) ";
            }
            elseif($typ == 17){
                $type_select = "WHERE ( ".
                            "akce LIKE '<b>akce: pridani routeru;</b>%' ".
                            " OR ".
                            "akce LIKE ' akce: pridani routeru ;%' ".
                        " ) ";
            }
            elseif($typ == 18){
                $type_select = "WHERE ( ".
                            "akce LIKE ' akce: uprava routeru ;%' ".
                            " OR ".
                            "akce LIKE '<b>akce: uprava routeru;</b>%' ".
                        " ) ";
            }
            elseif($typ == 19){
                $type_select = "WHERE akce LIKE '<b>akce: smazání routeru;</b>%' ";
            }
            elseif($typ == 20){
                $type_select = "WHERE akce LIKE '<b>akce: pridani nodu ; </b>%' ";
            }
            elseif($typ == 21){
                $type_select = "WHERE akce LIKE '<b>akce: uprava nodu;</b>%' ";
            }
            elseif($typ == 22){
                $type_select = "WHERE akce LIKE '<b>akce: smazání lokality / nodu; </b>%' ";
            }
            elseif($typ == 23){
                $type_select = "WHERE ( ".
                            "akce LIKE ' pridani/zmena  grafu%' ".
                            " OR ".
                            "akce LIKE '<b>akce: pridani/zmena  grafu;</b>%' ".
                        " ) ";
            }
            elseif($typ == 24){
                $type_select = "WHERE ( ".
                            "akce LIKE ' akce: smazani grafu ;%'".
                            " OR ".
                            "akce LIKE '<b>akce: smazani grafu;</b>%' ".
                        " ) ";
            }
            elseif($typ == 25){
                $type_select = "WHERE ( ".
                            "akce LIKE 'akce: zakazani netu z duvodu sikany %' ".
                            " OR ".
                            "akce LIKE '<b>akce: zakazani netu z duvodu sikany;</b>%' ".
                        " ) ";
            }
        
            $sql_result = $zaklad_sql." ".$type_select." ORDER BY id DESC "; 
        
        }
        elseif( $id > 0 )
        { 
            $sql_result = $zaklad_sql." WHERE ( ".
                            " akce LIKE '%[id_komplu]=> ".$id."%' ".
        					// " OR ".
        					// " akce LIKE '%[id_komplu]=> ".$id." ,%' ".
                            " ) ORDER BY id DESC ";
        }
        elseif($id_cloveka > 0)
        {
        
            $id_cloveka_sql = " where ( ( akce LIKE '%[id_cloveka]=> ".$id_cloveka." ,%' AND akce NOT LIKE '%[id_komplu]%' ) ";
            $id_cloveka_sql .= " OR ( akce LIKE '%[id_cloveka] => ".$id_cloveka." ,%' ) OR ( akce LIKE ";
            $id_cloveka_sql .= " '%[id_vlastnika] => ".$id_cloveka."%' ) OR ".
                        " ( (akce LIKE '%[id_vlastnika]=> ".$id_cloveka."%') AND (akce LIKE '%prirazeni objektu k vlastnikovi%') )".
                        " OR (akce LIKE '%[id_cloveka] => ".$id_cloveka."%') ) ";
            
            $sql_result = $zaklad_sql.$id_cloveka_sql." ORDER BY id DESC ";
            
        }
        elseif($id_stb > 0){
            
            $sql_stb = " WHERE ( ".
            
                    " ( (akce LIKE '%uprava stb objektu%') AND ( akce LIKE '%[id_stb]=> ".$id_stb.",%') ) ".
                    " OR ".
                    " ( ( akce LIKE '%typu STB%') AND ( akce LIKE '%[id_stb]=> ".$id_stb.",%') ) ".
                    " OR ".
                    " ( (akce LIKE '%odparovani stb objektu%') AND ( akce LIKE '%<b>[id_stb]</b> => ".$id_stb." %' ) ) ".
                    " OR ".
                    " ( (akce LIKE '%pridani stb objektu%') AND ( akce LIKE '%[id_stb]=> ".$id_stb.",%' ) ) ".
                    ") ORDER BY id DESC ";
        
            $sql_result = $zaklad_sql.$sql_stb;
        }
        elseif($id_nodu > 0)
        {

            $idnodu_select = " WHERE ( ".
                        " akce LIKE '% uprava nodu;%[id_nodu] => ".$id_nodu." %' ".
        //			    " OR ".
        //			    " akce LIKE '' ".
                    " ) ORDER BY id DESC ";

            $sql_result = $zaklad_sql.$idnodu_select;
        
        }
        elseif($id_routeru > 0){

            $idrouteru_select = " WHERE ( ".
                        "akce LIKE '<b>akce: uprava routeru;</b><br> [id_routeru] => <a href=\"topology-router-list.php\">".$id_routeru."</a>%' ".

    //			    " OR ".
    //			    " akce LIKE '' ".
                " ) ORDER BY id DESC ";

            $sql_result = $zaklad_sql.$idrouteru_select;
            
        }
        else
        { 
            $sql_result = $zaklad_sql." order by id DESC "; 
        }
        
        if($pocet > 0)
        {
            $sql_result = $sql_result." LIMIT ".$pocet;
        }
        else{
            $sql_result = $sql_result." LIMIT 50";
        }
        
        $vysl = $this->conn_mysql->query($sql_result);
        
        if (!$vysl) {
            $output .= "<div style=\"color: red;\" >Chyba při provádění databázového dotazu </div>";
            return $output;
        }
        
        $radku = $vysl->num_rows;

        //ted zjistime jeslti je archiv 
        if( isset($id) )
        {
            $output .= "<div style=\"padding-left: 5px; \">";
            
            $output .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; font-size: 18px; \">";
            $output .= "Historie objektu: </div>";
        
            $dotaz_objekty=pg_query("SELECT dns_jmeno, ip, mac FROM objekty WHERE id_komplu = '".intval($id_objektu)."' ");
        
            if( (pg_num_rows($dotaz_objekty) == 1) )
            {
                while( $data_objekty=pg_fetch_array($dotaz_objekty) )
                {
                    $output .= "<div >dns jméno: <span style=\"color: grey;\">".$data_objekty["dns_jmeno"]."</span></div>";
                    $output .= "<div >ip adresa: <span style=\"color: grey;\">".$data_objekty["ip"]."</span></div>";
                    $output .= "<div >mac adresa: <span style=\"color: grey;\">".$data_objekty["mac"]."</span></div>";
                        
                    $id_vlastnika=$data_objekty["id_cloveka"];
                }
        
                $dotaz_vlastnik = pg_query("SELECT archiv, firma FROM vlastnici WHERE id_cloveka = '".intval($id_vlastnika)."' ");
                while($data_vlastnik = pg_fetch_array($dotaz_vlastnik) )
                {
                    $firma_vlastnik=$data_vlastnik["firma"];
                    $archiv_vlastnik=$data_vlastnik["archiv"];

                    $output .= "<div style=\"padding-top: 5px; \" >Detail vlastníka: ";
        
                    if($archiv_vlastnik == 1)
                    { $output .= "<a href=\"vlastnici-archiv.php?find_id=".$data_vlastnik["id_cloveka"]."\" >".$data_vlastnik["id_cloveka"]."</a> \n"; }
                    else //if( $firma_vlastnik == 1 )
                    { $output .= "<a href=\"vlastnici2.php?find_id=".$data_vlastnik["id_cloveka"]."\" >".$data_vlastnik["id_cloveka"]."</a> \n"; }
                    //else
                    //{ $output .= "<a href=\"vlastnici.php?find_id=".$data_vlastnik["id_cloveka"]."\" >".$data_vlastnik["id_cloveka"]."</a> \n"; }

                    $output .= "</div>";
                }

                        
                $output .= "<div style=\"padding-bottom: 20px; \"></div>";
            } // konec if pg_num_rows
        
            $output .= "</div>\n";
        }//konec if isset id
        
        if ( $radku==0 )
        { $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"margin-top: 15px; margin-bottom: 15px;\">Žádné změny v archivu</div>"; }
        else
        {
            $output .= "<table border=\"0\" cellpadding=\"5\" class=\"az-main-table table table-striped fs-6\" >\n";
            
            $output .= "<tr class=\"table-light\">\n";    
                $output .= "<td class=\"az-border2\" ><b>id</b></td>\n";
                $output .= "<td class=\"az-border2\" ><b>akce</b></td>\n";
                $output .= "<td class=\"az-border2\" ><b>Provedeno kdy</b></td>\n";
                $output .= "<td class=\"az-border2\" ><b>Provedeno kým</b></td>\n";
                $output .= "<td class=\"az-border2\" ><b>Provedeno úspěšně</b></td>\n";
            $output .= "</tr>\n";
            
            while ($data = $vysl->fetch_array() ):
            
                $output .= "<tr>\n";    
                $output .= "<td class=\"az-border1\" >".$data["id"]."</td>\n";
                $output .= "<td class=\"az-border1\" ><span class=\"az-text\" >\n";
        
                $id_cloveka_res = "";  
                $akce = $data["akce"];
        
                if(preg_match("/id_stb/",$akce)){
        
                    $pm = preg_match("/<b>\[id_stb\]<\/b>/",$akce);
            
                    if( ($pm == 1) ){    
                        $stb_string = "<b>[id_stb]</b> =>";
                    } 
                    else{
                        $stb_string = "[id_stb]=>";    
                    }
                
                    $pom = explode($stb_string, $akce);    
                    $pom2 = explode(" ", $pom[1]);
                    
                    //$id_stb = $pom2[1];
                    $id_stb = preg_replace("/,/", "", $pom2["1"]);
                    $id_stb = trim($id_stb);
            
                    // if( !($id_stb > 0) )
                    //    $id_stb = trim($pom2[2]);
                    
                    $id_stb_pom_rs = "<a class=\"fs-6\" href=\"/objekty/stb?id_stb=".$id_stb."\" >".$id_stb."</a>";
                    
                    $akce = preg_replace("/ ".$id_stb."/"," ".$id_stb_pom_rs, $akce);    
                    
                }
        
                if( preg_match("/prirazeni objektu k vlastnikovi/", $akce))
                {
                    $pomocne = explode(" ", $akce);
                    $id_komplu_pomocne = preg_replace("/,/", "", $pomocne[7]);
                    
                    $id_cloveka_pomocne = $pomocne[9];
                    
                    if( !($id_cloveka_pomocne > 0) ){
                        $id_cloveka_pomocne = $pomocne[10];
                    }
            
                    $dotaz_id_komplu=pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."'");
                    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                    { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
                    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
                    $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
                
                    $akce = ereg_replace($id_komplu_pomocne, $id_komplu_pomocne_rs, $akce);    
        
                    $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");
                    while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
                    { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }
                    if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici-archiv.php"; }
                    elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici2.php"; }
                    else{ $id_cloveka_res .= "<a href=\"vlastnici.php"; }

                    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >".$id_cloveka_pomocne."</a>";
                
                    $akce = ereg_replace($id_cloveka_pomocne, $id_cloveka_res, $akce);    
            
                }
                elseif( preg_match("/smazani objektu/", $akce))
                {
                    //nic no, ale musi to tu bejt, jinak se vyhodnocujou blbe ty porovnani dole	    
                }
                elseif( preg_match("/pridani objektu do \"nove\" garant. tridy/", $akce) )
                {
                    //nic no, ale musi to tu bejt, jinak se vyhodnocujou blbe ty porovnani dole	    
                    
                }
                /*
                elseif( ereg('pridani objektu', $akce) == true )
                {
                    $pomocne = explode(" ", $akce);    
                
                    //$output .= "i".$pomocne[8]."/i";
                    
                    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$pomocne[8];
                    $id_komplu_pomocne_rs .= "\" >".$pomocne[8]."</a>";
                        
                    $akce = ereg_replace($pomocne[8], $id_komplu_pomocne_rs, $akce);    
                    
                }
                */
                elseif(preg_match("/odrazeni objektu/", $akce)){
        
                    $pomocne = explode("[id_komplu]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );	    
                    $pomocne3 = explode("<br>", $pomocne2[1] );	    
                    $id_komplu_pomocne = trim($pomocne3[0]);

                    $dotaz_id_komplu = pg_query("SELECT dns_jmeno FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
                    
                    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                    { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
                
                    // TODO: fix replacing link for objekt
                    // $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
                    // $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
                        
                    // $akce = preg_replace("/".$id_komplu_pomocne."/", "".$id_komplu_pomocne_rs."", $akce);    
        
                }
                elseif( preg_match("/\[id_vlastnika\]/", $akce))
                {
                    $pomocne = explode("[id_vlastnika]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );
                    $id_cloveka_pomocne = trim($pomocne2[2]);
                    
                    if( !( $id_cloveka_pomocne > 0 ) )
                    { $id_cloveka_pomocne = $pomocne2[1]; }
            
                    $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");
                
                    while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
                    { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }

                    if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici-archiv.php"; }
                    elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici2.php"; }
                    else{ $id_cloveka_res .= "<a href=\"vlastnici.php"; }

                    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >".$id_cloveka_pomocne."</a>";
                        
                    $akce = preg_replace("/".$id_cloveka_pomocne."/", $id_cloveka_res, $akce);
            
                }
                elseif( preg_match("/\[id_cloveka\]/", $akce))
                {
                    $pomocne = explode("[id_cloveka]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );
                    $id_cloveka_pomocne = trim($pomocne2[2]);
            
                    if( !( $id_cloveka_pomocne > 0 ) )
                    { $id_cloveka_pomocne = $pomocne2[1]; }
                    
                    $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka_pomocne)."' ");
                
                    while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
                    { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }

                    if( $archiv_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici-archiv.php"; }
                    elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "<a href=\"vlastnici2.php"; }
                    else{ $id_cloveka_res .= "<a href=\"vlastnici.php"; }

                    $id_cloveka_res .= "?find_id=".$id_cloveka_pomocne."\" >".$id_cloveka_pomocne."</a>";
                        
                    $akce = ereg_replace($id_cloveka_pomocne, $id_cloveka_res, $akce);

                }
                elseif(preg_match("/uprava objektu/", $akce))
                {
        
                    $pomocne = explode("[id_komplu]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );	    
                    $id_komplu_pomocne = ereg_replace(",", "", $pomocne2[1]);
                    
                    $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
                
                    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                    { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
                    
                    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
                    $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
                        
                    $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
                }
                elseif(preg_match("/zakazani netu z duvodu sikany/", $akce))
                {
                    $pomocne = explode("[id_komplu]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );	    
                    $id_komplu_pomocne = ereg_replace(",", "", $pomocne2[1]);
        
                    if( is_numeric($id_komplu_pomocne) )
                    {
                        $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
                        
                        while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                        { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
                    
                        $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
                        $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
                        
                        $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
                    }
                }
                elseif(preg_match("/pridani objektu/", $akce))
                {
                    $pomocne = explode("[id_komplu]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );	    
                    $id_komplu_pomocne = $pomocne2[1];
                
                    //$id_komplu_pomocne = ereg_replace(",", "", $pomocne2[1]);
                
                    if( is_numeric($id_komplu_pomocne) )
                    {
                        $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
                        
                        while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                            { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
                    
                        $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
                        $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
                        
                        $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
                    }
            
                }
                elseif(preg_match("/uprava nodu/", $akce))
                {
                    $pomocne = explode("[id_nodu]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );	    
                    $id_nodu_pomocne = $pomocne2[2];
                    
                    if( ereg('^([[:digit:]]+)$',$id_nodu_pomocne) )
                    {
                    $dotaz_id_nodu = mysql_query("SELECT * FROM nod_list WHERE id = '".intval($id_nodu_pomocne)."' ");
                        
                    while($data_nod = mysql_fetch_array($dotaz_id_nodu) )
                    { $nazev_nodu = $data_nod["jmeno"]; }
                    
                    $id_nodu_rs = "<a href=\"topology-nod-list.php?find=".$nazev_nodu."&typ_nodu=0";
                    $id_nodu_rs .= "\" >".$id_nodu_pomocne."</a>";
                            
                    //$id_nodu_pomocne2 = "[id_nodu] => ".$id_nodu_pomocne;
                    $akce = ereg_replace(" ".$id_nodu_pomocne." ", " ".$id_nodu_rs." ", $akce);    
                    }
                }
                elseif(preg_match("/automaticke nastaveni sikany/", $akce))
                {
                    $pomocne = explode("[id_komplu]", $akce);    
                    $pomocne2 = explode(" ", $pomocne[1] );	    
                    $pomocne3 = explode("<br>", $pomocne2[1] );	    
                    $id_komplu_pomocne = trim($pomocne3[0]);
                    
                    $dotaz_id_komplu = pg_query("SELECT * FROM objekty WHERE id_komplu = '".intval($id_komplu_pomocne)."' ");
                        
                    while($data_kompl = pg_fetch_array($dotaz_id_komplu) )
                    { $data_kompl_dns = $data_kompl["dns_jmeno"]; }
            
                    $id_komplu_pomocne_rs = "<a href=\"objekty.php?dns_find=".$data_kompl_dns;
                    $id_komplu_pomocne_rs .= "\" >".$id_komplu_pomocne."</a>";
                        
                    $akce = ereg_replace("".$id_komplu_pomocne."", "".$id_komplu_pomocne_rs."", $akce);    
                }
                elseif(preg_match("/uprava routeru/", $akce)){
        
                    $pomocne = explode("[id_routeru]", $akce);    
                    $pomocne2 = explode(">", $pomocne[1] );	    
                    $pomocne3 = explode("<", $pomocne2[2] );	    
                    $id_routeru_pomocne = trim($pomocne3[0]);
                
                    $akce = ereg_replace("href=\"topology-router-list.php\"", "href=\"topology-router-list.php?f_id_routeru=".intval($id_routeru_pomocne)."&odeslano=OK\"", $akce);
                }
        
                $output .=  $akce."</span>\n</td>\n";
            
                $output .= "<td class=\"az-border1\"><span class=\"az-provedeno-kdy\" >";
                if ( ( strlen($data["provedeno_kdy2"]) < 1 ) ){ $output .= "&nbsp;"; }
                else{ $output .=  $data["provedeno_kdy2"]; }
                $output .= "</span></td>\n";
                
                $output .= "<td class=\"az-border1\"><span class=\"az-provedeno-kym\" >";
                if ( ( strlen($data["provedeno_kym"]) < 1 ) ){ $output .= "&nbsp;"; }
                else{ $output .=  $data["provedeno_kym"]; }
                $output .= "</span></td>\n";		   
            
                $output .= "<td class=\"az-border1\">";
                if ( $data["vysledek"] == 1 ){ $output .= "<span class=\"az-vysl-ano\">Ano</span>"; }
                else{ $output .= "<span class=\"az-vysl-ne\">Ne</span>"; }
                $output .= "</td>\n";
            
                $output .= "</tr>\n\n";
            
            endwhile;
            
            $output .= "</table>\n";
        }

        return $output;
    }

    function archivZmenWork()
    {
        $output = "";

        $pocet=$_GET["pocet"];
    
        $output .= "<div style=\"padding-left: 5px; padding-top: 10px; \">";
        
        $output .= "<div style=\" padding-bottom: 10px; padding-right: 40px; font-size: 18px; font-weight: bold; float: left; \" >";
        $output .= " Archiv změn Work (restartování)</div>";
    
        $output .= "<div style=\" \" ><form method=\"GET\" action=\"\" >";
        
        $output .= "<span style=\"margin-right: 20px; \" ><label>Vyberte počet záznamů: </label></span>
        
                    <select name=\"pocet\" size=\"1\" >
                        <option value=\"1\" "; if ($pocet == "1" or !isset($pocet) ){ $output .= " selected "; } $output .= " >1</option>
                        <option value=\"3\" "; if( $pocet == "3" ){ $output .= " selected "; } $output .= " >3</option>
                        <option value=\"5\""; if( $pocet == "5" ){ $output .= " selected "; } $output .= " >5</option>
                    </select>";

        $output .= "<span style=\"margin-left: 10px; \"><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></span>
        
                    <span style=\"margin-left: 40px; \">
                        <!-- <a href=\"include\export-archiv-zmen.php\"> -->
                        export dat
                        <!--</a>-->
                    </span>
                    
                    </form></div>";
    
        $output .= "</div>"; //konec hlavni divu
        
        $pocet_check=preg_match('/^([[:digit:]]+)$/',$pocet);
        
        $zaklad_sql = "select *,DATE_FORMAT(provedeno_kdy, '%d.%m.%Y %H:%i:%s') as provedeno_kdy2 from archiv_zmen_work ";
        
        if( ($pocet_check) )
        {   
            if ( ( strlen($pocet) > 0 ) )
            { $sql=$zaklad_sql." order by id DESC LIMIT $pocet "; }
            else
            { $sql=$zaklad_sql." order by id DESC "; }
        }
        else
        { $sql=$zaklad_sql." order by id DESC LIMIT 1 "; }

        $vysl = $this->conn_mysql->query($sql);

        $radku = $vysl->num_rows;
        
        $output .=  '<br><!--<a href="include\export-archiv-zmen.php">-->export dat zde<!--</a>--><br><br>';     
        
        if ( $radku==0 )
        { $output .= "<div class=\"alert alert-warning\" role=\"alert\" style=\"margin-top: 15px; margin-bottom: 15px;\">Žádné změny v archivu</div>"; }
        else
        {
            $output .= "<table width=\"100%\" border=\"0\" cellpadding=\"5\" class=\"az-main-table\" >";
                
            $output .= "<tr >";    
                $output .= "<td class=\"az-border2\" ><b>id:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>akce:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>Provedeno kdy:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>Provedeno kým:</b></td>";
                $output .= "<td class=\"az-border2\" ><b>Provedeno úspěšně:</b></td>";
            $output .= "</tr>";
                
            while ($data=$vysl->fetch_array()):
                
                $output .= "<tr>";    
                    $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\" >".$data["id"]."</td>";
                $output .= "<td class=\"az-border1\" ><span class=\"az-text\" >";
            
                $id_cloveka_res = "";  
                $akce = $data["akce"];
                
                $output .= "<pre>".$akce."</pre></span></td>";
            
                $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\"><span class=\"az-provedeno-kdy\" >";
                if ( ( strlen($data["provedeno_kdy2"]) < 1 ) ){ $output .= "&nbsp;"; }
                else{ $output .=  $data["provedeno_kdy2"]; }
                $output .= "</span></td>";
                
                $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\"><span class=\"az-provedeno-kym\" >";
                if ( ( strlen($data["provedeno_kym"]) < 1 ) ){ $output .= "&nbsp;"; }
                else{ $output .=  $data["provedeno_kym"]; }
                $output .= "</span></td>";		   
            
                $output .= "<td class=\"az-border1\" style=\"vertical-align: top;\">";
                if ( $data["vysledek"] == 1 ){ $output .= "<span class=\"az-vysl-ano\">Ano</span>"; }
                else{ $output .= "<span class=\"az-vysl-ne\">&nbsp;</span>"; }
                $output .= "</td>";
            
                $output .= "</tr>";
                
            endwhile;
                
            $output .= "</table>";
            
        } //konec else
    
        return $output;
    }
}