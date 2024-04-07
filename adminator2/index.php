<?php

session_start();

require ("include/config.php");

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html>
      <head> ';

require ("include/charset.php");

$login=$_POST["login"];
$password=$_POST["password"];
$lo=$_GET["lo"];

if( ( strlen($login) > 0) )
{
  if( !(ereg('^([[:alnum:]]+)$',$login)) )
  {
    echo "</head><body>";
    echo "<p>Chyba prihlasovani! Neplatna vstupni data (jmeno).</p>";
    echo "</body></html>";
    
    exit;
  }
}

if((isset($login)) and (isset($password))):

    $p = md5($password);
    
    global $MSQ;

    try {
        $MSQ = $conn_mysql->query(
            "SELECT login, level FROM users ".
            " WHERE ( " 
            . " login LIKE '".$conn_mysql->real_escape_string($login)."') "
            . "AND (password LIKE '".$conn_mysql->real_escape_string($p)."') "
        );
    } catch (Exception $e) {
        die ('Login Failed: Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
    }

    if ($MSQ->num_rows <> 1){
        echo "</head><body>";
    	echo "<p>Neautorizovaný prístup. / Chyba prístupu.</p>";
	    echo "</body></html>";

        exit;
    }
    else{
        //
        // uzivatel se zalogoval spravne, ted to ulozit do db
	    //
	
        //hadry okolo session
        $SN = "autorizace";
        session_name("$SN");
        session_register("db_login_md5");
        session_register("db_level");
        session_register("db_nick");

        $time = date("U");
        $at = date("U") - 1800;

        // co budeme ukladat do db ? zahashovany jmeno usera, nejdriv ho ale musime zjistit
    
        $radek = $MSQ->fetch_array();
        
        $db_login=$radek["login"];
        $db_nick=$radek["login"];
        $db_level=$radek["level"];
    
        $db_login_md5 = md5($db_login);
    
        //ted to nahazem do session
        $_SESSION["db_login_md5"]=$db_login_md5;
        $_SESSION["db_level"]=$db_level;
        $_SESSION["db_nick"]=$db_nick;
    
        //ted zjistime jestli nejde o refresh stanky :)
        $MSQ_A = mysql_query("SELECT id FROM autorizace WHERE (id LIKE '".mysql_real_escape_string($db_login_md5)."')");
    
        if(mysql_num_rows($MSQ_A) == 1)
        {
            // ehm refresh, takze nic :)
        }
        elseif( mysql_num_rows($MSQ_A) < 1 )
        {
            // user v db neni, takze ho tam pridame
            $MSQ_A2 = mysql_query("INSERT INTO autorizace ".
                                  " VALUES ('".mysql_real_escape_string($db_login_md5)."',".
                                  " '".intval($time)."',".
                                  " '".mysql_real_escape_string($db_nick)."',".
                                  " '".intval($db_level)."') ");
    
            //hodime to este do logu
            $ip=$_SERVER['REMOTE_ADDR'];
    
            $MSQ_X = mysql_query("INSERT INTO login_log ".
                                 "VALUES ('','".mysql_real_escape_string($db_nick)."',".
                                 " '".mysql_real_escape_string($time)."',".
                                 " '".mysql_real_escape_string($ip)."' )");
        }

        // $MSQ_D = MySQL_Query("DELETE FROM autorizace WHERE time < $at");

    }

// presmerovani na zakladnu :)
echo "<meta http-equiv=\"Refresh\" content=\"2;url=home.php\">";
?>

<title>Adminator 2 :: prihlášení</title>

</head>

<BODY>

<p>Jste bezpecne prihlašováni do administracního systému síte Simelon ...</p>

<p>

<form name="hours">
  <table border="0" cellpadding="0" cellspacing="0" width="700">

   <tr>

      <td width="232">Pokud nebudete prihlášeni do <b>
      <input type="text" size="22" name="time" style="display: none">
      <input type="text" size="20" name="elapsed" style="display: none">
      <input type="text" size="10" name="timetojump" style="width: 25px; text-align: right; border: 0px;">

    </b> sekund ,klepnete <a href="home.php" >sem</a>
      </td>
    </tr>



   </table>
</form>



<script src="login_time.js"></script>

</p>

<?php

elseif (isset($lo)):

    //
    //log out
    //

    // presmerovani na login
    echo "<meta http-equiv=\"refresh\" content=\"1;url=index.php\" >";

    $SN = "autorizace";
    session_name("$SN");


    $sid=$_SESSION["db_login_md5"];

    echo "<H2>Byl(a) jste odhlášen(a)!</H2>";

    echo "<br><br>Prihlášení: ".'<a href="index.php">zde</a>';

    $delka=strlen($sid);

    $MSQ_D = mysql_query("DELETE FROM autorizace WHERE (id LIKE '".mysql_real_escape_string($sid)."')");
    $MSA_D = mysql_affected_rows($MSQ_D);

    echo '<div style="color: grey;"><br><br>'."debug info: <br> delka session: ".$delka."\n";
    echo " ,vysledek mazani: ".$MSQ_D."</div>";

    session_destroy();


else:

    // prihlasovaci dialog ...

    echo '

        <style>// nastavení CSS stylu dokumentu, jednotlivé šablony jsou za jménem HTML tagu ve složených závorkách;
                // pokud zacíná pojmenování tagu teckou, jedná se o tzv. trídu definovanou v dokumentu jako class
        body{font-family:arial ce;font-size:12;background-color:#000066;border-color:#000066}
        b{font-size:12;font-weight:bolder}
        table{border-width:1;border-color:#000066;background-color:white;color:#000066}
        input{font-family:arial ce;font-size:12;color:#000066;border-color:#000066; }

        td{border-width:0}
        .submit{font-family:arial ce;font-size:12;color:#000066;font-weight:bolder;border-width:1;border-color:#000066}
        .big{font-size:14;color:black}
        </style>
    ';

?>

</head>

<body>

<center>
<img alt="logo" src="img2/logo.png"><br><br><br><br>
</center>

<center>
    <b class=big>PRIHLÁŠENÍ</b><br>
</center>

<form method="POST" action="" >
<table width=300 border=1 align=center>

<tr>
    <td align=left width="150"><b>Login:</b></td>
    <td align=left ><input name="login" type="Text" <?php echo "value=\"".htmlspecialchars($_POST["prezdivka"])."\""; ?> ></td>
</tr>

<tr>
 <td align=left><b>Heslo:</b></td>
 <td align=left ><input name="password" type = "password" ></td>
</tr>

<tr>
  <td align=center colspan="2"><input type="Submit" name="odesli" value="OK"></td>
</tr>

</table>
 </form>

<?php endif; ?>

</body>
</html>

<?php $conn_mysql->close(); ?>

