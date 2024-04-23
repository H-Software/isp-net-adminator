<?php

namespace App\Core;

use Psr\Container\ContainerInterface;

class objekt extends adminator
{

    var $conn_pqsql;
    var $conn_mysql;

    var $logger;

    var $loggedUserEmail;

    var $dns_find;

    var $ip_find;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->validator = $container->validator;
        $this->conn_mysql = $container->connMysql;   
        $this->logger = $container->logger;

        $i = $container->auth->getIdentity();
        $this->loggedUserEmail = $i['username'];
    }

    public function vypis_razeni()
    {
        $output = "";

        $output .= "\n<tr>\n";
        $output .= '<td colspan="1">';
        
        // prvni dva
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

    public function objektyListGetBodyContent()
    {
        $output = "";
        $error = "";

        // TODO: fix checking levels for update/erase
        //promena pro update objektu
        // if ( check_level($level,29) ) { $update_povolen="true"; }
        // if ( check_level($level,33) ) { $mazani_povoleno="true"; }
        // if ( check_level($level,34) ) { $garant_akce="true"; }
        // if ( check_level($level,59) ) { $export_povolen="true"; }

        // TODO: fix export
        // if ( $export_povolen == true )
        // { objekt_a2::export_vypis_odkaz(); }	

        // prepare vars
        //
        $mod_vypisu = $_GET["mod_vypisu"];
    
        if( isset($mod_vypisu) )
        {
         if( !( preg_match('/^([[:digit:]])+$/',$mod_vypisu) ) )
         {
          $error .= "<div style=\"color: red; font-weight: bold; \" >Chyba! Nesouhlasi vstupni data. (mod vypisu) </div>";
          exit;
         }
        }

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

        $objekt_a2 = new \objekt_a2;
        $objekt_a2->echo = false;

        $output .= $objekt_a2->vypis_tab(1);
        
        $output .= $objekt_a2->vypis_tab_first_rows($mod_vypisu);

        list($output_razeni) = $this->vypis_razeni();
        $output .= $output_razeni;

        // TODO: add a2 list

        $output .= $objekt_a2->vypis_tab(2);  

        // TODO: add listing

        return array($output, $error);
    }
}