<?php

require "include/main.function.shared.php";

require "include/config.php";

use Cartalyst\Sentinel\Native\Facades\Sentinel;

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html>
      <head> ';

require ("include/charset.php");

$login=$_POST["login"];
$password=$_POST["password"];
$lo=$_GET["lo"];

if( ( strlen($login) > 0) )
{
  if( preg_match('/^([[:alnum:]]|@|\.|\-|_)+$/',$login) <> 1 )
  {
    echo "</head><body>";
    echo "<p>Chyba prihlasovani! Neplatna vstupni data (jmeno).</p>";
    echo "</body></html>";
    
    exit;
  }
}

if((isset($login)) and (isset($password))):

    $logged=false;

    $data = array(
        'email' => $_POST["login"],
        'password' => $_POST["password"],
    );

    try {
        if (
            !Sentinel::authenticate(array_clean($data, [
                'email',
                'password',
            ]), isset($data['persist']))
        ) {
            throw new Exception('Incorrect email or password.');
        }
        else 
        {
            $logged=true;
        }
    } catch (Exception $e) {
        $logger->error("authController\signin " . $e->getMessage(), array_clean($data, ['email', 'persist', 'csrf_name', 'csrf_value']));
    }

    if( $logged === false) {
        echo "</head><body>";
    	echo "<p>Neautorizovaný prístup. / Chyba prístupu.</p>";
    	echo "<p>(num rows: " . $MSQ->num_rows . ")</p>";
        // echo "<p>SQL DUMP: " . $SQL . "</p>";
        echo "</body></html>";

        exit;
    }

    // presmerovani na zakladnu :)
    echo "<meta http-equiv=\"Refresh\" content=\"1;url=home.php\">";

    echo '
    <title>Adminator 2 :: prihlášení</title>

    </head>
    <body>

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

    <script src="/include/js/login_time.js"></script>
    </p>';

elseif (isset($lo)):

    //log out
    $logger->info("signout called");
    $logger->debug("signout: dump user identity: ".var_export(Sentinel::getUser()->email, true));

    if (!Sentinel::guest()) {
        $rs = sentinel::logout();
        $logger->info("signout: signout action result: " . var_export($rs, true));
    }
    else{
        $logger->info("AuthController/signout: user is not logged");
    }

    // presmerovani na login
    echo "<meta http-equiv=\"refresh\" content=\"1;url=index.php\" >";

    echo "<H2>Byl(a) jste odhlášen(a)!</H2>";
    echo "<br><br>Prihlášení: ".'<a href="index.php">zde</a>';
    echo '<div style="color: grey;"><br><br>'."debug info: <br> result: ".var_export($rs, true)."\n</div>";

else:

    // prihlasovaci dialog ...
    echo '
    <style>
        body
        {
            font-family: arial ce;
            font-size: 12;

        }
        b
        {
            font-size: 12;
            font-weight: bolder
        }
        table
        {
            border-width: 1;
            border-color: #000066;
            background-color: white;
            color: #000066
        }
        input
        {
            font-family:arial ce;
            font-size:12;
            color:#000066;
            border-color:#000066; 
        }

        td
        {
            border-width:0
        }
        .submit
        {
            font-family:arial ce;
            font-size:12;
            color:#000066;
            font-weight: bolder;
            border-width: 1;
            border-color: #000066
        }
        .big
        {
            font-size:14;
            color:black
        }
    </style>

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
        <td align=left ><input name="login" type="Text"' . "value=\"".htmlspecialchars($_POST["prezdivka"])."\"" . ' ></td>
    </tr>

    <tr>
    <td align=left><b>Heslo:</b></td>
    <td align=left ><input name="password" type = "password" ></td>
    </tr>

    <tr>
    <td align=center colspan="2"><input type="Submit" name="odesli" value="OK"></td>
    </tr>

    </table>
    </form>';

endif;

echo '
</body>
</html>';
