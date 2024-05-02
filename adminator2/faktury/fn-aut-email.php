<?php

set_time_limit(0);

global $cesta;

$cesta = "../";

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 
require_once($cesta."include/check_login.php");
require_once($cesta."include/check_level.php");

if( ($_GET["no_login"] != "yes") )
{
 if( !( check_level2("","lvl_faktury_fn_aut_email") ) ) 
 {
  header("Location: ".$cesta."nolevelpage.php");
 
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
 }

}
 
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
require ($cesta."include/charset.php"); 

?>

<title>Adminator2 - Neuhrazené faktury </title> 

</head> 

<body> 

<?php require ($cesta."head.php"); ?> 

<?php require ($cesta."category.php"); ?> 

 <tr>
  <td colspan="2" height="20" bgcolor="silver" ><?php require($cesta."fn-cat.php"); ?> </td>
 </tr>
    
<tr>
  <td colspan="2">
    <!-- zacatek vlastniho obsahu -->

<?php

echo "<div style=\"font-size: 20px; font-weight: bold; padding-top: 20px; padding-bottom: 20px; \" >
	Upozornění na neuhrazení faktury emailem </div>";

$odeslano=$_GET["odeslano"];

$typ=$_GET["typ"];

if ( !isset($odeslano) )
{
  echo "<form action=\"\" method=\"GET\" >";
  
    echo "<div >Opravdu odeslat emaily o neuhrazených fakturách ? </div>";

    echo "<div style=\"padding-top: 20px; \" >
	    <span style=\"font-weight: bold; margin-right: 20px; \">Režim odesílání:</span>
    	    <select size=\"1\" name=\"typ\" >
		<option value=\"0\" class=\"fn-select-nevybrano\" >Nevybráno</option>
		<option value=\"1\" >Odeslat všem, kterým se email o neuh. fa. ještě neodesílal </option>
	    </select>
	    
	  </div>";

    echo "<div style=\"padding-top: 20px; padding-left: 120px; \" ><input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>";

  echo "</form>";
   
} // konec if !isset odeslano
elseif( $typ == 0)
{

    echo "<div style=\"padding-top: 20px; \" >Musíte vybrat nějakou možnost! </div>";

    echo "<div style=\"padding-top: 20px; \" ><a href=\"fn-aut-email.php\">Zpět</a></div>";

}
else
{

 //debug dotaz
// $dotaz=$conn_mysql->query("SELECT *,DATE_FORMAT(Datum, '%d.%c.%Y') as Datum2, DATE_FORMAT(DatSplat, '%d.%c.%Y') as DatSplat2
//			FROM faktury_neuhrazene WHERE ( ignorovat != '1' AND par_id_vlastnika > 0 and Cislo LIKE '280107026' ) LIMIT 1 ");
 
 if ( $typ == 1)
 {
  //normal dotaz 
  $dotaz=$conn_mysql->query("SELECT *,DATE_FORMAT(Datum, '%d.%c.%Y') as Datum2, DATE_FORMAT(DatSplat, '%d.%c.%Y') as DatSplat2
			FROM faktury_neuhrazene WHERE ( aut_email_stav = '0' AND ignorovat = '0' 
			AND par_id_vlastnika > 0 ) ");
 }
 	
 $dotaz_radku= $dotaz->num_rows;
 
 if ( $dotaz_radku == 0)
 { echo "<div>Žádné neuhrazené faktury v databázi.</div>"; }
 else
 {

  // require_once "../mailing/class.phpmailer.php";
  
  while( $data= $dotaz->fetch_array() )
  {
    $posilat_email="ano";
    
    echo "<div ><span style=\"padding-right: 20px; \">";
    echo "cislo: ".htmlspecialchars($data["Cislo"]).", Jmeno: ".htmlspecialchars($data["Jmeno"]).", Firma: ".htmlspecialchars($data["Firma"])."</span>";
    
    $vysl .= "cislo: ".htmlspecialchars($data["Cislo"]).", Jmeno: ".htmlspecialchars($data["Jmeno"]).", Firma: ".htmlspecialchars($data["Firma"])."";
    
     $id_faktury=$data["id"];
     
     $id_cloveka = $data["par_id_vlastnika"];
     $ico=$data["ICO"];
     
     //zjistime vlastnika
     $dotaz_vlastnik=pg_query("SELECT mail, splatnost FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
     $dotaz_vlastnik_radku=pg_num_rows($dotaz_vlastnik);
     
     if( $dotaz_vlastnik_radku <> 1)
     {
      $aut_email_stav = "9"; // chybovy stav, nelze urcit vlastnika faktury

      $posilat_email="ne";          
     } // konec dotaz_vlastnik_radku <> 1
     else
     {
     
      while( ( $data_vlastnik=pg_fetch_array($dotaz_vlastnik) ) )
      { 
       $vlastnik_email=$data_vlastnik["mail"]; 
       $vlastnik_splatnost=$data_vlastnik["splatnost"];
      }
            
       // ted zjistime jestli ma vlastnik email
       if ( ( strlen($vlastnik_email) < 5 ) )
       { 
        $aut_email_stav = "8"; // klient nemá email nebo email neni ve spravne podobe
	
        $posilat_email="ne";
	 
       } // konec else strlen vlastnik_email
       else
       {
        $prijemce=$vlastnik_email;
       }
	  
     
     } // konec else if dotaz_vlastniku_radku <> 1
		
    //ted to poslem mailem

    if ( $posilat_email == "ano" )
    {
    
    $predmet="Informace o neuhrazene fakture";
    
    $cislo_faktury=$data["Cislo"];
    
    $VarSym = $data["VarSym"];
    
    $Datum2 = $data["Datum2"];
    $DatSplat2 = $data["DatSplat2"];
    
    $KcCelkem = $data["KcCelkem"];
    $KcLikv = $data["KcLikv"];

    $Jmeno = $data["Jmeno"];
    $Firma = $data["Firma"];
    
    // $obsah="dluzite nam penize kruva drat :) cislo faktury: ".$data["Cislo"];
    
    require("fn-aut-email-obsah.php");
    
    require("fn-aut-email-obsah-plaintext.php");
    
    $mail = new PHPMailer();

    $mail->SetLanguage("cz");

    $mail->IsSMTP();  // k odeslání e-mailu použijeme SMTP server
    $mail->Host = "mail.simelon.net";  // zadáme adresu SMTP serveru
    $mail->SMTPAuth = false;               // nastavíme true v případě, že server vyžaduje SMTP autentizaci

    $mail->From = "upozorneni@simelon.net";   // adresa odesílatele skriptu
    $mail->FromName = "Upozornění od společnosti Simelon, s.r.o."; // jméno odesílatele skriptu 

    $mail->AddAddress($prijemce);  // přidáme příjemce
//    $mail->AddBCC("patrik.majer@simelon.net");  // přidáme příjemce

    $mail->Subject = $predmet; // nastavíme předmět e-mailu

     $mail->IsHTML(true); // tento řádek je zbytečný, protože níže nastavujeme obsah proměnné AltBody
     $mail->AltBody = $obsah_plaintext;
     
     $mail->Body = $obsah;  // nastavíme tělo e-mailu

    $mail->WordWrap = 50;   // je vhodné taky nastavit zalomení (po 50 znacích)
    $mail->CharSet = "utf-8";   // nastavíme kódování, ve kterém odesíláme e-mail

    if(!$mail->Send())
    {
      echo '<span style="color: red; font-weight: bold; ">Došlo k chybě při odeslání e-mailu. </span>';
      echo 'Chybová hláška: ' . $mail->ErrorInfo." ";
    
     $aut_email_stav="2";
    
     $vysl .= "Došlo k chybě při odeslání e-mailu.".'Chybová hláška: '.$mail->ErrorInfo." ";
    }
    else
    {
     echo '<span style="color: green; font-weight: bold; " >E-mail byl v pořádku odeslán.</span>';
     
     $aut_email_stav="1";
     
     $vysl .= "E-mail byl v pořádku odeslán.";
    } 
    
    } //konec if posilat_email == ano

    //ted to ulozime, i kdyz email nebyl odeslan
    
    $uprava=$conn_mysql->query("UPDATE faktury_neuhrazene 
			    SET aut_email_stav='".intval($aut_email_stav)."', aut_email_datum=Now() 
			    WHERE id=".intval($id_faktury)." Limit 1 ");
    
    echo "<span style=\"padding-left: 10px; padding-right: 10px; \">stav odeslani emailu: ".intval($aut_email_stav)."</span></div>";
          
    $vysl .= "stav odeslani emailu: ".intval($aut_email_stav);
    
    //ted ulozime do archivu poslani emailu
    $log=$conn_mysql->query("INSERT INTO fn_aut_email_log (zprava) VALUES ('".$conn_mysql->real_escape_string($vysl)."') ");
    
    $aut_email_stav="";
    
    $prijemce="";
    
    $vysl="";
    
  } // konec while
 
 } // konec else if dotaz_radku == 0

} // konec else ! isset odeslano

?>

   <!-- konec vlastniho obsahu -->
 </td>
  </tr>
  
 </table>

</body> 
</html> 

