<?

require "include/main.function.shared.php";
require "include/config.php";

 $data_s = "/srv/www/htdocs.ssl/reinhard.remote.log";

 /* smazem log soubor */
 system("sudo ../adminator2/scripts/delete.log.pl ".$data_s);

 if( $_GET["item"] == 1 )
 { exec("/srv/www/htdocs.ssl/adminator3/scripts/work.pl 1 0 0",$vysl); }
 elseif( $_GET["item"] == 2)
 { exec("/srv/www/htdocs.ssl/adminator3/scripts/work.pl 0 1 0",$vysl); }
 elseif( $_GET["item"] == 3)
 { exec("/srv/www/htdocs.ssl/adminator3/scripts/work.pl 0 0 1",$vysl); }

 if( is_array($vysl) )
 {
    foreach($vysl as $val) 
    {
	$val2 = htmlspecialchars($val);
	$odpoved .= " ".$val2;
    }
 }
 else
 { $odpoved = htmlspecialchars($vysl); }

 if( ( file_exists ($data_s) ) )
 {
   $fp = fopen($data_s, "r");
   $odpoved2 = fread($fp, filesize ($data_s));
   fclose ($fp);
 }
 else
 { $odpoved2 = "\n log soubor neexistuje \n"; }

 $odpoved2 = htmlspecialchars($odpoved2);
		
 header("Content-Type: text/xml");

 echo "<anketa>\n";
 echo "<odpoved id='odpoved0' >".intval($_GET["item"])."</odpoved>\n";
 echo "<odpoved id='odpoved1' >".$odpoved."</odpoved>\n";
 
 echo "<odpoved id='odpoved2' >".$odpoved2."</odpoved>\n";
 
 echo "</anketa>\n";

?>