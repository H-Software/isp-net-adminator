<?php

require ("include/config.php"); 
require_once ("include/check_login.php");

require_once ("include/check_level.php");

if ( !( check_level($level,74) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html> 
      <head> ';

require_once ("include/charset.php");

?>

<title>Adminator2 - platby</title>

<script type="text/javascript" src="checkboxChanger.js"></script>

</head>

<body>

<?php require("head.php"); ?>

<?php require("category.php"); ?> 

 <tr>
   <td colspan="2" ><?php require("platby-subcat-inc2.php"); ?></td>
 </tr>
    
 <tr>
  <td colspan="2">
  
<?php

 //funkce
 
 //--- http://www.regexlib.com/REDetails.aspx?regexp_id=284
 function isEmail($to_validate)
 {
    $RegExp ="/^([a-zA-Z0-9_\-])+(\.([a-zA-Z0-9_\-])+)*@((\[(((([0-1])?";
    $RegExp.="([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?";
    $RegExp.="([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?";
    $RegExp.="([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5])))\.(((([0-1])?";
    $RegExp.="([0-9])?[0-9])|(2[0-4][0-9])|(2[0-5][0-5]))\]))|";
    $RegExp.="((([a-zA-Z0-9])+(([\-])+([a-zA-Z0-9])+)*\.)+([a-zA-Z])+";
    $RegExp.= "(([\-])+([a-zA-Z0-9])+)*))$/";
   return preg_match($RegExp,$to_validate);
 }

//---Check Czech phone number optional interneational preposition
//---+420 and gaps betweeen trinity of numbers
//---KOntroluje Ceske teefonni cislo, nepovinny mezinarodni predpona
//--- +420 a nepovinne mezery mezi trojicemi cisel
function isCzechPhoneNumber($to_validate)
{
  $RegExp = "/^(\+420)? ?\d{3} ?\d{3} ?\d{3}$/";
  return preg_match($RegExp,$to_validate);
}

 //trida pro posilani emailu na urovni :)
 // require_once "mailing/class.phpmailer.php";
 		 
 // bude to trvat dlouho
 set_time_limit(0);
   
 $rok=$_POST["rok"];
 $start=$_POST["start"];
 $stop=$_POST["stop"];
 $odeslano=$_POST["odeslano"];
 $firma=$_POST["firma"];
  
 $vybrano=$_POST["vybrano"];
    
 if(!isset($rok)){
    $rok = date("Y");
 }
 
if( ( ( !isset($odeslano) ) and ( !isset($vybrano) ) ) )
{
  echo "<form method=\"POST\" action=\"\" >";
      
  echo "<table border=\"1\" width=\"75%\" >";
     
  echo "<tr>    
	  <td>Firma: </td>
	<td> F.O. - vlastníci</td>";

  echo "<td>";
            echo "<select size=\"1\" name=\"rok\" >";
                
                for($i = 2006; $i <= date("Y"); $i++){            
            	    echo "<option value=\"".$i."\" "; if ($i == $rok){ echo " selected "; } echo " >".$i."</option>";
                }
            echo "</select>";

        echo "</td>";
        echo "<td> měsíc/e: </td>";
        echo "<td> Od: </td>";
        echo "<td>";

        echo "<select size=\"1\" name=\"start\" >";

        for ($i=1;$i<13;$i++)
        {
    	    echo "<option value=\"".$i."\" ";
    	    if ( $start == $i){ echo " selected "; }
    	    echo " >".$i."</option>";
        }

        echo "</select>";
        echo "</td>";

        echo "<td> Do: </td>";
        echo "<td>";

        echo "<select size=\"1\" name=\"stop\" >";

        for ($i=1;$i<13;$i++)
        {
         echo "<option value=\"".$i."\" ";
         if( $stop == $i){ echo " selected "; }
         echo " >".$i."</option>";
        }

        echo "</select>";
        echo "</td>";								      
	echo "<td align=\"top\" ><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></td>";
									      
 echo "</tr></table>";
 echo "</form>";
    
} //konec if - zobrazeni formu
elseif ( isset($odeslano) )
{ // vlastni vypis 

// echo "vybrano: rok: ".$rok.", počáteční měsíc: ".$start.", koncový měsíc: ".$stop.", firma: ".$firma;
 echo "<div style=\"font-size: 18px; \"> Výpis neplatičů za dané období </div>";
  
 $dotaz_vlastnici = pg_query("SELECT * FROM vlastnici WHERE ( (k_platbe > 0) AND ( firma is NULL ) AND ( (archiv = 0) or ( archiv is null ) ) ) order by id_cloveka ");
 $dotaz_vlastnici_radku = pg_num_rows($dotaz_vlastnici);

 echo "<br> počet záznamů: ".$dotaz_vlastnici_radku." <br><br>";
 
 echo "<form name=\"checkboxform\" action=\"\" method=\"POST\"  >";
     
 echo "<table border=\"1\" width=\"100%\" >";
 
 // prvni radek
 echo "<tr>";
 
 echo "<td width=\"5%\" ><b>Číslo neplatiče:</b></td>";
 echo "<td width=\"7%\" ><b>id cloveka: </b></td>";
 echo "<td><b>člověk: </b></td>";
 echo "<td><b>nezaplacená období: </b></td>";
 echo "<td><b>chyba: </b></td>";
 echo "<td width=\"5%\" ><b>aplikovat akci:</b></td>";
 
 echo "</tr>";
 
  $neplatic=1;
 
 while ( $data=pg_fetch_array($dotaz_vlastnici) ):
 
    global $id_cloveka;
    $id_cloveka=$data["id_cloveka"];
 
   // nejdriv zjistime, jestli uz byl clovek pripojenej
   $datum_pridani_src=$data["pridano"]; 
							  
    $datum_orezano = split('-', $datum_pridani_src);
    $rok_orezano = $datum_orezano[0];
    $mesic_orezano = $datum_orezano[1];
			
    if ( ( $rok_orezano > $rok ) )
    { $neexistoval=1; }							       
    if ( ( $rok_orezano == $rok ) )
    { 
     if ( $start == $stop )
     { // resi se platby pouze za jeden mesic     
        if( ( $mesic_orezano >= $start ) ){ $neexistoval=1; }	
     } // konec if start == stop
     else
     { // platby re resi za vic mesicu
        if( ( $mesic_orezano >= $start ) ){ $neexistoval=2; }    
     } // konec else
     
    } // konec if rok_orezano == rok

    //zde jestli Má clovek NetN
     $dotaz_obj=pg_query("SELECT * FROM objekty WHERE id_cloveka LIKE '$id_cloveka' ");
     $dotaz_obj_radku=pg_num_rows($dotaz_obj);
    
     if ( $dotaz_obj_radku == 1 )
     {
        while($data_obj=pg_fetch_array($dotaz_obj) ):
 	  $net_n=$data_obj["dov_net"];
	  $sikana_status=$data_obj["sikana_status"];
	endwhile;
     }
     else
     {
	$net_n="a";
	$sikanovan=0;
     }
     
    if ( $net_n == "n")
    { $nesurfuje=1; }
    else
    { $nesurfuje=0; }
    
    if ( $sikana_status == "a" )
    { $sikanovan=1; }
    else
    { $sikanovan=0; }
    
   //zde výběr plateb
   if ( $start > $stop)
   { echo " debile "; }

        for ( $i=$start;$i <= $stop; $i++)
        {
	    if ( $i < 10){ $mesic="0".$i; }
	    else { $mesic=$i; }
            $datum=$rok."-".$mesic;	    
	    
            $dotaz_platba=pg_query("SELECT id FROM platby WHERE ( id_cloveka = '$id_cloveka' AND zaplaceno_za LIKE '%$datum%' ) ");
	    $dotaz_platba_radku=pg_num_rows($dotaz_platba);
	    
	    if ( $dotaz_platba_radku == 1)
            { $zobraz="no"; }
	    elseif ( $dotaz_platba_radku == 0 )
            { $chybi .= $datum.", "; }
	    else
	    { $error = "1"; }
	}
	
  // zde zobrazovani
  if ( ( ( ( strlen($chybi) > 2 ) or ($error == "1") ) and ( $neexistoval != 1) ) )
  {
  
    echo "<tr>\n"; 
  
    echo "<td>".$neplatic."</td>";
    echo "<td>".$id_cloveka."</td>";
  
    if ( ($data["fakturacni"] > 0) )
    { 
	echo "<td bgcolor=\"teal\" >"; 
    }
    elseif( $nesurfuje == 1)
    {
	echo "<td bgcolor=\"red\" >"; 
    }
    elseif ($sikanovan == 1)
    {
	echo "<td bgcolor=\"#FF6666\" >";
    }
    else
    {	 
	if ( $neexistoval == 1)
	{ echo "<td >"; }
	elseif ( $neexistoval == 2 )
	{ echo "<td bgcolor=\"yellow\" >"; }
	else
	{  echo "<td> "; }
    }
  
    echo $data["prijmeni"]." ".$data["jmeno"].", <b>".$data["vs"]."<b><br>";
    echo "<span style=\"color: #444444; \">".$data["ulice"].", ".$data["mesto"]."</span></td>";
  
    echo "<td>".$chybi."</td>";
  
    if( ( strlen($error) > 0 ) ){ echo "<td> Ano </td>"; }
    else{ echo "<td> Ne </td>"; }
    
    echo "<td><input type=\"checkbox\" name=\"q".$neplatic."\" value=\"".$id_cloveka."\" ></td>";

    echo "<td><input type=\"hidden\" name=\"v".$neplatic."\" value=\"".$chybi."\" ></td>";
  
    echo "\n</tr> \n\n";
 
   $neplatic = $neplatic + 1;
  }  // konec if zobraz
 
  
  // nulovani promennych
  $error="";
  $zobraz="";
  $chybi="";
  $neexistoval="";
  // $nesurfuje="";
  
  endwhile;
  
 echo "<tr><td colspan=\"6\" ><br></td></tr>"; 
  
 echo "<tr>";
 
	echo "<td colspan=\"2\" ><b>počet neplatičů: </b> </td>";
	echo "<td colspan=\"1\">".$neplatic."</td>";
 
	echo "<td colspan=\"3\">";
	
	echo "
	 <INPUT TYPE=\"BUTTON\" onClick=\"checkedAll();\" VALUE=\" Zaškrtni vše \" >
	 <INPUT TYPE=\"BUTTON\" onClick=\"unchecked();\" VALUE=\" Odškrtni vše \" >
	 <INPUT TYPE=\"BUTTON\" onClick=\"reverseAll();\" VALUE=\" Reverse \" >
	
	</td>";	
 echo "</tr>";
 
 echo "<tr><td colspan=\"6\" ><br></td></tr>"; 
 echo "<tr>
	    <td colspan=\"2\" >Vyberte akci: </td>
	    <td><input type=\"submit\" value=\"VYBRAT\" name=\"vybrano\" ></td>
	
	    <td>
		<span style=\"padding-right: 20px;\">Vyberte akci:</span>
		<span style=\"\">
		  <select size=\"1\" name=\"akce\">
		    <option value=\"0\" style=\"color: gray;\" >Není zvoleno</option>
		    <option value=\"1\">poslat email</option>
		    <option value=\"2\">poslat SMS</option>
		    <option value=\"3\">nastavit šikanu</option>

		  </select>
		  </span>	
	    </td>
	</tr>";
 
 echo "</table>";
 echo "</form>";

    
} // konec elseif odeslano
elseif( isset($vybrano) )
{

  echo "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold;\">Neplatiči - FO :: Akce </div>\n";

  // akce - 1. :: poslani emailu
  // akce - 2. :: poslani SMS
  // akce - 3. :: sikana
  
    echo "<div style=\"padding-bottom: 10px;\" >Zvolená akce: ";
    	if( ($_POST["akce"] == 1)){ echo "Poslání mailu"; }
	elseif( ($_POST["akce"] == 2) ){ echo "Poslání SMS"; }
	elseif( ($_POST["akce"] == 3) ){ echo "Šikana"; }
	else{ echo "Nelze zjistit"; }
    echo "</div>";

  if( ($_POST["akce"] == 1) )
  { 
    while( list($nazev, $hodnota) = each($_POST) )
    {
      if( ereg("^q+[[:digit:]]+",$nazev) ) //pouze promenny s klientama
      {
        //vyber textu co dluzi
         $promenna = $nazev;
	 $promenna = ereg_replace("q", "v", $promenna);
	
	//vyber emailu
	 $id_cloveka = intval($hodnota);
	
	 $dotaz_vlastnici = pg_query("SELECT id_cloveka,mail,vs, jmeno, prijmeni FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
	 $dotaz_vlastnici_pocet = pg_num_rows($dotaz_vlastnici);
	 
	 if( $dotaz_vlastnici_pocet <> 1)
	 { //spatny pocet vlastniku, nic nedelame ..
	   $email = "";
	   $vs = "";
	   $jmeno = "";
	 } 
	 else
	 { //zjistit email ..
	    while($data_vlastnik = pg_fetch_array($dotaz_vlastnici))
	    {
		if( isEmail($data_vlastnik["mail"]) )
		{ 
		    $email = $data_vlastnik["mail"]; 
		    $vs = $data_vlastnik["vs"];    
		    $jmeno = $data_vlastnik["jmeno"]." ".$data_vlastnik["prijmeni"];
		}
		else
		{ 
		    $email = ""; 
		    $vs = "";
		    $jmeno = "";
		}
	    } //konec while
	 } //konec else if dotaz_vlastnici_pocet <> 1

	$mail_vysl = "";
	    
       //zde testovne posilani emailu do me schranky :) v normalnim stavu komented
	// $email = "patrik.majer@simelon.net";	 
       
       //tuto dulezite, presyp toho co ma platit 
        $dluzne_obdobi = $_POST[$promenna];
       
       $mail_send_ok = 0;
       
       //vlastni posilani emailu    
       if( ( strlen($email) > 3 ) )
       {

	    $predmet="Informace o neuhrazené platbě";

	    // $obsah="dluzite nam penize kruva drat :) cislo faktury: ".$data["Cislo"];

	    require("platby-neplatici-email-obsah.php");

	    require("platby-neplatici-email-obsah-plaintext.php");

	    $mail = new PHPMailer();

	    $mail->SetLanguage("cz");

	    $mail->IsSMTP();  // k odeslání e-mailu použijeme SMTP server
	    $mail->Host = "mail.simelon.net";  // zadáme adresu SMTP serveru
	    $mail->SMTPAuth = false;               // nastavíme true v případě, že server vyžaduje SMTP autentizaci

	    $mail->From = "upozorneni@simelon.net";   // adresa odesílatele skriptu
	    $mail->FromName = "Upozornění od společnosti Simelon, s.r.o."; // jméno odesílatele skriptu

	    $mail->AddAddress($email);  // přidáme příjemce
	    //$mail->AddBCC("hujer@simelon.net");  // přidáme příjemce

	    $mail->Subject = $predmet; // nastavíme předmět e-mailu

    	    $mail->IsHTML(true); // tento řádek je zbytečný, protože níže nastavujeme obsah proměnné AltBody
    	    $mail->AltBody = $obsah_plaintext;

    	    $mail->Body = $obsah;  // nastavíme tělo e-mailu

	    $mail->WordWrap = 50;   // je vhodné taky nastavit zalomení (po 50 znacích)
	    $mail->CharSet = "utf-8";   // nastavíme kódování, ve kterém odesíláme e-mail
	    
	    if(!$mail->Send())
	    {
    		$mail_vysl .= '<div style="color: red; font-weight: bold; ">Došlo k chybě při odeslání e-mailu.';
    		$mail_vysl .= 'Chybová hláška: ' . $mail->ErrorInfo." </div>";
	    }
	    else
	    {
    		$mail_vysl .= '<div style="color: green; font-weight: bold; " >E-mail byl v pořádku odeslán.</div>';
		$mail_send_ok = 1;
	    }

	} //konec strlen(email) > 3

	//ulozeni od archivu zmen
	$pole2 = "";
					  
	$pole2 .= "<b>akce: poslani emailu z duvodu neplaceni;</b><br>";
	$pole2 .= " puvodni data: [id_cloveka]=> ".$id_cloveka." ,";
	$pole2 .= "<br>stavajici data: [email] => ".$email.", [jmeno] => ".$jmeno.", [dluzne_mesice] => ".$dluzne_obdobi;
	
	if( $mail_send_ok == 1 ){ $vysledek_write="1"; }
	
	$add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");
															  	 
	//vypis
	 echo "<div style=\"\">Zvolena polozka <b>č.".$id_cloveka."</b>, dluzi: ".$_POST[$promenna];
	 echo ", email: <b>".$email."</b>, vs: ".$vs.", jmeno: ".$jmeno." ";
	 echo "(debug: promenna: ".$promenna.", pocet_vlastniku_select: ".$dotaz_vlastnici_pocet.")</div>";
	 echo $mail_vysl;
	  
	 // echo "vypis: jmeno: $nazev , hodnota: $hodnota <br> \n";
      }// konec if ! id_polozky == "upraveno"
  
    } //konec while
    
    
  } //konec if AKCE == 1
  elseif( ($_POST["akce"] == 2) )
  {

    while( list($nazev, $hodnota) = each($_POST) )
    {
      if( ereg("^q+[[:digit:]]+",$nazev) ) //pouze promenny s klientama
      {
        //vyber textu co dluzi
         $promenna = $nazev;
	 $promenna = ereg_replace("q", "v", $promenna);
	
	//vyber 
	 $id_cloveka = intval($hodnota);
	
	 $dotaz_vlastnici = pg_query("SELECT id_cloveka,telefon FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
	 $dotaz_vlastnici_pocet = pg_num_rows($dotaz_vlastnici);
	 
	 if( $dotaz_vlastnici_pocet <> 1)
	 { //spatny pocet vlastniku, nic nedelame ..
	    $telefon = "";
	 }
	 else
	 { //zjistit telefon ..
	    while($data_vlastnik = pg_fetch_array($dotaz_vlastnici))
	    {
		if( isCzechPhoneNumber($data_vlastnik["telefon"]) )
		{ $telefon = $data_vlastnik["telefon"]; }
		else
		{ $telefon = ""; }
	    } //konec while
	    
	 } //konec else if dotaz_vlastnici_pocet <> 1
       
        if( (strlen($telefon) >= 3) ) // jestli je v prom. telefon neco, tak ..
	{
	  //zde vlastni posilani SMS
	  
	  //$text = "Vazeni klienti, omlouvame se Vam za pochybeni. Omylem Vam byla zaslana sms o ";
	  //$text .= "neuhrazene fakture. SMS ingorujte. Dekujeme za pochopeni SIMELON,s.r.o.";

	  $text = "Dobry den. Nemate uhrazenou platbu za internet. ";
	  $text .= "Prosim uhradte ji co nejdrive, aby nedoslo k omezeni sluzeb. Simelon.NET. tel.: 391 009 400";

	  //insert do DB do prislusne tabulky		   
	  $vlastnik_sms_send=mysql_query("insert into QUEUE (PHONE, MSG,SCRIPT) VALUES ('$telefon', '$text','Mobilem') ");
			 
	  sleep(2);
		 
	  $last_id=mysql_insert_id();
	  $dotaz_se=mysql_query("SELECT * FROM QUEUE WHERE ID = '$last_id' ");
			   
	  while( $data_se=mysql_fetch_array($dotaz_se) )
	  { $last_status=$data_se["STATUS"]; }
			   
	  if( $last_status == 1 ){ $aut_sms_stav = "OK"; } // uspesne odeslano
	  elseif( $last_status == 0){ $aut_sms_stav = "chyba"; } // nelze odeslat, chyba pri odesilani
	  else{ $last_status = "N/A"; } //ted este nevim
						         
	}

	//ulozeni od archivu zmen
	$pole2 = "";
					  
	$pole2 .= "<b>akce: poslani SMS z duvodu neplaceni;</b><br>";
	$pole2 .= " puvodni data: [id_cloveka]=> ".$id_cloveka." ,";
	$pole2 .= "<br>stavajici data: [tel] => ".$telefon.", [dluzne_mesice] => ".$_POST[$promenna];
	
	if( $aut_sms_stav = "OK" ){ $vysledek_write="1"; }
	
	$add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");
	
	//vypis
	 echo "<div style=\"\">Zvolena polozka <b>č.".$id_cloveka."</b>, dluzi: ".$_POST[$promenna];
	 echo ", telefon: <b>".$telefon."</b>, stav poslani SMS: ".$aut_sms_stav." ";
	 echo "(debug: promenna: ".$promenna.", pocet_vlastniku_select: ".$dotaz_vlastnici_pocet.")</div>";
	 echo $mail_vysl;             
       
        $aut_sms_stav = "";
	
       } //konec if ereg $nazev
       	 
    } //konec while
      	 
  } //konec elseif POST akce == 2
  elseif( ($_POST["akce"] == 3) )
  {
   //nastaveni sikany
    while( list($nazev, $hodnota) = each($_POST) )
    {
      if( ereg("^q+[[:digit:]]+",$nazev) ) //pouze promenny s klientama
      {
        //vyber textu co dluzi
         $promenna = $nazev;
	 $promenna = ereg_replace("q", "v", $promenna);
	
	//vyber 
	 $id_cloveka = intval($hodnota);

	//zda vyber objektu a nastaveni sikany :)
	$dotaz_obj = pg_query("SELECT 
				    id_komplu, ip, dov_net, sikana_status, typ 
			       FROM objekty WHERE id_cloveka = '$id_cloveka'");
	$dotaz_obj_num = pg_num_rows($dotaz_obj);

	if($dotaz_obj_num == 0)
	{ 
	    $ip_print = "E -nejsou objekty"; 
	    
	    //vypis
	    echo "<div style=\"\">Zvolena polozka <b>č.".$id_cloveka."</b>, dluzi: ".$_POST[$promenna];
	    echo ", ip_adresa: <b>".$ip_print."</b>, stav: ".$stav." ";
	    echo "(debug: promenna: ".$promenna.", pocet_objektu: ".$dotaz_obj_num.")</div>";

    	    $stav = "";
    
	}
	
	while($data_obj = pg_fetch_array($dotaz_obj))
	{
	    $ip = $data_obj["ip"];
	    $ip_print = $ip;
	    $id_objektu = $data_obj["id_komplu"];
	    
	    if( $data_obj["dov_net"] == "n")
	    { $stav = "E1 - má NetN"; }
	    elseif( $data_obj["sikana_status"] == "a")
	    { $stav = "E2 - má šikanu"; }
	    elseif( $data_obj["typ"] == "3" )
	    { $stav = "E3 - apčko"; }
	    else
	    {
		//zde nastaveni sikany, prezvato z sikanovani SRO lidi
		
		$sikana_text = "Máte nedoplatek za internet, období: ".$_POST[$promenna];
    		$sikana_text .= "Dostavte se na naši provozovnu - Žižkova 247, popř. tel. 391 009 400.";

    		$obj_upd = array( "sikana_status" => "a", "sikana_cas" => "8", "sikana_text" => $sikana_text);

    		$obj_id = array( "id_komplu" => $id_objektu );
    		$res = pg_update($db_ok2, 'objekty', $obj_upd, $obj_id);

    		if ($res) { $stav .= "<span style=\"color: green; \" >update objektu úspěšný </span>.\n"; }
    		else { $stav .= "<span style=\"color: red; \">update objektu neúspěšný </span>."; }

		//ulozeni od archivu zmen
		$pole2 = "";
		
		$pole2 .= "<b>akce: automaticke nastaveni sikany z duvodu neplaceni;</b><br>";
		$pole2 .= " puvodni data: [id_komplu]=> ".$id_objektu;
		$pole2 .= "<br>stavajici data: [sikana_status] => a, [sikana_cas] => 8, [sikana_text] => ".$sikana_text;

	        if ( $res == 1){ $vysledek_write="1"; }

		$add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write') ");

		
	    } //konec else nejde-li o typ, NetN, sikany
	    
	  //vypis
	  echo "<div style=\"\">Zvolena polozka <b>č.".$id_cloveka."</b>, dluzi: ".$_POST[$promenna];
	  echo ", ip_adresa: <b>".$ip_print."</b>, stav: ".$stav." ";
	  echo "(debug: promenna: ".$promenna.", pocet_objektu: ".$dotaz_obj_num.")</div>";

          $stav = "";


	} //konec while vypis objektu
	
       } //konec if ereg $nazev
       	 
    } //konec while
    
  } //konec elseif akce == 3
  else
  {
    echo "<div style=\"padding-top: 20px; padding-left: 20px; color: red; font-size: 18px; \">
	    Chyba! Nebyla zvolena žádná akce ..
	  </div>";
  }
  
} // konec elseif isset vybrano
else
{
    echo "<div>Chyba! Nepodporovaný mód</div>";
}

?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>
