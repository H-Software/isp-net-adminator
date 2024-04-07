<?php

function last_page()
{
    $uri=$_SERVER["REQUEST_URI"];
    
    list($x,$y) = explode("adminator3/",$uri);
    
    return $y;
    
} //konec funkce last page

function start_ses()
{
  global $sid, $level, $nick, $date, $ad;
  
  session_start(); 

  $SN = "autorizace"; 
  session_name("$SN"); 

  $sid = $_SESSION["db_login_md5"];
  $level = $_SESSION["db_level"];
  $nick = $_SESSION["db_nick"];

  $date = date("U"); 
  $ad = date("U") - 1200; 

}

function check_login() {
  global $sid, $ad, $level, $date, $conn_mysql;

  try {
    $MSQ_S = $conn_mysql->query("SELECT id FROM autorizace WHERE id != '".$conn_mysql->real_escape_string($sid)."' ");
    $MSQ_S_RADKU = $MSQ_S->num_rows;
  } catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Login Failed (check login): Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
  }
 
  if( $MSQ_S_RADKU == 0 ){
    //jestli je prihlasen pouze jeden clovek tak se neresi cas
    $MSQ = $conn_mysql->query("SELECT id FROM autorizace WHERE (id = '".$conn_mysql->real_escape_string($sid)."') "); 
  }
  else {
    $MSQ = $conn_mysql->query("SELECT id FROM autorizace ".
          "WHERE (id = '".$conn_mysql->real_escape_string($sid)."') AND (date >= ".$conn_mysql->real_escape_string($ad).") "); 
  }

  $MSQ_R = $MSQ->num_rows;
 
  if( $MSQ_R <> 1 ) {
    $ret = array();

    $ret[] = "false";
    $ret[] = "Neautorizovany pristup / Timeout Spojeni. (sid: ".$sid.", lvl: ".$level.", rows: ".$MSQ_R.",rows2: $MSQ_S_RADKU )";
   
    return $ret;  
  }

  $MSQ = $conn_mysql->query("UPDATE autorizace ".
    "SET date = ".$conn_mysql->real_escape_string($date)." WHERE id = '".$conn_mysql->real_escape_string($sid)."' "); 

  // sem asi odstranovani ostatnich useru co jim prosel limit
  $MSQ_D = $conn_mysql->query("DELETE FROM autorizace ".
    " WHERE ( date <= ".$conn_mysql->real_escape_string($ad).") AND (id != '".$conn_mysql->real_escape_string($sid)."') ");

  return true;
}

function check_level ($user_level,$id) {
  // co mame
  // v promeny level mame level prihlaseneho uzivatele
  // databazi levelu pro jednotlivy stranky

  // co chceme
  // porovnat level uzivatele s prislusnym levelem
  // stranky podle jejiho id

  global $conn_mysql;

  try {
    $dotaz = $conn_mysql->query("SELECT level FROM leveling WHERE id = '".intval($id)."' ");
    $radku = $dotaz->num_rows;
  } catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Check level Failed: Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
  }

  if ($radku==0)
  { return false; }

  while ($data = $dotaz->fetch_array())
  { $level_stranky = $data["level"]; }

  if ( $user_level >= $level_stranky)
  { return true; }

}

