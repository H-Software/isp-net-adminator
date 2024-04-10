<?
// hlavicka a promenne atd

$datum_uzavreni = $_GET["datum_uzavreni"];
$datum_vypovedi = $_GET["datum_vypovedi"];
$duvod = $_GET["duvod"];
$datum_vlozeni = $_GET["datum_vlozeni"];

$id_vlastnika=$_GET["id_vlastnika"];

$vypovedni_lhuta=$_GET["vypovedni_lhuta"];
$uhrazeni_vypovedni_lhuty=$_GET["uhrazeni_vypovedni_lhuty"];

require_once ("include/main.function.shared.php");
require_once ("include/config.php");

$dotaz_klient=pg_query("SELECT * from vlastnici WHERE id_cloveka = '" . intval($id_vlastnika) . "' ");

while ($data=pg_fetch_array($dotaz_klient) ):

global $jmeno;

    $jmeno = $data["jmeno"];
    $jmeno .= " ";
    $jmeno .= $data["prijmeni"];
    
    $adresa = $data["ulice"];
    
    $obec = $data["psc"];
    $obec .= " "; 
    $obec .= $data["mesto"];
    
endwhile;

 list ($rok1, $mesic1, $den1 ) = explode ("-", $datum_vlozeni);
 list ($rok2, $mesic2, $den2 ) = explode ("-", $datum_uzavreni);
 list ($rok3, $mesic3, $den3 ) = explode ("-", $datum_vypovedi);
		   
 $datum_vlozeni = $den1.".".$mesic1.".".$rok1;
 $datum_uzavreni = $den2.".".$mesic2.".".$rok2;
 $datum_vypovedi = $den3.".".$mesic3.".".$rok3;
					      
$firma=$_GET["firma"];

if ( $firma == 1)
{
    $jmeno_firmy = "Martin Lopušný ";
    $adresa_firmy = " Truhlářská 2161 ";
    $obec_firma =" 397 01 Písek ";
    $ic = "  73525111 ";
    $dic = " CZ8204151582 ";

}
else
{
    $jmeno_firmy = "Simelon, s.r.o. ";
    $adresa_firmy = " Žižkova 247 ";
    $obec_firma =" 397 01 Písek ";
    $ic = "261 09 824";
    $dic = "CZ26109824";

}

$email = "info@simelon.net";

$pocet_bunek = "5";

?>

<html>
<head>
    <title></title>

<meta http-equiv="Content-Language" content="cs">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta http-equiv="Cache-Control" content="must-revalidate, no-cache, post-check=0, pre-check=0" />
<meta http-equiv="Pragma" content="public" />

<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
      
</head>

<body>
      
<table border="0" width="100%" >

<tr><td <? echo "colspan=\"".$pocet_bunek."\""; ?> align="right" >
    <span style="padding-right: 30px; "><img width="250px" src="img2/logo.png" ></span>
</td></tr>

<tr><td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><br></td></tr>

<tr><td <? echo "colspan=\"".$pocet_bunek."\""; ?> align="center">
    <span style="font-weight: bold; font-size: 18px; font-family: Tahoma; ">
     UKONČENÍ SMLUVNÍHO VZTAHU SE SPOLEČNOSTÍ <? echo $jmeno_firmy; ?> 
    </span>
</td></tr>

<tr><td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><br><br></td></tr>

<tr>
    <td width="10%" ><span style="font-family: Tahoma; font-size: 12px; ">Jméno: </span></td>
    <td><span style="font-family: Tahoma; font-size: 12px; font-weight: bold; "><? echo $jmeno; ?></span></td>
    <td><b></td>
    <td><span style="font-family: Tahoma; font-size: 12px; font-weight: bold; "> <? echo $jmeno_firmy; ?></span></td>
    <td><br></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="padding-bottom: 10px; "></div></td>
</tr>

<tr>
    <td><span style="font-family: Tahoma; font-size: 12px; ">Adresa: </span></td>
    <td><span style="font-family: Tahoma; font-size: 12px; font-weight: bold; "><? echo $adresa; ?></span></td>
    <td><b></td>
    <td><span style="font-family: Tahoma; font-size: 12px; " ><? echo $adresa_firmy; ?> </span></td>
    <td><br></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="padding-bottom: 10px; "></div></td>
</tr>

<tr>
    <td><span style="font-family: Tahoma; font-size: 12px; ">Obec: </span></td>
    <td><span style="font-family: Tahoma; font-size: 12px; font-weight: bold; "><? echo $obec; ?></span></td>
    <td><b></td>
    <td><span style="font-family: Tahoma; font-size: 12px; "><? echo $obec_firma; ?> </span></td>
    <td><br></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 20px; "></div></td>
</tr>


<tr>
    <td><br></td>
    <td><br></td>
    <td><br></td>
    <td><span style="font-family: Tahoma; font-size: 12px; ">IČ: <? echo $ic; ?> </span></td>
    <td><br></td>
</tr>

