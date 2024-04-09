<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
$auth->page_level_id = 87;
$auth->check_all();

$smarty->assign("page_title","Adminator3 :: Ostatní :: board");

$smarty->assign("nick_a_level",$nick." (".$level.")");
$smarty->assign("login_ip",$_SERVER['REMOTE_ADDR']);

//kategorie
$uri=$_SERVER["REQUEST_URI"];
$uri_replace = str_replace ("adminator3", "", $uri);

list($kategorie, $kat_2radka, $mapa) = zobraz_kategorie($uri,$uri_replace);

$smarty->assign("kategorie",$kategorie);
$smarty->assign("kat_2radka",$kat_2radka);

$smarty->assign("show_se_cat_values", array("0","1"));
$smarty->assign("show_se_cat_output", array("Nezobr. odkazy","Zobrazit odkazy"));

$show_se_cat = $_POST["show_se_cat"];

if( $show_se_cat == 0 )
{ $smarty->assign("show_se_cat_selected", "0"); }
else
{ $smarty->assign("show_se_cat_selected", "1"); }

$smarty->assign("show_se_cat",$show_se_cat);

$prihl_uziv = vypis_prihlasene_uziv($nick);

if( $prihl_uziv[100] == true )
{
  $smarty->assign("pocet_prihl_uziv",0);
}
else
{
  $smarty->assign("pocet_prihl_uziv",$prihl_uziv[0]);

  $smarty->assign("prvni_jmeno",$prihl_uziv[1]);
  $smarty->assign("prvni_level",$prihl_uziv[2]);
}

//button na vypis vsech prihl. uziv.
$smarty->assign("windowtext2",$prihl_uziv[3]);

// velikost okna
$smarty->assign("windowdelka2","170");
$smarty->assign("windowpadding2","40");	 
// pozice okna
$smarty->assign("windowtop2","150");
$smarty->assign("windowleft2","350");

$smarty->assign("subcat_select",0);

//zacatek vlastniho obsahu

$nastenka = new board($conn_mysql);

 $smarty->assign("datum",date("j. m. Y")); 
 $smarty->assign("sid",$sid); 

 $nastenka->what = $_GET["what"];
 $nastenka->action = $_GET["action"];
 $nastenka->page = $_GET["page"];

 $nastenka->send = $_GET["send"];
 $nastenka->sent = $_POST["sent"];

 $nastenka->author = $_POST["author"];
 $nastenka->email = $_POST["email"];

 $nastenka->to_date = $_POST["to_date"];
 $nastenka->from_date = $_POST["from_date"];

 $nastenka->subject = $_POST["subject"];
 $nastenka->body = $_POST["body"];

 $nastenka->prepare_vars($nick);

if($nastenka->action == "view"):
 
  $smarty->assign("mod",1); 
 
  if($nastenka->what=="new")
  { $smarty->assign("mod_hlaska", "->> Aktuální zprávy"); }
  else
  { $smarty->assign("mod_hlaska","->> Staré zprávy"); }

  $nastenka->view_number = 10; //zprávy budou zobrazeny po ...

  $zpravy = $nastenka->show_messages();

  $smarty->assign("zpravy",$zpravy);

  $page = $nastenka->show_pages(); 
  $smarty->assign("strany",$page);

else:

 $smarty->assign("mod",2); 
 
 $nastenka->write = false; //prvne předpokládáme zobr. formuláře

 if( isset($nastenka->sent) )
 { $nastenka->check_vars(); }
 
 if($nastenka->write)
 { //ulozeni dat

    $smarty->assign("mod",3); //vysledny formular ulozeni
    
    $nastenka->convert_vars();
    $add = $nastenka->insert_into_db();
    
    $smarty->assign("rs",$add); 
    $smarty->assign("body",$nastenka->error); 
 
    if($add){ 
      header("Location: others-board.php"); //přesuneme se na úvodní stránku
    }
 }
 else
 { //zobrazujeme formulář

    $smarty->assign("enable_calendar",1); 

    $smarty->assign("mod",2); //zobrazujeme formular pro zadavani dat
    $smarty->assign("mod_hlaska", "->> Přidat zprávu - povinné údaje zvýrazněny tučným písmem");

    $smarty->assign("nick",$nick); 

    $smarty->assign("email",$nastenka->email); 
    $smarty->assign("subject",$nastenka->subject); 

    $smarty->assign("from_date",$nastenka->from_date);
    $smarty->assign("to_date",$nastenka->to_date);
    
    $smarty->assign("body",$nastenka->body); 

    $smarty->assign("error",$nastenka->error); 
 }

endif;

$smarty->display('others/board.tpl');
