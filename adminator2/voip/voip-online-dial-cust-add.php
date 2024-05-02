<?php

global $cesta;

$cesta = "../";

include ($cesta."include/config.php"); 
include ($cesta."include/check_login.php");

include ($cesta."include/check_level.php");

if ( !( check_level($level,123) ) )
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

<title>Adminator 2 - VoIP Online systém Dial - Přidání klienta</title> 

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
  
 <?php

    $odeslano = $_POST["odeslano"];
    
    $name = $_POST["name"];
    $blocked = $_POST["blocked"];
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $address = $_POST["address"];
    $city = $_POST["city"];
    $balance = $_POST["balance"];
    $companyname = $_POST["companyname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    
    // $note = $_POST["note"];
    
    //promene natvrdo
    
    $iso_4217 = "CZK"; // Mena
    $i_customer_type = "1"; // maloobchod
    $i_customer_class = "8"; // trida klientu - Simelon
 
    //dasli promenne
    $vlastnik_hledani = $_POST["vlastnik_hledani"];
    $id_vlastnika = $_POST["id_vlastnika"];
    
    $note = $id_vlastnika;
    
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
    
 echo "<div style=\"padding-top: 10px; \">
	<span style=\"font-weight: bold; font-size: 18px; border-bottom: 1px solid grey; \" >Online Voip systém DialTelecom</span>";
 
 // if( $item ==2 )
 { echo "<span style=\"padding-left: 20px; \" >- Přidání klienta</span>"; }

 echo "</div>";
 
 echo "<div style=\"padding-top: 10px; padding-left: 5px; padding-bottom: 20px; \">";
  
 // pridani klienta
 
    echo "<form action=\"\" method=\"post\" name=\"form3\" >";
    
    echo "<div style=\"padding-top: 20px; \"></div>";

    if( ( isset($odeslano) and ( strlen($name) > 1 ) ) )
    {
    
      $vysl = $name."&".$blocked."&".$firstname."&".$lastname."&".$address."&".$city."&".$balance;
      $vysl .= "&".$companyname."&".$phone."&".$email."&".$note."&".$iso_4217;
      
      system("/var/www/cgi-bin/cgi-adm2/customer_add.pl \"".$vysl."\"",$vysl);
     
      // echo "vysl: $vysl , vysl2: $vysl2 , error: $error ";
      
      //pridani do archivu zmen
      $pole2 .="<b>akce: voip - pridani klienta (customer); </b><br>";
    
     // foreach ($obj_upd as $key => $val) 
      $pole2 .= "[name] => ".$name.", [blocked] => ".$blocked.", [firstname] => ".$firstname;
      $pole2 .= ", [lastname] => ".$lastname.", [address] => ".$address.", [city] => ".$city;
      $pole2 .= ", [balance] => ".$balance.", [companyname] => ".$companyname;
      $pole2 .= ", [phone] => ".$phone.", [email] => ".$email.", [note] => ".$note;
		 
     if ( $vysl == 0){ $vysledek_write=1; }

     $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole2','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");
    
     echo "<div style=\"padding-top: 20px; \"><a href=\"voip-online-dial-cust-add.php\">Zpět</a></div>";
         
    }
    else
    {
     if( ( isset($odeslano) and ( strlen($name) < 1 ) ) )
     { 
       echo "<div style=\"padding-top: 20px; padding-bottom: 20px; font-weight: bold; color: red; \">";
       echo "Je potřeb vyplnit pole: \"Jméno klienta ( popis )\". </div>"; 
     }
    
     //nacteni html formulare
     require("voip-onlide-dial-add-customer-inc.php");    
    
    } // konec else if isset odeslano
    
    echo "</form>";
 
 echo "</div>";
 
 ?> 
  <!-- konec vlastniho obsahu -->  
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

