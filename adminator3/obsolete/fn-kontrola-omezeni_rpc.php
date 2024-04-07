<?

 require("include/function.fn-kontrola-omezeni.php");

 $zamek_stav = zamek("status");

 if( $zamek_stav == 1)
 {
   $odpoved = "Chyba! Generování souboru již probíhá";
 }
 else
 {
   zamek("lock");

   list($vlastnici, $vlastnici_pocet) = nacteni_vlastniku();

   $objekty_kontrola = vyber_objektu($vlastnici);

   $data = kontrola($objekty_kontrola);

   $smarty->assign("vlastnici_pocet",$vlastnici_pocet);

   //transformace do xml formatu
   $options = array( "addDecl" => true,  "defaultTagName" => "zaznam",
            "linebreak" => "\n",  "encoding" => "UTF-8",  "rootName" => "objekty");
   $serializer = new XML_Serializer($options);
   $serializer->serialize($data);

   $xml_data = $serializer->getSerializedData();

   $datum_nz = date('Y-m-d-H-i-s');
   $nazev_souboru = "export/fn_check/fn_check_log-".$datum_nz.".xml";

   // zapis xml formatu do souboru
   $soubor = fopen($nazev_souboru, "w");
   fwrite($soubor, $xml_data);
   fclose($soubor);

   $odpoved = "Vygenerování souboru proběhlo úspěšně";
 
   zamek("unlock");
 }
 
  //generovani zpetneho xml souboru        
  header("Content-Type: text/xml");
	
  echo "<vysledek>\n";
   echo "<odpoved id='odpoved0' >".$odpoved."</odpoved>\n";
  echo "</vysledek>\n";
	     
?>
