<?php

require "include/main.function.shared.php";
require "include/config.php";
require "include/main.function.php";

$smarty = new Smarty;
$smarty->compile_check = true;
//$smarty->debugging = true;

$auth = new auth_service($conn_mysql, $smarty, $logger);
$auth->page_level_id = 147;
$auth->check_all();

$smarty->assign("page_title","Adminator3 :: Změny pro účetní");

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
$action = $_GET["action"];

$smarty->assign("link_add","archiv-zmen-ucetni.php?action=add");

$zmena = new zmeny_ucetni($conn_mysql);

if( $action == "add")
{ //rezim pridani

    if( !( check_level($level,148) ) )
    { // neni level
      $smarty->assign("page_title","Adminator3 :: chybny level");
      $smarty->assign("body","<br>Neopravneny pristup /chyba pristupu. STOP <br>");
      $smarty->display('index-nolevel.tpl');
      exit;
    }

    if( ( $update_id > 0 ) ){ $update_status=1; }

    $zmena->send = $_POST["send"];
    $zmena->odeslano = $_POST["odeslano"];
    
    //nacitani promennych
    if( ( $update_status==1 and !( isset($zmena->send) ) ) )
    { //rezim upravy

    }
    else
    { //rezim pridani
      $zmena->typ = $_POST["typ"];
      $zmena->text = $_POST["text"];
    }
    
    //zde generovani a kontrola dat
    $zmena->check_inserted_vars();
    
    // jestli uz se odeslalo , checkne se jestli jsou vsechny udaje
    if( ( ($zmena->typ != "") and ($zmena->text != "") ) )
    {
      //zde check duplicitnich hodnot ( uprava i pridani )
    
      //checkem jestli se macklo na tlacitko "OK" :)
      if( ereg("OK",$zmena->odeslano) ){ /* zde nic */ }
      else 
      { 	
        $zmena->fail = "true"; 
	      $zmena->error .= "<div class=\"form-add-no-click-ok\"><h4>Data neuloženy, nebylo použito tlačítko \"OK\", pro uložení klepněte na tlačítko \"OK\" v dolní části obrazovky!!!</h4></div>"; 
      }
    
      if ( !( isset($zmena->fail) ) )
      { //ulozeni
        if( $update_status == 1 )
        { //rezim upravy
        
          //zde kontrola levelu pro update
        
        }
        else
        { //rezim pridani 
          $rs = $zmena->save_vars_to_db();
        
          if( $rs == true )
          { $db_result = "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze.</div></H3>\n"; }
          else
          { $db_result = "<br><H3><div style=\"color: red; \">Chyba! Data do databáze nelze uložit. </div></H3>\n"; }

          $smarty->assign("db_result",$db_result);
        }
      } //konec if ! isset fail
      else{} // konec else ( !(isset(fail) ), musi tu musi bejt, pac jinak nefunguje nadrazeny if-elseif
    
    }
    elseif( isset($zmena->send) )
    {
      $zmena->error = "<h4 style=\"color: red;\" >Chybí povinné údaje !!! (aktuálně jsou povinné: typ, text)</H4>";
    }
    
    if( (isset($zmena->error)) or (!isset($zmena->send)) )
    { //zobrazeni formu

        $smarty->assign("action",$_SERVER["PHP_SELF"]."?action=add");
          
        $smarty->assign("error",$zmena->error);
        $smarty->assign("info",$zmena->info);

        $pole_typy = $zmena->get_types();
        $smarty->assign("typ",$pole_typy);

        $smarty->assign("typ_select",$zmena->typ);
        $smarty->assign("text",$zmena->text);

        $template = "az-ucetni-add-form.tpl";	
    } 
    elseif( ( isset($zmena->writed) or isset($updated) ) )
    { //vypis vlozenych udaju
	    $template = "az-ucetni-add-list.tpl";
    }
    
} //konec if action == add
elseif($action == "accept")
{ //rezim akceptovani

}
elseif($action == "update" )
{ //rezim úpravy 


}
else
{
    $vypis_rs = $zmena->load_sql_result();
    $smarty->assign("zmeny",$vypis_rs);

    $smarty->assign("link_accept","xxx");

    $template = "az-ucetni.tpl";
}

$smarty->display($template);
