<?php

class board
{
    var $what;
    var $action;
    var $page;
    
    var $send;
    var $sent;
    
    var $author; 
    var $email;
    
    var $from_date;
    var $to_date;
    var $subject;
    var $body;
    
    var $error;
    var $view_number;
    var $sql;
    
    var $write; //jestli opravdu budem zapisovat, ci zobrazime form pro opraveni hodnot
    
    function prepare_vars($nick)
    {
      if( !isset($this->author) )
      { $this->author=$nick; }
      
      if ( ( (!isset($this->action)) and (!isset($this->send)) ) ) $this->action = "view"; //ještě není zinicializována proměnná $action
      if (!isset($this->what)) $this->what = "new"; //ještě není zinicializována proměnná $what
      if (!isset($this->page)) $this->page = 0; //ještě není zinicializována proměnná $page
	  
      return true;
    }
    
    function show_messages()
    {
 
	if( $this->what == "new" )
	{ $this->sql = " from_date <= NOW() AND to_date >= NOW() "; }
	else
	{ $this->sql = " to_date < NOW() "; }

	$sql_base = "SELECT *,DATE_FORMAT(from_date, '%d.%m.%Y') as from_date2";
	$sql_base .= ",DATE_FORMAT(to_date, '%d.%m.%Y') as to_date2 ";


	$start = $this->page*$this->view_number; //první zpráva, která se zobrazí
	$message = mysql_query($sql_base." FROM board WHERE ".$this->sql." ORDER BY id DESC LIMIT ".$start.",".$this->view_number);
	//    or die($this->query_error.", sql: "); //vybíráme zprávy - seřazeno podle id

	//vypíšeme tabulky se zprávami
	while($entry = mysql_fetch_array($message))
	{
	    $zpravy[] = array("id" => $entry["id"],"author" => $entry["author"],
                        "email" => $entry["email"], "subject" => $entry["subject"],
                        "body" => $entry["body"], "from_date" => $entry["from_date2"],
                        "to_date" => $entry["to_date2"] );
	}
        
	return $zpravy;
	
    } //konec funkce show_messages
    
    function show_pages()
    {
      //odkazy na starší zprávy (u právě zobrazené zprávy se odkaz nevytvoří)
      $count = mysql_query("SELECT id FROM board WHERE ".$this->sql); //vybíráme zprávy
      $page_count = ceil(mysql_num_rows($count)/$this->view_number); //počet stran, na kterých se zprávy zobrazí
	   
      $stranek = array();
  
      for($i=0;$i<$page_count;$i++)
      {
         $stranek[] = array("what" => $this->what, "i" => $i, "i2" => ($i+1), "i_akt" => $this->page);
      }

      return $stranek;			   
    
    } //konec funkce show_pages

    function check_vars()
    {
        list($from_day, $from_month, $from_year) = explode("-",$this->from_date);
	list($to_day, $to_month, $to_year) = explode("-",$this->to_date);
		  
	//byl odeslán formulář?
	if($this->author=="" || $this->subject=="" || $this->body==""):  //byly vyplněny všechny povinné údaje?
	     $this->error .= 'Musíte vyplnit všechny povinné údaje - označeny tučným písmem.';
	elseif(mktime(0,0,0,$from_month,$from_day,$from_year) > mktime(0,0,0,$to_month,$to_day,$to_year)): //zkontrolujeme data od-do
	     $this->error .= 'Datum OD nesmí být větší než datum DO.';
	elseif(mktime(0,0,0,$from_month,$from_day,$from_year) < mktime(0,0,0, date("m"), date("d"), date("Y"))):
	     $this->error .= 'Datum OD nesmí být menší než dnešní datum.';
	else:
	     $this->write = true; //provedeme zápis
	endif;
	
    } //konec funkce check_vars
    
