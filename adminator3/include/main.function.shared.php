<?php

function init_helper_base_html($app_name = "adminator"){
  $base_html = "<html>
  <head>
      <title>" . $app_name ." není dostupný</title>
      <meta http-equiv=\"Content-Language\" content=\"cs\" >
      <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">

      <meta http-equiv=\"Cache-Control\" content=\"must-revalidate, no-cache, post-check=0, pre-check=0\" >
      <meta http-equiv=\"Pragma\" content=\"public\" >

      <meta http-equiv=\"Cache-Control\" content=\"no-cache\" >
      <meta http-equiv=\"Pragma\" content=\"no-cache\" >
      <meta http-equiv=\"Expires\" content=\"-1\" >
  </head>
  <body>
  <img src=\"img2/logo.png\">";

  return $base_html;
}
function init_mysql($app_name = "adminator") {

  $hlaska_connect = init_helper_base_html($app_name)."\n<div style=\"color: black; padding-left: 20px;  \">\n";
  $hlaska_connect .= "<div style=\"padding-top: 50px; font-size: 18px; \">\n";
  $hlaska_connect .= "Omlouváme se, " . $app_name . " v tuto chvíli není dostupný! </div>\n";
  $hlaska_connect .= "<div style=\"padding-top: 10px; font-size: 12px; \" >\nDetailní informace: Chyba! Nelze se pripojit k Mysql databázi. </div>\n";

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  $MYSQL_SERVER = getenv("MYSQL_SERVER") ? getenv("MYSQL_SERVER") : "localhost";
  $MYSQL_USER = getenv("MYSQL_USER") ? getenv("MYSQL_USER") : "root";
  $MYSQL_PASSWD = getenv("MYSQL_PASSWD") ? getenv("MYSQL_PASSWD") : "password";

  global $conn_mysql;

  try {
      $conn_mysql = new mysqli(
          $MYSQL_SERVER,
          $MYSQL_USER,
          $MYSQL_PASSWD,
          "adminator2");
  } catch (Exception $e) {
      echo $hlaska_connect;
      echo 'Caught exception: Connect to mysql server failed! Message: ',  $e->getMessage(), "\n";
      echo "<div>Mysql server hostname: " . $MYSQL_SERVER . "</div>\n";
      if ($conn_mysql->connect_error) {
          echo "connection error: " . $conn_mysql->connect_error . "\n";
      }
      echo  "</div></div></body></html>\n";
      die();
  }

  try {
      $conn_mysql->query("SET NAMES 'utf8';");
  } catch (Exception $e) {
      die ($hlaska_connect . 'Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
  }

  try {
      $conn_mysql->query("SET CHARACTER SET 'utf8mb3';");
  } catch (Exception $e) {
      die ($hlaska_connect . 'Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
  }

}

function init_postgres($app_name = "adminator") {
  global $db_ok2;

  $hlaska_connect = init_helper_base_html($app_name)."<div style=\"color: black; padding-left: 20px;  \">";
  $hlaska_connect .= "<div style=\"padding-top: 50px; font-size: 18px; \">";
  $hlaska_connect .= "Omlouváme se, Adminátor2 v tuto chvíli není dostupný! </div>";
  $hlaska_connect .= "<div style=\"padding-top: 10px; font-size: 12px; \" >Detailní informace: Chyba! Nelze se pripojit k Postgre databázi. </div>";
  
  $POSTGRES_SERVER = getenv("POSTGRES_SERVER") ? getenv("POSTGRES_SERVER") : "localhost";
  $POSTGRES_USER = getenv("POSTGRES_USER") ? getenv("POSTGRES_USER") : "root";
  $POSTGRES_PASSWD = getenv("POSTGRES_PASSWD") ? getenv("POSTGRES_PASSWD") : "password";
  $POSTGRES_DB = getenv("POSTGRES_DB") ? getenv("POSTGRES_DB") : "password";
  $POSTGRES_PORT = "5432";
  $POSTGRES_CONNECT_TIMEOUT="5";
  
  $POSTGRES_CN = "host=" . $POSTGRES_SERVER . " ";
  $POSTGRES_CN .= "port=" . $POSTGRES_PORT . " ";
  $POSTGRES_CN .= "user=" . $POSTGRES_USER . " ";
  $POSTGRES_CN .= "password=" . $POSTGRES_PASSWD . " ";
  $POSTGRES_CN .= "dbname=" . $POSTGRES_DB . " ";
  $POSTGRES_CN .= "connect_timeout=" . $POSTGRES_CONNECT_TIMEOUT . " ";
  
  try {
      $db_ok2=pg_connect($POSTGRES_CN);
  } catch (Exception $e) {
      die ($hlaska_connect . 'Caught exception: ' .  $e->getMessage() . "\n" . "</div></div></body></html>\n");
  }
  
  if ( !($db_ok2) ){ 
      die ($hlaska_connect.pg_last_error($db_ok2)."</div></div></body></html>");
  }
  
}

function start_ses()
{
  global $sid, $level, $nick, $date, $ad, $logger;
  if(is_object($logger))
  {
    $logger->addInfo("start_ses called");
  }

  session_start(); 

  $SN = "autorizace"; 
  session_name("$SN"); 

  $sid = $_SESSION["db_login_md5"];
  $level = $_SESSION["db_level"];
  $nick = $_SESSION["db_nick"];

  $date = date("U"); 
  $ad = date("U") - 1200; 

  if(is_object($logger))
  {
    $logger->addInfo("start_ses: result: "
      . "[nick => " . $nick
      . ", level => " . $level
      . ", sid => " . $sid
      . "]");
  }

  return array($sid, $level, $nick);
}

function check_login($app_name = "adminator3") {
  global $sid, $ad, $level, $date, $conn_mysql, $cesta;

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
 
  if( $MSQ_R <> 1 and $app_name == "adminator3" ) {
    $ret = array();

    $ret[] = "false";
    $ret[] = "Neautorizovany pristup / Timeout Spojeni. (sid: ".$sid.", lvl: ".$level.", rows: ".$MSQ_R.",rows2: $MSQ_S_RADKU )";
   
    return $ret;  
  }

  if($MSQ->num_rows <> 1 and $app_name == "adminator2")
  {
 
     $stranka=$cesta.'nologinpage.php';
     header("Location: ".$stranka);
 
     echo "Neautorizovaný přístup / Timeout Spojení   ".htmlspecialchars($sid)."  ".htmlspecialchars($level)."";
     exit;
 
  }

  $MSQ = $conn_mysql->query("UPDATE autorizace ".
    "SET date = ".$conn_mysql->real_escape_string($date)." WHERE id = '".$conn_mysql->real_escape_string($sid)."' "); 

  // sem asi odstranovani ostatnich useru co jim prosel limit
  $MSQ_D = $conn_mysql->query("DELETE FROM autorizace ".
    " WHERE ( date <= ".$conn_mysql->real_escape_string($ad).") AND (id != '".$conn_mysql->real_escape_string($sid)."') ");

  return true;
}

function last_page(){
    $uri=$_SERVER["REQUEST_URI"];
    
    if (preg_match("/\/adminator3\//i", $uri)) {
      list($x,$y) = explode("adminator3/",$uri);
    } 
    elseif (preg_match("/\/adminator2\//i", $uri)) {
      list($x,$y) = explode("adminator2/",$uri);
    }
    else {
      $y = $_SERVER['REQUEST_URI'];
      // echo "<div>DEBUG: last page: " . $y . "," . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_URL'] . ",R.U: " . $_SERVER['REQUEST_URI'] . ",  </div>";
    }
    return $y;
}

function fix_link_to_another_adminator($link){

    $uri=$_SERVER["REQUEST_URI"];
    
    if (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
      $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }

    if (preg_match("/\/adminator3\//i", $uri)) {
      return "adminator2/" . $link;
    }
    elseif (preg_match("/\/adminator2\//i", $uri)){
      return "adminator3/" . $link;
    }
    elseif (preg_match("/adminator2/i", $_SERVER['HTTP_HOST'])){
      $host = str_replace("adminator2", "adminator3", $_SERVER['HTTP_HOST']);
      // die("debug: p: " . $protocol . ", host: " . $host . " link: " . $link);
      return $protocol . $host . $link;
    }
    elseif (preg_match("/adminator3/i", $_SERVER['HTTP_HOST'])){
      $host = str_replace("adminator3", "adminator2", $_SERVER['HTTP_HOST']);
      // die("debug: p: " . $protocol . ", host: " . $host . " link: " . $link);
      return $protocol . $host . $link;
    }
}

function list_logged_users_history($conn_mysql, $smarty, $action = "assign") {
  $r = array();

  $rs=$conn_mysql->query(
    "SELECT nick, date, ip FROM login_log ORDER BY date DESC LIMIT 5"
  );

  while ($data=$rs->fetch_array()){
     $datum = strftime("%d.%m.%Y %H:%M:%S", $data["date"] );
     $logged_users[] = array( "nick" => $data["nick"], "datum" => $datum, "ip" => $data["ip"]);    
  }
 
  if($action == "assign"){
    $smarty->assign("logged_users",$logged_users);
    $r[0] = TRUE;
  }
  elseif($action == "fetch"){
    $smarty->assign("logged_users",$logged_users);
    $render = $smarty->fetch("inc.home.list-logged-users.tpl");
    $r[0] = TRUE;
    $r[1] = $render;
  }
  else{
    $r[0] = FALSE;
    $r[1] = "unknown action";
  }

  return $r;
}

#
#  presun z export_ucetni.inc.function.php
#

#	pomocne funkce pro vytvoreni sesitu ..


function createsheet($typ,$cislo_sheetu,$nazev_sheetu,$pole_id_klientu)
{

 //predavane promenne 
 // typ - 1 pro DU, 2 pro FU
 // cislo_sheetu - cislo sesitu
 // nazev_sheetu - nazev sesitu
 // id_klientu - pole s idckama klientu ...
 
 global $workbook;
 global $id_klientu;

 global $header;
 
 global $bordercenter2;
 global $borderleftcolor;
 global $center_bold2;
 global $border5;
 global $du_fs_wifi_color;
    
 global $bordercenter;
 global $borderleftpolozka;
 global $center_bold;
 global $du_fs_wifi;

 global $center_bold_vyber4;
 global $borderleft4;
 global $border4;
 global $leftpozn2;
 global $du_tel;
 global $leftpozn;
	
 global $left;
 global $du_pozs_fa;
 global $center;
 global $text_color1;

 global $borderleft;
 global $fu_center;
 global $fu_2_line;      
 global $fu_pozs_fa;
 
 global $border5_fs;
 global $fu_center_fs;
 
 global $pocet_klientu;
 global $pocet_klientu2;
 global $pocet_klientu3;
 
 $worksheet = 'worksheet'.$cislo_sheetu;
 $$worksheet =& $workbook->addworksheet( iconv("UTF-8","CP1250",$nazev_sheetu) );
		      
 $$worksheet->freeze_panes(2, 0); // zmrazeni prvnich 2 radek 
 
 if($typ == 1)
 {
 //tvorime dle sablony DU

    // nastavení sirek sloupcu
    $$worksheet->set_column('A:A', 7);      //id

    $$worksheet->set_column('B:B', 28);     // jmeno a prijmeni
    $$worksheet->set_column('C:C', 23);     // adresa
    $$worksheet->set_column('D:D', 22);     // mesto a psc
    $$worksheet->set_column('E:E', 14);     // vs

    $$worksheet->set_column('F:F', 27);     // polozky
    $$worksheet->set_column('G:G', 8);      //policko sc
    $$worksheet->set_column('H:H', 8);      // policko mp
    $$worksheet->set_column('I:I', 8);      // policko ostatni

    $$worksheet->set_column('J:J', 8);      // nefakturacni
    $$worksheet->set_column('K:K', 8);      // optika

    //toto nevim
    $$worksheet->set_row(0, 20);
    $$worksheet->set_selection('C3');

    //prvni radek
    $$worksheet->write(0, 0, 'ui', $header);
    $$worksheet->write(0, 1, iconv("UTF-8","CP1250",'jméno uživatele'), $header);
    $$worksheet->write(0, 2, 'adresa', $header);
    $$worksheet->write(0, 3, iconv("UTF-8","CP1250",'město'), $header);
    $$worksheet->write(0, 4, 'vs', $header);
    $$worksheet->write(0, 5, iconv("UTF-8","CP1250",'položky'), $header);
    $$worksheet->write(0, 6, 'cena tarifu', $header);

    //druhej
    $$worksheet->write(1, 0, 'id', $header);
    $$worksheet->write(1, 1, 'email', $header);
    $$worksheet->write(1, 2, 'telefon', $header);
    $$worksheet->write(1, 3, iconv("UTF-8","CP1250",' poznámka '), $header);
    
    $$worksheet->write(1, 6, 'SC', $header);
    $$worksheet->write(1, 7, 'MP', $header);
    $$worksheet->write(1, 8, 'Ost', $header);
    $$worksheet->write(1, 9, 'Nef', $header);
    $$worksheet->write(1, 10, 'Optika', $header);

    $i=2;

  for ($p = 0; $p < count($pole_id_klientu); $p++)
  {

    $id_klienta = $pole_id_klientu[$p];
    
    $dotaz_du=pg_query("SELECT * FROM vlastnici WHERE ( id_cloveka = '$id_klienta' ) ");
    $dotaz_radku_du=pg_num_rows($dotaz_du);

    // vlastni data
    while( $data = pg_fetch_array($dotaz_du) )
    {

        //jednotlive promenne
        $id_cloveka = $data["id_cloveka"];

	$billing_freq = $data["billing_freq"];
	
        if( ( $billing_freq == 1) )
        { //ctvrtletni fakturace
	
            $border4=$bordercenter2;
            $borderleft4=$borderleftcolor;
            $center_bold_vyber4=$center_bold2;
            $border5 = $border5;

            $du_fs_wifi_s = $du_fs_wifi_color;

            if( $data["ucetni_index"] > 0)
	    { $ucetni_index = "99".sprintf("%05d", $data["ucetni_index"]); }
	    else
	    { $ucetni_index = ""; }
        
	}
        else
        {
            $border4=$bordercenter;
            $borderleft4=$borderleftpolozka;
            $center_bold_vyber4=$center_bold;
            $border5 = $border5;

            $du_fs_wifi_s = $du_fs_wifi;

	    $ucetni_index = $data["ucetni_index"];
        }

        $jmeno_conv = iconv("UTF-8","CP1250", $data["jmeno"]);
        $prijmeni_conv = iconv("UTF-8","CP1250", $data["prijmeni"]);

        $mesto_conv = iconv("UTF-8","CP1250", $data["mesto"] );
        $poznamka = iconv("UTF-8","CP1250", $data["poznamka"] );
    
        $$worksheet->write($i, 0, $ucetni_index, $center_bold_vyber4);
        $$worksheet->write($i, 1, $jmeno_conv." ".$prijmeni_conv , $borderleft4);
	
        $$worksheet->write($i, 2, iconv("UTF-8","CP1250", $data["ulice"]), $borderleft4);
        $$worksheet->write($i, 3, $mesto_conv." ".$data["psc"] , $border4);
        $$worksheet->write($i, 4, $data["vs"], $border4);

        //sem polozky
        $platit = $data["k_platbe"];
        $fakturacni_skupina_id = $data["fakturacni_skupina_id"];

        $polozka = "";
        $polozka2 = "";
        $cena_dph = "";
        $typ_sluzby = "";

	//zde fce asi ...
	$zjisteny_text_castka = zjisti_fa_text_a_castku($fakturacni_skupina_id,$platit);
	
        $$worksheet->write($i, 5, iconv("UTF-8","CP1250", $zjisteny_text_castka[1]), $borderleft4); // asi FA text

	//$$worksheet->write_url($i, 6,'internal:Sheet2!A1');
	  
        $$worksheet->write($i, 6, $zjisteny_text_castka[5], $du_fs_wifi_s);
        $$worksheet->write($i, 7, $zjisteny_text_castka[6], $du_fs_wifi_s);
        $$worksheet->write($i, 8, $zjisteny_text_castka[7], $du_fs_wifi_s);

        $$worksheet->write($i, 9, "", $borderleft4);

        $$worksheet->write($i, 10, $zjisteny_text_castka[10], $border5_fs);

        $i++;

        //druha radka

        $$worksheet->write($i, 0, $id_cloveka, $text_color1 );

        if( ( strlen($data["mail"]) > 6  ) )
        { $email = $data["mail"]; }
        else
        { $email = ""; }
	
        $$worksheet->write($i, 1, $email,$leftpozn2 );

        $$worksheet->write($i, 2, $data["telefon"], $du_tel );
        $$worksheet->write($i, 3, $poznamka, $leftpozn );
	
	//polozka2 ( asi jen pro wifi)
        $$worksheet->write($i, 5, $zjisteny_text_castka[2], $left);
	
	//castky pro wifi tarify ...
        $$worksheet->write($i, 6, $zjisteny_text_castka[11], $center);
        $$worksheet->write($i, 7, $zjisteny_text_castka[12], $center);
        $$worksheet->write($i, 8, $zjisteny_text_castka[13], $center);

        if ( $data["fakturacni_skupina_id"] == 6)
        { $$worksheet->write($i, 9, "ANO", $du_pozs_fa); }
        else{ $$worksheet->write($i, 9, "", $center); }

        $$worksheet->write($i, 10, $zjisteny_text_castka[14], $center);
        
        $i++;

    } //konec while
  
  } //konec for-u

  $i_puvodni = $i;
  
  $i = $i + 2;

  $$worksheet->write($i, 1, iconv("UTF-8","CP1250", "Součet klientů:"), $pocet_klientu); 
  
  $$worksheet->write($i, 4, "celkem: ", $pocet_klientu3); 
  $$worksheet->write($i, 5, '=ROWS(A3:A'.$i_puvodni.')/2', $pocet_klientu );
   
  $$worksheet->write_formula($i, 6, '=COUNT(G3:G'.$i_puvodni.')-COUNTIF(G3:G'.$i_puvodni.';">9000")-COUNTIF(A3:A'.$i_puvodni.';"FS-6")', $pocet_klientu2 );
  
  $i++;
  
  $$worksheet->write($i, 5, iconv("UTF-8","CP1250", "plus čtvrtletní "), $pocet_klientu3 );
  $$worksheet->write_formula($i, 6, '=COUNTIF(A3:A'.$i_puvodni.';">9000")', $pocet_klientu2 );
  
  $i++;
  
  $$worksheet->write($i, 5, iconv("UTF-8","CP1250", "plus pozastavené "), $pocet_klientu3 );
  $$worksheet->write_formula($i, 6, '=COUNTIF(G3:G'.$i_puvodni.';"FS-6")', $pocet_klientu2 );
 
 } //konec if typ == 1
 else
 {
 //tvorime FU
 
   // nastavení sirek sloupcu
   $$worksheet->set_column('A:A', 7);      //id cloveka

   $$worksheet->set_column('B:B', 28);     // název firmy
   $$worksheet->set_column('C:C', 23);     // sidlo firmy
   $$worksheet->set_column('D:D', 22);     // mesto a  PSC

   $$worksheet->set_column('E:E', 14);     // IČ
   $$worksheet->set_column('F:F', 14);     // DIC
   $$worksheet->set_column('G:G', 30);      // název tarifu

   $$worksheet->set_column('H:H', 8);      // SC (bez dph)
   $$worksheet->set_column('I:I', 8);      // MP (cena s DPH)
   $$worksheet->set_column('J:J', 8);      // Ost (kotaktni osoba)
   $$worksheet->set_column('K:K', 8);      // Nef (telefon)

   $$worksheet->set_column('L:L', 8);     // optika

   // nevim
   $$worksheet->set_row(0, 20);
   $$worksheet->set_selection('C3');

   //prvni radek
   $$worksheet->write(0, 0, "ui", $header);
   $$worksheet->write(0, 1, iconv("UTF-8","CP1250",' název firmy'), $header);
   $$worksheet->write(0, 2, iconv("UTF-8","CP1250",' sídlo firmy '), $header);
   $$worksheet->write(0, 3, iconv("UTF-8","CP1250", ' město a PSČ '), $header);
   $$worksheet->write(0, 4, 'IC', $header);
   $$worksheet->write(0, 5, 'DIC', $header);
   $$worksheet->write(0, 6, "", $header);
   $$worksheet->write(0, 7, "cena tarifu", $header);
   /*
   $$worksheet->write(0, 8, "", $header);
   $$worksheet->write(0, 9, "", $header);
   $$worksheet->write(0, 10, "", $header);
   $$worksheet->write(0, 11, "", $header);
   $$worksheet->write(0, 12, "", $header);
   $$worksheet->write(0, 13, "", $header);
*/

    //druhej
    $$worksheet->write(1, 0, 'id', $header);
    $$worksheet->write(1, 1, 'kontakt', $header);
    $$worksheet->write(1, 2, 'telefon', $header);
    $$worksheet->write(1, 3, 'email', $header);
    $$worksheet->write(1, 4, iconv("UTF-8","CP1250", ' poznámka '), $header);
    $$worksheet->write(1, 5, "", $header);

    $$worksheet->write(1, 6, 'nazev tarifu', $header);

    $$worksheet->write(1, 7, 'SC', $header);    $$worksheet->write(1, 8, 'MP', $header);
    $$worksheet->write(1, 9, 'Ost', $header);
    $$worksheet->write(1, 10, 'Nef', $header);
    $$worksheet->write(1, 11, 'Optika', $header);

    $i=2;

  for ($p = 0; $p < count($pole_id_klientu); $p++)
  {

   $id_klienta = $pole_id_klientu[$p];

   $dotaz_fu = pg_query("SELECT t1.id_cloveka,t1.jmeno, t1.prijmeni, t1.mail, t1.telefon, t1.k_platbe, t1.ucetni_index, t1.poznamka,
                             t1.fakturacni_skupina_id, t1.billing_freq, t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic
			     
		     FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id )
		     WHERE ( t1.id_cloveka = '$id_klienta' ) ORDER BY ucetni_index
                  ");

    // vlastni data
    while( $data2=pg_fetch_array($dotaz_fu) ):

        //jednotlive promenne
        $id_cloveka=$data2["id_cloveka"];

        $fmesto_conv=iconv("UTF-8","CP1250",$data2["fmesto"]);
        $fpsc_conv=iconv("UTF-8","CP1250",$data2["fpsc"]);

        $poznamka_conv=iconv("UTF-8","CP1250", $data2["poznamka"] );
        $kontakt=iconv("UTF-8","CP1250",$data2["prijmeni"]." ".$data2["jmeno"] );


	$billing_freq = $data2["billing_freq"];
	
        if( $billing_freq == 1 )
        { //ctvrtletni fakturace

            $border3=$bordercenter2;
            $borderleft2=$borderleftcolor;
            $center_bold_vyber=$center_bold2;

            if( $data2["ucetni_index"] > 0)
	    { $ucetni_index = "99".sprintf("%05d", $data2["ucetni_index"]); }
	    else
	    { $ucetni_index = ""; }
        }
        else
        {
            $border3=$bordercenter;
            $borderleft2=$borderleft;
            $center_bold_vyber=$center_bold;

            $ucetni_index = $data2["ucetni_index"];
        }

        $$worksheet->write($i, 0, $ucetni_index, $center_bold_vyber);
        $$worksheet->write($i, 1, iconv("UTF-8","CP1250",$data2["ftitle"]), $borderleft2);
        $$worksheet->write($i, 2, iconv("UTF-8","CP1250",$data2["fulice"]), $borderleft2);
        $$worksheet->write($i, 3, $fpsc_conv." ".$fmesto_conv, $borderleft2);

        $$worksheet->write($i, 4, iconv("UTF-8","CP1250",$data2["ico"]), $border3);
        $$worksheet->write($i, 5, iconv("UTF-8","CP1250",$data2["dic"]), $border3);
	
	//sem polozky
        $platit = $data2["k_platbe"];
        $fakturacni_skupina_id = $data2["fakturacni_skupina_id"];

	//zde fce asi ...
	$zjisteny_text_castka = zjisti_fa_text_a_castku($fakturacni_skupina_id,$platit);

        $$worksheet->write($i, 6, iconv("UTF-8","CP1250", $zjisteny_text_castka[1]), $borderleft2); //polozka, resp. FA text doufam

        $$worksheet->write($i, 7, $zjisteny_text_castka[5], $fu_center_fs);
        $$worksheet->write($i, 8, $zjisteny_text_castka[6], $fu_center_fs);
        $$worksheet->write($i, 9, $zjisteny_text_castka[7], $fu_center_fs);

        $$worksheet->write($i, 10, "", $fu_center); //nefakt.
        $$worksheet->write($i, 11, $zjisteny_text_castka[10], $fu_center_fs); //optika FS

        $i++;

	//druha radka
        $$worksheet->write($i, 0, $id_cloveka, $text_color1);        
	$$worksheet->write($i, 1, $kontakt, $fu_2_line);
        $$worksheet->write($i, 2, $data2["telefon"], $fu_2_line);
        $$worksheet->write($i, 3, $data2["mail"], $fu_2_line);
        $$worksheet->write($i, 4, iconv("UTF-8","CP1250",$data2["poznamka"]), $leftpozn); //poznamka	
        
	$$worksheet->write($i, 6, $zjisteny_text_castka[2], $left); //polozka2

        $$worksheet->write($i, 7, $zjisteny_text_castka[11], $center);
        $$worksheet->write($i, 8, $zjisteny_text_castka[12], $center);
        $$worksheet->write($i, 9, $zjisteny_text_castka[13], $center);
    
        if ( $data2["fakturacni_skupina_id"] == "8" )
        { $$worksheet->write($i, 10, "ANO", $fu_pozs_fa); }
        else{ $$worksheet->write($i, 10, "", $fu_pozs_fa); }

        $$worksheet->write($i, 11, $zjisteny_text_castka[14], $center); //optika castka
        
	$i++;

    endwhile;

   } //konec for FU
  
  $i_puvodni = $i;
  
  $i = $i + 2;

  $$worksheet->write($i, 1, iconv("UTF-8","CP1250", "Součet klientů:"), $pocet_klientu); 
  $$worksheet->write_formula($i, 5, '=ROWS(A3:A'.$i_puvodni.')/2', $pocet_klientu );
   
 } //konec else / if typ == neco

} //konec funkce createsheet

function zjisti_fa_text_a_castku($fakturacni_skupina_id,$platit)
{
    $vracena_data = array();
    
    //vypis fakturacniho textu .. pro wifi i optiku ...
    if( $fakturacni_skupina_id > 0 and $fakturacni_skupina_id != 6 and $fakturacni_skupina_id != 8 )
    {
      //je zadana fak. skupina (ale ne pozast. FA), cili vypis textu z DB

      $dotaz_fs = mysql_query("SELECT * FROM fakturacni_skupiny WHERE id = '$fakturacni_skupina_id' ");

      if( ( mysql_num_rows($dotaz_fs) == 1) )
      {
         while( $data_fs = mysql_fetch_array($dotaz_fs) )
         {
            $polozka = $data_fs["fakturacni_text"];
            $typ_sluzby = $data_fs["typ_sluzby"];
         }
      }
      else
      { $polozka = "chyba! nelze najit Fakt.Skup. ";}
    }
    else
    {
      //neni fakt. skupina, takze mod hadaní ... (jen wifi ) ..
      
      if( $platit == 250 or $platit == "248" )
      { $polozka=" Internet - tarif SMALL CITY "; $polozka2=""; $cena_dph="297,5"; }
      elseif( $platit == 420 or $platit == "416.5" )
      { $polozka=" Internet - tarif METROPOLITNI "; $polozka2=""; $cena_dph="500"; }
      elseif( $platit== 350 )
      { $polozka="Internet - tarif SMALL CITY";  $polozka2="Verejna IP adresa"; $cena_dph="416,50"; }
      elseif( $platit == 500 )
      { $polozka=" Internet - tarif SMALL CITY "; $polozka2=" 2x "; $cena_dph="595"; }
      elseif( $platit == 520 or $platit == 516 )
      { $polozka=" Internet - tarif METROPOLITNI "; $polozka2=" Verejna IP adresa "; $cena_dph="619"; }
      elseif( $platit > 0 )
      { $polozka=" nelze zjistit "; $polozka2="";  $cena_dph=$platit * 1.19; }
      else
      { $polozka=" zdarma "; $polozka2=""; $cena_dph="0"; }

    } //konec else if fakturacni_skupina_id > 0

    $vracena_data[5] = "";
    $vracena_data[6] = "";
    $vracena_data[7] = "";
	
    // vypis FS pro wifi ...
    if( $fakturacni_skupina_id > 0 and $typ_sluzby == 0 )
    {
      if( $platit == "250" OR $platit == "248" )
      { $vracena_data[5] = "FS-".$fakturacni_skupina_id; }
      elseif( $platit == "420" OR $platit == "416.5" )
      { $vracena_data[6] = "FS-".$fakturacni_skupina_id; }
      else
      { $vracena_data[7] = "FS-".$fakturacni_skupina_id; }
    }
    
    //vyplneni FA SK. pro optiku ..
    if( $fakturacni_skupina_id > 0 and $fakturacni_skupina_id != 6 and $fakturacni_skupina_id != 8 and $typ_sluzby == 1 )
    { $vracena_data[10] = "FS-".$fakturacni_skupina_id; }
    else
    { $vracena_data[10] = ""; }

    //vyplneni castky do prisl. sloupce, pro wifi
    if( ( $fakturacni_skupina_id > 0 or $fakturacni_skupina_id == 6 or $fakturacni_skupina_id == 8 ) )
    {
     //jestli je zvolena FS typu WIFI ...
     if( $typ_sluzby == 0)
     {
       if ( $platit == "250" OR $platit == "248" )
       { $vracena_data[11] = $platit; }
       elseif( $platit == "420" OR $platit == "416.5" )
       { $vracena_data[12] = $platit; }
       else
       { $vracena_data[13] = $platit; }
     
     }
     else{} //neni typu wifi, takze chvilecky prazdne ...

    }
    else
    {
     //bez fa-skupiny - hadame ala wifi veci ../ vzdy wifi ...

     if ( $platit == "250" OR $platit == "248" )
     { $vracena_data[11] = $platit; }
     elseif( $platit == "420" OR $platit == "416.5" )
     { $vracena_data[12] = $platit; }
     else
     { $vracena_data[13] = $platit; }
     
    }
    
    //vyplneni castky do prisl sloupce, pro optiku ...
    if( $fakturacni_skupina_id > 0 and $fakturacni_skupina_id != 6 and $fakturacni_skupina_id != 8 and $typ_sluzby == 1 )
    { $vracena_data[14] = $platit; }
    else
    { $vracena_data[14] = ""; }
    
    //zde co to bude vracet ...
    
    //1. polozka ( pro mod hadani, bez vybrane FS)
    //2. polozka2 ( dtto)
    //3. cena_dph ( dtto)
    //4. rezerva
    
    //5. FS v 6te bunce, pro wifi tarify 
    //6. FS v 7te bunce, dtto
    //7. FS v 8te bunce, dtro
    
    //10. FS v 10te bunce, pro optiku
    
    //11. castka v 6te bunce sekundarni radky, pro wifi 
    //12. dtto v 7me, wifi
    //13. dtto v 8me, wifi
    
    //14. castka v 10te bunce, pro optiku ..
    
    $vracena_data[1] = $polozka;
    $vracena_data[2] = $polozka2;
    $vracena_data[3] = $cena_dph;
    
    $vracena_data[4] = "";
    
    return $vracena_data;
    
} //konec funkce zjisti_fa_text_a_castku

#
#  konec presun z export_ucetni.inc.function.php
#

# gen.router.php

function gen_router_vypis_router($id)
{
    
    global $mac;
    
    $dotaz_router=mysql_query("SELECT * FROM router_list WHERE id = $id order by id");
    $dotaz_router_radku=mysql_num_rows($dotaz_router);

    if ($dotaz_router_radku <> 1 )
    {
    echo "Chybnej pocet radku";
    $mac="E";
    
    }
    else
    {
     while($data=mysql_fetch_array($dotaz_router))
     {
	$parent_router=$data["parent_router"];
	
	if ( $parent_router == 0)
	{ 
	//erik - nedelat nic
	
	}
	elseif ($parent_router == 1 )
	{
	// konec retezce, vypisem
	
	 if ( ( strlen($mac) <= 0) ){ $mac=$data["mac"]; }
	 // $mac="CCC";
	 //if ( ( strlen($rb_ip) <= 0) ) { $rb_ip=$data["ip_adresa"]; }
									  
	}
	else
	{
	    vypis_router($parent_router);
	}

    } // konec while
   } // konec else
   
} // konec funkce

# enf of gen.router.php

# hierarchy.php

function hierarchy_vypis_router($id,$uroven)
{

    global $uroven_max, $conn_mysql;
    
    $dotaz_router=$conn_mysql->query("SELECT * FROM router_list WHERE id = $id order by id");
    $dotaz_router_radku=$dotaz_router->num_rows;
          
    if ( $dotaz_router_radku > 0 )
    {
                
      while($data_router=$dotaz_router->fetch_array())
      {
                    
          echo "<tr>";
          
          for ( $j=0;$j<$uroven; $j++){ echo "<td><br></td>"; }
          
          echo "<td align=\"center\">|------> </td>";
          echo "<td>";
                
            echo " [".$data_router["id"]."] <b>".$data_router["nazev"]."</b>";
                          
            echo " <span style=\"color:grey; \">( ".$data_router["ip_adresa"]." ) </span>";
                            
            echo "</td>";
            
          echo "</tr>";

            //zde rekurze
            $parent_id=$data_router["id"];

            $dotaz_router_parent=$conn_mysql->query("SELECT * FROM router_list WHERE parent_router = $id order by id");
            $dotaz_router_parent_radku=$dotaz_router_parent->num_rows;
                
            if ( $dotaz_router_parent_radku > 0 )
            {
                
                $iterace = 1;
                
                while($data_router_parent=$dotaz_router_parent->fetch_array() )
                {
                
                    $uroven++;
                    
                    if ( ($uroven > $uroven_max) ){ $uroven_max = $uroven; }
                    
                    $id=$data_router_parent["id"];
                    
                    hierarchy_vypis_router($id,$uroven);
                    
                    $iterace++;
                    
                    if ( $iterace > 1){ $uroven--; }
                }
                // else
                // { $uroven--; }
            
            }
            
            //return echo $text;    
       }													  
        
    }
    else
    { 
      return false; 
    }

}

# enf of hierarchy.php