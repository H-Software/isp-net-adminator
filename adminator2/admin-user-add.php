<?php
 
require ("include/config.php"); 
require ("include/check_login.php");
require ("include/check_level.php");

if ( !( check_level($level,18) ) ) 
{
    $stranka='nolevelpage.php'; 
    header("Location: ".$stranka);
    
    echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
    exit;
}
   
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';
      
require ("include/charset.php"); 

?>

<title>Adminator2 - správa uživatelů </title> 

</head> 

<body> 

<?php require ("head.php"); ?> 

<?php require ("category.php"); ?> 

 <tr>
  <td colspan="2" height="20" bgcolor="silver">
     <?php require("admin-subcat2-inc.php"); ?>
  </td>
 </tr>
	      
<tr>
  <td colspan="2">

  <table width=100%>
    <tr>
      <td valign="top" colspan="2" >
	
<?php

global $update_id;

$update_id=$_POST["update_id"];
$odeslano=$_POST["odeslano"];
$send=$_POST["send"];

$zmenit_heslo=$_POST["zmenit_heslo"];
$zmenit_samba_heslo=$_POST["zmenit_samba_heslo"];

if (  ( $update_id > 0 ) ) { $update_status=1; }

// zde if pro prava na upravu
if( !( check_level($level,89) ) ) 
{
    echo "<br><span style=\"font-size: 18px; font-weight: bold; color: red;\">Nedostatečné práva k této operaci. </span><br>";  
    exit;
}

//
// uvodni nacteni promennych (bud z DB, nebo z odeslaneho formu)
//

if ( ( $update_status==1 and !( isset($send) ) ) )
{
    //
    // rezim upravy
    //
    $dotaz_upd = $conn_mysql->query("SELECT * FROM users_old WHERE id = '".intval($update_id)."' ");
    $radku_upd = $dotaz_upd->num_rows();
 
    if( $radku_upd==0 ) echo "Chyba! Původní data o uživateli nelze načíst! <span style=\"color: grey; \">id: ".intval($update_id)."</span>";
    else
    {
	while($data=mysql_fetch_array($dotaz_upd)){
	    $id=$data["id"];  
	    $login_jmeno=$data["login"]; 
	    $jmeno=$data["name"];	
	    $email=$data["email"];
	    $login_level=$data["level"];
	    $smb_user=$data["smb_user"];
        } //end of while
    
    } //end of else if radku_upd == 0
    
}
else
{

    // rezim pridani, ukladani

    $id=$_POST["id"];  
    $login_jmeno=$_POST["login_jmeno"]; 
    $login_password=$_POST["login_password"];
    $jmeno=$_POST["jmeno"]; 
    $email=$_POST["email"]; 
    $login_level=$_POST["login_level"]; 
    $smb_user=$_POST["smb_user"];
    $smb_pass=$_POST["smb_pass"];

    //systémove
    $send=$_POST["send"];
}

//													    
// jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
//
if ( ( ($login_jmeno != "") and ($jmeno != "") and ($login_level != "") ) ):

if ( ( $update_status!=1 ) )
{

 $dupl=$conn_mysql->query("SELECT login_name FROM users_old WHERE login like '$login_jmeno' ");
 $dupl_radku = $dupl->num_rows();
 
 if ($dupl_radku >= 1)
 { 
    $error .= "<br><H4>Přihlašovací jméno je již zaregistrováno. Prosím použijte jiné.</H4>"; 
    $fail = "true"; 
 }
 
}

// check v modu uprava
if ( ( $update_status==1 and (isset($odeslano)) ) )
{

 $dupl=$conn_mysql->query("SELECT login_name FROM users_old WHERE ( login like '$login_jmeno' and id != '$update_id' ) ");
 $dupl_radku = $dupl->num_rows();
 
 if ($dupl_radku >= 1)
 { 
    $error .= "<br><H4>Přihlašovací jméno je již zaregistrováno. Prosím použijte jiné.</H4>"; 
    $fail = "true"; 
 }

}

// kontrola dalsich promennych