    function convert_vars()
    {
	//odstraníme nebezpečné znaky
        $this->author = htmlspecialchars($this->author);
	$this->email = htmlspecialchars($this->email);
	$this->subject = htmlspecialchars($this->subject);
		 
	$this->body = substr($this->body, 0, 1500);         //bereme pouze 1500 znaků
	$this->body = trim($this->body);                            //odstraníme mezery ze začátku a konce řetězce
	$this->body = htmlspecialchars($this->body);        //odstraníme nebezpečné znaky
	$this->body = str_replace("\r\n"," <BR> ", $this->body);    //nahradíme konce řádků na tagy <BR>
				 
	//$body = wordwrap($body, 90, "\n", 1); //rozdělíme dlouhá slova
	     
	//vytvoříme odkazy
	$this->body = eregi_replace("(http://[^ ]+\.[^ ]+)", " <a href=\\1>\\1</a>", $this->body);
	$this->body = eregi_replace("[^/](www\.[^ ]+\.[^ ]+)", " <a href=http://\\1>\\1</a>", $this->body);
						 
	//povolíme tyto tagy - <b> <u> <i>, možnost přidat další
	$tag = array("b", "u", "i");
	
	for($y=0;$y<count($tag);$y++):
	    $this->body = eregi_replace("&lt;" . $tag[$y] . "&gt;", "<" . $tag[$y] . ">", $this->body);
	    $this->body = eregi_replace("&lt;/" . $tag[$y] . "&gt;", "</" . $tag[$y] . ">", $this->body);
	endfor;

	//prevedeni datumu
        list($from_day, $from_month, $from_year) = explode("-",$this->from_date);
	list($to_day, $to_month, $to_year) = explode("-",$this->to_date);
    									 
	$this->from_date = date("Y-m-d", mktime(0,0,0,$from_month,$from_day,$from_year)); //od
	$this->to_date = date("Y-m-d", mktime(0,0,0,$to_month,$to_day,$to_year));//do	
										 
    
    } //konec funkce convert_vars
    
    function insert_into_db()
    {
	$add = mysql_query("INSERT INTO board VALUES ('', '$this->author', '$this->email', '$this->from_date',
				 '$this->to_date', '$this->subject', '$this->body')");
    
	
	if( $add == 1 )
	{ return $add; }
	else
	{
	  $this->error .= "<div>Došlo k chybě při zpracování SQL dotazu v databázi!</div>";	  
          // $this->error .= mysql_error();
	  
	  return $add;
	}
    }
    
} //konec tridy opravy

class zmeny_ucetni
{
    //promene pro pridani
    var $send;
    var $typ;
    var $text;
    var $odeslano;
    
    var $fail;
    var $error;
    var $info;
    
    var $writed;
    
    function load_sql_result()
    {
	$sql = "SELECT az_ucetni.zu_id , az_ucetni.zu_typ, az_ucetni.zu_text, az_ucetni.zu_akceptovano, ";
	$sql .= "az_ucetni.zu_akceptovano_kdy, az_ucetni.zu_akceptovano_kym, az_ucetni.zu_akceptovano_pozn, ";
	$sql .= "DATE_FORMAT(az_ucetni.zu_akceptovano_kdy, '%d.%m.%Y %H:%i') zu_akceptovano_kdy2, ";
	$sql .= " az_ucetni.zu_vlozeno_kdy, DATE_FORMAT(az_ucetni.zu_vlozeno_kdy, '%d.%m.%Y %H:%i') zu_vlozeno_kdy2, zu_vlozeno_kym, ";
	$sql .= " az_ucetni_typy.zu_nazev_typ AS typ_nazev ";
	
	$sql .= " FROM az_ucetni LEFT JOIN az_ucetni_typy ON az_ucetni.zu_typ = az_ucetni_typy.zu_id_typ ORDER BY zu_id DESC";
	
	$qu = mysql_query($sql);
	
	$rs_main = array();
	
	global $nick;
	
	while( $rs = mysql_fetch_assoc($qu) )
	{ 
	    if( ( $rs["zu_vlozeno_kym"] == $nick ) and ($rs["zu_akceptovano"] == 0) )
	    { 
		$uprava = "<a href=\"".$_SERVER["php_self"]."?action=update";
		$uprava .= "&id=".$rs["zu_id"]."\" >upravit</a>";
		
		$rs["uprava"] = $uprava; 
	    }
	    else
	    { $rs["uprava"] = "<span style=\"color: gray;\" >upravit</span>"; }

	    //globalni presunuti pole do pole :)
	    $rs_main[] = $rs; 
	}
	    
	return $rs_main;
	
    } //konec funkce load_sql_result
    
