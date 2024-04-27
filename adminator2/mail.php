<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,15) ) )
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

include ("./include/charset.php"); 

?>

<title>Adminator2 - mail</title> 

</head> 

<body> 

<? 
    global $cesta;
    $cesta="./";
    include ("./head.php"); 
?> 

<? include ("./category.php"); ?> 

 
 <tr>
 <td colspan="2" bgcolor="silver" height=""><? include("others-subcat-inc.php"); ?></td>
  </tr>
 
  <tr>
  <td colspan="2">

    <!-- zacatek vlastniho obsahu --> 
     
  <?
  
  $odeslano=$_POST["odeslano"];
  $source=$_POST["source"];
  $predmet=$_POST["predmet"];
  $obsah=$_POST["obsah"];
  
  $typ=$_POST["typ"];
  
  for ($i=0;$i<10;$i++) 
  {
  $id_q="$q".$i;
  
  $q=$_POST[$id_q];
  
  }
  
  if ( $odeslano)
  {
  // odeslano, budeme posilat email
  
    // nandani emailu do pole
    
    // $bcc[]="test@adminator.net";
      
    $pocet_prijemcu=0;
    
    // while ( list($nazev, $hodnota) = each($_POST) ):
    foreach($_POST as $nazev => $hodnota) {

    if ( (  ( $nazev != "odeslano") and ( $nazev != "source") and ( $nazev != "predmet") and ( $nazev != "obsah") ) )
     {
      if ( ( strlen($hodnota) > 4 ) )
      { 
        $bcc[]=$hodnota; 
        $pocet_prijemcu++;
      }
      
      
     }
    }
    // endwhile;
     
    // konec nandavani emailu do pole
    
    require "mailing/class.phpmailer.php";
  
    $mail = new PHPMailer();
  
    $mail->SetLanguage("cz");
  
    $mail->IsSMTP();  // k odeslání e-mailu použijeme SMTP server
    $mail->Host = "mail.simelon.net";  // zadáme adresu SMTP serveru
    $mail->SMTPAuth = false;               // nastavíme true v případě, že server vyžaduje SMTP autentizaci
  
    $mail->From = $source;   // adresa odesílatele skriptu
    $mail->FromName = $source; // jméno odesílatele skriptu (zobrazí se vedle adresy odesílatele)

    // $mail->AddAddress("@simelon.net");  // přidáme příjemce
    // $mail->AddAddress("druhy.prijemce@example.net", "Jméno druhého příjemce");  // a klidně i druhého, včetně jména
    // $mail->AddBCC("patrik.majer@simelon.net");

    foreach ($bcc as $my_bcc) 
    {
	$mail->AddBCC($my_bcc);
    }
          
    $mail->Subject = $predmet;    // nastavíme předmět e-mailu
    
    if ($typ == 2)
    {
     $mail->IsHTML(true); // tento řádek je zbytečný, protože níže nastavujeme obsah proměnné AltBody
     $mail->AltBody = "Alternativní textový obsah pro e-mail, bez HTML značek";
    }
    else
    {	 
    $mail->Body = $obsah;  // nastavíme tělo e-mailu
    }
    
    $mail->WordWrap = 50;   // je vhodné taky nastavit zalomení (po 50 znacích)
    $mail->CharSet = "utf-8";   // nastavíme kódování, ve kterém odesíláme e-mail

    if(!$mail->Send()) 
    {  
	 echo '<span style="color: red; font-weight: bold; "><br>Došlo k chybě při odeslání e-mailu. <br><br></span>';
         echo 'Chybová hláška: ' . $mail->ErrorInfo." <br> ";
    }
    else
    {
     echo '<span style="color: green; font-weight: bold; " ><br>E-mail byl v pořádku odeslán.<br><br></span>';
     echo 'počet příjemců: '.$pocet_prijemcu.'</span><br><br>';
    }  
  
  } // konec if odeslano
  else
  {
  // zobrazeni formu
  
  global $id_prijemce;
  
  $id_prijemce=1;
  
  echo "<form action=\"\" method=\"POST\" >";
  
  echo "<table border=\"1\" width=\"100%\" >";
  
  echo "<tr>
	    <td colspan=\"2\" valign=\"top\" > Příjemci:  </td>
	    <td rowspan=\"30\" valign=\"top\" >";
	    
		// sem hlavni okno
		echo "<table border=\"1\" width=\"100%\" >";
	
		    echo "<tr>
				<td>Email poslat jako:</td>
				<td>
				    <select name=\"typ\" size=\"1\">";
				    
				    echo "<option value=\"1\"> prostý text</option>";
				    // echo "<><>";
				    echo "</select>
				</td>
				<td>Přijemce přidat do pole:</td>
				<td>
				    <select name=\"typ_kopie\" size=\"1\">";
					echo "<option value=\"1\">Skrytá kopie</td>";
					// echo "<option value=\"1\">Kopie</td>";
				    echo "</select>
				
				</td>
			  </tr>";
		        
		    echo "<tr>
				<td>zdrojová emailová adresa:</td>
				<td><input type=\"text\" name=\"source\" value=\"\" size=\"25\" ></td>
				<td>Předmět emailu: </td>
				<td><input type=\"text\" name=\"predmet\" value=\"\" size=\"40\" ></td>
			</tr>";
		    
		    echo "<tr><td colspan=\"4\" ><br></td></tr>";	    
	    
		    echo "<tr><td colspan=\"4\" ><textarea name=\"obsah\" cols=\"100\" rows=\"40\" ></textarea></td></tr>";
		    
		    echo "<tr><td colspan=\"4\" ><br></td></tr>";	    
	    	    
		    echo "<tr><td colspan=\"4\" ><input type=\"submit\" name=\"odeslano\" value=\"POSLAT\" ></td></tr>";
		    
		echo "</table>";
		
		// zde konci hlavni okno
	    echo "</td>
	</tr>";
  
  // tady generovani klikatek
  
  if (  !isset($_POST["odeslano2"]) )
  {
   echo "<tr><td width=\"5%\" >";
   // <input type=\"checkbox\" name=\"all\" value=\"1\" > 
   echo "</td>";
  
  echo "<td width=\"12%\" ><span style=\"text-align: center\">Všichni</span> </td></tr> \n";
  }
  else
  { echo "<tr><td colspan=\"1\" width=\"5%\"><br></td><td width=\"12%\"><br></td></tr> \n"; }
  
  echo "<tr><td colspan=\"2\" ><hr width=\"100%\"></td></tr> \n";
  
  if ( isset($_POST["odeslano2"]) )
  { // zde vypis neplaticu
  
    
  echo "<tr><td colspan=\"2\"><b> Vybraní příjemci: (neplatiči) </b></td></tr>";
  
    while ( list($nazev, $hodnota) = each($_POST) ):
    
    if ( ! ( $nazev == "odeslano2") )
     {
	
        $dotaz=pg_query("SELECT * from vlastnici WHERE id_cloveka = '$hodnota' ");
	$dotaz_radku=pg_num_rows($dotaz);
  
	if ( $dotaz_radku > 0 )
	{
  
	    while( $data=pg_fetch_array($dotaz) ):
    
		if ( ($data["mail"] == "NULL" ) )
		{
		echo "<tr><td ><input type=\"checkbox\" name=\"q".$id_prijemce."\" value=\"".$data["mail"]."\" ></td>";
		echo "<td>".$data["prijmeni"]." ".$data["jmeno"];
		echo "<br><span style=\"color: red; font-size: 12px\" > email není v systému</span></td></tr> \n\n";
  		
		}
		else
		{
		echo "<tr><td ><input type=\"checkbox\" name=\"q".$id_prijemce."\" value=\"".$data["mail"]."\" checked ></td>";
		echo "<td>".$data["prijmeni"]." ".$data["jmeno"];
		echo "<br><span style=\"color: #555555; font-size: 12px\" >".$data["mail"]."</span></td></tr> \n\n";
		}
		
		$id_prijemce++;
		
	    endwhile;
    
	} // konec if dotaz_radku > 0
		  
  
  
     } // konec if ! nazev == odeslano2
     endwhile;
  
  
  echo "<tr><td colspan=\"2\" ><hr width=\"100%\"></td></tr> \n";
  
  echo "<tr><td colspan=\"2\"><b> Výběr dalších příjemců: </b></td></tr>\n";
  
  }
  
  $dotaz=pg_query("SELECT * from vlastnici WHERE mail IS NOT null ORDER BY id_cloveka");
  $dotaz_radku=pg_num_rows($dotaz);
  
  if ( $dotaz_radku > 0 )
  {
  
    while( $data=pg_fetch_array($dotaz) ):
    
	echo "<tr><td ><input type=\"checkbox\" name=\"q".$id_prijemce."\" value=\"".$data["mail"]."\" ></td>";
	echo "<td>".$data["prijmeni"]." ".$data["jmeno"];
	echo "<br><span style=\"color: #555555; font-size: 12px\" >".$data["mail"]."</span></td></tr> \n\n";
  
	$id_prijemce++;
	
    endwhile;
    
  } // konec if dotaz_radku > 0
  
  
    
  echo "</table> \n";
  
  echo "</form> \n";
  
  }
  
  ?>
  
    <!-- konec vlastniho obsahu -->
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

