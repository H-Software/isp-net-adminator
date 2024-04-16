<?php

namespace App\Core;

class Topology extends adminator {
    var $conn_mysql;
    var $smarty;
    var $logger;
    
    public function __construct($conn_mysql, $smarty, $logger)
    {
        $this->conn_mysql = $conn_mysql;
        $this->smarty = $smarty;
        $this->logger = $logger;
        
        $this->logger->addInfo("topology\__construct called");
    }

    public function getNodeListForForm($search_string, $typ_nodu = 2, $show_zero_value = true)
    {
        $this->logger->addInfo("topology\getNodesFiltered called");

        if($show_zero_value === true)
        {
            $nodes[0] = "Není vybráno";
        }

        $search_string = $this->conn_mysql->real_escape_string($search_string);

        $sql = "SELECT id, jmeno, ip_rozsah from nod_list WHERE ( jmeno LIKE '%$search_string%' ";
        $sql .= " OR ip_rozsah LIKE '%$search_string%' OR adresa LIKE '%$search_string%' ";
        $sql .= " OR pozn LIKE '%$search_string%' ) AND ( typ_nodu = '" . intval($typ_nodu) . "' ) ORDER BY jmeno ASC ";
        
        $rs = $this->conn_mysql->query($sql);
        $num_rows = $rs->num_rows;
    
        if($num_rows < 1)
        {
            $nodes[0] = "nelze zjistit / žádný nod nenalezen";
            return $nodes;
        }
        else
        {
            while ($data = $rs->fetch_array() )
            {
                $nodes[$data['id']] = $data["jmeno"] . " (".$data["ip_rozsah"].")";
            }

            return $nodes;
        }
    }