if ( (!(ereg('^([[:digit:]]+)$',$login_level))) or ( (strlen($login_level) > 2) ) )
{ $fail="true"; $error .= "<br><div class=\"vlastnici-add-fail-nick\"><H4>Level ( ".$login_level." ) není ve správnem formátu !!! Zadejte číslo od 1 - 99. </H4></div>"; }

if ( ( !(ereg('.+@.+\..+',$email)) ) and ( strlen($email) > 0 ) )
{ $fail="true"; $error .= "<br><div class=\"vlastnici-add-fail-nick\"><H4>Email ( ".$email." ) není ve správnem formátu !!! Zadejte email ve tvaru cokoliv@domena.cz </H4></div>"; }

if ( !(ereg('^([[:alnum:]]|_|-)+$',$login_jmeno)) )
{ $fail="true"; $error .= "<br><div class=\"vlastnici-add-fail-nick\"><H4>Login jméno ( ".$login_jmeno." ) není ve správnem formátu !!! ( Povolené znaky a-Z, 0-9, _, - )</H4></div>"; }

if ( ( !(ereg('^([[:alnum:]]|_|-)+$',$smb_user)) ) and ( strlen($smb_user) > 1 ) )
{ $fail="true"; $error .= "<br><div class=\"vlastnici-add-fail-nick\"><H4>Login jméno do síť. disku ( ".$smb_user." ) není ve správnem formátu !!! ( Povolené znaky a-Z, 0-9, _, - )</H4></div>"; }

if ( $update_status != "1" )
{
// if ( !(ereg('^([[:alnum:]]|_|-)+$',$login_password)) )
// { $fail="true"; $error .= "<br><div class=\"vlastnici-add-fail-nick\"><H4>Heslo není ve správnem formátu !!! ( Povolené znaky a-Z, 0-9, _, - )</H4></div>"; }
 
 if ( ( !(ereg('^([[:alnum:]]|_|-)+$',$smb_pass)) and ( strlen($smb_pass) > 0 )  ) )
 { $fail="true"; $error .= "<br><div class=\"vlastnici-add-fail-nick\"><H4>Síťové heslo není ve správnem formátu !!! ( Povolené znaky a-Z, 0-9, _, - )</H4></div>"; }

}

