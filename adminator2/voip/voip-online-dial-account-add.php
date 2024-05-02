<?php

set_time_limit(0);

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if ( !( check_level($level,126) ) )
{
// neni level

 $stranka=$cesta.'nolevelpage.php';
 header("Location: ".$stranka);
 
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
      
}
	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ($cesta."include/charset.php"); 

?>

<title>Adminator 2 - VoIP Online systém Dial - Přidání účtu</title> 

<script type="text/javascript" src="include.js.1.js"></script>

</head> 

<body> 

<? include ($cesta."head.php"); ?> 

<? include ($cesta."category.php"); ?> 

 
 <tr>
 <td colspan="2" bgcolor="silver" height=""><? include("voip-subcat-inc.php"); ?></td>
  </tr>
 
  <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
 <?

    $odeslano = $_POST["odeslano"];    		$i_acl = $_POST["i_acl"];
    $id_customer = $_POST["id_customer"];	$blocked = $_POST["blocked"];
    $i_lang = $_POST["i_lang"];			$i_time_zone = $_POST["i_time_zone"];
    $h323_password = $_POST["h323_password"];	$id = $_POST["id"];
    $i_produkt = $_POST["i_produkt"];		$balance = $_POST["balance"];
    $login = $_POST["login"];			$password = $_POST["password"];

    $credit_limit = $_POST["credit_limit"];
    
    //promene natvrdo
    $billing_model = "1"; // Typ - kredit
    $batch_name = ""; // z customera - dodelat
    
    if( ( strlen($login) < 1) and ( $id > 0 ) ){ $login = $id; }
	
    if( (strlen($id_customer) < 1) ){ $id_customer = $_GET["id_customer"]; }
    
     $cas1 = explode(" ", microtime());
     $cas1 = $cas1[1] + $cas1[0];
     $rd = "10000"; /* zaokrouhlování */
       
    /*
    if( isset($note) )
    {
     //skusime nacist udaje z vlastniku 
     $dotaz_vlastnici = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$note' ");
     $dotaz_vlastnici_radku = pg_num_rows($dotaz_vlastnici);
    
      if( $dotaz_vlastnici_radku <> 1)
      { 
       echo "<div style=\"\">Chyba! Nelze vybrat vlastnika pro nacteni info. ";
       echo "<span style=\"color: grey;\">(debug: radku: $dotaz_vlastnici_radku )</span> </div>"; 
      }
      else
      {
        while( $data = pg_fetch_array($dotaz_vlastnici) )
	{
	  if( ( strlen($firstname) < 1) ){ $firstname = $data["jmeno"]; }
	  if( ( strlen($lastname) < 1) ){ $lastname = $data["prijmeni"]; }
	  if( ( strlen($address) < 1) ){ $address = $data["ulice"]; }
	  if( ( strlen($city) < 1) ){ $city = $data["mesto"]; }
	  if( ( strlen($phone) < 1) ){ $phone = $data["telefon"]; }
	  if( ( strlen($email) < 1) ){ $email = $data["mail"]; }
	}
	
      } // konec else if dotaz_vlastnici_radku <> 1
    } // konec if ! isset odeslano and isset note
    */
    
 //zde kontrola formu
 if( ( isset($odeslano) and !( $id_customer > 0 ) ) )
 { 
   $error .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; color: red; \">";
   $error .= "Je potřeb vyplnit pole: \"Klient\". </div>"; 
 }

 if( ( isset($odeslano) and !( $i_produkt > 0 ) ) )
 { 
   $error .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; color: red; \">";
   $error .= "Je potřeb vyplnit pole: \"Produkt\". </div>"; 
 }

 if( ( isset($odeslano) and !( $id > 0 ) ) )
 { 
   $error .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; color: red; \">";
   $error .= "Je potřeb vyplnit pole: \"Identifikace účtu - Číslo\". </div>"; 
 }

 if( ( isset($odeslano) and ( strlen($h323_password) < 1 ) ) )
 { 
   $error .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; color: red; \">";
   $error .= "Je potřeb vyplnit pole: \"VoIP heslo\". </div>"; 
 }

 if( ( isset($odeslano) and ( strlen($login) < 1 ) ) )
 { 
   $error .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; color: red; \">";
   $error .= "Je potřeb vyplnit pole: \"Login\". </div>"; 
 }

 if( ( isset($odeslano) and ( strlen($password) < 1 ) ) )
 { 
   $error .= "<div style=\"padding-top: 10px; padding-bottom: 10px; font-weight: bold; color: red; \">";
   $error .= "Je potřeb vyplnit pole: \"Heslo\". </div>"; 
 }

 
 echo "<div style=\"padding-top: 10px; \">
	<span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Online Voip systém DialTelecom</span>
	<span style=\"padding-left: 20px; \" >- Přidání účtu</span>
       </div>";
 
 echo "<div style=\"padding-top: 10px; padding-left: 5px; padding-bottom: 20px; \">"; 
 echo "<div style=\"padding-top: 20px; \"></div>";

    if( ( isset($odeslano) and  !isset($error) ) )
    {
    
      $vysl = $id_customer."&".$id."&".$blocked."&".$i_lang."&".$i_time_zone."&".$h323_password;
      $vysl .= "&".$i_produkt."&".$balance."&".$login."&".$password."&".$i_acl;
      $vysl .= "&".$billing_model."&".$batch_name;
      
    //  system("/srv/www/cgi-bin/cgi-adm2/customer_add.pl \"".$vysl."\"",$vysl);
     
      echo "vysl: $vysl , vysl2: $vysl2 , error: $error ";
    
    /*  
      //pridani do archivu zmen
      $pole2 .="<b>akce: voip - pridani klienta (customer); </b><br>";
    
     // foreach ($obj_upd as $key => $val) 
      $pole2 .= "[name] => ".$name.", [blocked] => ".$blocked.", [firstname] => ".$firstname;
      $pole2 .= ", [lastname] => ".$lastname.", [address] => ".$address.", [city] => ".$city;
      $pole2 .= ", [balance] => ".$balance.", [companyname] => ".$companyname;
      $pole2 .= ", [phone] => ".$phone.", [email] => ".$email.", [note] => ".$note;
		 
     if ( $vysl == 0){ $vysledek_write=1; }
     $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");
    
     echo "<div style=\"padding-top: 20px; \"><a href=\"voip-online-dial-account-add.php\">Zpět</a></div>";
    */     
    }
    else
    {
     echo $error; 
     echo "<form action=\"\" method=\"post\" name=\"form3\" >";
    
     //nacteni html formulare
     require("voip-onlide-dial-account-add-inc.php");    

     echo "</form>";
     
    } // konec else if isset odeslano


  $cas2 = explode(" ", microtime());
  $cas = (round((($cas2[1] + $cas2[0]) - $cas1) * $rd)) / $rd;   
  echo "<div>".$cas."</div>";
        
 echo "</div>";
 
 ?> 
  <!-- konec vlastniho obsahu -->  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

