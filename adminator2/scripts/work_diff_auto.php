<?php

//skript co radi do fronty urcite akce v urcity cas, jako nahrada za automaticke restarty

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
 
require_once("/srv/www/htdocs.ssl/adminator2/include/config.php");

// require_once("/srv/www/htdocs.ssl/adminator2/include/class.php");

$html_tags = 1;

$hlaska =  "work-diff-auto.php start [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
echo $hlaska;

$output_main .= $hlaska;
       
    $rs = $conn_mysql->query("SELECT id, number_request FROM workitems ORDER BY id ");
    $num_rows = mysql_num_rows($rs);
     
    if ($num_rows ==0 ) 
    { 
	echo " INFO: no requests on the system \n"; 
	$output_main .= " INFO: no requests on the system \n";
    }
    else
    {
      while($data = mysql_fetch_array($rs) )
      {
        $id = $data["id"];
        $number_request = $data["number_request"];

        execute_action($number_request, $id);
	
      } //end of while
	
   } // end of else if num_rows == 0


echo "work-diff.php stop [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
$output_main .= "work-diff.php stop [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";

if( ereg(".*<span.*>.*", $output_main) )
{ $soubor = fopen("/srv/www/htdocs.ssl/reinhard.remote.log", "w"); }
else
{ 
    $output_main = "- - - - - - - - - - - - - -\n".$output_main;
    $soubor = fopen("/srv/www/htdocs.ssl/reinhard.remote.log", "a");
}
 
fwrite($soubor, $output_main); 
fclose($soubor);

//
// zde uz jen funkce
//

function execute_request($cmd, $mess_ok, $mess_er) 
{
    global $html_tags, $output_main;
    
    exec($cmd, $output, $rs);
    //system($cmd, $rs);

    //print_r($output);
    $output_main .= "\n".implode("\n ", $output)."\n";
    	    
    if($rs == "0")
    { 		
	if($html_tags == 1)
	{ $hlaska = "  <span class=\"work-ok\">".$mess_ok." (message: ".$rs.")</span>\n"; }
	else
	{ $hlaska = "  ".$mess_ok." (message: ".$rs.")\n"; }

	echo $hlaska;
	$output_main .= $hlaska;
	
    }
    else
    { 
	if($html_tags == 1)
	{ $hlaska = "  <span class=\"work-error\">".$mess_er." (message: ".$rs.")</span>\n"; }
	else
	{ $hlaska = "  ".$mess_er." (message: ".$rs.")\n"; }

	echo $hlaska; 
	$output_main .= $hlaska;
		
    }

} //end of function execute_action

?>
