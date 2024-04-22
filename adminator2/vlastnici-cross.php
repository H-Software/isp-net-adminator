<?php

require("include/main.function.shared.php");
require("include/config.php"); 

echo "<html>
	<head>
	    <title>Vlastníci rozcestník</title>";

require("include/charset.php");

echo "</head>
    <body>";

$akce = $_GET["akce"];
$id_cloveka = $_GET["id_cloveka"];

if( !( preg_match('/^([[:digit:]]+)$/', $id_cloveka) ) )
{
 echo "Chyba! Nesouhlasi vstupni data. (id cloveka) ";
 exit;
}
  
if( !( preg_match('/^([[:digit:]]+)$/', $akce) ) )
{
 echo "Chyba! Nesouhlasi vstupni data. (akce) ";
 exit;
}

if( $akce == 0 or !isset($akce) )
{ 
    $dotaz_vlastnik_pom = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
      
    while($data_vlastnik_pom = pg_fetch_array($dotaz_vlastnik_pom) )
    { $firma_vlastnik=$data_vlastnik_pom["firma"]; $archiv_vlastnik=$data_vlastnik_pom["archiv"]; }
				
     if( $archiv_vlastnik == 1 ){ $id_cloveka_res = "vlastnici-archiv.php"; }
     elseif( $firma_vlastnik == 1 ){ $id_cloveka_res .= "vlastnici2.php"; }
     else{ $id_cloveka_res .= "vlastnici.php"; }
								       
     $id_cloveka_res .= "?find_id=".$id_cloveka;
										
    $stranka = $id_cloveka_res; 
}
elseif( $akce == 1 )
{ $stranka = "vlastnici2-add-obj.php?id_vlastnika=".$id_cloveka; }
elseif( $akce == 2 )
{ $stranka = "vlastnici2-add-fakt.php?id_vlastnika=".$id_cloveka; }
elseif( $akce == 3 )
{ 
    $rs_vl = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
      
    while($data_vl = pg_fetch_array($rs_vl) )
    { $fakturacni_id = $data_vl["fakturacni"]; }

    $stranka = "vlastnici2-erase-f.php?id=".$fakturacni_id; 
}
elseif( $akce == 4 )
{ 
    $rs_vl = pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka' ");
      
    while($data_vl = pg_fetch_array($rs_vl) )
    { $fakturacni_id = $data_vl["fakturacni"]; }

   $stranka = "vlastnici2-change-fakt.php?id=".$fakturacni_id; 
}
elseif( $akce == 5 )
{ $stranka = "opravy-index.php?typ=1&id_vlastnika=".$id_cloveka; }
elseif( $akce == 6 )
{ $stranka = "opravy-vlastnik.php?typ=2&id_vlastnika=".$id_cloveka."&ok=OK"; }
elseif( $akce == 7 ) //tisk smlouvy
{ 

 $url = "/adminator3/print/smlouva-2012-05.php";
 echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >\n\n";

 $rs_vl1 = pg_query("SELECT fakturacni_skupina_id FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."' ");
 
 while($data = pg_fetch_array($rs_vl1) )
 { $fakturacni_skupina_id = $data["fakturacni_skupina_id"]; }
  
 $rs_fs = $conn_mysql->query("SELECT typ_sluzby FROM fakturacni_skupiny WHERE id = '".intval($fakturacni_skupina_id)."' ");
 while($data = $rs_fs->fetch_array() )
 { $fakturacni_skupina_typ = $data["typ_sluzby"]; }
 
 echo "<input type=\"hidden\" name=\"id_cloveka\" value=\"".intval($id_cloveka)."\" >\n"; 
 
 $sql = "SELECT t1.id_cloveka, t1.jmeno, t1.prijmeni, t1.ulice, t1.psc, t1.mesto, t1.mail, t1.telefon, t1.k_platbe, t1.vs,
                t1.fakturacni, t1.fakturacni_skupina_id, t1.billing_freq, t1.typ_smlouvy, t1.trvani_do, 
                t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic
                             
                 FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id )
                 WHERE id_cloveka = '".intval($id_cloveka)."'";
                 
 $rs_vl = pg_query($sql);
       
 while($data_vl = pg_fetch_array($rs_vl))
 { 

  $vs = intval($data_vl["vs"]); 
  $ec = ($vs == 0) ? '' : $vs;

  print "<input type=\"hidden\" name=\"ec\" value=\"".$ec."\" >\n"; 

  print "<input type=\"hidden\" name=\"vs\" value=\"".$vs."\" >\n"; 

  $jmeno = $data_vl["jmeno"]." ".$data_vl["prijmeni"];
  print "<input type=\"hidden\" name=\"jmeno\" value=\"".$jmeno."\" >\n"; 
  print "<input type=\"hidden\" name=\"adresa\" value=\"".$data_vl["ulice"]."\" >\n"; 
  print "<input type=\"hidden\" name=\"telefon\" value=\"".$data_vl["telefon"]."\" >\n"; 
  print "<input type=\"hidden\" name=\"email\" value=\"".$data_vl["mail"]."\" >\n"; 

  print "<input type=\"hidden\" name=\"mesto\" value=\"".$data_vl["psc"]." ".$data_vl["mesto"]."\" >\n\n"; 

/*
  //cetnost fakturaci
  if($data_vl["billing_freq"] == 1){
     print "<input type=\"hidden\" name=\"platba\" value=\"2\" >\n\n"; 
  }
 
  //smlouva - typ, atd
  if( $data_vl["typ_smlouvy"] == 2){
     print "<input type=\"hidden\" name=\"min_plneni\" value=\"2\" >\n";      
     
     //list($a, $b, $c) = explode("-", $data_vl["trvani_do"]);
     //print "<input type=\"hidden\" name=\"min_plneni_doba\" value=\"".$c.".".$b.".".$a."\" >\n\n";      
    
  }
*/

  //firemní údaje    
  if($data_vl["fakturacni"] > 0)
  {
  
    print "<input type=\"hidden\" name=\"nazev_spol\" value=\"".$data_vl["ftitle"]."\" >\n\n"; 

    print "<input type=\"hidden\" name=\"f_adresa\" value=\"".$data_vl["fulice"]."\" >\n\n"; 

    print "<input type=\"hidden\" name=\"f_mesto\" value=\"".$data_vl["fmesto"].", ".$data_vl["fpsc"]."\" >\n\n"; 

    print "<input type=\"hidden\" name=\"ico\" value=\"".$data_vl["ico"]."\" >\n"; 

    print "<input type=\"hidden\" name=\"dic\" value=\"".$data_vl["dic"]."\" >\n"; 

  }
  
  //FS
  $rs_fs = $conn_mysql->query("SELECT typ_sluzby, sluzba_int, sluzba_int_id_tarifu, nazev, 
				sluzba_iptv, sluzba_iptv_id_tarifu, sluzba_voip 
			    FROM fakturacni_skupiny 
			    WHERE id = '".intval($data_vl["fakturacni_skupina_id"])."'");

  if( $rs_fs->num_rows == 1 )
  {
    //sluzba INTERNET

    // TODO: check if its right replacement for mysql_seek
    $rs_fs->data_seek(0);
    $rs_fs_r = $rs_fs->fetch_row();

    if($rs_fs_r[1] == 1){  //sluzba internet - ANO                
        
        //zjisteni poctu objektu
        $rs_obj = pg_query("SELECT * FROM objekty WHERE id_cloveka = '".intval($id_cloveka)."' ");
        $rs_obj_num = pg_num_rows($rs_obj);
        
        if($rs_obj_num == 1){
    	    print "<input type=\"hidden\" name=\"internet_sluzba\" value=\"1\" >\n"; 
        }
        elseif($rs_obj_num == 2){
    	    print "<input type=\"hidden\" name=\"internet_sluzba\" value=\"2\" >\n"; 
        }
    }
    
    //sluzba IPTV
    if($rs_fs_r[4] == 1){

        //sluzba IPTV - ANO
        print "<input type=\"hidden\" name=\"iptv_sluzba\" value=\"1\" >\n"; 
	//tarif
//	$iptv_sluzba_id_tarifu = intval(mysql_result($rs_fs, 0, 5));
//	print "<input type=\"hidden\" name=\"iptv_sluzba_id_tarifu\" value=\"".$iptv_sluzba_id_tarifu."\" >\n";
    
    }
    
    
    //VOIP
    if($rs_fs_r[6] == 1){
        print "<input type=\"hidden\" name=\"voip_sluzba\" value=\"1\" >\n"; 
    }
    
    
  } //konec if( mysql_num_rows rs_fs == 1 )
  
  //print "<input type=\"hidden\" name=\"tarif\" value=\"".$tarif."\" >\n"; 
							     
 }
  
 echo "</form>\n";
  
 echo "<script language=\"JavaScript\" >document.frm.submit(); </script>";
 echo "Pokud nedojde k automatickému přesměrování, pokračujte <a href=\"#\" onclick=\"document.frm.submit()\" >zde</a>";
 
 echo "</body>\n</html>\n";
 exit;
 
}
elseif( $akce == 8 ) //vlozeni vypovedi
{ 
 $url = "vypovedi-vlozeni.php";
 echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >";
 
 print "<input type=\"hidden\" name=\"firma\" value=\"2\" >"; 
 print "<input type=\"hidden\" name=\"klient\" value=\"".$id_cloveka."\" >"; 
  
 echo "
 </form> 
 <script language=\"JavaScript\" > document.frm.submit(); </script>";
 
 echo "</body></html>";
 exit;
 
}
elseif( $akce == 9 ) //vlozIT hot. platbu
{ 
 $url = "platby-akce2.php";
 echo "<form action=\"".$url."\" method=\"post\" name=\"frm\" >";
 
 print "<input type=\"hidden\" name=\"firma\" value=\"2\" >"; 
 print "<input type=\"hidden\" name=\"klient\" value=\"".$id_cloveka."\" >"; 
  
 echo "</form>";
  
 echo "<script language=\"JavaScript\" > document.frm.submit(); </script>";
 
 echo "</body></html>";
 exit;
 
}
elseif( $akce == 10 ) //vypis plateb
{ $stranka = "platby-vypis.php?id_vlastnika=".$id_cloveka."&ok=OK"; }
elseif( $akce == 11 ) //vypis neuhr. faktur
{ $stranka = "faktury/fn-index.php?id_cloveka=".$id_cloveka."&filtr_stav_emailu=99"; }
elseif( $akce == 12 ) //online xml faktury
{ $stranka = "platby-vypis-xml.php?id_vlastnika=".$id_cloveka; }
elseif( $akce == 13 ) //historie
{ $stranka = "archiv-zmen.php?id_cloveka=".$id_cloveka; }
elseif( $akce == 14 ) // online faktury - voip
{ $stranka = "platby-vypis-xml-voip.php?id_vlastnika=".$id_cloveka; }
elseif( $akce == 15 ) // priradit objekt stb
{ $stranka = "vlastnici2-add-obj-stb.php?id_vlastnika=".$id_cloveka; }

elseif( $akce == 16 ) // vypis faktur - pohoda SQL
{ $stranka = "pohoda_sql/phd_list_fa.php?id_vlastnika=".$id_cloveka; }

else
{ $home = "home.php"; }

//header("Location: https://".$_SERVER["SERVER_NAME"]."/adminator2/".$stranka);

// echo "<html>
// 	<head>
// 	    <title>Vlastníci rozcestník</title>";

// include("include/charset.php");

// echo "</head>
//     <body>";

if (isset($_SERVER['HTTPS']))
{
 $prot = "https://";
}
else
{
  $prot = "http://";
}

echo "<div><a href=\"" . $prot . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]. '/' . $stranka . "\">" . $_SERVER["SERVER_NAME"] . '/' .$stranka . "</a></div>"; 

echo "</body></html>";
 