    function get_types()
    {
	$sql .= "SELECT zu_id_typ AS id,zu_nazev_typ AS nazev FROM az_ucetni_typy ";
	
	$qu = mysql_query($sql);
	
	while( $rs = mysql_fetch_assoc($qu) )
	{ $rs_main[] = $rs; }
	
	return $rs_main;
	
    } //konec funkce get_types

    function check_inserted_vars()
    {
	if( !(ereg('^([[:digit:]]+)$',$this->typ)) )
	{
	  $this->fail = true;
	  $this->error .= "<div class=\"form-add-fail\" ><H4>Zadaný typ (".$this->typ." ) není ve  správném formátu!!!</H4></div>";
	}
						    
    } //konec funkce checK-inserted_vars
    
    function save_vars_to_db()
    {
	global $nick;
	
        $add = mysql_query("INSERT INTO az_ucetni (zu_typ, zu_text, zu_vlozeno_kdy, zu_vlozeno_kym)
                              VALUES ('$this->typ','$this->text',now(),'$nick') ");
			      
	// pridame to do archivu zmen
	$pole="<b>akce: pridani zmeny pro ucetni; </b><br>";
	$pole .= "[typ_id]=> ".$this->typ.", [text]=> ".$this->text."";
					    
	if ( $add == 1){ $vysledek_write="1"; }
	$add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','$nick','$vysledek_write')");
	
	$this->writed = "true";
	
	if($add == 1){ return true; }
	else { return false; }
	
    } //save_vars_to_db
    
} //konec tridy zmeny_ucetni

class vlastnik2
{

    function show_fakt_skupiny($fu_select)
    {
	$fu_sql_base = " SELECT * FROM fakturacni_skupiny ";
        
	if( $fu_select == 2)
	{ $fu_sql_select .= " WHERE typ = '2' "; } //Pouze FU
	if( $fu_select == 3 )
	{ $fu_sql_select .= " WHERE typ = '1' "; } //pouze DU
				     
	$dotaz_fakt_skup = mysql_query($fu_sql_base." ".$fu_sql_select." ORDER BY nazev DESC");
	
	while( $data_fs = mysql_fetch_array($dotaz_fakt_skup) )
	{
	    $fs[]= array( "id" => $data_fs["id"], "nazev" => $data_fs["nazev"], "typ" =>$data_fs["typ"] );
	}
	
	return $fs;
		
    } //konec funkce show_fakt_skupiny

} //konec tridy vlastnik2

class opravy
{

