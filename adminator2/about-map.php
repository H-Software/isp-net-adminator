<?php
require("include/main.function.shared.php");
require("include/config.php"); 
require("include/check_login.php");
require("include/check_level.php");

if ( !( check_level($level,96) ) )
{
// neni level

$stranka='nolevelpage.php';
 header("Location: ".$stranka);
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
      
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require ("include/charset.php"); 

?>

<title>Adminator 2 - mapa webu</title>

</head>

<body>

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" ><? include("about-cat.php"); ?></td>
  </tr>
 
 <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu --> 
  
  
  <div style="padding-left: 10px; padding-top: 5px; padding-bottom: 10px; font-size: 18px; font-weight: bold; ">Mapa webu</div>
  
  <table border="0" width="" >
  
  <tr>
    <td width="150px" class="map-tab" ><a href="home.php" >Úvodní stránka</a></td>
    <td width="250px" class="map-tab" ><a href="vlastnici-cat.php">Zákazníci</a></td>
    <td width="250px" class="map-tab-right" ><a href="vlastnici.php">Vlastníci</a></td>
    
    <td width="250px" ><br></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    
    <td class="map-tab-right" ><a href="vlastnici2.php">Vlastníci2 ( s.r.o. )</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    
    <td class="map-tab-right" ><a href="vlastnici-archiv.php">Archiv vlastníků </a></td>
  </tr>
  
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    
    <td class="map-tab map-tab-right"><a href="vypovedi.php">Výpovědi smluv </a></td>
    <td class="map-tab-right" >
      <a href="vypovedi.php">Výpis výpovědí</a>
    </td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    
    <td class="map-tab-right" >
       <a href="vypovedi-vlozeni.php">Vložená výpovědi</a>
    </td>
  </tr>
  
  <tr>

    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    
    <td class="map-tab-right" >
     <a href="vypovedi-plaintisk.php">Tisk nevyplněné žádosti</a>
    </td>
  </tr>
  
  <tr>

    <td colspan="2"><br></td>
    <td colspan="1" class="map-tab-right" >
	<a href="vlastnici2-fakt-skupiny.php">Fakturační skupiny </a>
    </td>
    
    <td class="map-tab-right" >
     
    </td>
  </tr>
  
  <tr>
    <td><br></td>
    <td class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td><br></td>    
    <td class="map-tab map-tab-right" ><a href="objekty-subcat.php">Zákazníci</a></td>
    <td class="map-tab-right" >Internet :: <a href="objekty.php">objekty</a></td>
  
  <tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    
    <td class="map-tab-right" >Internet :: <a href="objekty-add.php">Přidání objektu </a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >Internet :: <a href="objekty-lite.php">Omezený výpis objektů </a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >IPTV :: <a href="objekty-stb.php">set-top-boxy</a></td>
    <td><a href="objekty-stb-add.php" >přidání set-top-boxu</a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    
    <td class="map-tab map-tab-right" ><a href="platby-subcat.php" >Platby</a></td>
    <td class="map-tab map-tab-right" ><a href="platby.php" >Vlastní platby</a></td>
    <td class="map-tab-right" ><a href="platby-soucet.php" >Platby - soucet</a></td>
 
  </tr>
    
  <tr>
    <td colspan="1" <br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td colspan="1" ><br></td>

    <td class="map-tab-right"><a href="platby-akce.php" >Vložení hotovostní platby</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    
    <td class="map-tab-right" ><a href="platby-hot-vypis.php" >Výpis hotovostních plateb</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-hot-stats.php" >Statistiky</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-hot-export.php" >Export hotovostních plateb</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-ucet-vypis.php" >Výpis plateb - roční</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-ucet-vypis-detail.php" >Výpis platby - detail</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-ucet-import.php" >Import plateb z účtu</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-ucet-vypis-polozek.php" >Výpis položek z účtu</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="platby-neplatici.php" >Neplatiči</a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab" ><a href="faktury.php" >Daňové doklady</a></td>

    <td class="map-tab-right" ><a href="faktury/faktury-add.php" >Vložení daňového dokladu</a></td>
  </tr>
 
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td colspan="1" class="map-tab-right"><a href="faktury/faktury-list.php">Výpis daň. dokladů</a></td>
    
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab" ><a href="faktury.php" >Neuhrazené faktury</a></td>
    <td class="map-tab-right" ><a href="faktury/fn-index.php?filtr_stav_emailu=99" >Výpis neuhr. faktur</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="faktury/fn-parovani.php" >Párování neuhr. faktur</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="faktury/fn-update.php" >Úprava neuhr. faktur</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="faktury/fn-aut-email.php" >Aut. odesílání emailů o N.FA.</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="faktury/fn-aut-sms.php" >Aut. odesílání SMS o N.FA.</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="faktury/fn-aut-sikana.php" >Aut. nastavení šikany u N.FA.</a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="faktury/fn-aut-check-splatnost.php" >Aut. zjišťování splatnosti faktur</a></td>
  </tr>
  
  <tr>
    <td colspan="1" height="30" ><br></td>
    <td class="map-tab map-tab-right" ><a href="work.php" >Work</a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td class="map-tab map-tab-right" ><a href="topology-nod-list.php" >Topologie </a></td>
    <td class="map-tab-right" ><a href="topology-router-list.php" >Výpis Routerů </a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  
    <td class="map-tab-right" ><a href="topology-router-add.php" >Přidání routeru </a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  
    <td class="map-tab-right" ><a href="topology-nod-list.php" >Výpis lokalit/nodů </a></td>
  </tr>
  
  <tr>
    <td colspan="1"><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  
    <td class="map-tab-right" ><a href="topology-nod-add.php" >Přidání lokality/nodu </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  
    <td class="map-tab-right" ><a href="topology-user-list.php" >Výpis objektů dle přiřazení / dle nodů </a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td> 
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td class="map-tab map-tab-right" ><a href="admin-subcat.php" >Nastavení </a></td>
    <td class="map-tab" ><a href="admin.php" >Vlastní nastavení systému</a></td>
    <td class="map-tab-right"><a href="admin.php" >Home </a></td>

  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 
     
    <td class="map-tab-right" ><a href="admin-change-password.php" >Změna hesla </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-print-smlouva.php" >Smlouva </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-user-add.php" >Přidání uživatele </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-user-list.php" >Výpis uživatelů </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-level-add.php" >Přidání levelu pro stránku </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-level-list.php" >Výpis levelů stránek </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-kontrola.php" >Kontrola databáze </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="admin-kontrola-k-platbe.php" >Kontrola údajů k platbám </a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td> 
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td> 
    
    <td class="map-tab" ><a href="automatika.php" >Nastavení automatiky</a></td>
    <td class="map-tab-right" ><a href="automatika.php" >Automatika </a></td>

  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="automatika-sikana-odpocet.php" >Šikana - odpočet </a></td>
  </tr>

  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="automatika-sikana-zakazani.php" >Šikana - zákazy </a></td>
  </tr>

  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

    <td class="map-tab-right" ><a href="automatika-fn-check-vlastnik.php" >Kontrola splatnosti faktury vs. vlastník</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td> 
</tr>

  <tr>
    <td colspan="1" ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td> 

    <td class="map-tab" ><a href="monitoring-control.php" >Nastavení monitoringu</a></td>
    <td class="map-tab-right" ><a href="monitoring-control.php" >Home </a></td>
  </tr>

  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-work.php" >Restarty </a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-grafy.php?typ=1" >Výpis grafů - ping</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-grafy.php?typ=2" >Výpis grafů - traffic</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-grafy-add.php?typ=1" >Přidání grafu - ping</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-grafy-add.php?typ=2" >Přidání grafu - traffic</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-cat-add.php" >Přidání kategorie</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-cat-list.php" >Výpis kategorií</a></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="2" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="monitoring-control-view-graf.php" >Prohlížení grafů</a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td colspan="1" ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td> 

   <td class="map-tab-right" ><a href="admin-partner.php" >Nastavení externího partner programu</a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
  
  <tr>
    <td ><br></td>
    <td colspan="1" class="map-tab-right map-tab" ><a href="partner.php" >Partner-program</a></td>
    <td class="map-tab-right" ><a href="partner.php">Vložení žádosti</a></td>
    <td class="" ><br></td>
  </tr>

  <tr>
    <td ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="partner-vypis.php">Výpis žádostí</a></td>
 
  </tr>
   
  <tr>
    <td ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="partner-vyrizeni.php" >Akceptování žádosti</a></td>
  </tr>

  <tr>
    <td ><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="partner-pripojeni.php" >Změna stavu připojení</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
   
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><a href="archiv-zmen.php?pocet=50" >Archiv změn</a></td>
  </tr>
  
  
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>
   
  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><a href="soubory.php" >Správce souborů</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right map-tab" ><a href="board-header.php">Nástěnka</a></td>
    <td class="map-tab" ><br></td>
    <td class="map-tab-right" ><a href="board-main.php?action=post">PŘIDAT ZPRÁVU</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="board-main.php?action=view&what=new">AKTUÁLNÍ ZPRÁVY</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="board-main.php?action=view&what=old">STARÉ ZPRÁVY</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right map-tab" ><a href="others-subcat.php">Ostatní</a></td>

    <td colspan="2" class="map-tab-right" ><a href="syslog.php">Syslog</a></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="mail.php" >Mail</a></td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right map-tab" ><a href="opravy.php" >Závady/opravy</a></td>
    <td  class="map-tab-right" ><a href="opravy-index.php?typ=1" >Vložení závady/opravy</a></td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="opravy-index.php?zobr_vlastnika=0&typ=2&priorita_filtr=99&v_reseni_filtr=99&vyreseno_filtr=99" >Výpis závad/oprav</a>
    </td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right map-tab" ><a href="mail.php" >Statistiky</a></td>
    <td  class="map-tab-right" ><a href="stats-objekty.php" >Počty objektů</a></td>
 
  </tr>

 <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="stats-vlastnici.php" >Počty vlastníků</a>
    </td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="platby-hot-stats.php" >Hotovostní platby</a>
    </td>
  </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="voip/voip-index.php" >VoIP</a>
    </td>
    <td class="map-tab-right" ><a href="voip/voip-import-vypisu.php?item=1" >Import výpisů</a></td>  
 </tr>

 <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="voip/voip-online-dial-cust-list.php" >Klienti (Customers)</a>
    </td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" ><a href="voip/voip-cisla.php" >Tel. čísla</a></td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="2" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="voip/voip-hovory.php?item=1" >Hovory</a>
    </td>
  </tr>

 <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
    <td class="map-tab-right" >
	<a href="voip/voip-index.php" >Exporty</a>
    </td>
 </tr>

  <tr>
    <td><br></td>
    <td colspan="1" class="map-tab-right" ><br></td>
  </tr>

 <tr>
    <td><br></td>
    <td class="map-tab-right" >
	<a href="" >O programu</a>
    </td>
    <td><a href="about-map.php" >Mapa</a> [: zde stojite :] </td>
 </tr>

 <tr>
    <td colspan="2" ><br></td>
    <td  class="map-tab-right" ><a href="about-changes.php">Changes</a></td>
  </tr>
  
  <tr>
    <td><br></td>
    <td><br></td>
  </tr>

  </table>
  
  <!-- konec vlastniho obsahu -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

