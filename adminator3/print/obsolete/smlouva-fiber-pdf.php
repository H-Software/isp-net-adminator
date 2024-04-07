<?php

require_once("../include/config.php");

require_once ("../include/main.function.php");

start_ses();
$cl = check_login();

if( $cl[0] == "false" )
{ //chybny login ...

 require("inc.headend.php");
 echo "Adminator3 :: Tisk :: chybný login";
 
 exit;
}


if( !( check_level($level,146) ) )
{ // neni level

 require("inc.headend.php");
 echo "Adminator3 :: chybny level";
 
 exit;
}


$ec = $_POST["ec"];

$jmeno = $_POST["jmeno"];
$nazev_spol = $_POST["nazev_spol"];

$adresa = $_POST["adresa"];
$ico_dic = $_POST["ico_dic"];

$mesto = $_POST["mesto"];
$email = $_POST["email"];
$telefon = $_POST["telefon"];

$kor_adresa = $_POST["kor_adresa"];
$kor_mesto = $_POST["kor_mesto"];

$spec_prip_mista = $_POST["spec_prip_mista"];

$prip_misto_adresa = $_POST["prip_misto_adresa"];
$prip_misto_cp = $_POST["prip_misto_cp"];
$prip_misto_mesto = $_POST["prip_misto_mesto"];
$prip_misto_psc = $_POST["prip_misto_psc"];

$adr_prip_jako_kor = $_POST["adr_prip_jako_kor"];

$prip_tech = $_POST["prip_tech"];

//sluzba internet
$internet_sluzba = $_POST["internet_sluzba"];

$int_tarify_id_tarifu = $_POST["int_tarify_id_tarifu"];
$int_sluzba_tarif_text = $_POST["int_sluzba_tarif_text"];
$int_sluzba_tarif_agr = $_POST["int_sluzba_tarif_agr"];

$int_sluzba_tarif_cena = $_POST["int_sluzba_tarif_cena"];
$int_sluzba_tarif_cena_s_dph = $_POST["int_sluzba_tarif_cena_s_dph"];

$int_sluzba_rychlost = $_POST["int_sluzba_rychlost"];

$int_verejna_ip = $_POST["int_verejna_ip"];

$int_verejna_ip_cena = $_POST["int_verejna_ip_cena"];
$int_verejna_ip_cena_s_dph = $_POST["int_verejna_ip_cena_s_dph"];

if( $int_verejna_ip == 1 )
{
  if( !isset($int_verejna_ip_cena) )
  { $int_verejna_ip_cena = "100"; }
	  
  if( !isset($int_verejna_ip_cena_s_dph) )
  { $int_verejna_ip_cena_s_dph = "119"; }
}

//sluzba iptv
$iptv_sluzba = $_POST["iptv_sluzba"];

if( $iptv_sluzba == 1 )
{
 $iptv_sluzba_id_tarifu = $_POST["iptv_sluzba_id_tarifu"];

 $iptv_sluzba_cena = $_POST["iptv_sluzba_cena"];
 $iptv_sluzba_cena_s_dph = $_POST["iptv_sluzba_cena_s_dph"];

 $pocet_tb = $_POST["pocet_tb"];

 for($i=1; $i<=$pocet_tb; $i++)
 {	
     $tb = "tb".$i;
     $$tb = $_POST[$tb];
     
     $tb_cena = "tb_cena_".$i;
     $tb_cena_s_dph = "tb_cena_s_dph_".$i;
     
     $$tb_cena = $_POST[$tb_cena];
     $$tb_cena_s_dph = $_POST[$tb_cena_s_dph];     
 }

}
	    
//sluzba voip
$voip_sluzba = $_POST["voip_sluzba"];

$voip_cislo = $_POST["voip_cislo"];
$voip_typ = $_POST["voip_typ"];

//soucet, pro potreby slev
$soucet_bez_dph_pole = array($int_sluzba_tarif_cena,$int_verejna_ip_cena,$iptv_sluzba_cena);
$soucet_s_dph_pole = array($int_sluzba_tarif_cena_s_dph,$int_verejna_ip_cena_s_dph,$iptv_sluzba_cena_s_dph);

for($i=1; $i<=$pocet_tb; $i++)
{
    $tb = "tb".$i;
    
    $tb_cena = "tb_cena_".$i;
    $tb_cena_s_dph = "tb_cena_s_dph_".$i;
     	    
    $soucet_bez_dph_pole[] = $$tb_cena;
    $soucet_s_dph_pole[] = $$tb_cena_s_dph;
}
	
$soucet_bez_dph = array_sum($soucet_bez_dph_pole);
$soucet_s_dph = array_sum($soucet_s_dph_pole);

//sleva a dal

// prepinac jestli doporucit slevu
if( ($internet_sluzba == 1 ) and ($iptv_sluzba == 1) )
{
    if( $soucet_s_dph > 900 )
    {
	$sleva_doporucena = "1";
	$sleva_hodnota_dop = "15";
    }
    elseif( $soucet_s_dph > 700 )
    {
	$sleva_doporucena = "1";
	$sleva_hodnota_dop = "10";
    }
    elseif( $soucet_s_dph > 500 )
    {
	$sleva_doporucena = "1";
	$sleva_hodnota_dop = "5";
    }
}

$sleva_select = $_POST["sleva_select"];

if( $sleva_select == 1)
{
    $sleva_hodnota = $_POST["sleva_hodnota"];

    if( (strlen($sleva_hodnota) < 1 ) )
    { $sleva_hodnota = $sleva_hodnota_dop; }
    
    if( (strlen($sleva_hodnota) < 1 ) )
    { $sleva_hodnota = "0"; }
    
    $slevova_cast = ( $soucet_bez_dph / 100 ) * $sleva_hodnota;

    $celk_cena_po_sleve = $soucet_bez_dph - $slevova_cast;
}

$zpusob_placeni = $_POST["zpusob_placeni"];
$vs = $_POST["vs"];

if( (strlen($vs) < 1 ))
{ 
    if( (strlen($ico_dic) > 1 ))
    { $vs = "dle faktury"; }
    else
    { $vs = $ec; }
}

$splatnost_ke_dni = $_POST["splatnost_ke_dni"];

if( (strlen($splatnost_ke_dni) < 1 ))
{ $splatnost_ke_dni = "15."; }

$celk_cena = $_POST["celk_cena"];
$celk_cena_s_dph = $_POST["celk_cena_s_dph"];