function zobraz_kategorie($uri,$uri_replace)
{

  $kategorie = array();

  $kategorie[0] = array( "nazev" => "Zákazníci", "url" => "vlastnici-cat.php", "align" => "center", "width" => "18%" );

  if( ereg("^.+vlastnici.+",$uri) or ereg("^.+vlastnici-cat.php+",$uri) or ereg("^.+vypovedi",$uri) )
  { $kategorie[0]["barva"] = "silver"; }

  $kategorie[1] = array( "nazev" => "Služby", "url" => "/adminator2/objekty-subcat.php", "align" => "center", "width" => "18%" );

  if( ereg("^.+objekty.",$uri) or ereg("^.+objekty-subcat.php",$uri) )
  { $kategorie[1]["barva"] = "silver"; }

  $kategorie[2] = array( "nazev" => "Platby", "url" => "platby-cat.php", "align" => "center", "width" => "18%" );

  if( ereg("^.+platby.+$",$uri) )
  { $kategorie[2]["barva"] = "silver"; }

  $kategorie[3] = array( "nazev" => "Topologie", "url" => "/adminator2/topology-nod-list.php", "align" => "center", "width" => "" );

  if( ereg("^.+topology",$uri) )
  { $kategorie[3]["barva"] = "silver"; }

  $kategorie[4] = array( "nazev" => "Nastavení", "url" => "/adminator2/admin-subcat.php", "align" => "center", "width" => "" );

  if( ereg("^.+admin.+$",$uri_replace ) or ereg("^.+admin-subcat.php$",$uri) )
  {  $kategorie[4]["barva"] = "silver"; }

  $kategorie[5] = array( "nazev" => "Úvodní strana", "url" => "home.php", "align" => "center", "width" => "" );
 
  if( ereg("^.+home.php$",$uri) )
  { $kategorie[5]["barva"] = "silver"; }

  $kat_2radka = array();

  $kat_2radka[0] = array( "nazev" => "Partner program", "url" => "/adminator2/partner.php", "width" => "", "align" => "center" );

  if( (ereg("partner",$uri_replace) and !ereg("admin",$uri_replace)) )
  { $kat_2radka[0]["barva"] = "silver"; }

  $kat_2radka[1] = array( "nazev" => "Změny", "url" => "archiv-zmen-cat.php", "width" => "", "align" => "center" );

  if( ereg("^.+archiv-zmen.+$",$uri) )
  { $kat_2radka[1]["barva"] = "silver"; }

  $kat_2radka[2] = array( "nazev" => "Work", "url" => "work.php", "width" => "", "align" => "center" );

  if( ereg("^.+work.+$",$uri) )
  { $kat_2radka[2]["barva"] = "silver"; }

  $kat_2radka[3] = array( "nazev" => "Ostatní", "url" => "others-cat.php", "width" => "", "align" => "center" );

  if( ereg("^.+others.+$",$uri) or ereg("^.+syslog.+$",$uri) or ereg("^.+/mail.php$",$uri) or ereg("^.+opravy.+$",$uri) )
  { $kat_2radka[3]["barva"] = "silver"; }

  $kat_2radka[4] = array( "nazev" => "O programu", "url" => "about.php", "width" => "", "align" => "center" );

  if( ereg("^.+about.+$",$uri) )
  { $kat_2radka[4]["barva"] = "silver"; }
 
  $ret = array( $kategorie, $kat_2radka, $mapa);
    
  return $ret;

}

function vypis_prihlasene_uziv($nick)
{
  global $conn_mysql;
  $ret = array();

 $MSQ_USER2 = $conn_mysql->query("SELECT nick, level FROM autorizace");
 $MSQ_USER_COUNT = $MSQ_USER2->num_rows;

 $ret[0] = $MSQ_USER_COUNT;

 //prvne vypisem prihlaseneho
 $MSQ_USER_NICK = $conn_mysql->query("SELECT nick, level FROM autorizace WHERE nick LIKE '".$conn_mysql->real_escape_string($nick)."' ");

 if ($MSQ_USER_NICK->num_rows <> 1)
 {
  $ret[100] = true;
  $ret[101] = "Chyba! Vyber nicku nelze provest.";
 }
 else
 {
    while ($data_user_nick = $MSQ_USER_NICK->fetch_array() )
    {
      $ret[1] = $data_user_nick["nick"];
      $ret[2] = $data_user_nick["level"];
    }
 } // konec else

  // ted najilejeme prihlaseny lidi ( vsecky ) do pop-up okna
  if ( $MSQ_USER_COUNT < 1 )
  { $obsah_pop_okna .= "Nikdo nepřihlášen. (divny)"; }
  else
  {

   while ($data_user2 = $MSQ_USER2->fetch_array())
   {
     $obsah_pop_okna .= "jméno: ".$data_user2["nick"].", level: ".$data_user2["level"].", ";
   } //konec while

   $ret[3] = $obsah_pop_okna;

  } // konec if

  return $ret;
}

function show_stats_faktury_neuhr()
{
 //
 // vypis neuhrazenych faktur
 //
 // return hodnoty
 //
 // 0. neuhr. faktur celkem
 // 1. nf ignorovane
 // 2. nf nesparovane
 // 3. datum posl. importu
 
  global $conn_mysql;
  $ret = array();
 
// TODO: doresit
//  $dotaz_fn=mysql_query("SELECT * FROM faktury_neuhrazene ");
//  $dotaz_fn_radku=mysql_num_rows($dotaz_fn);
 
//  $ret[0] = $dotaz_fn_radku;
 
//  $dotaz_fn4=mysql_query("SELECT * FROM faktury_neuhrazene WHERE ( ignorovat = '1' ) order by id");
//  $dotaz_fn4_radku=mysql_num_rows($dotaz_fn4);
   
//  $ret[1] = $dotaz_fn4_radku;
 
//  $dotaz_fn2=mysql_query("SELECT * FROM faktury_neuhrazene WHERE par_id_vlastnika = '0' ");
//  $dotaz_fn2_radku=mysql_num_rows($dotaz_fn2);

//  $ret[2] = $dotaz_fn2_radku;
      
//  $dotaz_fn3=mysql_query("SELECT datum,DATE_FORMAT(datum, '%d.%m.%Y %H:%i:%s') as datum FROM fn_import_log order by id");
//  $dotaz_fn3_radku=mysql_num_rows($dotaz_fn3);
       
//  while( $data3=mysql_fetch_array($dotaz_fn3) )
//  { $datum_fn3=$data3["datum"]; }
	 
//  $ret[3] = $datum_fn3;

 return $ret;
}
