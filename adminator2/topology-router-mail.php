<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");

if( !( check_level($level,132) ) )
{
  // neni level
 header("Location: nolevelpage.php");
 
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require("include/charset.php"); 

?>

<title>Adminator2 - Topology - routery - výpis emailů</title>

</head>

<body>

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?>
 
 <tr>
  <td colspan="2" bgcolor="silver" >
   <?php require("topology-cat2.php"); ?>
  </td>
 </tr>
 	      
 <tr>
  <td colspan="2">
    <!-- ZACATAK VLASTNIHO OBSAHU -->

<?php

 $id_routeru = $_GET["id"];
 
 if( !( ereg('^([[:digit:]]+)$',$id_routeru) ) )
 {
   echo "Chyba! Vstupni data ve spatnem formatu! <br>"; 
   exit;
 }

 //hlavni DIV
 echo "<div style=\"padding-top: 5px; padding-left: 5px; \">";
 
 echo "<div style=\"padding-bottom: 20px; font-weight: bold; \">".
	"Výpis souborů ze (zálohovacího) emailu pro router_id: ".$id_routeru."</div>";
 
 ob_flush();
 flush();
 
 function writeAttachmentsToDisk($mailbox, $msg_number, $dir){
  
  if(!file_exists($dir))
  { 
    //mkdir($dir); 
    if (!mkdir($dir)) {
      echo "<div>error: Failed to create folder \"".$dir."\"</div>\n";
    }
      
  }
  
  $filename = "tmp.eml";
  $email_file = $dir."/".$filename;
  // write the message body to disk
  $rs_is = imap_savebody($mailbox, $email_file, $msg_number);  
  if( !($rs_is == 1) )
  { 
    echo "<div>error in function imap_savebody with msg_number ".$msq_number."</div>\n";
    return false; 
  }
  
  $command = "munpack -C $dir -fq $email_file";
  // invoke munpack which will 
  // write all the attachments to $dir
  exec($command,$output,$rs_exec);
  //$ex_ret = passthru($command, $rs_exec);
  
  if( $rs_exec == "0" )
  {
    //OK
    
  }
  else
  {
    $output_main .= "\n".implode("\n ", $output)."\n";
  
    echo "<div style=\"padding-left: 20px; \" >".
	"error in function exec, with command: \"".htmlspecialchars($command).
	"\", ouput: ".htmlspecialchars($output_main)." </div>\n";
  
    //print "exec result: ".$ex_ret.", ".$rs_exec.", ";
    //print_r($output);
    //print "<br>\n";
  
  }
  
  // if($output[0]!='Did not find anything to unpack from $filename') {
  $found_file = false;
  foreach( $output as $attach ) 
  {
    $pieces = explode(" ", $attach);
    $part = $pieces[0];
    if (file_exists($dir."/".$part))
    {
      $found_file = true;
      $files[] = $part;
    }
  }
  
  if(!$found_file)
  {
    //echo ("\nMail.php : no files found - cleaning up. ");
    // didn't find any output files - delete the directory and email file
    unlink($email_file);
    //rmdir($dir);
    return false;
  }
  else 
  {
    // found some files-  just delete the email file
    unlink($email_file);
    return $files;
  }
}

 $dotaz_router = $conn_mysql->query("SELECT nazev FROM router_list WHERE id = '".intval($id_routeru)."' ");

 if( $dotaz_router->num_rows <> 1 )
 { 
  echo "Chyba! Nelze vybrat router! <br>";
  exit;
 }
 
 ob_flush();  
 flush();
  
 while( $data_router = $dotaz_router->fetch_array() )
 { $nazev_routeru = $data_router["nazev"]; }
 
 $mbox = imap_open("{lamia.adminator.net.net:993/imap/ssl}INBOX", "router-board@adminator.net", "archimedes");

 if($mbox)
 { echo "<div style=\"color: green; font-size: 16px; \" >Připojení k emailové schránce se zdařilo</div>\n"; }
 else 
 {
   echo "<div style=\"color: red; \" >Připojení k emailové schránce se nezdařilo.</div>\n"; 
   
   echo "<div>".imap_last_error()."</div>\n";
   exit;
 }
 
 echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-weight: bold; \" >";
 echo "Výpis souborů: </div>";
 
 ob_flush();
 flush();

 $MC = imap_check($mbox);
 // Fetch an overview for all messages in INBOX
 $result = imap_fetch_overview($mbox,"1:{$MC->Nmsgs}",0);
 
 foreach ($result as $overview) 
 {	
    $from = $overview->from;    
    $nazev_routeru_2 = $nazev_routeru."@adminator.net";
    
    if( $from == $nazev_routeru_2 )
    { $maily[] = $overview->msgno; }
 
    //echo "debug: from: ".$from." <br>";
 }

 $cesta = "export/mail";
 $cesta2 = "/var/www/html/htdocs.ssl/adminator2/export/mail";

 $prilohy_final = array();
   
 foreach( $maily as $val ) 
 {
   //echo $val;
   //$first = false;
 
   $prilohy = writeAttachmentsToDisk($mbox,$val,$cesta2);

   if( $prilohy == false)
   { echo "<div style=\"color: red; \" >Chyba! Prilohy za zpravy #".$val." se nepodarilo extrahovat! </div>"; }
   else
   { 
    //$prilohy_final[] = $prilohy_final[] + $prilohy[]; 
    $prilohy_final = array_merge($prilohy_final, $prilohy);
   
   }
  
   ob_flush();  flush();
   
  }

 /*	
 print "<pre>"; 
 print_r($maily);
 print_r($prilohy_final);  
 print "</pre>";
 */
 
 //tady
 foreach( $prilohy_final as $val ) 
 {
  echo "<a href=\"export/mail/".$val."\" >".$val."</a><br>";
 }
 
 echo "<br>";
 
 //ukoncovani az nakonec :)
 $ukonci=imap_close($mbox);

 if($ukonci)
 { echo "<div style=\"color: green; font-size: 16px; \" >Odpojení od emailové schránky se zdařilo</div>"; }
 else
 { echo "<div style=\"color: red; \" >Odpojení od emailové schránky se nezdařilo.</div>"; }

 //KONEC hlavniho divu
 echo "</div>";
 
?>
    <!-- KONEC VLASTNIHO OBSAHU -->
  </td>
  </tr>

 </table>
 
</body> 
</html>
