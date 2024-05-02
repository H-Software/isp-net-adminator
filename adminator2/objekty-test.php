<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");


if ( !( check_level($level,139) ) )
{
// neni level

 $stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ("include/charset.php"); 

?>

<title>Adminator 2 - test objektu</title> 

</head> 

<body> 

<?php include ("head.php"); ?>

<?php include ("category.php"); ?>

 <tr>
   <td colspan="2" height="20" bgcolor="silver" >
    <?php include("objekty-subcat-inc.php"); ?>
   </td>
 </tr>
	 
  <tr>
  <td colspan="2">
  
<?php
    
  $id_objektu = $_GET["id_objektu"];
  $id_stb = $_GET["id_stb"];

  echo "<div style=\"padding-top: 20px; padding-bottom: 20px; font-family: arial; font-size: 18px; 
			font-weight: bold; \" >Test optického objektu</div>";

  if( (ereg('^([[:digit:]]+)$',$id_objektu)) )
  {
    //prvne zjisteni detailu objektu
    $dotaz_klient = pg_query("SELECT * FROM objekty WHERE id_komplu = '$id_objektu' ");
    $dotaz_klient_radku = pg_num_rows($dotaz_klient);
    
    if( $dotaz_klient_radku <> 1 )
    {
       echo "Chyba! nelze zjistit vlastnika dle ID. \n<br>";
    }
    else
    {
	while($data_klient = pg_fetch_array($dotaz_klient) )
	{
	  $ip_klienta = $data_klient["ip"];
	  $port_id = $data_klient["port_id"];
	  $id_nodu = $data_klient["id_nodu"];
	  $mac = $data_klient["mac"];	  
	
	  $mac_lower = strtolower ($mac);
	}
    }
  
  }
  elseif( ereg('^([[:digit:]]+)$',$id_stb) )
  {
    //prvne zjisteni detailu objektu
    $dotaz_stb = $conn_mysql->query("SELECT * FROM objekty_stb WHERE id_stb = '$id_stb' ");
    $dotaz_stb_radku = mysql_num_rows($dotaz_stb);
    
    if( $dotaz_stb_radku <> 1 )
    {
       echo "Chyba! nelze zjistit objekt dle ID. \n<br>";
    }
    else
    {
	while($data_stb = mysql_fetch_array($dotaz_stb) )
	{
	  $ip_klienta = $data_stb["ip_adresa"];
	  $port_id = $data_stb["sw_port"];
	  $id_nodu = $data_stb["id_nodu"];
	  $mac = $data_stb["mac_adresa"];	  
	
	  $mac_lower = strtolower ($mac);
	}
    }
        
  }
  else
  { 
    echo "<div class=\"objekty-add-fail-ip\">
		<H4>IP adresa ( ".$ip_objektu." ) není ve správném formátu !!!</H4></div>"; 
    exit;
  }
    
    //zjisteni nodu a detailu nodu
    $dotaz_nod = $conn_mysql->query("SELECT jmeno, ip_rozsah, device_type_id FROM nod_list WHERE id = '".intval($id_nodu)."' ");
    $dotaz_nod_radku = mysql_num_rows($dotaz_nod);
    
    if( $dotaz_nod_radku <> 1 )
    {
     echo "Chyba! nelze zjistit nod dle ID. \n<br>";
    }
    else
    {
	while($data_nod = mysql_fetch_array($dotaz_nod) )
	{
	  $jmeno_nodu = $data_nod["jmeno"];
	  $ip_nodu = $data_nod["ip_rozsah"];
	  $device_type_id = $data_nod["device_type_id"];
	}
    }

    if( ( ($device_type_id == 1) or ($device_type_id == 2) ) )
    {
     list($a,$b,$c,$d) = split("[.]",$ip_nodu);
    
     $ip_sw = $a.".135.".$c.".2";
    
     echo "<table border=\"0\" width=\"\" >";
    
     echo "<tr>
	    <td style=\"\" ><b>Informace o objektu:</b></td>
	    <td style=\"padding-left: 20px; \" ><b>Informace o přípojném bodu:</b></td>    
	    <td style=\"padding-left: 20px; \" ><b>Informace vygenerované:</b></td>
	  </tr>";
	  
     echo "<tr>
	    <td>IP adresa: ".$ip_klienta."</td>
	    <td style=\"padding-left: 20px; \" >id nodu: ".$id_nodu."</td>
	    <td style=\"padding-left: 20px; \" >ip switche: ".$ip_sw."</td>
	  </tr>";
	    
    echo "<tr>
	    <td>mac adresa: ".$mac."</td>
	    <td style=\"padding-left: 20px; \" >jméno: ".$jmeno_nodu."</td>
	    <td style=\"padding-left: 20px; \" >mac klienta - lower: ".$mac_lower."</td>

	  </tr>";
    
     echo "<tr>
	    <td>port ve switchi: ".$port_id."</td>
	    <td style=\"padding-left: 20px; \" >ip rozsah: ".$ip_nodu."</td>
	  </tr>";
         
     echo "</table>";
    
     echo "<div style=\"font-weight: bold; padding: 10 0 10 0px; \" >Status portu/mac auth. ve switchi h3c 3100:</div>";

     $status_macauth = `scripts/test_h3c_3100.pl show_macauth $ip_sw $port_id 2>&1`;    
     echo "<PRE>".$status_macauth."</PRE>";
     
     echo "<div style=\"font-weight: bold; padding: 10 0 10 0px; \" >Status mac adresy ve switchi h3c 3100:</div>";
     
     $status_macaddr = `scripts/test_h3c_3100.pl show_macaddr $ip_sw $port_id 2>&1`;    
     echo "<PRE>".$status_macaddr."</PRE>";
      
     echo "<br><br>";
    }
    elseif($device_type_id == 0)
    {
    
     list($a,$b,$c,$d) = split("[.]",$ip_nodu);
    
     $ip_sw = $a.".135.".$c.".2";
    
     echo "<table border=\"0\" width=\"\" >";
    
     echo "<tr>
	    <td style=\"\" ><b>Informace o objektu:</b></td>
	    <td style=\"padding-left: 20px; \" ><b>Informace o přípojném bodu:</b></td>    
	    <td style=\"padding-left: 20px; \" ><b>Informace vygenerované:</b></td>
	  </tr>";
	  
     echo "<tr>
	    <td>IP adresa: ".$ip_klienta."</td>
	    <td style=\"padding-left: 20px; \" >id nodu: ".$id_nodu."</td>
	    <td style=\"padding-left: 20px; \" >ip switche: ".$ip_sw."</td>
	  </tr>";
	    
    echo "<tr>
	    <td>mac adresa: ".$mac."</td>
	    <td style=\"padding-left: 20px; \" >jméno: ".$jmeno_nodu."</td>
	    <td style=\"padding-left: 20px; \" >mac klienta - lower: ".$mac_lower."</td>

	  </tr>";
    
     echo "<tr>
	    <td>port ve switchi: ".$port_id."</td>
	    <td style=\"padding-left: 20px; \" >ip rozsah: ".$ip_nodu."</td>
	  </tr>";
         
     echo "</table>";
    
    echo "<div style=\"font-weight: bold; padding: 10 0 10 0px; \" >Status portu ve switchi at-8000S:</div>";
    
     $portstatus = `scripts/test_at8000.pl show_portstatus $ip_sw $port_id 2>&1`;    
    
     if( ereg('.bad.parameter.value.',$portstatus) )
     {
       echo "<div style=\"color: red; \">Chyba! Status portu nelze zjistit. (error: % bad parameter value ) </div>";
     }
     elseif( ereg('.No.route.to.host.',$portstatus) )
     {
       echo "<div style=\"color: red; \">Chyba! Status portu nelze zjistit. (error: % No route to host ) </div>";
     }
     elseif( ereg('.connect.timed.out.',$portstatus) )
     {
       echo "<div style=\"color: red; \">Chyba! Status portu nelze zjistit. (error: % connect timed-out ) </div>";
     }
     else
     {
      //priprava promennych, polí ..
      $ps_ex = explode("\n",$portstatus);
      
      $ps_ex[2] = ereg_replace(" +", " ", $ps_ex[2]);
      $ps_ex[4] = ereg_replace(" +", " ", $ps_ex[4]);
                
      $ps_ex_l2 = explode(" ",$ps_ex[2]);
      $ps_ex_l4 = explode(" ",$ps_ex[4]);
           
      $ps_ex_l2[5] = "Flow ".$ps_ex_l2[5];
      $ps_ex_l2[6] = "Link ".$ps_ex_l2[6];
      
      $ps_ex_l2[7] = "Back ".$ps_ex_l2[7];
      $ps_ex_l2[8] = "Mdix ".$ps_ex_l2[8];
      
      unset($ps_ex_l4[9]);
      
      echo "<div><span style=\"color: green; \" >příkaz proveden úspěšně... </span>";  
      echo "<span style=\"color: grey; \" >(".$ps_ex[0].")</span></div>";
    
      echo "<div style=\"font-weight: bold; padding: 5 0 5 5px; \" >Statistika portu:</div>";
    
      if( $ps_ex_l4[6] == "Up" )
      {
        echo "<div style=\"color: green; padding: 5 0 0 5px; \" >PORT aktivní (UP)</div>";
      }
      else
      {
       echo "<div style=\"color: red; padding: 5 0 0 5px; \" >PORT ne-aktivní (DOWN)</div>";
      }
      
      echo "<div style=\"color: grey; padding: 5 0 0 5px; \" >debug: ";
      
      echo "<table border=\"1\" width=\"\" >";
      
        echo "<tr>";
	
        for($p = 0; $p < count($ps_ex_l2); $p++)
	{
	 echo "<td align=\"center\" style=\"padding-left: 10px; padding-right: 10px; font-weight: bold; \" >";
	 echo $ps_ex_l2[$p]."</td>";
	 
	  $radka2_tab .= "<td align=\"center\" >".$ps_ex_l4[$p]."</td>";
	}
	
	echo "</tr>";
	
	echo "<tr>".$radka2_tab."</tr>";
	
      echo "</table>";
      
      //echo "<pre>";
      
      //print_r($ps_ex_l2);
      //print_r($ps_ex_l4);
    
      //echo "</pre>";
          
      echo "</div>"; //konec debug portu...
      
    }
    
    //autorizace dot1x vs. radius
    $status_dot1x = `scripts/test_at8000.pl show_dot1x $ip_sw 2>&1`;

/*    
    $dot1x_ex = explode("\n",$status_dot1x);
    
    //echo count($dot1x_ex);
    
    foreach ($dot1x_ex as $k => $val)
    {
	if( ereg("$mac_lower",$val) ) 
	{ $dot1x_vyber = $k; }
    }
    
    //vymazani vice mezer ..    			    
    $dot1x_ex2[3] = ereg_replace(" +", " ", $dot1x_ex[3]);
    $dot1x_ex2[$dot1x_vyber] = ereg_replace(" +", " ", $dot1x_ex[$dot1x_vyber]);
    
    //rozdeleni dle mezer
    $dot1x_l3 = explode(" ",$dot1x_ex2[3]);    
    $dot1x_lx = explode(" ",$dot1x_ex2[$dot1x_vyber]);
    
    //uprava nazvu sloupcu
    $dot1x_l3[2] = "Session ".$dot1x_l3[2];
    $dot1x_l3[3] = "Auth ".$dot1x_l3[3];
    
    $dot1x_l3[4] = "MAC ".$dot1x_l3[4];
    $dot1x_l3[5] = "VLAN";
            
#    $dot1x_lx[2] = $dot1x_lx[2]." ".$dot1x_lx[3];
#    $dot1x_lx[3] = $dot1x_lx[4]; 
#    $dot1x_lx[4] = $dot1x_lx[5]; 
#    $dot1x_lx[5] = $dot1x_lx[6]; 

    unset($dot1x_lx[6]);
*/

    echo "<div style=\"font-weight: bold; padding: 10 0 10 5px; \" >Stav autorizace ve switchi at-8000S:</div>";

/*    
    
    echo "<div style=\"padding: 10 0 10 5px; \" >";
     
    if( ( isset($dot1x_vyber) ) )
    { echo "MAC adresa v seznamu autorizovaných Mac <span style=\"color: green; \">NALEZENA</span>."; }
    else
    { echo "MAC adresa v seznamu autorizovaných Mac <span style=\"color: red; \">NE-NALEZENA </span>."; }
    
    echo "<br><span style=\"color: grey; \"> (debug: radka cislo: ".$dot1x_vyber.", příkaz: show dot1x users ) </span>";
    
    echo "<br><br>";
    
    echo "<table border=\"1\" width=\"\" >";
      
      echo "<tr>";
	
      for($p = 0; $p < count($dot1x_l3); $p++)
      {
	 echo "<td align=\"center\" style=\"padding-left: 10px; padding-right: 10px; font-weight: bold; \" >";
	 echo $dot1x_l3[$p]."</td>";
	 
	 $dot1x_l2_tab .= "<td align=\"center\" >".$dot1x_lx[$p]."</td>";
      }
	
      echo "</tr>";
	
      echo "<tr>".$dot1x_l2_tab."</tr>";
	
     echo "</table>";

*/    
     //pro debug
     echo "<pre>";

    echo $status_dot1x;    
     //print_r($dot1x_ex);
     echo "</pre>";
    
     echo "</div>";
    
    }
    else
    {  echo "<div>Informace nejsou dostupné. <br>
    	Tento model switche (device_type_id: ".$device_type_id.") není podporován.</div>";
      exit;
    }
    
    //ted info z DHCP-cke
    echo "<div style=\"font-weight: bold; padding: 0 0 10 0px; \" >Stav DHCP serveru No.1: ( 10.135.0.30)</div>";
    
    echo "<pre>";
    system("sudo /var/www/html/htdocs.ssl/adminator2/scripts/test_dhcp.pl 10.128.0.30 ".$ip_klienta,$retval1);
    echo "</pre>";
     
    echo "<div style=\"font-weight: bold; padding: 0 0 10 0px; \" >Stav DHCP serveru No.2: ( 10.135.0.31)</div>";

    echo "<pre>";    
    system("sudo /var/www/html/htdocs.ssl/adminator2/scripts/test_dhcp.pl 10.128.0.31 ".$ip_klienta,$retval2);
    echo "</pre>";
    
    
?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 
