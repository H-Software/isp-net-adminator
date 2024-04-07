<?php

include ("include/config.php"); 
include ("include/check_login.php");

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ("include/charset.php"); 

?>

<title>Adminator2 - změny</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
  <td colspan="2" ><? include("about-cat.php"); ?></td>
 </tr>
  
  <tr>
  <td colspan="2" align="center">
    
  <div style="text-align: left; padding-left: 25px; padding-top: 10px; font-weight: bold; font-size: 18px;  ">Změny v systému: </div>
  
  <table border="0" width="70%">

    <tr>
	<td> .12.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
	</td>
    </tr>

    <tr>
	<td> 6.12.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
	takze nekolik minoritnich zmen: 
	 1. opraveno vypisovani zaloh(souboru) u router/ů (sekce topology) 
	  2. pri vypisu zmen z archivu je nove videt i akce pridani objektu (plati jak u objektu internetovych, tak STB, ale pouze u nově přidaných - od teď)
	</td>
    </tr>

    <tr>
	<td> 30.11.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
	do adminatora pridano genrovani noveho (resp. uz aktualniho) registracniho formulare .. 
	 a to jak noveho, tak pro existujici objekty ( i STB) :)
	</td>
    </tr>
    
    <tr>
	<td> 18.11.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
	
	nekolik mensich změn u objektů v adminátoru 
	
	 úprava objektů 
	  - pokud se upraví pouze tarif(linka), prida se do fronty osvezeni pouze konkretniho reinharda, resp. QoS-u na něm (pokud se nenajde konkretni, osvezujou se vsechny reinhardi) - ZLEPSENI 
	  
	   přidání objektu 
	    - již se přidáva do fronty osvežováni shaperu na konkretním einhardu (pripadne na vsech) - OPRAVA CHYBY
	</td>
    </tr>

    <tr>
	<td> 16.11.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
    
    vylepseno pridavani objektů 
     - zlepseni vkladani do archivu zmen 
      - automaticke pridavani pozadavku na osvezeni / restarty
	</td>
    </tr>

    <tr>
	<td> 31.10.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
	
	byl prepracovan system pozastavenych fakturaci u vlastniku(2). místo fakturacních skupin je príznak u kazdeho vlastnika (vč. důvodu, od kdy atd).
	</td>
    </tr>

    <tr>
	<td> 21.10.2011</td>
	<td><span class="changes-upraveno" > - upraveno: </span>
	pri uprave ci pridani settopboxu se do fronty pro restart (work) nově přidají odpovídající položky automaticky
	</td>
    </tr>
	
    <tr>
	<td> 15.9.2009</td>
	<td><span class="changes-repaid" > - Konec vývoje - </span> Konec vývoje a změn v projektu Adminátor2, 
				    věskeré nové funkce budou implementovány do systému Adminator3, který dále 
				    bude přebírat funkce Adminátora2 (dle přepisů kódu). Oba systémy jsou propojeny a 
				    je do nich stejný login atd.</td>
    </tr>

    <tr>
	<td> 9.9.2009</td>
	<td><span class="changes-repaid" > - opravena: </span> chyba v sekci "Vlastníci2", kde při zvolení pouze "FU" či "DÚ" klientů 
	se vypisovaly fakturační skupiny obou typů ( FU, DU) zárověn</td>
    </tr>

    <tr>
	<td> 9.9.2009</td>
	<td><span class="changes-upraveno" > - upravena: </span> sekce "Archiv změn" na "Změny" a přidány podsekce</td>
    </tr>

    <tr>
	<td> 9.9.2009</td>
	<td><span class="changes-upraveno" > - upraveno: </span> v exportu vlastníků pro účetní přidána součtová řádka</td>
    </tr>

    <tr>
	<td> 8.9.2009</td>
	<td><span class="changes-upraveno" > - upraveno: </span> Přidána /uprava sekci "O programu", se této sekce přesunuta mapa webu a změny</td>
    </tr>

    <tr>
	<td> 1.9.2009</td>
	<td><span class="changes-upraveno" > - upraveno: </span> Fakturační skupiny u vlastníků přecházení do ostrého provozu</td>
    </tr>

    <tr>
	<td> 1.9.2009</td>
	<td><span class="changes-add" > - přidáno: </span> v sekci "Vlastníci - Fakturační skupiny" přidány prvky pro úpravu a mazání skupin</td>
    </tr>

    <tr>
	<td> 11.8.2009</td>
	<td><span class="changes-upraveno" > - upraveno: </span> v sekci "Vlastní platby" předělány odkazy do horní části</td>
    </tr>

    <tr>
	<td> 7.2009</td>
	<td><span class="changes-upraveno" > - upraveno: </span> v sekci "Vlastníci2" export pro účetní - přidány prvky pro zobrazování klientů na optice</td>
    </tr>

    <tr>
	<td> 30.6.2009</td>
	<td><span class="changes-add"> - přidáno: </span> v sekci "Vlastníci2" přidáno zobrazování objektů dle typu</td>
    </tr>

    <tr>
	<td> 19.6.2009</td>
	<td><span class="changes-repaid"> - opraveno: </span> opraveny chyby v objektech, kdy se nesprávně přepínal prvek "bezdrátové/optická síť" </td>
    </tr>

    <tr>
	<td> 20.5.2009</td>
	<td><span class="changes-add"> - přidáno: </span> v "Objekty" přidána sekce "Set-Top-Boxy" (výpis, db struktura) </td>
    </tr>

    <tr>
	<td> 05 / 2009</td>
	<td><span class="changes-add"> - přidáno: </span> v "Nastavení" přidána sekce "Tarify" </td>
    </tr>

    <tr>
	<td> 05 / 2009</td>
	<td><span class="changes-upraveno"> - upraveno: </span> přechod na určování tarifů dle sekce "Tarify" </td>
    </tr>

    <tr>
	<td>17.4.2009</td>
	<td><span class="changes-upraveno"> - upraveno: </span> v historii vlastníka je nyní vidět i úprava/přidání fakturačních údajů</td>
    </tr>

    <tr>
	<td>17.4.2009</td>
	<td><span class="changes-add"> - přidáno: </span> v sekci "Topology - Výpis lokalit/nodů" přidán prvek pro 
	rozlišení optické a bezdrátové sítě</td>
    </tr>

    <tr>
	<td>17.4.2009</td>
	<td><span class="changes-repaid"> - opraveno: </span> v sekci "Topology - výpis routerů" při použítí
	 "vypsat vysílače/nody" nefungoval odkaz na detail nodu</td>
    </tr>

    <tr>
	<td>16.4.2009</td>
	<td><span class="changes-repaid"> - opraveno: </span> při tisku smlouvy z vlastníků2 se nepřenášelo pole "Město a PSČ"</td>
    </tr>

    <tr>
	<td>15.4.2009</td>
	<td><span class="changes-add"> - přidáno: </span> v sekci "Voip" přidán prvek pro mazání tel. čísel</td>
    </tr>
    
    <tr>
	<td>15.4.2009</td>
	<td><span class="changes-repaid"> - opraveno: </span> v sekci "Topology - Výpis routerů" opraveno zobrazování souborů se zálohami</td>
    </tr>
    
    <tr>
	<td>15.4.2009</td>
	<td><span class="changes-repaid"> - opraveno: </span> ve výpisu objektů opraven prvek pro výběr módu objektů</td>
    </tr>
    
    <tr>
	<td>5.3.2009</td>
	<td><span class="changes-add"> - přidán </span> výpis emailových exportů v sekci "Topology - výpis routerů"</td>
    </tr>

    <tr>
	<td>2.3.2009</td>
	<td><span class="changes-upraveno"> - upraven </span> prvek v adminátoru pro více tarifů, plus rozdělení wifi/optické tarify (betaverze)</td>
    </tr>

    <tr>
	<td>1.3.2009</td>
	<td><span class="changes-add"> - přidán </span> systém pro výpis faktur online ( betaverze ), dostupné u vlastníků2</td>
    </tr>

    <tr>
	<td>11.1.2009</td>
	<td><span class="changes-upraveno"> - upravena </span> sekce "Úprava nodu/lokality" - upraven kód, design stránky a vkládání do archivu změn</td>
    </tr>

    <tr>
	<td>8.1.2009</td>
	<td><span class="changes-upraveno"> - upraven </span> design a funkce stránky "Přidání lokality/nodu" </td>
    </tr>

    <tr>
	<td>8.1.2009</td>
	<td><span class="changes-upraveno"> - upraveno </span> v sekci "Archiv změn" přidány odkazy na vlastníky/objekty, kterých se týká změna </td>
    </tr>

    <tr>
	<td>6.1.2009</td>
	<td><span class="changes-upraveno"> - upraveno </span> v sekci "Archiv změn" ukládání pouze diferenciálních změn u úpravy objektů </td>
    </tr>

    <tr>
	<td>6.1.2009</td>
	<td><span class="changes-upraveno"> - upraveno </span> -generální úprava sekce "Archiv změn" </td>
    </tr>

    <tr>
	<td>21.12.2008</td>
	<td><span class="changes-repaid"> - opravena </span> chyba v sekcích "Vlastníci ..", nyní jde hledat i dle informací v poznámce. </td>
    </tr>

    <tr>
	<td>19.12.2008</td>
	<td><span class="changes-repaid"> - opravena </span> chyba v sekci "Neuhrazené faktury - Aut. zjišťování splatnosti faktur",
	kdy se špatně označovaly faktury přímo v den splatnosti. </td>
    </tr>

    <tr>
	<td>18.12.2008</td>
	<td><span class="changes-add"> - přidáno: </span> v sekci "Topology - výpis nodů/lokalit" přidány prvky pro filtrování záznamů.</td>
    </tr>

    <tr>
	<td>3.12.2008</td>
	<td><span class="changes-add"> - přidáno: </span> v sekci "Partner program" je možno upravovat poznámky technika. odkaz 
	<a href="partner-pozn-update.php" >zde</a></td>
    </tr>

    <tr>
	<td>3.12.2008</td>
	<td><span class="changes-add"> - přidáno: </span> v sekci "Topology - výpis lokalit /nodů" přidán prvek pro mazání.</td>
    </tr>

    <tr>
	<td>2.12.2008</td>
	<td><span class="changes-upraveno"> - vylepšena </span> sekce "archiv změn" </td>
    </tr>
    <tr>
	<td>25.11.2008</td>
	<td><span class="changes-repaid"> - opravena </span> chyba v hledání v sekci "Topology - Výpis lokalit/nodů" </td>
    </tr>

    <tr>
	<td>25.11.2008</td>
	<td><span class="changes-upraveno"> - upraveno </span> "sekce Topology - Výpis lokalit/nodů" - přidáno listování záznamů, pole pro hledání </td>
    </tr>
    
    <tr>
	<td>21.11.2008</td>
	<td><span class="changes-add"> - přidáno: </span> u vlastníku2 (s.r.o) přidány informace o smlouvě (typ, datum podpisu, atd.) </td>
    </tr>

    <tr>
	<td>11.11.2008</td>
	<td><span class="changes-add"> - přidáno: </span> administrace externí sekce partner programu </td>
    </tr>

    <tr>
	<td>10.11.2008</td>
	<td><span class="changes-upraveno"> - generální úprava: </span> sekce partner-program, přidány prvky poznámka technika,připojeno </td>
    </tr>

    <tr>
	<td>26.10.2008</td>
	<td><span class="changes-add"> - přidáno: </span> export dat do xls v sekci "Topology - výpis lokalit/nodů" </td>
    </tr>

    <tr>
	<td>25.10.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> upraven systém kontroly odhlášení.
	Timeout prodloužen na 20minut. Pokud je přihlášena pouze jedna osoba, systém odhlašování se neaplikuje.</td>
    </tr>

    <tr>
	<td>8.10.2008</td>
	<td><span class="changes-add"> - přidáno: </span> sekce "Závady/opravy" </td>
    </tr>

    <tr>
	<td>2.10.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> úvodní stánka, přidán log přihlášení a výpis závad/oprav </td>
    </tr>

    <tr>
	<td>19.8.2008</td>
	<td><span class="changes-add"> - přidána: </span> stránka statistika počtu vlastníků </td>
    </tr>

    <tr>
	<td>19.8.2008</td>
	<td><span class="changes-add"> - přidána: </span> sekce Statistika </td>
    </tr>

    <tr>
	<td>18.9.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> export archivu změn, data se exportují do jednotlivých listů, 
	aby nedocházelo k chybám "došel počet řádků v listu"
	</td>
    </tr>

    <tr>
	<td>16.8.2008</td>
	<td><span class="changes-add"> - přidáno: </span> stránka pro globální vyhledávání vlastníků </td>
    </tr>

    <tr>
	<td>10.9.2008</td>
	<td><span class="changes-add"> - přidáno: </span> do úpravy objektu přidána kontrola, jestli vlastník objektu nemá pozastavené fakturace 
	( funguje pouze při NetN -> NetA ) </td>
    </tr>

    <tr>
	<td>9.9.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> v sekci vlastníci2 byly tlačítka fakturační / nefakturační nahrazeny
	popisem FÚ, DÚ </td>
    </tr>

    <tr>
	<td>5.9.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> Statistika počtu objektů </td>
    </tr>

    <tr>
	<td>20.8.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> zobrazování přihlášených uživatelů </td>
    </tr>

    <tr>
	<td>15.4.2008</td>
	<td><span class="changes-repaid"> - opraveno: </span> v prohlížeci firefox nešlo stáhnout export hot. plateb </td>
    </tr>

    <tr>
	<td>15.4.2008</td>
	<td><span class="changes-repaid"> - opraveno: </span> v prohlížeci firefox ze špatně zobrazovalo hlavní menu </td>
    </tr>

    <tr>
	<td>1.8.2008</td>
	<td><span class="changes-add"> - přidáno: </span> pop-up okno do výpisu plateb </td>
    </tr>

    <tr>
	<td>1.8.2008</td>
	<td><span class="changes-add"> - přidáno: </span> mapa portálu </td>
    </tr>

    <tr>
	<td>4.7.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> design hlavních odkazů a přidání sub-kategorií</td>
    </tr>

    <tr>
	<td>3.7.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> přidání / úprava uživatelů, prování se i kontrola zadaných údajů </td>
    </tr>

    <tr>
	<td>5.6.2008</td>
	<td><span class="changes-add"> - přidáno: </span> nástěnka </td>
    </tr>

    <tr>
	<td> 5/2008 - <br>6/2008</td>
	<td><span class="changes-add"> - přidáno: </span> sekce routery </td>
    </tr>

    <tr>
	<td>13.5.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> vylepšena statistika hotovostních plateb, již jsou vidět jednotlivé měsíce dle 
	zvoleného roku </td>
    </tr>

    <tr>
	<td>13.5.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> vylepšeny výpisy výpovědí, již se zobrazuje jestli se vlastník v archivu </td>
    </tr>

    <tr>
	<td>12.5.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> vylepšena stránka výpisu plateb u vlastníků
	 - již se zobrazují chybějící a neověřené platby </td>
    </tr>


    <tr>
	<td>2.4.2008</td>
	<td><span class="changes-repaid"> - opraveno: </span> chyba při řazení vlastníků, řazení se aplikovalo pouze na první stránku </td>
    </tr>

    <tr>
	<td>2.4.2008</td>
	<td><span class="changes-add"> - přidáno: </span> sekce "vlastníci - archiv" kde budou klienti s vypovězenou smlouvou </td>
    </tr>

    <tr>
	<td>1.4.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> design celého systému </td>
    </tr>

    <tr>
	<td>27.3.2008</td>
	<td><span class="changes-add"> - přidáno: </span> do vlastníků přidán odkaz "zobraz na mapě", který zobrazí adresu na serveru mapy.cz </td>
    </tr>

    <tr>
	<td>19.3.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> přidávání objektů je svázáno se stavem vysílačů v "Topology" </td>
    </tr>

    <tr>
	<td>6.3.2008</td>
	<td><span class="changes-add"> - přidáno: </span> do sekce "Vlastníci" a "Vlastníci2" přidáno řazení </td>
    </tr>

    <tr>
	<td>6.3.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> export v sekci "Archiv změn" předělán do nativního xls formátu. </td>
    </tr>
    
    <tr>
	<td>5.3.2008</td>
	<td><span class="changes-add"> - přidáno: </span> do sekce "Výpis výpovědí" přidán prvek pro tisk vložených výpovědí </td>
    </tr>
    
    <tr>
	<td>4.3.2008</td>
	<td><span class="changes-upraveno"> - upraveno: </span> stránka "Výpis lokalit" v sekci Topology </td>
    </tr>
    
    <tr>
	<td>20.2.2008</td>
	<td><span class="changes-add"> - přidána: </span> sekce "Výpovědi smluv" </td>
    </tr>
            
    <tr>
	<td>18.2.2008</td>
	<td><span class="changes-upraveno" > - upraveno: </span> sekce "Partner" umožnuje vyřizování žádostí </td>
    </tr>    
    
    <tr>
	<td>14.2.2008</td>
	<td><span class="changes-upraveno" > - upraveno: </span> tisk hotovostních plateb rozlišuje fy.  Simelon, s.r.o. a Martin Lopušný</td>
    </tr>    
  
    <tr>
	<td>30.1.2008</td>
	<td><span class="changes-upraveno" > - upraveno: </span> sekce "Topology" </td>
    </tr>    
    
    <tr>
	<td>30.1.2008</td>
	<td><span class="changes-add"> - přidáno: </span> sekce "Partner-program" </td>
    </tr>
    
    <tr>
	<td>13.1.2008</td>
	<td><span class="changes-add" > - přidáno: </span> stránka pro posílání emailů </td>
    </tr>    
  
    <tr>
	<td>13.1.2008</td>
	<td><span class="changes-add" > - přidáno: </span> stránka pro výpis neplatičů za určité období </td>
    </tr>    
  
    <tr>
	<td>13.1.2008</td>
	<td><span class="changes-upraveno" > - upraveno: </span> rozmístění hlavních odkazů </td>
    </tr>    
   
    <tr>
	<td>11.1.2008</td>
	<td><span class="changes-repaid" > - opraveno: </span> u změny vlasníka se neakceptovala změna nicku </td>
    </tr>    
  
    <tr>
	<td>8.1.2007</td>
	<td><span class="changes-repaid"> - opraveno: </span> v importu plateb se neukládaly položky s větší částkou </td>
    </tr>
    
    <tr>
	<td>30.12.2007</td>
	<td><span class="changes-upraveno"> -upraveno:</span> v ročním výpisu plateb se nyní vypisují políčka N/E, které naznačují, že klient daném v období nebyl připojen</td>
    </tr>
    
    <tr>
	<td>18.12.2007</td>
	<td><span class="changes-upraveno"> -upraveno:</span> export hot. plateb se exportuje do jednoho nativního xls sešitu, druhy plateb dle listů v šešitě</td>
    </tr>
    
    <tr>
	<td>14.12.2007</td>
	<td><span class="changes-upraveno"> -upraveno:</span> ve funkci "vložení hotovostní platby" lze přidat více plateb  </td>
    </tr>

    <tr>
	<td> 10.12.2007</td>
	<td><span class="changes-add" > -přidáno:</span> platební systém pro bezhotovostní platby</td
    </tr>
    
    <tr>
	<td>25.11.2007</td>
	<td><span class="changes-upraveno"> -upraveno: </span> "kontrola údajů k platbám" nyní 
						zobrazuje pouze klienty s  nesedícími platbami</td>
    </tr>
    
    <tr>
	<td>25.11.2007</td>
	<td><span class="changes-add" > -přidáno: </span> zobrazování "výpisu plateb na účet" </td>
    </tr>
	
    <tr>
	<td>24.11.2007</td>
	<td><span class="changes-add" > - přidáno: </span> funkce pro úpravu fakturačních údajů </td>
    </tr>
    
    <tr>
	<td>23.11.2007</td>
	<td><span class="changes-repaid" > - opraveno:</span> zobrazování diakritiky u exportu v archivu změn </td>
    </tr>

    <tr>
	<td>23.11.2007</td>
	<td><span class="changes-add" > -přidáno:</span> úprava fakturačních údajů u vlastníků</td>
    </tr>
    
    <tr>
	<td>23.11.2007</td>
	<td><span class="changes-repaid" > -opraveno:</span> zobrazování diakritiky v popisech u vlastníků</td>
    </tr>
    
    <tr>
	<td>20.11.2007</td>
	<td><span class="changes-add"> -přidáno: </span> mazání fakturačních údajů</td>
    </tr>
    
    <tr>
	<td>14.11.2007</td>
	<td><span class="changes-upraveno">upraveno:</span> export hotovostních plateb dle jednolivých společností</td>
    </tr>
    
    </tr>

    <tr>
	<td>13.11.2007</td>
	<td><span class="">info: </span> začátek použ ívání sekce "Vlastníci2 " </td>
    </tr>

    <tr>
	<td>9.11.2007</td>
	<td><span class="changes-add"> -přidáno:</span> listovaní do sekce "Vlastnící2" </td>
    </tr>

    <tr>
	<td>1.11.2007</td>
	<td><span class="changes-repaid" > -opraveno:</span> v úpravě vlasníků tlačítko "Zpět na vlastníka" </td>
    </tr>

    <tr>
	<td>30.10.2007</td>
	<td><span class="changes-add">-přidáno:</span> do sekce monitoring-control přidány odkazy pro výpis <br>
	a přidání kategorií a pro prohlížení grafů</td>
    </tr>
    
    <tr>
	<td>22.10.2007</td>
	<td><span class="changes-add">-přidáno:</span> export dat do sekce objekty </td>
    </tr>
    
    <tr>
	<td>1.10.2007</td>
	<td><span class="changes-add">-přidána:</span> sekce "monitoring-control" </td>
    </tr>
    
    <tr>
	<td>10.9.2007</td>
	<td><span class="changes-repaid" >-opraveno: </span> řazení v sekci objekty</td>
    </tr>

    <tr>
	<td>7.9.2007</td>
	<td><span class="changes-add" >-přidáno: </span> ovládání a tvoření snat/dnat veřejných ip adres</td>
    </tr>
    
    <tr>
    <td>4.9.2007</td>
    <td><span class="changes-add" >-přidáno: </span> přiřazování objektů k vlastníkům</td>
    </tr>

    <tr>
	<td>29.8.2007</td>
	<td><span class="changes-add" >- přidáno: </span> přepínače pro selekci zákazníků v sekci "vlastníci" </td>
    </tr>

    <tr>
	<td>28.8.2007</td>
	<td><span class="changes-add">- přidáno: </span> listování v sekci "vlastníci"</td>
    </tr>
    
    <tr>
	<td>14.8.2007</td>
	<td><span class="changes-add">- přidáno: </span> listování v sekci "objekty"</td>
    </tr>

    <tr>
    <td>4.7.2007</td>
    <td><span class="changes-add">- přidáno: </span> prvek pro odebrání objektu z garant. třídy </td>
    </tr>

    <tr>
    <td>12.6.2007</td>
    <td><span class="changes-add" >- přidáno: </span> v sekci "Vlastníci" zobrazování fakturačních údajů</td>
    </tr>
  
  <tr>
  <td>12.6.2007</td>
  <td><span class="changes-upraveno" >- upraveno: </span> v sekci "Archiv změn" zobrazování položek po částech </td>
  </tr>
  
  <tr>
  <td>30.5.2007</td>
  <td><span class="changes-add"> - přidán: </span> prvek pro přidání objektu do garant. třídy </td>
  </tr>
  
  <tr>
  <td>29.5.2007</td>
  <td><span class="changes-add"> - přidán: </span> prvek pro smazání objektu </td>
  </tr>
  
  <tr>
  <td>8.5.2007</td>
  <td><span class="changes-add">- přidána: </span> sekce automatika</td>
  </tr>
  
  <tr>
  <td>6.5.2007 </td>
  <td><span class="changes-add">-přidána:</span> "šikanovací" stránka, ovládání přes "úpravu objektu" </td>
  </tr>
  
  <tr>
  <td>6.5.2007</td>
  <td><span class="changes-upraveno">upraveno: </span> úprava objektů se zapisuje do archivu změn</td>
  </tr>
  
  <tr>
  <td>5.5.2007</td>
  <td><span class="changes-add">- přidána</span> úprava objektů</td>
  </tr>
  
  <tr>
  <td>23.4.2007</td>
  <td>- <span class="changes-repaid">opravena</span> chyba při přidávání objektů která končila chybovou hláškou "Data do databáze nelze uložit", příčinou byla chybná maska pro kontrolu mac adresy</td>
  </tr>
  
    <tr>
    <td>23.7.2007</td>
    <td>- <span class="changes-add">přidána</span> sekce "archiv změn"</td>
    </tr>
    
    <tr>
    <td>23.4.2007</td>
    <td>- <span class="changes-add">přidáno:</span> řazení položek ve výpisu objektů</td>
    </tr>
    
    <tr>
    <td>23.4.2007</td>
    <td>- <span class="changes-repaid">opraveno:</span> v sekci "přidání objektu" odstraněno dvojí vypisování chybových hlášek</td>
    </tr>
    
    <tr>
    <td>21.4.2007</td>
    <td>- <span class="changes-upraveno">upraveno:</span> timeout zvýšen na 15minut</td>
    </tr>
    
    <tr>
    <td>20.4.2007</td>
    <td>- <span class="changes-add">přidání</span> finální verze sekce "work"</td>
    </tr>
    
    <tr>
    <td>17.4.2007</td>
    <td>- <span class="changes-repaid">opraveno:</span> v sekci "topology" opraveno přiřazování a odebírání 'objekty vs. nody' </td>
    </tr>
    
    <tr>
	<td>16.4.2007</td>
	<td>- <span class="changes-add">přidáno:</span> ve výpisu objektů se v poznámce zobrazuje datum a čas přidání objektu </td>
    </tr>
    
    <tr>
      <td>16.4.2007</td>
      <td>- <span class="changes-repaid">opraveno:</span>  hledání v sekci "vlastníci" , nyní lze hledat dle většiny údajů </td>
    </tr>
		  

    <tr>
    <td valign="top">12.4.2007</td>
    <td>- <span class="changes-info">info:</span> v sekci "Topology - výpis lokalit" přibyla kolonka 'mac' ,která vyjadřuje pod 
	jakou mac adresou vystupují ip adresy v daném rozsahu
    	a která se zároveň používá ke generování ip při přidání objektu</td>
    </tr>
    
    <tr>
    <td>11.4.2007</td>
    <td>- přidána sekce "Správce souborů" </td>
    </tr>

    <tr>
    <td>5.4.2007</td>
    <td>- do systému přidána stránka pro přidání objektu</td>
    </tr>
    
    <tr>
    <td>3.4.2007</td>
    <td>- do systému přidáno zobrazování šířky pásma u zákazníků s garantovanou rychlostí</td>
    </tr>

    <tr>
    <td>31.3.2007</td>
    <td>- do sekce "Ojekty" přidáno zobrazování poznámek</td>
    </tr>

    <tr>
      <td>31.3.2007</td>
      <td>- do sekce "Objekty" přidány radiobutony pro sekundární výběr </td>
    </tr>

    <tr>
      <td>29.3.2007</td>
      <td>- přidání sekce "Vlastníci" </td>
    </tr>

    <tr>
	<td>27.3.2007</td>
	<td>- <span class="changes-upraveno" >upraveno:</span> formulář - Topology - přidání lokality ( přidána pole pro ip rozsah a umístění aliasu) </td>
    </tr>

    <tr>
	<td>27.3.2007</td>
	<td>- přidán formulář pro tisk smluv - "Administrace -> Tisk - smlouva" </td>
    </tr>
      
   <tr>
	<td>20.3.2007</td>
        <td>- přidána stránka pro změnu hesla - "Administrace -> Změna hesla" </td>
    </tr>
			   		 		 
      <tr>
    	 <td>18.3.2007</td>
	 <td>- přidána stránka upozorňující na nedostatečný level ( nolevelpage.php ) </td>
    </tr>
			  	       
     <tr>
	<td>18.3.2007</td>
	<td>- upravena stránka upozorňující na nekorektní login ( nologinpage.php) </td>
    </tr>
			     

     <tr>
        <td>18.3.2007</td>.
	<td>- přidána sekce "Administrace -> Výpis levelů" </td>
    </tr>
			     
     <tr>
	<td>18.3.2007</td>
	<td>- přidána sekce "Administrace -> Přidání levelu" </td>
    </tr>
			   
    <tr>
	 <td>18.3.2007</td>
	 <td>- přidána sekce "Administrace -> Výpis uživatelů" </td>
    </tr>
			      			  
     <tr>
	 <td>18.3.2007</td>
	 <td>- přidána sekce "Administrace -> Přidání uživatele" </td>
    </tr>
			   
     <tr>
	<td>18.3.2007</td>
	<td>- upraveno odhlašování ze systému</td>
    </tr>
    
     <tr>
	<td>17.3.2007</td>
	<td>- přidána sekce "Administrace"</td>
    </tr>
				           
     <tr>
          <td>17.3.2007</td>
	  <td>- do systému přidána levelizace přihlášených uživatelů</td>
    </tr>
		 
     <tr>
        <td>15.3.2007</td>
	<td>- přidána sekce "Changes"</td>
    </tr>
		
    <tr>
	 <td>14.3.2007</td>
	 <td>- opravení chyby při zavření prohlížeče a následného přihlášení do systému</td>
    </tr>
		    
        
    
    <!-- POSLEDNI ZAZNAM -->
  <tr>
    <td>1.3.2007</td>
    <td>- přidání sekce objekty</td>
    </tr>
    
   </table>
  
  <?
  
   
  
  ?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

