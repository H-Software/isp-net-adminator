<? 
init_ses();

require("include/main.function.shared.php");
require("include/config.php"); 

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ("include/charset.php"); 

$login=$_POST["login"];
$password=$_POST["password"];
$lo=$_GET["lo"];

If ((IsSet($login)) AND (IsSet($password))): 
$p = MD5($password); 
$MSQ = $conn_mysql->query("SELECT * FROM users WHERE (login LIKE '$login') AND (password LIKE '$p')"); 

If ($MSQ->num_rows <> 1): 
echo "<p>Neautorizovaný přístup. / Chyba přístupu.</p>"; 
exit; 

else: 

// uzivatel se zalogoval spravne, ted to ulozit do db


//hadry okolo session
// $SN = "autorizace"; 
// Session_name("$SN"); 
// session_register("db_login_md5");
// session_register("db_level");
// session_register("db_nick");

$time = Date("U"); 
$at = Date("U") - 1800; 

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

//force update :)

$MSQ = $conn_mysql->query("UPDATE autorizace SET date = $time WHERE id = '$db_login_md5'");

Endif; 

// presmerovani na zakladnu :)

// pokracujem

echo "<meta http-equiv=\"Refresh\" content=\"6, URL=home.php\">";
?> 

<title>Adminator 2</title> 

</head> 

<BODY>

<p>Jste bezpečně přihlašováni do administračního systému sítě Simelon ...</p>
<p>


<form name="hours">
  <table border="0" cellpadding="0" cellspacing="0" width="700">

   <tr>
      
      <td width="232">Pokud nebudete přihlášeni do 
      <input type="text" size="22" name="time" style="display: none">
      <input type="text" size="20" name="elapsed" style="display: none">
      <input type="text" size="10" name="timetojump" style="width: 25px; text-align: right;">
      
    sekund ,klepněte <a href="home.php">sem</a>
      </td>
    </tr>
    
   
    
   </table>
</form>



<script src="login_time.js"></script>


 </p>

<?
else: 

// prihlasovaci dialog ...
?> 

<? echo '
<style>// nastavení CSS stylů dokumentu, jednotlivé šablony jsou za jménem HTML tagu ve složených závorkách; 
// pokud začíná pojmenování tagu tečkou, jedná se o tzv. třídu definovanou v dokumentu jako class

body{font-family:arial ce;font-size:12;background-color:#000066;border-color:#000066}
b{font-size:12;font-weight:bolder}
table{border-width:1;border-color:#000066;background-color:white;color:#000066}
input{font-family:arial ce;font-size:12;color:#000066;border-color:#000066;background:url("pozadi.gif")
}
td{border-width:0}
.submit{font-family:arial ce;font-size:12;color:#000066;font-weight:bolder;border-width:1;border-color:#000066}
.big{font-size:14;color:black}
</style>
'; ?>

</head> 

<body> 

<center>
    <b class=big>PRIHLASENI</b><br>  
</center>

<form method="POST" action="relogin.php">
<table width=300 border=1 align=center>

<tr>
    <td align=left width="150"><b>Login:</b></td>
    <td align=left ><input name="login" type="Text" value="<? echo $_POST["prezdivka"] ?>"></td>
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
 
<?Endif;?> 
</body> 
</html> 