if( $sleva_select == 1)
{
    if( (strlen($celk_cena) < 1 ) )
    { $celk_cena = $celk_cena_po_sleve; }

    if( (strlen($celk_cena_s_dph) < 1 ) )
    { $celk_cena_s_dph = round( $celk_cena_po_sleve * 1.19); }
}
else
{
    if( (strlen($celk_cena) < 1 ) )
    { $celk_cena = $soucet_bez_dph; }
    
    if( (strlen($celk_cena_s_dph) < 1 ) )
    { $celk_cena_s_dph = $soucet_s_dph; }
}

if ( $prip_tech == 1 )
{
 $celk_cena = round($celk_cena,2);
 $celk_cena_s_dph = round($celk_cena_s_dph);
}

$odeslano = $_POST["odeslano"];

$min_plneni = $_POST["min_plneni"];
$min_plneni_doba = $_POST["min_plneni_doba"];


#
#	zacatek formu
#

if( ( ( strlen($jmeno) < 2 ) or ( !isset($odeslano) ) ) )
{

echo '<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<title>Průvodce tiskem smlouvy </title>

<style>

.input1{ width: 80px; height: 17px; font-size: 10px; }

.input-size-big{ font-size: 12px; }

.label-font { font-size: 13px; }

.select1 { font-size: 10px;; color: grey; }

.input-border{ border: 2px solid black; }

</style>

</head>

<body>';

if ( $send != "OK" )
{ echo "<p><span style=\"color: blue; font-weight: bold; \"> Pro odeslání formuláře použijte tlačítko OK. </span></p>"; }

echo "<form method=\"POST\" action=\"\" name=\"form1\" >";

echo '<table border="0" width="1000px">

    <tr>
        <td colspan="5"><span style="font-size: 18px; font-weight: bold; " >
	Průvodce tiskem smlouvy - nový typ:
	</span></td>

    </tr>

        <tr>
                <td align="center" colspan="5"><br></td>
        </tr>

        <tr>
                <td colspan="1" align="center" class="label-font" ><label>Evidenční číslo smlouvy: </label></td>
                <td><input type="text" name="ec" size="30" class="input1" value="'.$ec.'"></td>
    		<td colspan="3"><br></td>
	</tr>

	<tr>
	    <td><br></td>
	</tr>
	
	<tr>
	    <td colspan="4" ><div style="padding-left: 20px; font-weight: bold; ">Oprávněný Zákazník</div></td>
	    
	</tr>
	
	<tr>
	    <td colspan="5"><br></td>
	</tr>
	
        <tr>
                <td align="center" class="label-font" ><label>Jméno a příjmení:  </label></td>
                <td><input type="text" name="jmeno" class="input-size-big" size="25" value="'.$jmeno.'" ></td>
        
		<td><br></td>
		
		<td align="center" class="label-font" ><label>Název společnosti: </label></td>
                <td><input type="text" name="nazev_spol" class="input-size-big" size="20" value="'.$nazev_spol.'" ></td>
        </tr>
	
        <tr>
                <td align="center" class="label-font" ><label>Adresa trvalého bydliště, č.p.: </label></td>
                <td><input type="text" name="adresa" class="input-size-big" size="25" value="'.$adresa.'" ></td>
        
		<td><br></td>
	        
		<td align="center" class="label-font" ><label>IČ / DIČ: </label></td>
                <td><input type="text" name="ico_dic" size="20" class="input-size-big" value="'.$ico_dic.'" ></td>
        </tr>
	</tr>

        <tr>
                <td align="center" class="label-font" ><label>Město a PSČ: </label></td>
                <td><input type="text" name="mesto" class="input-size-big" size="25" value="'.$mesto.'" ></td>
    	
		<td><br></td>
	        
		<td align="center" class="label-font" ><label>E-mail: </label></td>
                <td><input type="text" name="email" size="20" class="input-size-big" value="'.$email.'" ></td>
			
	</tr>


        <tr>
                <td align="center" class="label-font" ><label>Telefon: </label></td>
                <td><input type="text" name="telefon" class="input-size-big" size="25" value="'.$telefon.'" ></td>
    	
		<td colspan="3" ><br></td>
		
	</tr>
	        
        <tr>
                <td align="center" class="label-font" ><label>Korespondenční adresa: </label></td>
                <td><input type="text" name="kor_adresa" class="input-size-big" size="25" value="'.$kor_adresa.'" ></td>
    	
		<td colspan="3" ><br></td>
		
	</tr>
        <tr>
                <td align="center" class="label-font" ><label>Kor. město, PSČ: </label></td>
                <td><input type="text" name="kor_mesto" class="input-size-big" size="25" value="'.$kor_mesto.'" ></td>
    	
		<td colspan="3" ><br></td>
		
	</tr>
	
	<tr><td colspan="5" height="20px">
		    <div style="border-bottom: 1px grey dashed;" ></div>
		  </td>
	      </tr>
	
	<tr>
	    <td colspan="4" >
		<div style="padding-left: 20px; font-weight: bold; ">Specifikace přípojného místa</div>
	    </td>
	</tr>
	
	<tr>
	    <td colspan="5"><br></td>
	</tr>

        <tr>
                <td colspan="1"><br></td>
                <td colspan="4">';
		
                    echo "<select size=\"1\" name=\"spec_prip_mista\" onChange=\"self.document.forms.form1.submit()\"  >";
                      echo "<option value=\"1\" ";
		        if( ($spec_prip_mista == 1) or (!isset($spec_prip_mista)) ){ echo " selected "; }
		      echo ">Přípojné místo stejné jako jako trvalé bydliště Zákazníka</option>";
		      
                      echo "<option value=\"2\" ";
		        if ( $spec_prip_mista == 2 ){ echo " selected "; }
		      echo " >jiné...</option>";
		      
                    echo "</select>";
    
    echo '      </td>
	  </tr>
	  
	  <tr>
	    <td colspan="5"><br></td>
	  </tr>

	
	';
		

        if( $spec_prip_mista == 2 )
        {
         echo "<tr>
                <td align=\"center\" class=\"label-font\" ><label>Adresa připojeného místa: </label></td>
                <td><input type=\"text\" name=\"prip_misto_adresa\" class=\"input-size-big\" size=\"25\" value=\"".$prip_misto_adresa."\"></td>
        	<td><br></td> 
	        <td align=\"center\" class=\"label-font\" ><label>č.p: </label></td>
                <td><input type=\"text\" name=\"prip_misto_cp\" size=\"20\" value=\"".$prip_misto_cp."\"></td>
    	      </tr>";

         echo "<tr>
                <td align=\"center\" class=\"label-font\" ><label>Město: </label></td>
                <td><input type=\"text\" name=\"prip_misto_mesto\" class=\"input-size-big\" size=\"25\" value=\"".$prip_misto_mesto."\"></td>
		<td><br></td>
	        <td align=\"center\" class=\"label-font\" ><label>PSČ: </label></td>
                <td><input type=\"text\" name=\"prip_misto_psc\"  size=\"20\" value=\"".$prip_misto_psc."\" ></td>
	      </tr>";

	  echo "<tr><td colspan=\"5\"><br></td></tr>";
	
	  echo "<tr>
	            <td colspan=\"2\" align=\"center\" >Použí tuto adresu jako korespondenční:</td>
		    
		    <td><br></td>
		    
		    <td colspan=\"2\">
		     <select name=\"adr_prip_jako_kor\" size=\"1\" >
		     
		        <option value=\"1\" "; if( $adr_prip_jako_kor == 1 or !isset($adr_prip_jako_kor)) echo " select "; echo " >Ne</option>	
			<option value=\"2\" "; if( $adr_prip_jako_kor == 2) echo " selected "; echo " >Ano</option>
			
		     </select>
		    
		    </td>
		</tr>";
	
	} // konec if spec. pripojneho mista


	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px black solid;\"></div>
		  </td>
	      </tr>";
	      	
	echo "<tr>
                <td align=\"center\" class=\"label-font\" ><label>Připojená technologie: </label></td>
                <td colspan=\"4\" >
		    <span style=\"margin-left: 10px; \" ></span>
		        
		    <input type=\"radio\" name=\"prip_tech\" value=\"1\" onChange=\"self.document.forms.form1.submit()\" ";
			 { echo " checked=\"checked\" "; } echo " >
		    <span style=\"margin-left: 10px; \" >Optiká síť</span>
		    |
		    <input type=\"radio\" name=\"prip_tech\" value=\"2\" onChange=\"self.document.forms.form1.submit()\" ";
			if( $prip_tech == 2 ){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"margin-left: 10px; \" >Metalický okruh</span>
		    |
		    <input type=\"radio\" name=\"prip_tech\" value=\"3\" onChange=\"self.document.forms.form1.submit()\" ";
			if( $prip_tech == 3 ){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"margin-left: 10px; \" >Bezdrátová síť</span>
		</td>
          </tr>";
	
        echo "</tr>";
    	
	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td colspan=\"5\" >
		    <div style=\"padding-left: 20px; font-weight: bold; border-bottom: 1px grey solid; \">
		    Tarify a ceny:</div>
		</td>
	     </tr>";
	
	//internet
	echo "<tr><td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >Internet:</td>
	        <td>
		    <select name=\"internet_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			
			<option value=\"0\" "; 
			    if( $internet_sluzba == 0 or !isset($internet_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $internet_sluzba == 1 ){ echo " selected "; } echo " >Ano</option>
			
		    </select>
		</td>";
		
		echo "<td>&nbsp;</td>";
		
		if( $internet_sluzba == 1 )
		{
	
		  echo "
		    <td class=\"label-font\" align=\"center\" >Vyberte tarif:</td>
		    <td colspan=\"4\">";
		    
		    if( $prip_tech == 1 )
		    { $sql_int = " WHERE typ_tarifu = '1' "; }
		    elseif( $prip_tech == 2 or $prip_tech == 3 )
		    { $sql_int = " WHERE typ_tarifu = '0' ";}
		    else
		    { $sql_int = ""; }
		    
		    $dotaz_int_tarify = mysql_query("SELECT * FROM tarify_int ".$sql_int." ORDER BY id_tarifu");
		    
		    echo "<select size=\"1\" name=\"int_tarify_id_tarifu\" onChange=\"self.document.forms.form1.submit()\" >";
		    
		    while( $data_int = mysql_fetch_array($dotaz_int_tarify))
		    {
			echo "<option value=\"".$data_int["id_tarifu"]."\" ";
			
			if( $int_tarify_id_tarifu == $data_int["id_tarifu"] )
			{ echo " selected "; }
			
			echo " >".$data_int["jmeno_tarifu"];
			echo " (".$data_int["zkratka_tarifu"].")</option>";
		    
		    }
		    
		    echo "</select>\n";
		    
		    echo "</td>";
	
		  }
		  else
		  { echo "<td colspan=\"2\">&nbsp;</td>"; }	
	
		echo "</tr>";
	
	if( $internet_sluzba == 1 )
	{
	
	    $int_se = mysql_query("SELECT * FROM tarify_int WHERE id_tarifu = '$int_tarify_id_tarifu' ");

	    while( $data_int_se = mysql_fetch_array($int_se))
	    { 
		$int_sluzba_tarif_text_db = $data_int_se["jmeno_tarifu"];
		$typ_tarifu_db = $data_int_se["typ_tarifu"];
		$garant_db = $data_int_se["garant"];
	    
		$int_sluzba_tarif_cena_db = $data_int_se["cena_bez_dph"];
		$int_sluzba_tarif_cena_s_dph_db = $data_int_se["cena_s_dph"];
	    
		$speed_dwn_db = $data_int_se["speed_dwn"]; 
	    }
		
	    if( strlen($int_sluzba_tarif_cena) < 1 )
	    { $int_sluzba_tarif_cena = $int_sluzba_tarif_cena_db; }
	    
	    if( strlen($int_sluzba_tarif_cena_s_dph) < 1 )
	    { 
	    	//zde workarourng pro SC linky
		if( $int_tarify_id_tarifu == 1 )
		{
		 if( (strlen($ico_dic) >= 1) )
		 { $int_sluzba_tarif_cena_s_dph = "298"; }
		 else
		 { $int_sluzba_tarif_cena_s_dph = "297.5"; }
		}
		else
		{ $int_sluzba_tarif_cena_s_dph = $int_sluzba_tarif_cena_s_dph_db; }
	    }
	    	    
	    echo "<tr>
		    <td class=\"label-font\" align=\"center\" >Internet - tarif / Max. agregace:</td>
		    <td>";
		    
		    if( $int_tarify_id_tarifu >= 0 and ( strlen($int_sluzba_tarif_text) < 1 ) )
		    { $int_sluzba_tarif_text = $int_sluzba_tarif_text_db; } 

		    echo "<input type=\"text\" name=\"int_sluzba_tarif_text\" value=\"".$int_sluzba_tarif_text."\" >";
		    
		    if( strlen($int_sluzba_tarif_agr) < 1 )
		    {  //detailni agregaci dodelat
		       if( $garant_db == 1 )
		       { $int_sluzba_tarif_agr = "1"; }
		       elseif( $int_tarify_id_tarifu == 1 )
		       { $int_sluzba_tarif_agr = "15"; }
		       else
		       { $int_sluzba_tarif_agr = "5"; }
		    }

		    
		    echo "<input type=\"text\" name=\"int_sluzba_tarif_cena\" value=\"".$int_sluzba_tarif_cena."\" size=\"5\" >,-";
		    
		    echo "<span class=\"label-font\" style=\"margin-left: 10px;\" >
			    1: <input type=\"text\" size=\"4\" name=\"int_sluzba_tarif_agr\" value=\"".$int_sluzba_tarif_agr."\" >
			  </span>";
		    
		    echo "</td>
		    <td>&nbsp;</td>
		    <td class=\"label-font\" align=\"center\" >CENA TARIFU (bez DPH / s DPH): </td>
		    <td>
		    			
			<span style=\"margin-left: 10px; \" >
			 <input type=\"text\" name=\"int_sluzba_tarif_cena_s_dph\" value=\"".$int_sluzba_tarif_cena_s_dph."\" size=\"5\" >
			,-</span>
			
			</td>
		  </tr>";
	
	    echo "<tr>
		    <td class=\"label-font\" align=\"center\">Max. rychlost (Mb/s):</td>";
		    
		if( (strlen($int_sluzba_rychlost) < 1) )
		{ $int_sluzba_rychlost = $speed_dwn_db/1024; }
		
		echo "<td><input type=\"text\" name=\"int_sluzba_rychlost\" value=\"".$int_sluzba_rychlost."\" ></td>
		    
		    <td colspan=\"3\" >&nbsp;</td>
		  </tr>";
	}
	
	echo "<tr><td colspan=\"5\" ><br></td></tr>";
	
	echo "<tr>
		<td class=\"label-font\" align=\"center\">Veřejná IP adresa: </td>
		<td colspan=\"\">
		    <select name=\"int_verejna_ip\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" "; 
			    if( $int_verejna_ip == 0 or !isset($int_verejna_ip)){ echo " selected "; }
		    echo " >Ne</option>
			<option value=\"1\" "; if($int_verejna_ip == 1){ echo " selected "; } echo " >Ano</option>    
		    </select>
		</td>";
		
	echo "<td>&nbsp;</td>";
	
	
	
	if( $int_verejna_ip == 1 )
	{
	    echo "<td class=\"label-font\" align=\"center\" >CENA, bez DPH / s DPH:</td>
		  <td>
		    <input type=\"text\" name=\"int_verejna_ip_cena\" value=\"".$int_verejna_ip_cena."\" size=\"5\" >,-
			
		    <span style=\"margin-left: 10px; \" >
		      <input type=\"text\" name=\"int_verejna_ip_cena_s_dph\" value=\"".$int_verejna_ip_cena_s_dph."\" size=\"5\" >
		    ,-</span>
		  
		  </td>";
	}
	else
	{
	  echo "<td colspan=\"2\" >&nbsp;</td>";
	}
		
	echo "</tr>";
	
	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";

	//iptv 
	echo "<tr><td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >
	IPTV:</td>
	        <td>
		    <select name=\"iptv_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" ";
			    if( $iptv_sluzba == 0 or !isset($iptv_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $iptv_sluzba == 1){ echo " selected "; } echo " >Ano</option>
		    </select>
		</td>";
	
	if( $iptv_sluzba == 1 )
	{
	    $iptv_se = mysql_query("SELECT * FROM tarify_iptv WHERE id_tarifu = '$iptv_sluzba_id_tarifu' ");

	    while( $data_iptv_se = mysql_fetch_array($iptv_se) )
	    {
		$iptv_sluzba_cena_bez_dph_db = $data_iptv_se["cena_bez_dph"];
		$iptv_sluzba_cena_s_dph_db = $data_iptv_se["cena_s_dph"];
	    }

	    if( (strlen($iptv_sluzba_cena) < 1 ) )
	    { $iptv_sluzba_cena = $iptv_sluzba_cena_bez_dph_db; }
	    
	    if( (strlen($iptv_sluzba_cena_s_dph) < 1 ) )
	    { $iptv_sluzba_cena_s_dph = $iptv_sluzba_cena_s_dph_db; }
	    
	     
	    echo "<td>&nbsp;</td>";
	    echo "<td class=\"label-font\" align=\"center\" >Vyberte tarif:</td>";
	
	    echo "<td>
		    <select size=\"1\" name=\"iptv_sluzba_id_tarifu\" onChange=\"self.document.forms.form1.submit()\" >";
	
	    $iptv_se = mysql_query("SELECT * FROM tarify_iptv ORDER BY zkratka_tarifu ");

	    while( $data_iptv_se = mysql_fetch_array($iptv_se))
	    { 
		echo "<option value=\"".$data_iptv_se["id_tarifu"]."\" ";
		if( $iptv_sluzba_id_tarifu == $data_iptv_se["id_tarifu"] )
		{ echo " selected "; }
		
		echo " >";
		echo $data_iptv_se["jmeno_tarifu"]." (".$data_iptv_se["zkratka_tarifu"].")</option>";
	
	    }
	    
	    echo "</option></td>";
	
	    //dalsi radka :: cena tarifu
	    echo "<tr>
		    <td colspan=\"2\" >&nbsp;</td>
		    <td>&nbsp;</td>
		    
		    <td class=\"label-font\" align=\"center\" >CENA, bez DPH / s DPH:</td>
		    <td>
			<input type=\"text\" name=\"iptv_sluzba_cena\" size=\"5\" value=\"".$iptv_sluzba_cena."\" >
			,-
			<span style=\"margin-left: 8px; \" >
			  <input type=\"text\" name=\"iptv_sluzba_cena_s_dph\" size=\"5\" value=\"".$iptv_sluzba_cena_s_dph."\" >
			,-</span>
		    
		    </td>";
	
	    //treti radek IPTV - tematicke balicky
	    echo "<tr>
		    <td class=\"label-font\" align=\"center\" >Počet tématických balíčků: </td>
		    <td>
			<select size=\"1\" name=\"pocet_tb\" onChange=\"self.document.forms.form1.submit()\" >
			  <option value=\"0\" style=\"color: gray; \" ";
			  if( $pocet_tb == 0 or !isset($pocet_tb) ){ echo " selected "; }
			   echo " >0 (žádný)</option>
			 
			  <option value=\"1\" "; if($pocet_tb==1){ echo " selected "; } echo " >1</option>
			  <option value=\"2\" "; if($pocet_tb==2){ echo " selected "; } echo " >2</option>
			  <option value=\"3\" "; if($pocet_tb==3){ echo " selected "; } echo " >3</option>
			</select>
		    </td>
		    <td>&nbsp;</td>
		    <td colspan=\"2\" >&nbsp;</td>
		  </tr>";
	
	    for($i=1; $i<=$pocet_tb; $i++)
	    {

	     $tb = "tb".$i;
    
    	     $tb_cena = "tb_cena_".$i;
    	     $tb_cena_s_dph = "tb_cena_s_dph_".$i;
     	    
	    echo "<tr>	      
	    	    <td class=\"label-font\" align=\"center\" >Tématický balíček č.".$i."</td>
		    <td style=\"\">
		    	<span class=\"label-font\" style=\"padding-right: 10px; \" >Název: </span>
			<input type=\"text\" name=\"".$tb."\" size=\"20\" value=\"".$$tb."\" >
		    </td>
		    <td>&nbsp;</td>
		    <td class=\"label-font\" align=\"center\" >CENA, bez DPH / s DPH:</td>
		    <td>
		        <input type=\"text\" name=\"".$tb_cena."\" size=\"5\" value=\"".$$tb_cena."\" >
		        ,-
		        <span style=\"margin-left: 8px; \" >
		        <input type=\"text\" name=\"".$tb_cena_s_dph."\" size=\"5\" value=\"".$$tb_cena_s_dph."\" >
		        ,-</span>
		    
		    </td>
		  </tr>";
	    }
	    
	}
	else
	{ echo "<td colspan=\"3\" ><br></td>"; }
	
	echo "</tr>";

	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";
	
	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td class=\"label-font\" align=\"center\" style=\"font-weight: bold; \" >VoIP:</td>
	        <td>
		    <select name=\"voip_sluzba\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >	
			<option value=\"0\" "; 
			    if( $voip_sluzba == 0 or !isset($voip_sluzba) ){ echo " selected "; }
			echo " >Ne</option>
			<option value=\"1\" "; if( $voip_sluzba == 1 ){ echo " selected "; } echo " >Ano</option>
		    </select>
		</td>
	
		<td colspan=\"3\" ><br></td>
	       </tr>";
	
	if($voip_sluzba == 1)
	{
	 echo "<tr>
		<td class=\"label-font\" align=\"center\" >Telefonní číslo:</td>
		<td><input type=\"text\" name=\"voip_cislo\" value=\"".$voip_cislo."\" ></td>
		<td>&nbsp;</td>

		<td colspan=\"2\" >
		  <span style=\"margin-left: 20px;\" ></span>
		  
		  <input type=\"radio\" name=\"voip_typ\" value=\"1\" ";
		  if( $voip_typ == 1 or !isset($voip_typ) ){ echo " checked=\"checked\" "; } echo " >
		  <span style=\"margin-left: 20px;\" >
		    Paušál (postpaid)
		  </span>
		  <span style=\"margin-left: 10px; margin-right: 10px;\" >|</span>
		  
		  <input type=\"radio\" name=\"voip_typ\" value=\"2\" ";
		  if( $voip_typ == 2 ){ echo " checked=\"checked\" "; }
		  echo " >
		  <span style=\"margin-left: 20px;\" >Kredit (prepaid)</span>
		</td>	
	       </tr>";

	}

	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px black solid;\"></div>
		  </td>
	      </tr>";
		
//zde zacina sleva	
	echo "<tr>
		<td style=\"text-align: center; font-weight: bold;\" >sleva:</td>
		<td>
		  <div style=\"float: left; \" >
		    <select size=\"1\" name=\"sleva_select\" onChange=\"self.document.forms.form1.submit()\" >
			<option value=\"0\" "; 
			    if( $sleva_select == 0 or !isset($sleva_select) ){ echo " selected "; } 
			echo " >Ne</option>
			<option value=\"1\" "; if($sleva_select == 1){ echo " selected "; } echo " >Ano</option>
		    </select>
		  </div>";
		  
	if( $sleva_doporucena == 1)
	{ echo "<div style=\"text-align: right; padding-right: 5px; font-weight: bold;\" >sleva doporučena</div>"; }
	else
	{ echo "<div style=\"text-align: right; padding-right: 5px;\" >sleva nedoporučena</div>"; }
	
	echo " </td>
	       <td>&nbsp;</td>";
	
	if( $sleva_select == 1)
	{
	    echo "<td class=\"label-font\" align=\"center\" >Součet před slevou: (netiskne se)</td>";
	    echo "<td style=\"font-weight: bold; font-style: italic; \" >".$soucet_bez_dph.",-
		    <span style=\"padding-left: 20px;\"></span>"
		    .$soucet_s_dph.",-";
	    echo "</td>
	      </tr>";
	
	}
	else
	{
	 echo "<td colspan=\"2\" >&nbsp;</td>
	      </tr>";
	
	}

	if( $sleva_select == 1)
	{
	  echo "<tr>
		 <td class=\"label-font\" align=\"center\" >výše slevy: </td>
		 <td>
		    <input type=\"text\" name=\"sleva_hodnota\" size=\"10\" value=\"".$sleva_hodnota."\" >
		    <span style=\"padding-left: 10px; \" >%</span>
		 </td>
		</tr>";
	}


	echo "<tr><td colspan=\"5\" height=\"20px\">
		    <div style=\"border-bottom: 1px grey dashed;\"></div>
		  </td>
	      </tr>";

//platební podmínky atd
	echo "<tr>
		<td colspan=\"5\" >
		    <div style=\"padding-left: 20px; font-weight: bold; \">Platební podmínky:</div>
		</td>
	     </tr>";
	
	echo "<tr>
		<td class=\"label-font\" align=\"center\" >Způsob placení:</td>
	        <td colspan=\"2\" >
		    <span style=\"padding-left: 10px; \" ></span>
		    <input type=\"radio\" name=\"zpusob_placeni\" value=\"1\" ";
			if( $zpusob_placeni == 1 or !isset($zpusob_placeni) ){ echo " checked=\"checked\" "; }
		    echo " >
		    <span style=\"padding-left: 3px;\" >Trvalý příkaz</span>
		    <span style=\"padding-left: 5px; padding-right: 5px; \" >|</span>
		    
		    <input type=\"radio\" name=\"zpusob_placeni\" value=\"2\" ";
			if($zpusob_placeni == 2){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"padding-left: 3px;\" >Inkaso</span>
		    <span style=\"padding-left: 5px; padding-right: 5px; \" >|</span>
		
		
		    <input type=\"radio\" name=\"zpusob_placeni\" value=\"3\" ";
			if($zpusob_placeni == 3){ echo " checked=\"checked\" "; } echo " >
		    <span style=\"padding-left: 3px;\" >SIPO</span>
		    
		</td> 
		<td class=\"label-font\" align=\"center\">Variabilní symbol:</td>
		<td><input type=\"text\" name=\"vs\" value=\"".$vs."\" ></td>
		
	      </tr>";
	
	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td class=\"label-font\" align=\"center\" >doba splatnosti ke dni:</td>
	        <td ><input type=\"text\" name=\"splatnost_ke_dni\" size=\"10\" value=\"".$splatnost_ke_dni."\" ></td>
	        <td>&nbsp;</td>
		<td style=\"font-weight: bold; text-align: center;\" >
		    CELKOVÁ CENA, <span style=\"font-size: 10px; \" >bez DPH / s DPH:</span></td>
		<td>
		    <input class=\"input-border\" type=\"text\" name=\"celk_cena\" size=\"5\" value=\"".$celk_cena."\" >
		    ,-
		    <span style=\"margin-left: 8px; \" >
		    <input class=\"input-border\" type=\"text\" name=\"celk_cena_s_dph\" size=\"5\" value=\"".$celk_cena_s_dph."\" >
		    ,-</span>
		</td>
	      </tr>";

	echo "<tr><td colspan=\"5\"><br></td></tr>";

	echo "<tr>
		<td class=\"label-font\" align=\"center\" >
		  Minimální plnění:
		</td>
		<td>
		  <select size=\"1\" name=\"min_plneni\" onChange=\"self.document.forms.form1.submit()\" >
		    <option value=\"1\" "; 
			if($min_plneni == 1 or !isset($min_plneni)){ echo " selected "; }
			echo " >Ne</option>
		    <option value=\"2\" "; if( $min_plneni == 2){ echo " selected "; }
			 echo " >Ano</option>
		  </select>
		</td>
		<td>&nbsp;</td>";
		
	    if( $min_plneni == 2 )
	    {
	      echo "<td>Doba min. plnění(v měsících): </td>
	    	    <td class=\"label-font\" >
			<input type=\"text\" size=\"6\" name=\"min_plneni_doba\" value=\"".$min_plneni_doba."\" >
		    </td>
		    ";
	    }
	    else
	    { echo "<td colspan=\"2\" >&nbsp;</td>"; }
	    
	    echo "</tr>";
	    
//konec policek
 echo "
        <tr>
                <td align=\"center\" colspan=\"5\" ><br></td>
        </tr>

	<tr>
	        <td align=\"center\" colspan=\"2\" ><input type=\"submit\" name=\"odeslano\" value=\"OK -- VYGENEROVAT\" ></td>
		<td>&nbsp;</td>
		<td align=\"center\" colspan=\"2\" ><input type=\"submit\" name=\"reg\" value=\"PŘEPOČÍTAT FORMULÁŘ\" ></td>
	</tr>
							

 </table>
 </form>

 </body>

 </html>";

} // konec if !isset nazev
else
{ //budeme generovat
 
 // konverze promennych
 $ec = iconv("UTF-8","CP1250", $ec);
 
 $jmeno = iconv("UTF-8","CP1250", $jmeno );
 
 if( (strlen($nazev_spol) > 1 ) )
 { $nazev_spol = iconv("UTF-8","CP1250", $nazev_spol); }
 else
 { $nazev_spol = "- - - - -"; }

 $adresa = iconv("UTF-8","CP1250", $adresa );
 
 if( (strlen($ico_dic) > 1) )
 { $ico_dic = iconv("UTF-8","CP1250", $ico_dic); }
 else
 { $ico_dic = "- - - - -"; }
 
 $mesto = iconv("UTF-8","CP1250", $mesto );
 $email = iconv("UTF-8","CP1250", $email );

 $telefon = iconv("UTF-8","CP1250", $telefon );

 if( (strlen($kor_adresa) > 1) )
 { $kor_adresa = iconv("UTF-8","CP1250", $kor_adresa); }
 else
 { $kor_adresa = "- - - - -"; }
 
 if( (strlen($kor_mesto) > 1) )
 { $kor_mesto = iconv("UTF-8","CP1250", $kor_mesto); }
 else
 { $kor_mesto = "- - - - -"; }

 //rozhodovani jestli budou udaje ci pomlcky
 if( $spec_prip_mista == 1 )
 {
    $spec_prip_mista = "X";
    
    $prip_misto_adresa = "- - - - - -";
    $prip_misto_cp = "- - -";
    $prip_misto_mesto = "- - - - - ";
    $prip_misto_psc = "- - -";
 }
 else
 {
    $spec_prip_mista = "-";
    $prip_misto_adresa = iconv("UTF-8","CP1250", $prip_misto_adresa);
    $prip_misto_cp = iconv("UTF-8","CP1250", $prip_misto_cp);
    $prip_misto_mesto = iconv("UTF-8","CP1250", $prip_misto_mesto);
    $prip_misto_psc = iconv("UTF-8","CP1250", $prip_misto_psc);    
 }
 
 if( $adr_prip_jako_kor == 2 )
 { $adr_prip_jako_kor = "X"; }
 else
 { $adr_prip_jako_kor = "-"; }

 if( $prip_tech == 1 )
 { $prip_tech_1 = "X"; }
 elseif( $prip_tech == 2 ) 
 { $prip_tech_2 = "X"; }
 elseif( $prip_tech == 3 )
 { $prip_tech_3 = "X"; }

 if( (strlen($int_sluzba_tarif_text) >= 1 ) )
 { $int_sluzba_tarif_text = iconv("UTF-8","CP1250", $int_sluzba_tarif_text); }
 else
 { $int_sluzba_tarif_text = "- - - - -"; }
 
 if( (strlen($int_sluzba_tarif_cena) >= 1 ) )
 { $int_sluzba_tarif_cena = iconv("UTF-8","CP1250", $int_sluzba_tarif_cena).",-"; }
 else
 { $int_sluzba_tarif_cena = "- - -"; }
 
 if( (strlen($int_sluzba_tarif_cena_s_dph) >= 1 ) )
 { $int_sluzba_tarif_cena_s_dph = iconv("UTF-8","CP1250", $int_sluzba_tarif_cena_s_dph).",-"; }
 else
 { $int_sluzba_tarif_cena_s_dph = "- - -"; }

 if( (strlen($int_sluzba_rychlost) >= 1 ) )
 { $int_sluzba_rychlost = iconv("UTF-8","CP1250", $int_sluzba_rychlost); }
 else
 { $int_sluzba_rychlost = "- -"; }
 
 if( (strlen($int_sluzba_tarif_agr) >= 1 ) )
 { $int_sluzba_tarif_agr = iconv("UTF-8","CP1250", $int_sluzba_tarif_agr); }
 else
 { $int_sluzba_tarif_agr = "-"; }
 
 //$int_verejna_ip = $_POST["int_verejna_ip"];
 
 if( $int_verejna_ip == 1 )
 { $int_verejna_ip_x = "X"; }
 else
 { $int_verejna_ip_x = "-"; }
 
 if( (strlen($int_verejna_ip_cena) >= 1 ) )
 { $int_verejna_ip_cena = iconv("UTF-8","CP1250", $int_verejna_ip_cena).",-"; }
 else
 { $int_verejna_ip_cena = "- - -"; }
 
 if( (strlen($int_verejna_ip_cena_s_dph ) >= 1 ) )
 { $int_verejna_ip_cena_s_dph = iconv("UTF-8","CP1250", $int_verejna_ip_cena_s_dph).",-"; }
 else
 { $int_verejna_ip_cena_s_dph = "- - -"; } 

 //$iptv_sluzba = $_POST["iptv_sluzba"];
 if( $iptv_sluzba_id_tarifu ==1 )
 {
  $iptv_sluzba_tarif_1 = "X";
  $iptv_sluzba_tarif_2 = "-";
 }
 elseif( $iptv_sluzba_id_tarifu == 2 )
 {
  $iptv_sluzba_tarif_1 = "-";
  $iptv_sluzba_tarif_2 = "X";
 }
 else
 {
  $iptv_sluzba_tarif_1 = "-";
  $iptv_sluzba_tarif_2 = "-";
 }
 
 if( (strlen($iptv_sluzba_cena ) >= 1 ) )
 { $iptv_sluzba_cena = $iptv_sluzba_cena.",-"; }
 else
 { $iptv_sluzba_cena = "- - -"; }
 
 if( (strlen($iptv_sluzba_cena_s_dph) >= 1 ) )
 { $iptv_sluzba_cena_s_dph = $iptv_sluzba_cena_s_dph.",-"; }
 else
 { $iptv_sluzba_cena_s_dph = "- - -"; }

 for($i=1; $i<=$pocet_tb; $i++)
 {
    $tb = "tb".$i;
    
    $tb_cena = "tb_cena_".$i;
    $tb_cena_s_dph = "tb_cena_s_dph_".$i;
    
    $tb_x = "tb".$i."_x";
	
    if( (strlen($$tb) > 1) )
    { 
	$$tb_x = "X";
	$$tb = iconv("UTF-8","CP1250", $$tb); 
    }
    else
    { 
	$$tb_x = " -";
	$$tb = "- - - - -"; 
    }

    if( (strlen($$tb_cena) >= 1) )
    { $$tb_cena = iconv("UTF-8","CP1250", $$tb_cena).",-"; }
    else
    { $$tb_cena = "- - -"; }
    
    if( (strlen($$tb_cena_s_dph) >= 1) )
    { $$tb_cena_s_dph = iconv("UTF-8","CP1250", $$tb_cena_s_dph).",-"; }
    else
    { $$tb_cena_s_dph = "- - -"; }
    
 }

 for($i=1; $i<=3; $i++)
 {
    $tb = "tb".$i;
    $tb_x = "tb".$i."_x";
    
    $tb_cena = "tb_cena_".$i;
    $tb_cena_s_dph = "tb_cena_s_dph_".$i;

    if( (strlen($$tb_x) < 1) )
    { $$tb_x = " -"; }
    
    if( (strlen($$tb) < 1) )
    { $$tb = "- - - - -"; }
    
    if( (strlen($$tb_cena) < 1) )
    { $$tb_cena = "- - -"; }

    if( (strlen($$tb_cena_s_dph) < 1) )
    { $$tb_cena_s_dph = "- - -"; }
    
 }
  
 if( (strlen($voip_cislo) < 1) )
 {
   $voip_cislo = "- - - - -";
 }

 if( $voip_typ == 1 )
 {
  $voip_postpaid = "X";
  $voip_prepaid = "-";
 }
 elseif( $voip_typ == 2 )
 {
  $voip_postpaid = "-";
  $voip_prepaid = "X";
 }
 else
 {
  $voip_postpaid = "-";
  $voip_prepaid = "-";
 }

 if( $zpusob_placeni == 1)
 {
  $zpusob_placeni_1 = "X";
  $zpusob_placeni_2 = " -";
  $zpusob_placeni_3 = " -";
 }
 elseif( $zpusob_placeni == 2)
 {
  $zpusob_placeni_1 = " -";
  $zpusob_placeni_2 = "X";
  $zpusob_placeni_3 = " -";
 }
 elseif( $zpusob_placeni == 3)
 {
  $zpusob_placeni_1 = " -";
  $zpusob_placeni_2 = " -";
  $zpusob_placeni_3 = "X";
 }
 else
 {
  $zpusob_placeni_1 = "-";
  $zpusob_placeni_2 = "-";
  $zpusob_placeni_3 = "-";
 }
 
 if( (strlen($celk_cena) > 1) )
 {
  $celk_cena = $celk_cena.",-";
 }

 if( (strlen($celk_cena_s_dph) > 1) )
 {
  $celk_cena_s_dph = $celk_cena_s_dph.",-";
 }	

 if( $sleva_select == 1 )
 {
  $sleva_on = "X";
  $sleva_hodnota = iconv("UTF-8","CP1250", $sleva_hodnota);
 }
 else
 { 
  $sleva_on = "-"; 
  $sleva_hodnota = "- -";
 }
 
 if( $min_plneni == 2 )
 { $min_plneni_on = "X"; }
 else
 { 
    $min_plneni_on = "-"; 
    $min_plneni_doba = "- -"; 
 }
 
// konec pripravy promennych

// opravdovy zacatek generovani 
define('FPDF_FONTPATH',"../include/font/");
require("../include/fpdf.class.php");

//zaklad, vytvoreni objektu a pridani stranky
$pdf=new FPDF("P","mm","A4");
$pdf->Open();
$pdf->AddPage();

// ceskej arial
$pdf->AddFont('arial','','arial.php');

// autor a podobny hemzy

//Nastaví autora dokumentu.
$pdf->SetAuthor("Patrik Majer"); 

//Nastaví tvůrce dokumentu (většinou název aplikace)
$pdf->SetCreator("Smlouva o poskytování služeb"); 

//Titulek dokumentu
$pdf->SetTitle("Smlouva");
 
// vlozeni obrazku na pozadi
$img="../img2/print/smlouva-s-r-o-new5.jpg";
$pdf->Image($img,0,0,210);

$pdf->SetFont('Arial','',10);

$pdf->Cell(0,1,'',0,1);

$pdf->Cell(145); $pdf->Cell(50,3,$ec,0,1);

$pdf->Cell(0,34,'',0,1);

 $pdf->Cell(35); $pdf->Cell(5,5,$jmeno,0,0);
 $pdf->Cell(95); $pdf->Cell(5,5,$nazev_spol,0,1);

 $pdf->Cell(53); $pdf->Cell(5,6,$adresa,0,0);
 $pdf->Cell(77); $pdf->Cell(5,6,$ico_dic,0,1);

 $pdf->Cell(53); $pdf->Cell(5,4,$mesto,0,0);
 $pdf->Cell(77); $pdf->Cell(5,4,$email,0,1);

 $pdf->Cell(53); $pdf->Cell(5,6,$telefon,0,1);

 $pdf->Cell(53); $pdf->Cell(5,4,$kor_adresa,0,1);
 $pdf->Cell(53); $pdf->Cell(5,5,$kor_mesto,0,1);

 $pdf->Cell(0,21,'',0,1); 

 $pdf->Cell(66); $pdf->Cell(5,4,$spec_prip_mista,0,1); //prip misto jako trvale bydl.

 $pdf->Cell(52); $pdf->Cell(5,8,$prip_misto_adresa,0,0);
 $pdf->Cell(45); $pdf->Cell(5,8,$prip_misto_cp,0,0);

 $pdf->Cell(22); $pdf->Cell(5,8,$adr_prip_jako_kor,0,1); //pouz. adresu prip. jako kor.

 $pdf->Cell(52); $pdf->Cell(5,2,$prip_misto_mesto,0,0);
 $pdf->Cell(45); $pdf->Cell(5,2,$prip_misto_psc,0,1);

 $pdf->Cell(0,4,'',0,1); 

 $pdf->Cell(53); $pdf->Cell(5,5,$prip_tech_1,0,0);
 $pdf->Cell(43); $pdf->Cell(5,5,$prip_tech_2,0,0);
 $pdf->Cell(40); $pdf->Cell(5,5,$prip_tech_3,0,1);

 $pdf->Cell(0,6,'',0,1); 

 $pdf->Cell(53); $pdf->Cell(5,6,$int_sluzba_tarif_text,0,0);

 $pdf->Cell(87); $pdf->Cell(5,6,$int_sluzba_tarif_cena,0,0);
 $pdf->Cell(20); $pdf->Cell(5,6,$int_sluzba_tarif_cena_s_dph,0,1);


 $pdf->Cell(52); $pdf->Cell(5,4,$int_sluzba_rychlost,0,0);
 $pdf->Cell(33); $pdf->Cell(5,3,$int_sluzba_tarif_agr,0,0);

 $pdf->Cell(9); $pdf->Cell(5,3,$int_verejna_ip_x,0,0);
 $pdf->Cell(36); $pdf->Cell(5,3,$int_verejna_ip_cena,0,0);
 $pdf->Cell(20); $pdf->Cell(5,3,$int_verejna_ip_cena_s_dph,0,1);

 $pdf->Cell(0,7,'',0,1); 

//IPTV
 $pdf->Cell(25); $pdf->Cell(5,3,$iptv_sluzba_tarif_1,0,0);
 $pdf->Cell(28); $pdf->Cell(5,3,$iptv_sluzba_tarif_2,0,0);
 $pdf->Cell(82); $pdf->Cell(5,3,$iptv_sluzba_cena,0,0);
 $pdf->Cell(20); $pdf->Cell(5,3,$iptv_sluzba_cena_s_dph,0,1);

 $pdf->Cell(0,3,'',0,1); 

//tem. balicky
 $pdf->Cell(47); $pdf->Cell(5,4,$tb1_x,0,0);

 $pdf->Cell(25); $pdf->Cell(5,5,$tb1,0,0);
 $pdf->Cell(63); $pdf->Cell(5,5,$tb_cena_1,0,0);
 $pdf->Cell(20); $pdf->Cell(5,5,$tb_cena_s_dph_1,0,1);

 $pdf->Cell(47); $pdf->Cell(5,4,$tb2_x,0,0);

 $pdf->Cell(25); $pdf->Cell(5,5,$tb2,0,0);
 $pdf->Cell(63); $pdf->Cell(5,5,$tb_cena_2,0,0);
 $pdf->Cell(20); $pdf->Cell(5,5,$tb_cena_s_dph_2,0,1);

 $pdf->Cell(47); $pdf->Cell(5,4,$tb3_x,0,0);

 $pdf->Cell(25); $pdf->Cell(5,5,$tb3,0,0);
 $pdf->Cell(63); $pdf->Cell(5,5,$tb_cena_3,0,0);
 $pdf->Cell(20); $pdf->Cell(5,5,$tb_cena_s_dph_3,0,1);

 $pdf->Cell(0,3,'',0,1); 

//voip
 $pdf->Cell(55); $pdf->Cell(5,5,$voip_cislo,0,0);

 $pdf->Cell(41); $pdf->Cell(5,5,$voip_postpaid,0,0);
 $pdf->Cell(32); $pdf->Cell(5,5,$voip_prepaid,0,1);

 $pdf->Cell(0,5,'',0,1); 

//sleva
 $pdf->Cell(148); $pdf->Cell(5,6,$sleva_on,0,0);
 $pdf->Cell(12); $pdf->Cell(5,6,$sleva_hodnota,0,1);

 $pdf->Cell(0,8,'',0,1);

//placeni
 $pdf->Cell(4); $pdf->Cell(5,5,$zpusob_placeni_1,0,0);
 $pdf->Cell(81); $pdf->Cell(5,5,$vs,0,1);

 $pdf->Cell(4); $pdf->Cell(5,5,$zpusob_placeni_2,0,1);
 
 $pdf->Cell(4); $pdf->Cell(5,4,$zpusob_placeni_3,0,0);
 $pdf->Cell(81); $pdf->Cell(5,5,$splatnost_ke_dni,0,0);

 $pdf->Cell(50); $pdf->Cell(5,4,$celk_cena,0,0);
 $pdf->Cell(19); $pdf->Cell(5,4,$celk_cena_s_dph,0,1);

//MIN DOBA PLNENI

 $pdf->Cell(0,25,'',0,1);
 
 $pdf->Cell(77); $pdf->Cell(5,5,$min_plneni_on,0,0);
 $pdf->Cell(28); $pdf->Cell(5,5,$min_plneni_doba,0,1);

 // $pdf->Output("smlouva.pdf",true);
 //$pdf->Output();

 $datum_nz = date('Y-m-d-H-i-s');

 if($id_cloveka > 0 )
 { $nazev_souboru = "temp/smlouva-fiber-pdf-id-".$id_cloveka."-".$datum_nz.".pdf"; }
 else
 { $nazev_souboru = "temp/smlouva-fiber-pdf-ec-".$ec."-".$datum_nz.".pdf"; }

 $rs = $pdf->Output($nazev_souboru,"F");

 //presmerovani na dpdf soubor

 echo '<html>
        <head>
            <meta http-equiv="refresh" content="1;url='.$nazev_souboru.'">
            <title>Tisk smlouvy</title>
        </head>
       <body>
          Vygenerovany soubor je <a href="'.$nazev_souboru.'" >zde</a>.
       </body>
      </html>';
								    
} //konec else !isset nazev

?>