    function vypis_opravy()
    {


        $sql="SELECT * FROM opravy WHERE ( id_opravy > 0 ";

        $order=" ORDER BY datum_vlozeni DESC ";

	$dotaz=mysql_query($sql.$select." ) ".$order);

	$dotaz_radku = mysql_num_rows($dotaz);

	if ( $dotaz_radku == 0){ echo "<tr><td colspan=\"".$pocet_bunek."\" >Žádné opravy v databázi neuloženy. </tr>"; }
	else
	{

	    $zobrazeno_limit="0";
  
	    while($data=mysql_fetch_array($dotaz) )
	    {
   
		if( $zobrazeno_limit >= $limit)
		{
		   // prozatimni reseni limitu
   
		   $exit ="ano";
   
		}
   
   $zobrazovat="ne";
   
   $zobrazeno="ne";
   $sekundarni_show="ne";
    
   $id_opravy=$data["id_opravy"];

   $dotaz_S1=mysql_query("SELECT * FROM opravy WHERE id_predchozi_opravy = '$id_opravy' "); 
   $dotaz_radku_S1=mysql_num_rows($dotaz_S1);
   
   // zde zjistit jestli uz se zobrazilo
   for ($p = 0; $p < count($zobrazene_polozky); ++$p)
   {
     if( $zobrazene_polozky[$p] == $id_opravy){ $zobrazeno = "ano"; }
     else{ $zobrazeno = "ne"; }
   }
   
   if( $v_reseni_filtr == 1 )
   {  	    
	  if ( ( ($data["v_reseni"] == 1) and ( $dotaz_radku_S1 == 0 ) ) )
	  { $zobrazovat="ano"; }
	  elseif ( $dotaz_radku_S1 > 0 )
	  { 
	    while($data_S1=mysql_fetch_array($dotaz_S1) )
	    { if ( $data_S1["v_reseni"] == 1 ){ $zobrazovat="ano"; $sekundarni_show="ano"; } }
	  } 	
   } // konec if v_reseni_filtr == 1
   elseif( $v_reseni_filtr == 0 ) 
   {          	    
	  if( ( ($data["v_reseni"] == 0 ) and ( $dotaz_radku_S1 == 0 ) ) )
	  { $zobrazovat="ano"; }
	  elseif ( $dotaz_radku_S1 > 0 )
	  { 
	    while($data_S1=mysql_fetch_array($dotaz_S1) )
	    { 
	      if ( $data_S1["v_reseni"] == 0 ){ $zobrazovat="ano"; $sekundarni_show="ano"; } 
	      else{ $zobrazovat="ne"; $sekundarni_show="ne"; }
	    }
	  }
	  else
	  { $zobrazovat="ne"; }
	   	   
   } // konec elseif v_reseni_filtr == 0
   
 
   if( $vyreseno_filtr == 1)
   {
     // prvne zjistime jestli jde o singl prispevek bo jestli je jich vic
     if ( ( ($data["vyreseno"] == 1) and ( $dotaz_radku_S1 == 0 ) ) )
     { $zobrazovat="ano"; }
     elseif( $dotaz_radku_S1 > 0 )
     {
      while($data_S1=mysql_fetch_array($dotaz_S1) )
      { if ( $data_S1["vyreseno"] == 1 ){ $zobrazovat="ano"; $sekundarni_show="ano"; } }
     
     }
     else
     { $zobrazovat="ne"; }
   
   } // konec if vyreseno_filtr == 0
   elseif ( $vyreseno_filtr == 0 )
   {
     // prvne zjistime jestli jde o singl prispevek bo jestli je jich vic
     if ( ( ($data["vyreseno"] == 0) and ( $dotaz_radku_S1 == 0 ) ) )
     { $zobrazovat="ano"; }
     elseif( $dotaz_radku_S1 > 0 )
     {
      while($data_S1=mysql_fetch_array($dotaz_S1) )
      { 
       if ( $data_S1["vyreseno"] == 1 ){ $zobrazovat="ne"; $sekundarni_show="ne"; } 
       else
       { $zobrazovat="ano"; $sekundarni_show="ano"; }
       
      }// konec while
     
     }// konec elseif dotaz_radku_S1
   
   } // konec elseif vyreseno_filrt == 0

      
   if ( ( $v_reseni_filtr == 99 and $vyreseno_filtr == 99 ) )
   { $zobrazovat="ano"; }
   
   if( ($zobrazovat == "ano" and $zobrazeno == "ne" and $exit != "ano" ) )
   {
   
   $zobrazene_polozky[]=$data["id_opravy"];

   $zobrazeno_limit++;
   
    $class="opravy-tab-line4";
   
   // zde zjistit jestli uz se vyresilo
   if( $dotaz_radku_S1 == 0 )
   { // rezim singl problemu
     if ( $data["vyreseno"] == 1 ){ $barva="green"; }
     elseif ( $data["v_reseni"] == 1 ){ $barva="orange"; }
     else{ $barva="red"; }
   
   } // if dotaz_radku_S1 == 0
   else
   { 
     while($data_S1=mysql_fetch_array($dotaz_S1) )
     {
       if( $data_S1["vyreseno"] == 1 ){ $barva="green"; }
       elseif( $data_S1["v_reseni"] == 1 ){ $barva="orange"; } 
       else{ $barva="red"; }
     } 
   }
   
//    $barva="red";
    
    echo "<tr>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["id_opravy"]."</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["id_predchozi_opravy"]."</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

        $id_cloveka=$data["id_vlastnika"];

        $vlastnik_dotaz=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka'");
        $vlastnik_radku=pg_num_rows($vlastnik_dotaz);

        while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
        { 
	  $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; 
	  $popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
	  $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";
	  $popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
	}

        if ( $archiv_vlastnik == 1 )
        { echo "<a href=\"vlastnici-archiv.php?find_id=".$data["id_vlastnika"]."\" "; }
        elseif ($firma_vlastnik == 1 )
        { echo "<a href=\"vlastnici2.php?find_id=".$data["id_vlastnika"]."\" "; }
        else
        { echo "<a href=\"vlastnici.php?find_id=".$data["id_vlastnika"]."\" "; }

	echo "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$data["id_vlastnika"]."</a> \n\n";
        
	echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data["datum_vlozeni"]."</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

        if ( $data["priorita"] == 0) echo "Nízká";
        elseif ( $data["priorita"] == 1) echo "Normální";
        elseif ( $data["priorita"] == 2) echo "Vysoká";
        else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
            if ( $data["v_reseni"] == 0 ) echo "Ne";
            elseif ( $data["v_reseni"] == 1 ) echo "Ano (".$data["v_reseni_kym"].") ";
            else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
            if ( $data["vyreseno"] == 0 ) echo "Ne";
            elseif ( $data["vyreseno"] == 1 ) echo "Ano (".$data["vyreseno_kym"].") ";
            else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
        if ( ( strlen($data["vlozil"]) > 1 ) ){ echo $data["vlozil"]; }
        else { echo "<br>"; }

        echo "</td>";

	echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" >
		<a href=\"opravy-zacit-resit.php?id_opravy=".$data["id_opravy"]."\" >začít řešit</a></td>";

	echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" ><a href=\"opravy-index.php?typ=1&id_vlastnika=".$data["id_vlastnika"];
	
	if( $data["id_predchozi_opravy"] == 0){ echo "&id_predchozi_opravy=".$data["id_opravy"]; }
	else{ echo "&id_predchozi_opravy=".$data["id_predchozi_opravy"]; }
	
	echo "\" >vložit odpověď</a></td>";

    echo "</tr>";

    echo "<tr><td colspan=\"".$pocet_bunek."\" class=\"opravy-tab-line3\" >".$data["text"]."</td></tr>";

   } // konec if zobrazovat == ano
   
   if( ( $sekundarni_show == "ano" and $zobrazeno == "ne" ) )
   {
   
    // $zobrazene_polozky[]=$id_opravy;
   
    $dotaz_S2=mysql_query("SELECT * FROM opravy WHERE id_predchozi_opravy = '$id_opravy' ");
    
    while($data_S2=mysql_fetch_array($dotaz_S2) )
    {
    
    // zde zjistit jestli uz se zobrazilo
   for ($p = 0; $p < count($zobrazene_polozky); ++$p)
   {
     if( $zobrazene_polozky[$p] == $id_opravy){ $zobrazeno = "ano"; }
     else{ $zobrazeno = "ne"; }
   }
   
    $zobrazene_polozky[]=$data_S2["id_opravy"];

    $id_opravy_S3=$data_S2["id_opravy"];
    
  $dotaz_S3=mysql_query("SELECT * FROM opravy WHERE id_predchozi_opravy = '$id_opravy_S3' "); 
   $dotaz_radku_S3=mysql_num_rows($dotaz_S3);
 
// neni jiste jestli barveni ma bejt zde
  
   // zde zjistit jestli uz se vyresilo
   if( $dotaz_radku_S3 == 0 )
   { // rezim singl problemu
     if ( $data_S2["vyreseno"] == 1 ){ $barva="green"; }
     elseif ( $data_S2["v_reseni"] == 1 ){ $barva="orange"; }
     else{ $barva="red"; }
   
   } // if dotaz_radku_S1 == 0
   else
   { 
     while($data_S3=mysql_fetch_array($dotaz_S3) )
     {
       if( $data_S3["vyreseno"] == 1 ){ $barva="green"; }
       elseif( $data_S3["v_reseni"] == 1 ){ $barva="orange"; } 
       else{ $barva="red"; }
     } 
   }
  
  if ( $zobrazeno == "ne" and $exit != "ano" )
  {    
  
  $zobrazeno_limit++;
   
    echo "<tr>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["id_opravy"]."</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["id_predchozi_opravy"]."</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

        $id_cloveka=$data["id_vlastnika"];

        $vlastnik_dotaz=pg_query("SELECT * FROM vlastnici WHERE id_cloveka = '$id_cloveka'");
        $vlastnik_radku=pg_num_rows($vlastnik_dotaz);

        while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
        { 
	  $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; 
	  $popis_vlastnika = " ".$data_vlastnik["prijmeni"]." ".$data_vlastnik["jmeno"].", ";
	  $popis_vlastnika .= $data_vlastnik["ulice"]." ".$data_vlastnik["mesto"].", ";
	  $popis_vlastnika .= "VS: ".$data_vlastnik["vs"]." ";
	}

        if ( $archiv_vlastnik == 1 )
        { echo "<a href=\"vlastnici-archiv.php?find_id=".$data_S2["id_vlastnika"]."\" "; }
        elseif ($firma_vlastnik == 1 )
        { echo "<a href=\"vlastnici2.php?find_id=".$data_S2["id_vlastnika"]."\" "; }
        else
        { echo "<a href=\"vlastnici.php?find_id=".$data_S2["id_vlastnika"]."\" "; }

	echo "title=\"Detail vlastníka: ".$popis_vlastnika."\" >".$data_S2["id_vlastnika"]."</a> \n\n";
        
	echo "</td>";
        // echo "<td class=\"".$class."\" >".$data_S2["text"]."</td>";
        echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" >".$data_S2["datum_vlozeni"]."</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";

        if ( $data_S2["priorita"] == 0) echo "Nízká";
        elseif ( $data_S2["priorita"] == 1) echo "Normální";
        elseif ( $data_S2["priorita"] == 2) echo "Vysoká";
        else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
            if ( $data_S2["v_reseni"] == 0 ) echo "Ne";
            elseif ( $data_S2["v_reseni"] == 1 ) echo "Ano (".$data_S2["v_reseni_kym"].") ";
            else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
            if ( $data_S2["vyreseno"] == 0 ) echo "Ne";
            elseif ( $data_S2["vyreseno"] == 1 ) echo "Ano (".$data_S2["vyreseno_kym"].") ";
            else echo "Nelze zjistit";

        echo "</td>
        <td class=\"".$class."\" style=\" color: ".$barva."; \" >";
        if ( ( strlen($data_S2["vlozil"]) > 1 ) ){ echo $data_S2["vlozil"]; }
        else { echo "<br>"; }

        echo "</td>";

	echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" >
		<a href=\"opravy-zacit-resit.php?id_opravy=".$data_S2["id_opravy"]."\" >začít řešit</a></td>";

	echo "<td class=\"".$class."\" style=\" color: ".$barva."; \" ><a href=\"opravy-index.php?typ=1&id_vlastnika=".$data_S2["id_vlastnika"];
	
	if( $data_S2["id_predchozi_opravy"] == 0){ echo "&id_predchozi_opravy=".$data_S2["id_opravy"]; }
	else{ echo "&id_predchozi_opravy=".$data_S2["id_predchozi_opravy"]; }
	
	echo "\" >vložit odpověď</a></td>";
        
//	echo "<tr><td class=\"".$class."\" colspan=\"\" >".$data_S2["text"]."</td></tr>";

    echo "<tr><td colspan=\"".$pocet_bunek."\" class=\"opravy-tab-line3\" >".$data_S2["text"]."</td></tr>";

    echo "</tr>";
    
    } // konec if zobrazeno ne
    
    } // konec while2
    
   } // konec if sekundar == 1 

  } // konec while 1

} // konec else radku == 0

//echo "</table>";

 }//konec funkce vypis_opravy

}// konec tridy opravy


//
// class print_reg_form
//

class print_reg_form
{
    