//checkem jestli se macklo na tlacitko "OK" :)
if ( ereg("^OK$",$odeslano) ) { echo ""; }
else { $fail="true"; $error.="<div class=\"objekty-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; }


//
//ulozeni
//
if( !( isset($fail) ) ) 
{ 

    if( $update_status == "1" )
    {
	//
	// rezim upravy
	//

	//uvodni text pro archiv zmen
	$pole2 .= "<b>akce: uprava admina</b><br>";
	
	//prvne stavajici data docasne ulozime
	$vysl4 = $conn_mysql->query("SELECT login,name,email,level,smb_user FROM users_old WHERE id='".intval($update_id)."' ");
    
	if(!$vysl4)
	{
	    echo "<b>Chyba! Nelze zjistit puvodni data o uživateli (query error)</p>";    
	    exit;
	}
	
        if( ( mysql_num_rows($vysl4) <> 1 ) ) 
	{ 
	    echo "<p>Chyba! Nelze zjistit puvodni data pro ulozeni do archivu </p>"; 
	    exit;
	}
	else  
	{ 
	    while($data4=mysql_fetch_array($vysl4)):
		   
		   $pole_puvodni_data["login"] = $data4["login"];
		   $pole_puvodni_data["name"] = $data4["name"];
		   $pole_puvodni_data["email"] = $data4["email"];
		   $pole_puvodni_data["login_level"] = $data4["level"];
		   $pole_puvodni_data["smb_user"] = $data4["smb_user"];
		   
    	    endwhile; 
	}
       		 
	//zacatek tvorby SQL prikazu
	if($zmenit_heslo == 1)
	{ 
	    $password=md5($login_password);
    	    $zaklad2=", password='".$conn_mysql->real_escape_string($password)."' "; 
    	}

	if( $zmenit_samba_heslo == 1 )
	{ 
	    $smb_pass_crypt=md5($smb_pass);
	    $zaklad3=", smb_pass='$smb_pass_crypt' "; 
	}
    
        $zaklad="UPDATE users_old SET login='$login_jmeno', name='$jmeno', email='$email', level='$login_level',smb_user='$smb_user'";
    
	//vlastni update    
        $res = $conn_mysql->query($zaklad.$zaklad2.$zaklad3." where id=".intval($update_id)." Limit 1 ");
		       	    
        if ($res) 
        { echo "<br><H3><div style=\"color: green; \" >Data v databázi úspěšně změněny.</div></H3>\n"; }
        else 
        { echo "<div style=\"color: red; \">Chyba! Data v databázi nelze změnit. </div><br>\n".mysql_error(); }
	
	//ulozeni zmeny do archivu zmen     
        require("admin-user-add-inc-archiv.php");
                                                                                            
        //zmena hesla s samba serveru                                                                                             
	if ( $zmenit_samba_heslo == 1 )		      
	{
	    // tady pridat do samba systému
	    $info = exec("sudo /root/bin/smbctl pwd ".$smb_user." ".$smb_pass,$output,$res2);
    
	    echo "<br><H3><div style=\"color: ; \" >Výsledek vkládání/úpravy do samba systému: </div></H3>\n";
	    echo "<span style=\"color: #555555; \"> "; echo implode(",",$output); echo "</span>"; 
	}
     
        //nataveni priznaku
	$updated="true";
    }
    else
    {
	//
	// rezim pridani
	//
    
	$password=md5($login_password);    
	$smb_pass_crypt=md5($smb_pass);
    
	$res=$conn_mysql->query("INSERT INTO users_old (login, password, name, email, level, smb_user, smb_pass) 
	VALUES ('$login_jmeno','$password','$jmeno','$email','$login_level','$smb_user','$smb_pass_crypt')");
                                                                                                
	if ($res) { echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; } 
	else { echo "<div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div><br>\n".mysql_error().mysql_errno(); }	

	//
	// pridame to do archivu zmen
	//
	$pole = "<b>akce: pridani admina</b><br>";
    
	$pole=$pole." [login]=> ".$login_jmeno.", [name]=> ".$jmeno;
	$pole=$pole.", [email]=> ".$email.", [level]=> ".$login_level.", [smb_user]=> ".$smb_user;
    
	if( !($res === false) ){ $vysledek_write=1; }
        
	$add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ".
                    "('".$conn_mysql->real_escape_string($pole)."',".
                    "'".$conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email)."',".
                    "'".intval($vysledek_write)."')");                                                                                                
    
	// tady pridat do samba systému
	$info = exec("sudo /root/bin/smbctl add ".$smb_user." ".$smb_pass,$output,$res2);
    
	echo "<br><H3><div style=\"color: ; \" >Výsledek vkládání do samba systému: </div></H3>\n";
	echo "<span style=\"color: #555555; \"> "; echo implode(",",$output); echo "</span>"; 
    
	$writed = "true"; 
    
	
    } // konec else - rezim pridani

}else {} // konec else ( !(isset(fail) ), musi tu musi bejt, pac jinak nefunguje nadrazeny if-elseif

elseif ( isset($send) ): 
$error = "<h4>Chybí povinné údaje !!! </H4>"; 
endif;

// jestli byli zadany duplicitni udaje, popr. se jeste form neodesilal, zobrazime form
if ( (isset($error)) or (!isset($send)) ): 
echo $error; 

// vlozeni vlastniho formu
require("admin-user-add-inc.php");


elseif( ( isset($writed) or isset($updated) ) ): ?> 


<br><br>
Admin byl přidán/upraven , zadané údaje:<br><br> 
<b>login jméno</b>: <?php echo $login_jmeno; ?><br> 
<b>jméno</b>: <?php echo $jmeno; ?><br> 
<b>heslo</b>: <?php echo $password; ?><br> 

<b>email</b>: <?php echo $email; ?><br>
<b>level</b>: <?php echo $login_level; ?><br>

<b>login do síťového disku</b>: <?php echo $smb_user; ?><br>
<b>heslo do síťového disku</b>: <?php echo $smb_pass_crypt; ?><br>

<br> 
																									      
<?php endif; ?> 

 </td></tr></table>

 </td>
  </tr>
  
 </table>

</body> 
</html> 
