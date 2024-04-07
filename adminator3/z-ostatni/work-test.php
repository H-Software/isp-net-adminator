<html>

<head>
 <script language="JavaScript" src="include/js/work.js" ></script>

<link href="style.css" rel="stylesheet" type="text/css" >

</head>
<body>

<div style="padding-left: 5px;" >

<form method="GET" action=<? echo "\"".$_SERVER["PHP_SELF"]."\""; ?> >
 
 <div style="width: 100px; float: left; font-weight: bold;" >RESTART: </div>
				 
 <div style="float: left; padding-left: 20px;" >
    <input type="checkbox" value="1" name="item1" id="item1" onclick='return !restart_item(1);' >
    <label> :: wifi iptables & shaper  </label>
 </div>
						     
 <div style="float: left; padding-left: 20px;" >
    <input type="checkbox" value="1" name="item2" id="item2" onclick='return !restart_item(2);' >
    <label> :: dns </label>
 </div>
       
 <div style="float: left; padding-left: 20px;" >
    <input type="checkbox" value="1" name="item3" id="item3" onclick='return !restart_item(3);' >
    <label> :: optika all ( shape, ipt, radius )</label>
 </div>
											     
 <div style="float: left; padding-left: 30px;" >
    <input type="hidden" value="true" name="akce" >
    <input type="submit" value="OK" name="odeslat" >
 </div>
 </form>

 <div style="clear: both;" ></div>
<?

include("include/config.php");

$akce = $_POST["akce"];

$iptables = $_POST["iptables"];
$dns = $_POST["dns"];
$optika = $_POST["optika"];

$data_s = "/srv/www/htdocs.ssl/reinhard.remote.log";

if( $iptables == 1){ $prvni=$iptables; $pocet+20; }else{ $prvni = 0; }
if( $dns == 1){ $druha=$dns; $pocet+20; }else{ $druha = 0; }
if( $optika == 1){ $treti=$optika; $pocet+20; }else{ $treti = 0; }
if( ( $iptables==0 and $dns==0 and $optika == 0 ) ){ $akce=""; }

// system("/srv/www/htdocs.ssl/adminator2/scripts/work.pl ".$prvni." ".$druha." ".$treti,$vysl);

 // uložení odpovědi v případě vypnutého JavaScriptu
 if( isset($_GET["akce"]) )
 { // nelze pouzi JS/ajax    
    echo "neumim AJAX ";
    //mysql_query("UPDATE anketa SET pocet = pocet + 1 WHERE id = " . intval($_GET["anketa"]));
 }

 echo "<div class=\"work-main-window\" id='restart-stav' ><div id='work-vyberte-akci' >Vyberte požadovanou akci:</div></div>";
  
 echo "<div class=\"work-result\" id='odpoved1'></div>\n";
  
?>
	
</div>

</body>
</html>