    public function getNodeList()
    {
        $output = "";

        // prepare vars
        //
        $list=$_GET["list"];
        $razeni=$_GET["razeni"];
        
        $datum = strftime("%d/%m/%Y %H:%M:%S", time());

        $ping=$_GET["ping"];
        $find=$_GET["find"];

        $typ_vysilace = $_GET["typ_vysilace"];
        $stav = $_GET["stav"];

        $typ_nodu = $_GET["typ_nodu"];
        if( !isset($typ_nodu) )
        { $typ_nodu = "1"; }
            
        $find_orez = str_replace("%","",$find);
            
        if( (strlen($find) < 1) )
        { $find="%"; }
        else
        {
            if( !(ereg("^%.*%$",$find)) ){ $find="%".$find."%"; }
        }
        
        // "list" header
        $output .= "<div style=\"padding-top: 10px; padding-bottom: 20px;\" >
            <span style=\"padding-left: 20px; font-size: 20px; font-weight: bold; \">
            Výpis lokalit / přípojných bodů
            </span>
            <span style=\"padding-left: 80px; \" ><!--<a href=\"include/export-topology.php\" >-->export lokalit/nodů<!--</a>--></span>  
        
            <span style=\"padding-left: 80px; \" >
            Výpis lokalit/nodů s latencemi ";
            
        if($ping == 1)
        { 
            $output .= "<a href=\"".$_SERVER["PHP_SELF"]."?razeni=".$razeni."&ping=&find=".$find_orez;
            $output .= "&list=".$list."&typ_nodu=".$typ_nodu."\">vypnout</a>"; 
        }
        else
        { 
            $output .= "<a href=\"".$_SERVER["PHP_SELF"]."?razeni=".$razeni."&ping=1&find=".$find_orez;
            $output .= "&list=".$list."&typ_nodu=".$typ_nodu."\">zapnout</a>"; 
        }
        
        $output .= "</span>
        </div>";

        // filter/search
        //
        $output .= "<div style=\"padding-left: 20px; padding-bottom: 10px;\" >
            <form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\" >
                        
                <input type=\"hidden\" name=\"razeni\" value=\"".$razeni."\" >
                <input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >
                    
                <span style=\"font-weight: bold; \" >Hledání:</span>
                
                <span style=\"padding-left: 10px; \" >
                    <input type=\"text\" name=\"find\" size=\"15\" value=\"".$find_orez."\" style=\"font-size: 12px; \" >
                </span>
            
            
            <span style=\"padding-left: 10px; \" ><span style=\"color: grey; font-weight: bold; \">filtr:</span></span>
            
            <span style=\"padding-left: 10px; \" >typ:</span>
            
            <span style=\"padding-left: 10px; \" >
                <select name=\"typ_vysilace\" size=\"1\">
                <option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>    
                <option value=\"1\" "; if( $typ_vysilace == 1){ $output .= " selected "; } $output .= ">Metallic</option>    
                    
                <option value=\"2\" "; if( $typ_vysilace == 2){ $output .= " selected "; } $output .= ">ap-2,4GHz-OMNI</option>    
                    <option value=\"3\" "; if( $typ_vysilace == 3){ $output .= " selected "; } $output .= ">ap-2,4GHz-sektor</option>    
                    <option value=\"4\" "; if( $typ_vysilace == 4){ $output .= " selected "; } $output .= ">ap-2.4GHz-smerovka</option>    
                    <option value=\"5\" "; if( $typ_vysilace == 5){ $output .= " selected "; } $output .= ">ap-5.8Ghz-OMNI</option>    
                    <option value=\"6\" "; if( $typ_vysilace == 6){ $output .= " selected "; } $output .= ">ap-5.8Ghz-sektor</option>
                    <option value=\"7\" "; if( $typ_vysilace == 7){ $output .= " selected "; } $output .= ">ap-5.8Ghz-smerovka</option>
                <option value=\"8\" "; if( $typ_vysilace == 8){ $output .= " selected "; } $output .= ">jiné</option>
                    
                </select>
            </span>

            <span style=\"padding-left: 20px; \" >stav: </span>

            <span style=\"padding-left: 10px; \" >
                <select name=\"stav\" size=\"1\" >
                <option value=\"0\" class=\"select-nevybrano\">Nevybráno</option>
                <option value=\"1\" "; if( $stav == 1){ $output .= " selected "; } $output .= ">V pořádku</option>
                <option value=\"2\" "; if( $stav == 2){ $output .= " selected "; } $output .= ">Vytížen</option>
                <option value=\"3\" "; if( $stav == 3){ $output .= " selected "; } $output .= ">Přetížen</option>
                </select>
            </span>
            
            <span style=\"padding-left: 10px; \" >mód:</span>
                <select name=\"typ_nodu\" size=\"1\" >
                <option value=\"0\" class=\"select-nevybrano\">Nevybráno</option>
                <option value=\"1\" style=\"color: #CC0033; \" "; 
                if( $typ_nodu == 1){ $output .= " selected "; } 
                $output .= ">bezdrátová síť</option>
                
                <option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
                if( $typ_nodu == 2){ $output .= " selected "; } 
                $output .= ">optická síť</option>
                </select>
            
            <span style=\"padding-left: 30px; \" ><input type=\"submit\" name=\"odeslat\" value=\"OK\" ></span>
            
            </form>
        </div>
        
        <div style=\"padding-left: 20px; padding-bottom: 10px; \" >
            <span style=\"font-weight: bold; padding-right: 10px; \">Hledaný výraz:</span> ".$find."
        </div>";
        
        //aby se stihli pingy
        // set_time_limit(0);
            
        // tvoreni dotazu
        //
        if ( $razeni == 1 ){ $order=" order by id asc"; }
        elseif ( $razeni== 2 ){ $order=" order by id desc"; }
        elseif ( $razeni== 3 ){ $order=" order by jmeno asc"; }
        elseif ( $razeni== 4 ){ $order=" order by jmeno desc"; }
        elseif ( $razeni== 5 ){ $order=" order by adresa asc"; }
        elseif ( $razeni== 6 ){ $order=" order by adresa desc"; }
        elseif ( $razeni== 7 ){ $order=" order by pozn asc"; }
        elseif ( $razeni== 8 ){ $order=" order by pozn desc"; }
        elseif ( $razeni== 9 ){ $order=" order by ip_rozsah asc"; }
        elseif ( $razeni== 10 ){ $order=" order by ip_rozsah desc"; }
        elseif ( $razeni== 11 ){ $order=" order by umisteni_aliasu asc"; }
        elseif ( $razeni== 12 ){ $order=" order by umisteni_aliasu desc"; }
        elseif ( $razeni== 13 ){ $order=" order by mac asc"; }
        elseif ( $razeni== 14 ){ $order=" order by mac desc"; }

        $where = " WHERE ( id = '$find' OR jmeno LIKE '$find' OR adresa LIKE '$find' ";
        $where .= "OR pozn LIKE '$find' OR ip_rozsah LIKE '$find' ) ";
        
        if( $typ_vysilace > 0 ){ $where .= "AND ( typ_vysilace = '$typ_vysilace' ) "; }
        
        if( $stav > 0 ){ $where .= "AND ( stav = '$stav' ) "; }
        
        if( $typ_nodu > 0 ){ $where .= " AND ( typ_nodu = '$typ_nodu' ) "; }
        
        $sql="select * from nod_list ".$where." ".$order;

        $sql_source = "/topology/nod-list?razeni=".$razeni."&ping=".$ping;
        $sql_source .= "&typ_vysilace=".$typ_vysilace."&stav=".$stav."&find=".$find_orez;
        $sql_source .= "&typ_nodu=".$typ_nodu;
        
        // TODO: fix paging
        // $listovani = new c_listing_topology($this->conn_mysql, $sql_source, 30, $list,
        //                 "<center><div class=\"text-listing\">\n", "</div></center>\n",$sql." ; ");
                        
        if (($list == "")||($list == "1"))
        {    //pokud není list zadán nebo je první
            $bude_chybet = 0; //bude ve výběru sql dotazem chybet 0 záznamů
        }
        else
        {
            $bude_chybet = (($list-1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
        }
            
        /// TODO: fix paging
        $vysledek = $this->conn_mysql->query($sql); // $sql . " LIMIT ".$bude_chybet.",".$listovani->interval." ");

        // TODO: fix paging
        // $output .= "<div style=\"padding-top: 10px; padding-bottom: 10px; \" >".$listovani->listInterval()."</div>";    //zobrazení stránkovače
        
        $radku = $vysledek->num_rows;
        
        if ($radku==0)
        {
            $output .= "<div style=\"padding-top: 15px; padding-left: 15px;\" class=\"alert alert-warning\" role=\"alert\">"
                        . "Žadné lokality/nody dle hladeného výrazu ( ".$find." ) v databázi neuloženy."
                        . "</div>";
            // $output .= "<div >debug: sql: ".$sql." </div>";
        }
        else
        {
            // $output .= '<br>Výpis lokalit/nodů: <span style="color: silver">řazeno dle id: '.$_POST["razeni"].'</span><BR><BR>';
       
            $colspan_id="1";
            $colspan_jmeno="3";
            $colspan_adresa="3";
            $colspan_pozn="2";
            $colspan_rozsah_ip="1";
            $typ_nodu = "1";
            $colspan_umisteni="2";
            
            $colspan_celkem = $colspan_id + $colspan_jmeno + $colspan_adresa + $colspan_pozn + $colspan_rozsah_ip + $colspan_umisteni;
            
            $output .= "<table border=\"0\" >";

            // $output .= "<tr><td colspan=\"".$colspan_celkem."\"><hr></td></tr>";
            
            $output .= "\n<tr>
                <td width=\"5%\" colspan=\"".$colspan_id."\"  class=\"tab-topology2 tab-topology-dolni2\" >
                
                <table border=\"0\" width=\"\" >
                <tr>
                    <td><b>id:</b></td>";
                
            $output .= "<td>";
                $output .= "<form name=\"form1\" method=\"GET\" action=\"\" > ";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"1\" >";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form1.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
            
                $output .= "<form  name=\"form2\" method=\"GET\" action=\"\" > ";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"2\">";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form2.submit()\">";
                $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
        
            $output .= "</td></tr></table>";
            
            $output .= "</td>";
            
            $output .= "<td width=\"20%\" colspan=\"".$colspan_jmeno."\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Jméno: </b></td>";
        
            $output .= "<td >";
                $output .= "<form  name=\"form3\" method=\"GET\" action=\"\" >";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"3\" >";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form3.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
            
                $output .= "<form  name=\"form4\" method=\"GET\" action=\"\" >";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"4\">";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form4.submit()\">";
                $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
        
            $output .= "</td></tr></table>";
                
            $output .= "</td>";
            
            
            $output .= "<td colspan=\"".$colspan_adresa."\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Adresa: </b></td>";
                
            $output .= "<td>";
                $output .= "<form  name=\"form5\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"5\" >";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form5.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
            
                $output .= "<form  name=\"form6\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"6\">";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form6.submit()\">";
                $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
        
            $output .= "</td></tr></table>";
                
            $output .= "</td>";
            
            $output .= "<td colspan=\"1\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Poznámka: </b></td>";
                
            $output .= "<td>";
                $output .= "<form  name=\"form7\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"7\" >";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form7.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
            
                $output .= "<form  name=\"form8\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"8\">";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form8.submit()\">";
                $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
        
            $output .= "</td></tr></table>";
            
            $output .= "</td>";
        
            $output .= "<td colspan=\"1\" width=\"15%\" class=\"tab-topology2 tab-topology-dolni2\" align=\"center\" >
            <b>Vlan ID</b><br></td>";
            
            
            $output .= "<td width=\"10%\" colspan=\"".$colspan_rozsah_ip."\" class=\"tab-topology2 tab-topology-dolni2\" >
            
            <table border=\"0\" width=\"100%\" >
            <tr>
                <td><b>Rozsah ip adres: </b></td>";
                
            $output .= "<td>";
                $output .= "<form  name=\"form9\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"9\" >";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form9.submit()\">
                <img src=\"/img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
            
                $output .= "<form  name=\"form10\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
                $output .= "<input type=\"hidden\" name=\"razeni\" value=\"10\">";
                
                $output .= "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
                $output .= "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
                $output .= "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
                
                $output .= "<a href=\"javascript:self.document.forms.form10.submit()\">";
                $output .= "<img src=\"/img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
                </a></form>";
        
            $output .= "</td></tr></table>";
            
            $output .= "</td>";
                
            $output .= "<td width=\"10%\" colspan=\"".$typ_nodu."\" class=\"tab-topology2 tab-topology-dolni2\" >
                <b>Mód nodu</b></td>";
                    
            // bunky druhej radek
            $colspan_filtrace="1";
            
            $colspan_mac="3";
            
            $colspan_typ_vysilace="3";
            $colspan_aktivni="1";
            $colspan_stav="1";
                
            $colspan_uprava="2";
            $colspan_smazani="2";
                
            $output .= "
            
            </tr>
            
            <tr>
            <td colspan=\"1\" class=\"tab-topology2\" ><br></td>
            <td colspan=\"3\" class=\"tab-topology2\" >
                <span style=\"color: #666666; font-weight: bold; \">Umístění aliasu (název routeru): </span></td>
            
            <td colspan=\"".$colspan_typ_vysilace."\" class=\"tab-topology2\" ><span style=\"color: #666666; font-weight: bold; \">Typ vysílače: </span></td>
            
            <td colspan=\"".$colspan_aktivni."\" class=\"tab-topology2\" align=\"center\" >
            <span style=\"color: #666666; font-weight: bold; \">Aktivní: </span></td>
            <td colspan=\"".$colspan_stav."\" class=\"tab-topology2\" align=\"center\" ><span style=\"color: #666666; font-weight: bold; \">Stav: </span></td>
                    
            <td colspan=\"".$colspan_uprava."\" class=\"tab-topology2\" ><span style=\"color: #666666; font-weight: bold; \">Úprava / Smazání: </span></td>
            
                </tr>\n";

            //treti radek
            $output .= "<tr><td colspan=\"".$colspan_celkem."\"><hr></td></tr>";
            
            //vnejsi tabulka
            $output .= "</tr>";
            
            $output .= "<tr>";
                                        
            $output .= "\n";
            while ($zaznam=$vysledek->fetch_array()):
            
                $id=$zaznam["id"];
                
                // prvni radek
                $output .= "<tr>";
                    $output .= "<td colspan=\"".$colspan_id."\"><span style=\"font-size: 12px; padding-right: 5px;\" >";
                        $output .= $id."</span><a name=\"".$id."\" ></a>";
                    $output .= "</td>\n";

                $output .= "<td colspan=\"".$colspan_jmeno."\">
                        <span style=\"font-weight: bold; font-size: 14px; \">".
                        "<a href=\"/topology/user-list?vysilac=".intval($zaznam["id"])."\" >".$zaznam["jmeno"]."</a>".
                        "</span>\n".
                    "</td>\n";
                $output .= "<td colspan=\"".$colspan_adresa."\" >".
                        "<span style=\"font-size: 13px; padding-right: 10px; \">".$zaznam["adresa"]."</span>".
                        "<a href=\"\"><a href=\"http://www.mapy.cz?query=".$zaznam["adresa"]."\" target=\"_blank\" >na mapě</a>".
                    "</td>\n";
                
                //if( $_GET["typ_nodu"] == 2 )
                {
                $output .= "<td colspan=\"1\" ><span style=\"font-size: 13px; \">".$zaznam["pozn"]."</span></td>\n";
                $output .= "<td colspan=\"1\" align=\"center\">
                        <span style=\"font-size: 13px; \">".$zaznam["vlan_id"]."</span>
                    </td>\n";
                
                }
                //else{ $output .= "<td colspan=\"".$colspan_pozn."\" ><span style=\"font-size: 13px; \">".$zaznam["pozn"]."</span></td>\n";  }
                    
                $output .= "<td colspan=\"".$colspan_rozsah_ip."\" ><span style=\"font-size: 13px; \">".$zaznam["ip_rozsah"]."</span></td>\n";
                $output .= "<td colspan=\"".$typ_nodu."\" ><span style=\"font-size: 13px; \">";
                if( $zaznam["typ_nodu"] == 0 )
                { $output .= "Nezvoleno"; }
                elseif( $zaznam["typ_nodu"] == 1 )
                { $output .= "<span style=\"color: #CC0033; \">bezdrátová síť</span>"; }
                elseif( $zaznam["typ_nodu"] == 2 )
                { $output .= "<span style=\"color: #e37d2b; font-weight: bold; \" >optická síť</span>"; }
                
                $output .= "</span></td>\n";
                    
                $output .= "<td colspan=\"".$colspan_umisteni."\" rowspan=\"2\" class=\"tab-topology\"><span style=\"font-size: 13px; \">";
                
                $output .= "</span></td>\n";
                    
                $output .= "</tr>";
                    
                // druhej radek
                $output .= "<tr>";
                
                $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_filtrace."\" >
                <a href=\"/archiv-zmen?id_nodu=".intval($id). "\" style=\"font-size: 12px; \">H: ".$id."</a>".
                "</td>\n";
                        
                $output .= "<td class=\"tab-topology\" colspan=\"3\">
                <span style=\"color: #666666; font-size: 13px; padding-right: 10px; \" >";
                    
                $router_id = $zaznam["router_id"];
            
                if ($router_id <= 0)
                { $router_nazev="<span style=\"color: red\">nelze zjistit </span>"; $router_ip=""; }
                else
                {
                    $vysledek_router=$this->conn_mysql->query("SELECT nazev, ip_adresa FROM router_list where id = ".intval($router_id)." ");
                    while($data_router=$vysledek_router->fetch_array())
                    { $router_nazev = $data_router["nazev"]; $router_ip = $data_router["ip_adresa"]; }
                }
                
                $output .= "<span style=\"color: teal; \">".$router_nazev."</span> ".$router_ip."</span>";
                $output .= "<a href=\"/topology/router-list?odeslano=OK&f_search=".$router_ip."&\">link</a>";

                $output .= "</td>\n";
                
                $typ_vysilace=$zaznam["typ_vysilace"];
                
                if ( $typ_vysilace == 1 ){ $typ_vysilace2="Metallic"; }
                elseif ( $typ_vysilace == 2 ){ $typ_vysilace2="ap-2,4GHz-OMNI"; }
                elseif ( $typ_vysilace == 3 ){ $typ_vysilace2="ap-2,4Ghz-sektor"; }
                elseif ( $typ_vysilace == 4 ){ $typ_vysilace2="ap-2.4Ghz-smerovka"; }
                elseif ( $typ_vysilace == 5 ){ $typ_vysilace2="ap-5.8Ghz-OMNI"; }
                elseif ( $typ_vysilace == 6 ){ $typ_vysilace2="ap-5.8Ghz-sektor"; }
                elseif ( $typ_vysilace == 7 ){ $typ_vysilace2="ap-5.8Ghz-smerovka"; }
                elseif ( $typ_vysilace == 8 ){ $typ_vysilace2="jiné"; }
                else { $typ_vysilace2=$typ_vysilace; }			
                                                        
                $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_typ_vysilace."\" ><span style=\"color: #666666; font-size: 13px; \">".$typ_vysilace2."</span> </td>\n";
                
                list($a,$b,$c,$d) = split("[.]",$zaznam["ip_rozsah"]);
                
                if ( $c == 0) { $c=1; }
                
                $d=1;
                $ip_akt=$a.".".$b.".".$c.".".$d;
                
                $akt_par="class=\"tab-topology\" colspan=\"".$colspan_aktivni."\" ";
                
                if ( ( $_GET["ping"] == 1 ) )
                { 
                    $aktivni=exec("/srv/www/htdocs.ssl/adminator2/scripts/ping.sh $ip_akt"); 
                
                    if ( ( $aktivni > 0 and $aktivni < 50 ) ) 
                    {  $output .= "<td ".$akt_par." align=\"center\" bgcolor=\"green\"><span style=\"color: white; font-size: 13px; \">".$aktivni."</span>"; }
                    elseif ( $aktivni > 0)
                    { $output .= "<td ".$akt_par." align=\"center\" bgcolor=\"orange\"><span style=\"color: white; font-size: 13px; \">".$aktivni."</span>"; }
                    else { $output .= "<td ".$akt_par." align=\"center\" bgcolor=\"red\">"; $output .= "<br>"; }
                }
                else
                {	$output .= "<td ".$akt_par." align=\"center\" ><span style=\"color: #666666; font-size: 13px; \">N/A</span>"; }
                
                $output .= "</td>";
                
                if ( $zaznam["stav"] == 1)
                { 
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"green\" align=\"center\" >
                        <span style=\"color: white; font-size: 13px; \"> v pořádku </span></td>"; 
                }
                elseif ( $zaznam["stav"] == 2)
                { 
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"orange\" align=\"center\" >
                        <span style=\"color: white; font-size: 13px; \"> vytížen </span></td>"; 
                }
                elseif( $zaznam["stav"] == 3 )
                { 
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"red\" align=\"center\" >
                        <span style=\"color: white; font-size: 13px; \"> přetížen </span></td>"; 
                }
                else
                { 
                    $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"silver\" align=\"center\" >
                        <span style=\"color: black; font-size: 13px; \"> nezvoleno </span></td>"; 
                }

                $output .= "<td class=\"tab-topology\" colspan=\"".$colspan_uprava."\" >";
                
                //vnitrni tabulka
                $output .= "<table width=\"100%\" border=\"0\"><tr>";
                
                // upraveni 
                $output .= "<td><form method=\"POST\" action=\"/topology/nod-update\">
                <input type=\"hidden\" name=\"update_id\" value=\"".$id."\">
                <input type=\"submit\" value=\"update\">
                </form>
                </td>";
                
                //smazani
                //$output .= "<td class=\"tab-topology\" colspan=\"\" >";
                
                $output .= "<td><form action=\"/topology/nod-erase\" method=\"POST\" >";
                $output .= "<input type=\"hidden\" name=\"erase_id\" value=\"".$id."\">";
                $output .= "<input type=\"submit\" value=\"Smazat\">
                    </form>
                    </td>";
                
                    //konec vnirni tabulky
                    $output .= "</tr></table>";
                    
                $output .= "</td>";
                                    
                $output .= "</tr>";

            endwhile;
        }

        $output .= "</table>";

        $output .= "<div style=\"padding-top: 20px; margin-bottom: 20px; \" >";
            // TODO: fix paging
            // $output .= "<span style=\"margin-top: 5px; margin-bottom: 15px; \">".$listovani->listInterval();
        $output .= "</div>";
        
        return $output;
    }
}