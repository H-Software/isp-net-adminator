<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class objekt extends adminator
{

    var $conn_pqsql;
    var $conn_mysql;

    var $logger;

    var $loggedUserEmail;

    var $adminator; // handler for instace of adminator class

    var $dns_find;

    var $ip_find;

    var $mod_vypisu;

    var $es;

    var $razeni;

    var $list;

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
            $exportRs = $objekt_a2->export_vypis_odkaz(); 
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

        return array($output, $error);
    }
}