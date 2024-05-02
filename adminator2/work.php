<?php

require("include/main.function.shared.php");
require("include/config.php"); 

require("include/check_login.php");
require("include/check_level.php");

if( !( check_level($level,16) ) )
{ // neni level

 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;      
}
	
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require_once ("include/charset.php"); 

?>

<title>Adminator2 - Work </title> 

<script src="include/js/work-odpocet.js"></script>

</head> 

<body onload="startclock()"> 


<?php 
 require ("head.php");
 
 require ("category.php"); 

 echo '<tr>
	<td colspan="2">';

    echo "<div style=\"padding-left: 10px; float: left; \" >restartování samostatné položky:</div>\n";

    echo "<form method=\"POST\" action=\"\" >";
    
    echo "<div style=\"padding-left: 30px; padding-right: 20px; float: left; width: 440px; \" >
	    <select name=\"single_action\" size=\"1\" >";
	    
	$dotaz_akce = $conn_mysql->query("SELECT id, name FROM workitems_names ORDER BY id");
    	
	while( $data = $dotaz_akce->fetch_array() )
	{ echo "<option value=\"".$data["id"]."\">".$data["name"]."</option>\n"; }
	
    echo "</select>
	  </div>\n";
    
    echo "<input type=\"submit\" value=\"PRIDAT do fronty\" name=\"ok\" >\n";

    echo "<input type=\"hidden\" value=\"true\" name=\"akce_single\" >\n";
    
    echo "</form>\n";
?>
	</td>
    </tr>
    
    <tr>
    <td colspan="2">
	<div>
	    <span style="margin-left: 10px;">automatický restart</span>
	    <span style="margin-left: 100px;">restart za: <span style="padding-left: 20px;" id="autorestart"></span>.</span>
	</div>
	<div style="margin-left: 10px; margin-top: 10px; font-weight: bold;" >
	    Výpis požadavků na restart
	</div>

	<div style="margin-left: 10px; margin-top: 5px;" >
<?php

    //výpis fronty pro restart

//    $sql = "SELECT workitems.id, workitems_names.name,workitems.number_request ".
//	   " FROM workitems, workitems_names WHERE workitems.number_request = workitems_names.id ";

    $sql = "SELECT workitems.id, workitems_names.name, workitems.number_request, workitems.in_progress, workitems_names.priority ".
	   " FROM workitems, workitems_names WHERE workitems.number_request = workitems_names.id ";
     
    $dotaz_fronta = $conn_mysql->query($sql);
        
    if( ($dotaz_fronta->num_rows <= 0) ) {
      echo "<div stle=\"font-size: 12px; font-family: arial;\">Žádný požadavek v databázi</div>\n";
    }
    else{

	echo "<div style=\"font-weight: bold; border-bottom: 1px solid gray; padding-top: 5px; width: 50px; float: left; \" >ID</div>\n";
	echo "<div style=\"font-weight: bold; border-bottom: 1px solid gray; padding-top: 5px; width: 600px; float: left; \">jméno</div>\n"; 

	echo "<div style=\"font-weight: bold; border-bottom: 1px solid gray; padding-top: 5px; width: 200px; float: left; \">v řešení</div>\n"; 
	echo "<div style=\"font-weight: bold; border-bottom: 1px solid gray; padding-top: 5px; width: 200px; \">"."</span>priorita</div>\n"; 
	
    }
    
    while($data = $dotaz_fronta->fetch_array())
    {
	echo "<div style=\"padding-top: 5px; width: 50px; float: left; \" >".$data["number_request"]."</div>\n";
	echo "<div style=\"padding-top: 5px; width: 600px; float: left; \">"."</span> ".$data["name"]."</div>\n"; 
	
	echo "<div style=\"padding-top: 5px; width: 200px; float: left; \">"."</span> ";
	
	if($data["in_progress"] == 1)
	{ echo "Ano"; }
	else
	{ echo "Ne"; }
	
	echo "</div>\n"; 
	
	echo "<div style=\"padding-top: 5px; width: 200px; float: left; \">"."</span> ".$data["priority"]."</div>\n"; 
	   
	echo "<div style=\"clear: both;\"></div>";
    }
    
?>
       </div>
		
    </td>
   </tr>
       
  </table>
<?php
 
 $akce = $_POST["akce"];
 $akce_single = $_POST["akce_single"];
 
 $iptables = $_POST["iptables"];
 $dns = $_POST["dns"];
 $optika = $_POST["optika"];

 $single_action = intval($_POST["single_action"]);
 
 $data_s = "/var/www/html/htdocs.ssl/reinhard.remote.log";
 
 if( $akce_single == "true" )
 {
    echo "<div style=\"position: relative; top: 0px; height: 75px; width: 700px; padding : 15px; text-align: center; background-color: silver;\">";
    
    if( $single_action > 0 )
    { Aglobal::work_handler($single_action); }
    
    echo " </div>\n";
 }
 else
 {
     
    echo '<div style="position: relative; top: 0px; height: 75px; width: 700px; padding : 15px; text-align: center; background-color: silver; ">
        <b>Vyberte požadovanou akci...</b></div>';
 }

 $last_log_rs = $conn_mysql->query("SELECT akce FROM archiv_zmen_work ORDER BY id DESC LIMIT 1");
 
 while($data_last_log = $last_log_rs->fetch_array()){
 
    echo "<PRE>";

    echo $data_last_log["akce"];
    
    echo "</PRE>";

 }
 
/*
 if( ( file_exists ($data_s) ) )
 { 
   echo "<PRE>";
   $fp = fopen ($data_s, "r");	
   $data = fread ($fp, filesize ($data_s) );	
   echo $data;	
   fclose ($fp); 
 
   echo "</pre>";
 }
 else
 { echo "\n log soubor chybí \n"; }
*/

echo "</div>";

?>

</body>
</html>