<tr>
    <td><br></td>
    <td><br></td>
    <td><br></td>
    <td><span style="font-family: Tahoma; font-size: 12px; ">DIČ: <? echo $dic; ?> </span></td>
    <td><br></td>
</tr>

<tr>
    <td><br></td>
    <td><br></td>
    <td><br></td>
    <td><span style="font-family: Tahoma; font-size: 12px; ">e-mail: <? echo $email; ?></span></td>
    <td><br></td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>

<tr>
    <td colspan="2"><span style="font-family: Tahoma; font-size: 12px; font-weight: bold; ">dále jen „zákazník“</span></td>
    <td><br></td>
    <td><span style="font-family: Tahoma; font-size: 12px; font-weight: bold; ">dále jen „poskytovatel“</span></td>
    <td><br></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 30px; "></div></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
	    Zákazník vypovídá smluvní vztah poskytovateli, uzavřený dne  
	    <span style="font-weight: bold; "><? echo $datum_uzavreni; ?></span>. 
    </span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
	    Zákazník vypovídá smlouvu z důvodu* <span style="font-weight: bold; "><? echo $duvod; ?></span>. 
    </span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
	    Zákazník vypovídá smlouvu ke dni: <span style="font-weight: bold; "><? echo $datum_vypovedi; ?></span>. 
    </span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
    Zákazník se zavazuje dodržet tříměsíční výpovědní lhůtu, platnou dle všeobecných podmínek.
     Tuto výpovědní lhůtu uhradí 
     <? 
        if ( $vypovedni_lhuta == 2 )
	{ echo "( <span style=\"text-decoration: line-through; \"><b>hotově</b></span> / <span style=\"text-decoration: line-through; \"><b>převodem</b></span> / <span style=\"text-decoration: line-through; \"><b>doběhnutím trvalého příkazu</b></span> )**. "; } 
	elseif ( $uhrazeni_vypovedni_lhuty == 1 ) 
	{ echo "(<b>hotově</b> / <span style=\"text-decoration: line-through; \"><b>převodem</b></span> / <span style=\"text-decoration: line-through; \"> <b>doběhnutím trvalého příkazu</b></span> )**. "; } 
	elseif ( $uhrazeni_vypovedni_lhuty == 2 ) 
	{ echo "( <span style=\"text-decoration: line-through; \"><b>hotově</b></span> / <b>převodem</b> / <span style=\"text-decoration: line-through; \"><b>doběhnutím trvalého příkazu</b></span> )**. "; } 
	elseif ( $uhrazeni_vypovedni_lhuty == 3 ) 
	{ echo "( <span style=\"text-decoration: line-through; \"><b>hotově</b></span> / <span style=\"text-decoration: line-through; \"><b>převodem</b></span> / <b>doběhnutím trvalého příkazu</b> )**. "; } 
    ?>
    </span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
    Odpuštění výpovědní lhůty lze akceptovat pouze na základě výpovědi z důvodu nefunkční služby,
     s přiloženým potvrzením technického oddělení o řešení tohoto problému.</span></td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
    Toto ukončení smluvního vztahu se vyhotovuje ve dvojím provedení, z nichž každá strana obdrží jeden originál.</span></td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><span style="font-family: Tahoma; font-size: 12px; ">
    Bez řádně vyplněného potvrzení nelze akceptovat jakékoliv finanční nesrovnalosti, týkající se výpovědi smluvního vztahu.
    </span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 100px; "></div></td>
</tr>

<tr>
    <td colspan="2" align="center" > . . . . . . . . . . . . </td>
    <td colspan="2" align="center" > . . . . . . . . . . . . </td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 15px; "></div></td>
</tr>

<tr>
    <td colspan="2" align="center" ><span style="font-family: Tahoma; font-size: 12px; "> za zákazníka  </span></td>
    <td colspan="2" align="center" ><span style="font-family: Tahoma; font-size: 12px; "> za poskytovatele </span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 20px; "></div></td>
</tr>

<tr>
    <td colspan="2" align="center" > dne: <? echo $datum_vlozeni; ?>  </td>
    <td colspan="2" align="center" > dne: <? echo $datum_vlozeni; ?>  </td>
</tr>


<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 30px; "></div></td>
</tr>

<tr>
    <td colspan="5" align="" ><span style="font-family: Tahoma; font-size: 12px; ">
     * tento parametr není povinný pro univerzální typ výpovědi<br>
    ** nehodící se škrtněte, v platnost se považuje pouze jedna varianta</span></td>
</tr>

<tr>
    <td <? echo "colspan=\"".$pocet_bunek."\""; ?> ><div style="margin-bottom: 10px; "></div></td>
</tr>

<tr>
    <td colspan="5" align="center" ><span style="color: grey; font-size: 10px; ">SIMELON, s.r.o. zapsáno dne 9. září 2006 do obchodního rejstříku vedeného 
    Krajským soudem v ČB pod spisovou značkou oddíl C, vložka 14547</span></td>
</tr>

</table>


</body>
</html>
