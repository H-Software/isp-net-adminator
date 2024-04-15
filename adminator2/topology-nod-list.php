<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");
require "./include/c_listing_topology.php";    //předpokládáme třídu uloženou v externím souboru

if( !( check_level($level,5) ) )
{
 // neni level
 header("Location: nolevelpage.php");

 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
}
  
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ("include/charset.php");

?>

<title>Adminator2 - Topology</title>

</head>

<body> 

<?php require ("head.php"); ?>

<?php require ("category.php"); ?>

   <tr>
     <td colspan="2" bgcolor="silver" >
     <?php require ("topology-cat2.php"); ?>
     </td>
   </tr>
   
  <tr>
  
    <td colspan="2">
    <!-- zacatek vlastniho obsahu -->
  
  <?php

   $list=$_GET["list"];
   $razeni=$_GET["razeni"];
   
   $datum = strftime("%d/%m/%Y %H:%M:%S", time());

   $ping=$_GET["ping"];
   $find=$_GET["find"];

   $typ_vysilace = $_GET["typ_vysilace"];
   $stav = $_GET["stav"];

   $typ_nodu = $_GET["typ_nodu"];
   if( !isset($typ_nodu) )
   { $typ_nodu = "1"; }
    
   $find_orez = str_replace("%","",$find);
    
 if( (strlen($find) < 1) )
 { $find="%"; }
 else
 {
   if( !(ereg("^%.*%$",$find)) ){ $find="%".$find."%"; }
 }
  
  // jako sem zbytek
  echo "<div style=\"padding-top: 10px; padding-bottom: 20px; \" >
    <span style=\"padding-left: 20px; font-size: 20px; font-weight: bold; \">
    Výpis lokalit / přípojných bodů
    </span>
    <span style=\"padding-left: 80px; \" ><a href=\"include/export-topology.php\" >export lokalit/nodů</a></span>  
 
    <span style=\"padding-left: 80px; \" >
	Výpis lokalit/nodů s latencemi ";
	
	if($ping == 1)
	{ 
	  echo "<a href=\"".$_SERVER["PHP_SELF"]."?razeni=".$razeni."&ping=&find=".$find_orez;
	  echo "&list=".$list."&typ_nodu=".$typ_nodu."\">vypnout</a>"; 
	}
	else
	{ 
	  echo "<a href=\"".$_SERVER["PHP_SELF"]."?razeni=".$razeni."&ping=1&find=".$find_orez;
	  echo "&list=".$list."&typ_nodu=".$typ_nodu."\">zapnout</a>"; 
	}
	
    echo "</span>
 </div>";

  echo "<div style=\"padding-left: 20px; \" >
	<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"GET\" >
	    	    
	    <input type=\"hidden\" name=\"razeni\" value=\"".$razeni."\" >
	    <input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >
	        
	     <span style=\"font-weight: bold; \" >Hledání:</span>
	    
	     <span style=\"padding-left: 10px; \" >
	        <input type=\"text\" name=\"find\" size=\"15\" value=\"".$find_orez."\" style=\"font-size: 12px; \" >
	     </span>
	
	
	<span style=\"padding-left: 10px; \" ><span style=\"color: grey; font-weight: bold; \">filtr:</span></span>
	
	<span style=\"padding-left: 10px; \" >typ:</span>
	
	<span style=\"padding-left: 10px; \" >
	    <select name=\"typ_vysilace\" size=\"1\">
		<option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>    
		<option value=\"1\" "; if( $typ_vysilace == 1){ echo " selected "; } echo ">Metallic</option>    
	        
		<option value=\"2\" "; if( $typ_vysilace == 2){ echo " selected "; } echo ">ap-2,4GHz-OMNI</option>    
	        <option value=\"3\" "; if( $typ_vysilace == 3){ echo " selected "; } echo ">ap-2,4GHz-sektor</option>    
	        <option value=\"4\" "; if( $typ_vysilace == 4){ echo " selected "; } echo ">ap-2.4GHz-smerovka</option>    
	        <option value=\"5\" "; if( $typ_vysilace == 5){ echo " selected "; } echo ">ap-5.8Ghz-OMNI</option>    
	        <option value=\"6\" "; if( $typ_vysilace == 6){ echo " selected "; } echo ">ap-5.8Ghz-sektor</option>
	        <option value=\"7\" "; if( $typ_vysilace == 7){ echo " selected "; } echo ">ap-5.8Ghz-smerovka</option>
		<option value=\"8\" "; if( $typ_vysilace == 8){ echo " selected "; } echo ">jiné</option>
	        
	    </select>
	</span>

	<span style=\"padding-left: 20px; \" >stav: </span>

	<span style=\"padding-left: 10px; \" >
	    <select name=\"stav\" size=\"1\" >
		<option value=\"0\" class=\"select-nevybrano\">Nevybráno</option>
		<option value=\"1\" "; if( $stav == 1){ echo " selected "; } echo ">V pořádku</option>
		<option value=\"2\" "; if( $stav == 2){ echo " selected "; } echo ">Vytížen</option>
		<option value=\"3\" "; if( $stav == 3){ echo " selected "; } echo ">Přetížen</option>
	    </select>
	</span>
	
	<span style=\"padding-left: 10px; \" >mód:</span>
	    <select name=\"typ_nodu\" size=\"1\" >
		<option value=\"0\" class=\"select-nevybrano\">Nevybráno</option>
		<option value=\"1\" style=\"color: #CC0033; \" "; 
		  if( $typ_nodu == 1){ echo " selected "; } 
		echo ">bezdrátová síť</option>
		
		<option value=\"2\" style=\"color: #e37d2b; font-weight: bold;\" ";
		  if( $typ_nodu == 2){ echo " selected "; } 
		echo ">optická síť</option>
	    </select>
	
	<span style=\"padding-left: 30px; \" ><input type=\"submit\" name=\"odeslat\" value=\"OK\" ></span>
	
	</form>
  </div>
  
  <div style=\"padding-left: 20px; \" >
    <span style=\"font-weight: bold; padding-right: 10px; \">Hledaný výraz:</span> ".$find."
  </div>";
  

 //aby se stihli pingy
 // set_time_limit(0);
	
   // tvoreni dotazu
	
	if ( $razeni == 1 ){ $order=" order by id asc"; }
	elseif ( $razeni== 2 ){ $order=" order by id desc"; }
	elseif ( $razeni== 3 ){ $order=" order by jmeno asc"; }
	elseif ( $razeni== 4 ){ $order=" order by jmeno desc"; }
	elseif ( $razeni== 5 ){ $order=" order by adresa asc"; }
	elseif ( $razeni== 6 ){ $order=" order by adresa desc"; }
	elseif ( $razeni== 7 ){ $order=" order by pozn asc"; }
	elseif ( $razeni== 8 ){ $order=" order by pozn desc"; }
	elseif ( $razeni== 9 ){ $order=" order by ip_rozsah asc"; }
	elseif ( $razeni== 10 ){ $order=" order by ip_rozsah desc"; }
	elseif ( $razeni== 11 ){ $order=" order by umisteni_aliasu asc"; }
	elseif ( $razeni== 12 ){ $order=" order by umisteni_aliasu desc"; }
	elseif ( $razeni== 13 ){ $order=" order by mac asc"; }
	elseif ( $razeni== 14 ){ $order=" order by mac desc"; }

	$where = " WHERE ( id = '$find' OR jmeno LIKE '$find' OR adresa LIKE '$find' ";
	$where .= "OR pozn LIKE '$find' OR ip_rozsah LIKE '$find' ) ";
	
	if( $typ_vysilace > 0 ){ $where .= "AND ( typ_vysilace = '$typ_vysilace' ) "; }
	
	if( $stav > 0 ){ $where .= "AND ( stav = '$stav' ) "; }
	
	if( $typ_nodu > 0 ){ $where .= " AND ( typ_nodu = '$typ_nodu' ) "; }
	
	$sql="select * from nod_list ".$where." ".$order;

    $sql_source = "./topology-nod-list.php?razeni=".$razeni."&ping=".$ping;
    $sql_source .= "&typ_vysilace=".$typ_vysilace."&stav=".$stav."&find=".$find_orez;
    $sql_source .= "&typ_nodu=".$typ_nodu;
    
   //vytvoreni objektu
   $listovani = new c_listing_topology($sql_source, 30, $list,
	"<center><div class=\"text-listing\">\n", "</div></center>\n",$sql." ; ");
				     
    if (($list == "")||($list == "1"))
    {    //pokud není list zadán nebo je první
     $bude_chybet = 0; //bude ve výběru sql dotazem chybet 0 záznamů
    }
    else
    {
     $bude_chybet = (($list-1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
    }
    
    $vysledek = $conn_mysql->query($sql." LIMIT ".$bude_chybet.",".$listovani->interval." ");
	echo "SQL DUMP: ".$sql." LIMIT ".$bude_chybet.",".$listovani->interval." ";
	
    //$vysledek = mysql_query($sql);
			     
    echo "<div style=\"padding-top: 10px; padding-bottom: 10px; \" >".$listovani->listInterval()."</div>";    //zobrazení stránkovače
    
    $radku=$vysledek->num_rows;
    
    if ($radku==0)
    {
	 echo "<div >Žadné lokality/nody dle hladeného výrazu ( ".$find." ) v databázi neuloženy. </div>";
	// echo "<div >debug: sql: ".$sql." </div>";
    }
    else
    {
	
	// echo '<br>Výpis lokalit/nodů: <span style="color: silver">řazeno dle id: '.$_POST["razeni"].'</span><BR><BR>';
	
	$colspan_id="1";
	$colspan_jmeno="3";
	$colspan_adresa="3";
	$colspan_pozn="2";
	$colspan_rozsah_ip="1";
	$typ_nodu = "1";
	$colspan_umisteni="2";
	
	$colspan_celkem = $colspan_id + $colspan_jmeno + $colspan_adresa + $colspan_pozn + $colspan_rozsah_ip + $colspan_umisteni;
	
	echo "<table border=\"0\" >";

	// echo "<tr><td colspan=\"".$colspan_celkem."\"><hr></td></tr>";
	
	echo "\n<tr>
	    <td width=\"5%\" colspan=\"".$colspan_id."\"  class=\"tab-topology2 tab-topology-dolni2\" >
	    
		<table border=\"0\" width=\"\" >
		<tr>
		    <td><b>id:</b></td>";
		
		echo "<td>";
		    echo "<form name=\"form1\" method=\"GET\" action=\"\" > ";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"1\" >";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form1.submit()\">
		    <img src=\"img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	    
		    echo "<form  name=\"form2\" method=\"GET\" action=\"\" > ";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"2\">";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form2.submit()\">";
		    echo "<img src=\"img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	
		echo "</td></tr></table>";
		
	    echo "</td>";
	    
	    echo "<td width=\"20%\" colspan=\"".$colspan_jmeno."\" class=\"tab-topology2 tab-topology-dolni2\" >
	    
		<table border=\"0\" width=\"100%\" >
		<tr>
		    <td><b>Jméno: </b></td>";
	
		echo "<td >";
		    echo "<form  name=\"form3\" method=\"GET\" action=\"\" >";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"3\" >";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form3.submit()\">
		    <img src=\"img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	    
		    echo "<form  name=\"form4\" method=\"GET\" action=\"\" >";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"4\">";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form4.submit()\">";
		    echo "<img src=\"img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	
		echo "</td></tr></table>";
		    
	    echo "</td>";
	    
	    
	    echo "<td colspan=\"".$colspan_adresa."\" class=\"tab-topology2 tab-topology-dolni2\" >
	    
		<table border=\"0\" width=\"100%\" >
		<tr>
		    <td><b>Adresa: </b></td>";
		    
		   echo "<td>";
		    echo "<form  name=\"form5\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"5\" >";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form5.submit()\">
		    <img src=\"img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	    
		    echo "<form  name=\"form6\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"6\">";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form6.submit()\">";
		    echo "<img src=\"img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	
		echo "</td></tr></table>";
		    
	    echo "</td>";
	    
	    echo "<td colspan=\"1\" class=\"tab-topology2 tab-topology-dolni2\" >
		
	      <table border=\"0\" width=\"100%\" >
		<tr>
		    <td><b>Poznámka: </b></td>";
		    
		 echo "<td>";
		    echo "<form  name=\"form7\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"7\" >";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form7.submit()\">
		    <img src=\"img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	    
		    echo "<form  name=\"form8\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"8\">";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form8.submit()\">";
		    echo "<img src=\"img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	
		echo "</td></tr></table>";
		
		echo "</td>";
	
		echo "<td colspan=\"1\" width=\"15%\" class=\"tab-topology2 tab-topology-dolni2\" align=\"center\" >
		<b>Vlan ID</b><br></td>";
		
		
	    echo "<td width=\"10%\" colspan=\"".$colspan_rozsah_ip."\" class=\"tab-topology2 tab-topology-dolni2\" >
	    
		<table border=\"0\" width=\"100%\" >
		<tr>
		    <td><b>Rozsah ip adres: </b></td>";
		    
		 echo "<td>";
		    echo "<form  name=\"form9\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"9\" >";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form9.submit()\">
		    <img src=\"img2/vzes.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	    
		    echo "<form  name=\"form10\" method=\"GET\" action=\"".$_SERVER["PHP_SELF"]."\">";
		    echo "<input type=\"hidden\" name=\"razeni\" value=\"10\">";
		    
		    echo "<input type=\"hidden\" name=\"find\" value=\"".$find_orez."\" >";
		    echo "<input type=\"hidden\" name=\"list\" value=\"".$list."\" >";
		    echo "<input type=\"hidden\" name=\"ping\" value=\"".$ping."\" >";
		    
		    echo "<a href=\"javascript:self.document.forms.form10.submit()\">";
		    echo "<img src=\"img2/ses.png\" width=\"20px\" height=\"10px\" border=\"0\" alt=\"Po kliknutí se formulář odešle\">
		    </a></form>";
	
		echo "</td></tr></table>";
		
	    echo "</td>";
	        
	    echo "<td width=\"10%\" colspan=\"".$typ_nodu."\" class=\"tab-topology2 tab-topology-dolni2\" >
		     <b>Mód nodu</b></td>";
	    	    
	    // bunky druhej radek
	    $colspan_filtrace="1";
	    
	    $colspan_mac="3";
	    
	    $colspan_typ_vysilace="3";
	    $colspan_aktivni="1";
	    $colspan_stav="1";
		    
	    $colspan_uprava="2";
	    $colspan_smazani="2";
		    
	    echo "
	    
	    </tr>
	    
	    <tr>
	     <td colspan=\"1\" class=\"tab-topology2\" ><br></td>
	     <td colspan=\"3\" class=\"tab-topology2\" >
	        <span style=\"color: #666666; font-weight: bold; \">Umístění aliasu (název routeru): </span></td>
	    
	     <td colspan=\"".$colspan_typ_vysilace."\" class=\"tab-topology2\" ><span style=\"color: #666666; font-weight: bold; \">Typ vysílače: </span></td>
	    
	     <td colspan=\"".$colspan_aktivni."\" class=\"tab-topology2\" align=\"center\" >
	     <span style=\"color: #666666; font-weight: bold; \">Aktivní: </span></td>
	     <td colspan=\"".$colspan_stav."\" class=\"tab-topology2\" align=\"center\" ><span style=\"color: #666666; font-weight: bold; \">Stav: </span></td>
	            
	     <td colspan=\"".$colspan_uprava."\" class=\"tab-topology2\" ><span style=\"color: #666666; font-weight: bold; \">Úprava / Smazání: </span></td>
	    
    	    </tr>\n";

	    //treti radek
	    echo "<tr><td colspan=\"".$colspan_celkem."\"><hr></td></tr>";
	    
	//vnejsi tabulka
	echo "</tr>";
	
	echo "<tr>";
								 
	echo "\n";
        while ($zaznam=$vysledek->fetch_array()):
	
	 $id=$zaznam["id"];
	
	 // prvni radek
         echo "<tr>";
	 echo "<td colspan=\"".$colspan_id."\"><span style=\"font-size: 12px; padding-right: 5px;\" >";
	    echo $id."</span><a name=\"".$id."\" ></a>";
	 echo "</td>\n";

         echo "<td colspan=\"".$colspan_jmeno."\">
        	    <span style=\"font-weight: bold; font-size: 14px; \">".
        		"<a href=\"topology-user-list.php?vysilac=".intval($zaznam["id"])."\" >".$zaznam["jmeno"]."</a>".
        	    "</span>\n".
    	      "</td>\n";
         echo "<td colspan=\"".$colspan_adresa."\" >".
        	    "<span style=\"font-size: 13px; padding-right: 10px; \">".$zaznam["adresa"]."</span>".
        	    "<a href=\"\"><a href=\"http://www.mapy.cz?query=".$zaznam["adresa"]."\" target=\"_blank\" >na mapě</a>".
    		"</td>\n";
         
	 //if( $_GET["typ_nodu"] == 2 )
	 {
	  echo "<td colspan=\"1\" ><span style=\"font-size: 13px; \">".$zaznam["pozn"]."</span></td>\n";
	  echo "<td colspan=\"1\" align=\"center\">
		    <span style=\"font-size: 13px; \">".$zaznam["vlan_id"]."</span>
		</td>\n";
	 
	 }
	 //else{ echo "<td colspan=\"".$colspan_pozn."\" ><span style=\"font-size: 13px; \">".$zaznam["pozn"]."</span></td>\n";  }
	     
         echo "<td colspan=\"".$colspan_rozsah_ip."\" ><span style=\"font-size: 13px; \">".$zaznam["ip_rozsah"]."</span></td>\n";
         echo "<td colspan=\"".$typ_nodu."\" ><span style=\"font-size: 13px; \">";
	    if( $zaznam["typ_nodu"] == 0 )
	    { echo "Nezvoleno"; }
	    elseif( $zaznam["typ_nodu"] == 1 )
	    { echo "<span style=\"color: #CC0033; \">bezdrátová síť</span>"; }
	    elseif( $zaznam["typ_nodu"] == 2 )
	    { echo "<span style=\"color: #e37d2b; font-weight: bold; \" >optická síť</span>"; }
	    
	 echo "</span></td>\n";
         
	 echo "<td colspan=\"".$colspan_umisteni."\" rowspan=\"2\" class=\"tab-topology\"><span style=\"font-size: 13px; \">";
	 
	 echo "</span></td>\n";
	    
	echo "</tr>";
	    
	// druhej radek
	echo "<tr>";
	    
	    echo "<td class=\"tab-topology\" colspan=\"".$colspan_filtrace."\" >
		<a href=\"archiv-zmen.php?id_nodu=".intval($id)."\" style=\"font-size: 12px; \">H: ".$id."</a>".
		"</td>\n";
	    	    
	    echo "<td class=\"tab-topology\" colspan=\"3\">
	    <span style=\"color: #666666; font-size: 13px; padding-right: 10px; \" >";
	        
	    $router_id = $zaznam["router_id"];
	
	    if ($router_id <= 0)
	    { $router_nazev="<span style=\"color: red\">nelze zjistit </span>"; $router_ip=""; }
	    else
	    {
		$vysledek_router=$conn_mysql->query("SELECT nazev, ip_adresa FROM router_list where id = ".intval($router_id)." ");
		while($data_router=$vysledek_router->fetch_array())
		{ $router_nazev = $data_router["nazev"]; $router_ip = $data_router["ip_adresa"]; }
            }
	    
	    echo "<span style=\"color: teal; \">".$router_nazev."</span> ".$router_ip."</span>";
	    echo "<a href=\"topology-router-list.php?odeslano=OK&f_search=".$router_ip."&\">link</a>";

	    echo "</td>\n";
	    
	    $typ_vysilace=$zaznam["typ_vysilace"];
	    
	     if ( $typ_vysilace == 1 ){ $typ_vysilace2="Metallic"; }
	     elseif ( $typ_vysilace == 2 ){ $typ_vysilace2="ap-2,4GHz-OMNI"; }
	     elseif ( $typ_vysilace == 3 ){ $typ_vysilace2="ap-2,4Ghz-sektor"; }
	     elseif ( $typ_vysilace == 4 ){ $typ_vysilace2="ap-2.4Ghz-smerovka"; }
	     elseif ( $typ_vysilace == 5 ){ $typ_vysilace2="ap-5.8Ghz-OMNI"; }
	     elseif ( $typ_vysilace == 6 ){ $typ_vysilace2="ap-5.8Ghz-sektor"; }
	     elseif ( $typ_vysilace == 7 ){ $typ_vysilace2="ap-5.8Ghz-smerovka"; }
	     elseif ( $typ_vysilace == 8 ){ $typ_vysilace2="jiné"; }
	     else { $typ_vysilace2=$typ_vysilace; }			
	     																		       
	    echo "<td class=\"tab-topology\" colspan=\"".$colspan_typ_vysilace."\" ><span style=\"color: #666666; font-size: 13px; \">".$typ_vysilace2."</span> </td>\n";
	    
	     list($a,$b,$c,$d) = split("[.]",$zaznam["ip_rozsah"]);
	    
	    if ( $c == 0) { $c=1; }
	     
	     $d=1;
	    $ip_akt=$a.".".$b.".".$c.".".$d;
	    
	    $akt_par="class=\"tab-topology\" colspan=\"".$colspan_aktivni."\" ";
	    
	    if ( ( $_GET["ping"] == 1 ) )
	    { 
		$aktivni=exec("/srv/www/htdocs.ssl/adminator2/scripts/ping.sh $ip_akt"); 
	    
		if ( ( $aktivni > 0 and $aktivni < 50 ) ) 
		{  echo "<td ".$akt_par." align=\"center\" bgcolor=\"green\"><span style=\"color: white; font-size: 13px; \">".$aktivni."</span>"; }
		elseif ( $aktivni > 0)
		{ echo "<td ".$akt_par." align=\"center\" bgcolor=\"orange\"><span style=\"color: white; font-size: 13px; \">".$aktivni."</span>"; }
		else { echo "<td ".$akt_par." align=\"center\" bgcolor=\"red\">"; echo "<br>"; }
	    }
	    else
	    {	echo "<td ".$akt_par." align=\"center\" ><span style=\"color: #666666; font-size: 13px; \">N/A</span>"; }
	    
	    echo "</td>";
	    
	    if ( $zaznam["stav"] == 1)
	    { 
		echo "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"green\" align=\"center\" >
		<span style=\"color: white; font-size: 13px; \"> v pořádku </span></td>"; 
	    }
	    elseif ( $zaznam["stav"] == 2)
	    { 
		echo "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"orange\" align=\"center\" >
		<span style=\"color: white; font-size: 13px; \"> vytížen </span></td>"; 
	    }
	    elseif( $zaznam["stav"] == 3 )
	    { 
		echo "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"red\" align=\"center\" >
		<span style=\"color: white; font-size: 13px; \"> přetížen </span></td>"; 
	    }
	    else
	    { 
		echo "<td class=\"tab-topology\" colspan=\"".$colspan_stav."\" bgcolor=\"silver\" align=\"center\" >
		<span style=\"color: black; font-size: 13px; \"> nezvoleno </span></td>"; 
	    }

	    echo "<td class=\"tab-topology\" colspan=\"".$colspan_uprava."\" >";
	    
	    //vnitrni tabulka
	    echo "<table width=\"100%\" border=\"0\"><tr>";
	    
	    // upraveni 
	     echo "<td><form method=\"POST\" action=\"topology-nod-update.php\">
		  <input type=\"hidden\" name=\"update_id\" value=\"".$id."\">
		  <input type=\"submit\" value=\"update\">
		  </form>
		  </td>";
	    
	    //smazani
	    //echo "<td class=\"tab-topology\" colspan=\"\" >";
	    
	   echo "<td><form action=\"topology-nod-erase.php\" method=\"POST\" >";
	   echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$id."\">";
	   echo "<input type=\"submit\" value=\"Smazat\">
		</form>
		</td>";
	
	    //konec vnirni tabulky
	    echo "</tr></table>";
	    
	   echo "</td>";
	    			     
	  echo "</tr>";

          endwhile;
 }

    echo "</table>";

    echo "<div style=\"padding-top: 20px; margin-bottom: 20px; \" >";
    echo "<span style=\"margin-top: 5px; margin-bottom: 15px; \">".$listovani->listInterval();
    
    echo "</div>";    //zobrazení stránkovače
    
//	$listovani->listInterval();    //zobrazení stránkovače
	 
?>
 
  </td>
  </tr>
  
 </table>

</body> 
</html> 