    //
    // variables
    //
    var $file_name;  //file name of generated pdf file
    var $id_cloveka; //internal key from DB, where if generate file for existing object
    
    //form vars
    var $input_ec;
    var $input_jmeno_a_prijmeni;
    var $input_adresa_odber;
    var $input_adresa_tr_byd;
    var $input_pozadovany_tarif;
    
    
    //
    //  functions
    //
    
    //
    // load_input_vars
    //
    
    public function load_input_vars(){
    
	 reset ($_POST);
	 
	 while ( list($name, $value) = each($_POST) ){
	 
	    if(preg_match("/^input_/",$name) == 1){
		
		$this->$name = htmlspecialchars($value);
	    
		//zde pripadne doplnovani pomlcek
	    
	    } //end of if(preg_math(...
	    
	 } //end of while	    
	     
    } //end of function "load_input_vars"

    //
    // generate_pdf_file
    //
    
    public function generate_pdf_file(){
	
	
	define('FPDF_FONTPATH',"include/font/");
	require_once("include/fpdf.class.php");

	//zaklad, vytvoreni objektu a pridani stranky
	$pdf=new FPDF("P","mm","A4");
	$pdf->Open();
	$pdf->AddPage();

	// ceskej arial
	$pdf->AddFont('arial','','arial.php');

	// autor a podobny hemzy

	//Nastaví autora dokumentu.
	$pdf->SetAuthor("Simelon Adminator3");

	//Nastaví tvůrce dokumentu (většinou název aplikace)
	$pdf->SetCreator("Registrační formulář");

	//Titulek dokumentu
	$pdf->SetTitle("Reg. Formulář");
	
	// vlozeni obrazku na pozadi
	$img="img2/print/2012-05-form.jpg";
	$pdf->Image($img,0,0,210);

	$pdf->SetFont('Arial','',10);

	$pdf->Cell(0,1,'',0,1);

	//zacatek formu - Ev. Cislo
	$pdf->Cell(145); 
	$pdf->Cell(50,0,$this->input_ec,0,1);
	
	//
	//sekce zakaznik
	//
	$pdf->Cell(0,70,"",0,1); 
	
	//1.radka
	$pdf->Cell(37,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_jmeno_a_prijmeni),0,0);
	
	$pdf->Cell(77,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_adresa_odber),0,1);
	
	//2.radka
	$pdf->Cell(37,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_adresa_tr_byd),0,0);
	
	$pdf->Cell(77,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_pozadovany_tarif),0,1);
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//POZNAMKA 2
	$pdf->Cell(0,10,'',0,1);
	  
	$pdf->Cell(8); 
	$pdf->MultiCell(175,7,$poznamka2,0,1);
	// $pdf->Cell(21); $pdf->Cell(5,5,$celk_cena_s_dph,0,1);
	   
	//end of inputs arrays
	
	$datum_nz = date('Y-m-d-H-i-s');
	    
	if( $this->id_cloveka > 0 )
	{ $this->file_name = "print/temp/reg-form-v3-id-".$this->id_cloveka."-".$datum_nz.".pdf"; }
	else
	{ $this->file_name = "print/temp/reg-form-v3-ec-".$this->form_ec."-".$datum_nz.".pdf"; }
	        
	$rs = $pdf->Output($this->file_name,"F");
	         
    } //end of function "generate_pdf_file"



} //end of class "print_reg_form"



?>
