<?php
  require "mailing/class.phpmailer.php";

 $dotaz_emaily=mysql_query("SELECT * FROM users WHERE (  email_opravy = '1' and id = 2 )");
  
 while( $data = mysql_fetch_array($dotaz_emaily) )
 { 
    $prijemce = $data["email"];
         
    $predmet="Informace o vložení závady/opravy ";

    require("opravy-email-obsah-inc.php");
    require("opravy-email-obsah-plain-inc.php");

    $mail = new PHPMailer();
    $mail->SetLanguage("cz");

    $mail->IsSMTP();  // k odeslání e-mailu použijeme SMTP server
    $mail->Host = "mail.simelon.net";  // zadáme adresu SMTP serveru
    $mail->SMTPAuth = false;               // nastavíme true v případě, že server vyžaduje SMTP autentizaci

    $mail->From = "adminator2@simelon.net";   // adresa odesílatele skriptu
    $mail->FromName = "Automat systému Adminátor2"; // jméno odesílatele skriptu

    $mail->AddAddress($prijemce);  // přidáme příjemce
    $mail->Subject = $predmet; // nastavíme předmět e-mailu

     $mail->IsHTML(true); // tento řádek je zbytečný, protože níže nastavujeme obsah proměnné AltBody
     $mail->AltBody = $obsah_plaintext;
     $mail->Body = $obsah;  // nastavíme tělo e-mailu

    $mail->WordWrap = 50;   // je vhodné taky nastavit zalomení (po 50 znacích)
    $mail->CharSet = "utf-8";   // nastavíme kódování, ve kterém odesíláme e-mail

    if(!$mail->Send())
    {
      echo "<div style=\"color: red; font-weight: bold; \">Došlo k chybě při odeslání e-mailu. </div>";
      echo "Chybová hláška: ".$mail->ErrorInfo." ";
    }
    else
    {
     echo "<span style=\"color: green; font-weight: bold; \" >E-mail byl v pořádku odeslán.</span>";
    }

 }
