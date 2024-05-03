<?php

$cesta = "../";

require ($cesta."include/main.function.shared.php");
require ($cesta."include/config.php"); 
require ($cesta."include/check_login.php");
require ($cesta."include/check_level.php");

// $level_col = "lvl_partner_servis_add";

if( !( check_level($level, 304) ) )
{ // neni level
  header("Location: ".$cesta."nolevelpage.php");

  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ($cesta."include/charset.php"); 

?>

  <title>Adminator 2 - partner - servis vložení</title> 

</head> 

<body>

<?php require ($cesta."head.php"); ?> 

<?php require ($cesta."category.php"); ?> 

 <tr>
   <td colspan="2" height="20" bgcolor="silver" >
	<?php require ("partner-servis-cat.php"); ?>
   </td>
 </tr>
  
 <tr>
    <td colspan="2" >
    
<?

 $ps = new partner_servis($conn_mysql);
 
 $ps->klient_hledat = $conn_mysql->real_escape_string($_POST["klient_hledat"]);
 $ps->klient_id = intval($_POST["klient_id"]);

 $ps->fill_form = $conn_mysql->real_escape_string($_POST["fill_form"]);

 if( (strlen($ps->fill_form) > 4 ) ){

    $ps->form_copy_values();
        
 }
 else {
   
    $ps->jmeno_klienta = $conn_mysql->real_escape_string($_POST["jmeno_klienta"]);
    $ps->bydliste      = $conn_mysql->real_escape_string($_POST["bydliste"]);
    $ps->email 	       = $conn_mysql->real_escape_string($_POST["email"]);
    $ps->tel 	       = $conn_mysql->real_escape_string($_POST["tel"]);
    
 }
 
 $ps->pozn = $conn_mysql->real_escape_string($_POST["pozn"]);
 $ps->prio = intval($_POST["prio"]);
 
 $ps->odeslat = $conn_mysql->real_escape_string($_POST["odeslat"]);

 //kontrola promennych
 $ps->check_insert_value();
    
 if( ( ($ps->odeslat == "ULOŽIT") and ($ps->fail == false) ) )
 { // mod ukladani
    
    $ps->save_form();
 
 }
 else
 { // zobrazime formular

    echo "<form action=\"\" method=\"post\" class=\"form-partner-servis-insert\" >";

    if( isset($ps->odeslat) ){ echo $ps->error; }

    $ps->show_insert_form();

    echo "</form>";
    
 }
    
?>
    <!-- konec vnejsi tabulky -->
    </td>
 </tr>
  
 </table>

</body> 
</html> 
