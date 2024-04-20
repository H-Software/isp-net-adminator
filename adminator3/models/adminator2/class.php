<?php

class objekt_a2
{

	var $conn_mysql;

 function vypis_tab($par)
 {
   
   if ($par == 2) { echo "\n".'</table>'."\n";  }
   else  { echo "chybny vyber"; }
   
   // konec funkce
 }
   
 public static function select($es,$razeni)  
 {
  global $db_ok2;
  // co - co hledat, 1- podle dns, 2-podle ip
    
  $ds=pg_query(" SET DATESTYLE TO 'SQL, EUROPEAN' ");

  // prvne vyresime sekundarni select
  $se_id=$es;

  //global $se;
  global $order;

  if( $se_id ==1 ){ $se=''; }
  elseif( $se_id==2 ){ $se=" AND typ LIKE '1' "; }
  elseif( $se_id==3 ){ $se=" AND typ LIKE '2' "; }
  elseif( $se_id==4 ){ $se=" AND typ LIKE '3' "; }
  elseif( $se_id==5 ){ $se=" AND id_tridy > 0 "; }
  elseif( $se_id==6 ){ $se=" AND verejna !=99 "; }
  elseif( $se_id==7 ){ $se=" AND id_cloveka is null "; }
  elseif( $se_id==8 ){ $se=" AND dov_net LIKE 'n' "; }
  elseif( $se_id==9 ){ $se=" AND sikana_status LIKE 'a' "; }

  // tvoreni dotazu
  // $order=$_POST["razeni"];
 
  if ( $razeni == 1 ) { $order=" order by dns_jmeno DESC"; }
  elseif ( $razeni == 2 ){ $order=" order by dns_jmeno ASC"; }
  elseif ( $razeni == 3 ){ $order=" order by ip DESC"; }
  elseif ( $razeni == 4 ){ $order=" order by ip ASC"; }
  elseif ( $razeni == 7 ){ $order=" order by mac DESC"; }
  elseif ( $razeni == 8 ){ $order=" order by mac ASC"; }
  //# elseif ( $razeni == 9 ){ $order=" order by typ DESC"; }
  //# elseif ( $razeni == 10){ $order=" order by typ ASC"; }
  else { $order=" order by id_komplu ASC "; }	   		
 
  $pole[]=$se;
  $pole[]=$order;
  
  return $pole;
  
 } //konec funkce select
 
 //zde funkce export
 function export_vypis_odkaz()
 {

    $fp=fopen("export/objekty.xls","w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor
     fputs($fp,"<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky
     fputs($fp,"<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

     $vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='objekty' ORDER BY ordinal_position ");

     while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
     { fputs($fp,"<td><b> ".$vysledek_array_pole[0]." </b></td> \n"); }

        fputs($fp,"</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

        $vysledek = pg_query("SELECT * FROM objekty ORDER BY id_komplu ASC");

        while ($data=pg_fetch_array($vysledek) )
        {
          fputs($fp,"\n <tr>");

          fputs($fp,"<td> ".$data["id_komplu"]."</td> ");
          fputs($fp,"<td> ".$data["id_tridy"]."</td> ");
          fputs($fp,"<td> ".$data["id_cloveka"]."</td> ");
          fputs($fp,"<td> ".$data["dns_jmeno"]."</td> ");
          fputs($fp,"<td> ".$data["ip"]."</td> ");
          fputs($fp,"<td> ".$data["mac"]."</td> ");
          fputs($fp,"<td> ".$data["rra"]."</td> ");
          fputs($fp,"<td> ".$data["vezeni"]."</td> ");
          fputs($fp,"<td> ".$data["dov_net"]."</td> ");
          fputs($fp,"<td> ".$data["swz"]."</td> ");
     //     fputs($fp,"<td> ".$data["sc"]."</td> ");
          fputs($fp,"<td> ".$data["typ"]."</td> ");
          fputs($fp,"<td> ".$data["poznamka"]."</td> ");
          fputs($fp,"<td> ".$data["verejna"]."</td> ");
          fputs($fp,"<td> ".$data["ftp_update"]."</td> ");
          fputs($fp,"<td> ".$data["pridano"]."</td> ");
          fputs($fp,"<td> ".$data["id_nodu"]."</td> ");
          fputs($fp,"<td> ".$data["rb_mac"]."</td> ");
          fputs($fp,"<td> ".$data["rb_ip"]."</td> ");
          fputs($fp,"<td> ".$data["pridal"]."</td> ");
          fputs($fp,"<td> ".$data["upravil"]."</td> ");
          fputs($fp,"<td> ".$data["sikana_status"]."</td> ");
          fputs($fp,"<td> ".$data["sikana_cas"]."</td> ");
          fputs($fp,"<td> ".$data["sikana_text"]."</td> ");
          fputs($fp,"<td> ".$data["vip_snat"]."</td> ");
          fputs($fp,"<td> ".$data["vip_snat_lip"]."</td> ");

          fputs($fp,"</tr> \n ");
          // echo "vysledek_array: ".$vysledek_array[$i];

        }

        fputs($fp,"</table>");   // Zapíšeme do souboru konec tabulky
        fclose($fp);   // Zavřeme soubor

        echo "<span style=\"padding-left: 25px; padding-right: 20px; \" >";
        echo "<a href=\"export\objekty.xls\">export dat zde</a></span>";
	 
 
 } //konec funkce vypis odkaz
 
 public static function vypis_razeni()
 {
 
   $input_value="1";
   $input_value2="2";

 for ($i=1; $i < 6 ; $i++):

    //vnejsi tab
    echo "<td>";

    //vnitrni tab
    echo "\n <table><tr><td>";

    if( $i=="3" or $i=="4" ){ echo ""; }
    else
    {

      echo "\n\n <input type=\"radio\" ";
         if ( ($_GET["razeni"]== $input_value) ){ echo " checked "; }
      echo "name=\"razeni\" value=\"".$input_value."\" onClick=\"form1.submit();\" > ";

     // obr, prvni sestupne -descent
     echo "<img src=\"img2/ses.png\" alt=\"ses\" width=\"15px\" height=\"10px\" >";
      if ($i!=5){ echo " | "; }
     echo "</td> \n\n <td>";

     echo "<input type=\"radio\" ";
         if ( ($_GET["razeni"]== $input_value2) ){ echo " checked "; }
     echo " name=\"razeni\" value=\"".$input_value2."\" onClick=\"form1.submit();\"> \n";

     // obr, druhy vzestupne - asc
     echo "<img src=\"img2/vzes.png\" alt=\"vzes\" width=\"15px\" height=\"10px\" >";

    }

    // vnitrni tab
    echo "\n </td></tr></table> \n\n";

    $input_value=$input_value+2;
    $input_value2=$input_value2+2;

    // konec vnitrni tab
    echo "</td>";

 endfor;
 
 }
 
 function zjistipocet($mod,$id)
 {
    if ( $mod == 1 ) //wifi sit ...
    {
      //prvne vyberem wifi tarify...
      $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");
      
      $i = 0;
      while( $data_f = $dotaz_f->fetch_array() )
      {
         if( $i == 0 ){ $tarif_sql .= " AND ( "; }
         if( $i > 0 ){ $tarif_sql .= " OR "; }
			    
         $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
		 
         $i++;
      }
					  
      if( $i > 0 ){ $tarif_sql .= " ) "; }
    }
    elseif ( $mod == 2 ) //fiber sit ...
    { 
      $dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");
      
      $i = 0;
      while( $data_f = $dotaz_f->fetch_array() )
      {
         if( $i == 0 ){ $tarif_sql .= " AND ( "; }
         if( $i > 0 ){ $tarif_sql .= " OR "; }
			    
         $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
		 
         $i++;
      }
					  
      if( $i > 0 ){ $tarif_sql .= " ) "; }    
    }

    $dotaz = pg_query("SELECT id_cloveka FROM objekty WHERE ( id_cloveka = '".intval($id)."' ".$tarif_sql." ) ");     
    $radku = pg_num_rows($dotaz);
  
  return $radku;
  
 } //konec funkce zjistipocet
 
 function vypis($sql,$co,$id,$dotaz_final = "")
 {
   global $db_ok2;
    
   $list=$_GET["list"];

    if ( $co==3 ) //wifi sit ...vypis u vlastniku (dalsi pouziti nevim)
    { 
      //prvne vyberem wifi tarify...
	  try {
		$dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '0' ");
	  } catch (Exception $e) {
			die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	  }
      
      $i = 0;
	  
      while( $data_f = $dotaz_f->fetch_array() )
      {
         if( $i == 0 ){ $tarif_sql .= "AND ( "; }
         if( $i > 0 ){ $tarif_sql .= " OR "; }
			    
         $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
		 
         $i++;
      }
					  
      if( $i > 0 ){ $tarif_sql .= " ) "; }
				       
     $dotaz=pg_query($db_ok2,"SELECT * FROM objekty WHERE id_cloveka='".intval($id)."' ".$tarif_sql); 
    
    }
    elseif ( $co==4 ) //fiber sit ...vypis pouze u vlastniku
    { 
	  try {
		$dotaz_f = $this->conn_mysql->query("SELECT id_tarifu FROM tarify_int WHERE typ_tarifu = '1' ");
	  } catch (Exception $e) {
			die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	  }
      
      $i = 0;
	  
      while( $data_f = $dotaz_f->fetch_array() )
      {
         if( $i == 0 ){ $tarif_sql .= "AND ( "; }
         if( $i > 0 ){ $tarif_sql .= " OR "; }
			    
         $tarif_sql .= " id_tarifu = ".$data_f["id_tarifu"]." ";
		 
         $i++;
      }
					  
      if( $i > 0 ){ $tarif_sql .= " ) "; }
				       
     $dotaz=pg_query($db_ok2,"SELECT * FROM objekty WHERE id_cloveka='".intval($id)."' ".$tarif_sql); 
    
    }
    else
    { $dotaz= pg_query($db_ok2, $dotaz_final); }
 
  $radku=pg_num_rows($dotaz);
  
 if ($radku==0) 
 {
 
  //if( ( ( $co == 3 ) or ( $co == 4 ) ) )
  
  if( $co == 3 or $co == 4 )
  {
    echo "<tr><td colspan=\"9\" >";
    echo "<span style=\"color: #555555; \">Žádný objekt není přiřazen. </span></td></tr>";
  }
  else
  {
   echo "<tr><td colspan=\"8\" ><span style=\"color: red; \">Nenalezeny žádné odpovídající data dle hledaného \"".htmlspecialchars($sql)."\" ";
   // echo " (dotaz: ".$dotaz_final.") ";
   echo "</td></tr>";
  }
 
 }
 else
 {
 
   while (  $data=pg_fetch_array($dotaz) ) 
   {
    // echo $data[sloupec1]." ".$data[sloupec2]; 
    // echo "<br />";
   
//    if( $data["id_tridy"] > 0 ){ $garant=1; }
    if( $data["verejna"] <> 99 ){ $verejna=1; }
   
/*
   if ( $garant==1)
   {
    $id_tridy=$data["id_tridy"];
    //zjistime sirku pasma
    $dotaz_g = pg_exec($db_ok2, "SELECT * FROM tridy WHERE id_tridy = '$id_tridy' ");
   
    while (  $data_g=pg_fetch_array($dotaz_g) ) { $sirka=$data_g["sirka"]; }
   }
*/
         
   //zacatek radny a prvni bunka
    echo "\n <tr>"."<td class=\"tab-objekty2\">".$data["dns_jmeno"]."</td> \n\n";

     $pridano=$data["pridano"];

    // treti bunka - ip adresa
    if ($verejna==1)
    { 
	if ( $data["vip_snat"] == 1)
	{ echo "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"orange\" >".$data["ip"]." </td> \n"; }
	elseif( $data["tunnelling_ip"] == 1)
	{ echo "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"#00CC33\" >".$data["ip"]." </td> \n"; }
	else
	{ echo "<td colspan=\"2\" class=\"tab-objekty2\" bgcolor=\"#FFFF99\" >".$data["ip"]." </td> \n"; }
    }
    else
    { echo "<td colspan=\"2\" class=\"tab-objekty2\">".$data["ip"]."</td> \n"; }
    
    // druha bunka - pozn
    echo "<td class=\"tab-objekty2\" align=\"center\" ><span class=\"pozn\"> <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
    echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." , Vytvořeno: ".$pridano." ');\" ></span></td> \n";
	       
    // 4-ta bunka - mac
    echo "<td class=\"tab-objekty2\">".$data["mac"]."</td> \n";
    	       
    // 5-ta typ
    if ( $data["typ"] == 1 ){ echo "<td class=\"tab-objekty\">"."daně"."</td> \n"; }
    elseif ( $data["typ"] ==2 ){ echo "<td class=\"tab-objekty\" bgcolor=\"#008000\" ><font color=\"#FFFFFF\">"." free "."</font></td> \n"; }
    elseif ( $data["typ"] ==3 ){ echo "<td class=\"tab-objekty\" bgcolor=\"yellow\" >"." ap "."</td> \n"; }
    else { echo "<td class=\"tab-objekty\" >Error </td> \n"; }
    
    // rra - client ip -- CISLO portu
    echo "<td class=\"tab-objekty2\" align=\"center\" ><span style=\"\"> ";
    
    global $mod_vypisu; 
    
    if( $mod_vypisu == 2)
    {
     echo "".$data["port_id"]."";
    }
    else
    { 
     if( ( strlen($data["client_ap_ip"]) < 1 ) )
     { echo "&nbsp;"; }
     else { echo $data["client_ap_ip"]; }
    }
    
    echo "</span></td> \n";

    //oprava a mazani
    global $update_povolen;
    
     $update_mod_vypisu = $_GET["mod_vypisu"];
      
      $id_tarifu = $data["id_tarifu"];
      
      $dotaz_update = $this->conn_mysql->query("SELECT typ_tarifu FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ");
      $rs_update = $dotaz_update->mysql_num_rows;
              
      if( $rs_update <> 1 ){ echo "Chyba! Nelze specifikovat tarif!"; }
      
      while($data_update = $dotaz_update->fetch_array())
      { 
        if( $data_update["typ_tarifu"] == 1 )
	{ $update_mod_vypisu = 2; }
	else
	{ $update_mod_vypisu = 1; }
      }
     
    // 6-ta update
    if ( !( $update_povolen =="true") )
    { echo "<td class=\"tab-objekty2\" style=\"font-size: 10px; font-family: arial; color: gray;\">Upravit</td> \n"; }
    else
    {
      echo "<td class=\"tab-objekty2\" > <form method=\"POST\" action=\"objekty-add.php\" >";
      echo "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_komplu"]."\" >";
      
      
      echo "<input type=\"hidden\" name=\"mod_objektu\" value=\"".$update_mod_vypisu."\" >";
      
      echo "<input class=\"\" type=\"submit\" value=\"update\" >";
        
      echo "</td></form> \n";
    }
     
    // 7-ma smazat
    global $mazani_povoleno;
     
    if ( !( $mazani_povoleno =="true") )
    { echo "<td class=\"tab-objekty2\" style=\"font-size: 10px; font-family: arial; color: gray;\">Smazat</td>"; }
    else
    { 
     echo "<td class=\"tab-objekty2\" > <form method=\"POST\" action=\"objekty-erase.php\" >";
     echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_komplu"]."\" >";
     echo "<input class=\"\" type=\"submit\" value=\"smazat\" >";
    
     echo "</td> </form> \n";   
    }
     
    // 8-ma typ objektu :)
    $id=$data["id_komplu"];
    $class_id=$data["id_tridy"];
    
    global $garant_akce;
    
    // generovani tridy    
    if( $data["typ"] == 3){ echo ""; }
    else 
    { 
      {	echo "<td class=\"tab-objekty2\"><font color=\"red\">"." peasant "."</font></td> \n"; }      
    }

    // prirava promennych pro tresty a odmeny
    if( $data["dov_net"] =="a" ){ $dov_net="<font color=\"green\">NetA</font>"; }
    else{ $dov_net="<font color=\"orange\">NetN</font> \n"; }
    
    if( ereg("a",$data["sikana_status"]) )
    { 
	$sikana_status_s = "<span class=\"obj-link-sikana\" >".
			    "<a href=\"http://damokles.simelon.net:8009/index.php".
			    "?sc=".intval($data["sikana_cas"])."&st=".urlencode($data["sikana_text"])."\" target=\"_new\" >".
			    "Sikana-A (".$data["sikana_cas"].")</a></span>\n"; 
    
    } 
    else{ $sikana_status_s="<span style=\"color: green;\" >Sikana-N</span>"; }

    //tresty a odmeny - 6 bunek
    if( $data["typ"] == 3 )
    { echo "<td class=\"tab-objekty2\" colspan=\"5\" bgcolor=\"yellow\" align=\"center\"> ap-čko jaxvine </td> \n"; }
    else 
    { 
      echo "<td class=\"tab-objekty2\" >".$dov_net."</td>";
     
      //test objetktu
      echo "<td class=\"tab-objekty2\" >";
    
      if( $update_mod_vypisu == 2 )    
      {
	echo "<a href=\"objekty-test.php?id_objektu=".$data["id_komplu"]."\" >test</a>";
      }
      else
      { echo "<br>"; }
      
      echo "</td> \n";   
      //zde tarif 2 gen.
      echo "<td class=\"tab-objekty2\" >";
       $id_tarifu = $data["id_tarifu"];
       
       //dodelat klikatko pro sc
       //{ $tarif="<span class=\"tarifsc\"><a href=\"https://trinity.simelon.net/monitoring/data/cat_sc.php?ip=".$data["ip"]."\" target=\"_blank\" >sc</a></span>"; } 
    
       $tarif_f = mysql_query("SELECT barva, id_tarifu, zkratka_tarifu FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ");
       $tarif_f_r = mysql_num_rows($tarif_f);
              
       if( $tarif_f_r <> 1){ echo "<span style=\"font-weight: bold; color: red;\" >E</span>"; }
       else
       {
        while($data_f = mysql_fetch_array($tarif_f))
	{ 
	    echo "<span style=\"color: ".$data_f["barva"]."; \" >";
	    echo "<a href=\"admin-tarify.php?id_tarifu=".$data_f["id_tarifu"]."\" >".$data_f["zkratka_tarifu"]."</a>";
	
	    echo "</span>\n";
        }	 
       }
      echo "</td>\n"; 
      
      echo "<td class=\"tab-objekty2\" colspan=\"2\" >".$sikana_status_s."</td>\n";
    }
    
    echo "</tr>\n<tr>\n";
    
    // tady uz asi druhej radek :) 
    echo "<td class=\"tab-objekty\" colspan=\"2\" >"; 
    
    $id_nodu=$data["id_nodu"];
          
    $vysledek_bod = mysql_query("SELECT jmeno FROM nod_list WHERE id='".intval($id_nodu)."' ");
    $radku_bod = mysql_num_rows($vysledek_bod);
				      
     if($radku_bod==0) echo "<span style=\"color: gray; \">přípojný bod nelze zjistit </span>";
     else
     {
       while ($zaznam_bod=mysql_fetch_array($vysledek_bod) )
       { 
        //pouze text 
	//echo "<span class=\"objekty-2radka\">NOD: ".$zaznam_bod["jmeno"]."</span> "; 

	echo "<span class=\"objekty-2radka objekty-odkaz\">NOD: ".
	     "<a href=\"topology-nod-list.php?find=".$zaznam_bod["jmeno"]."\" >".
	     $zaznam_bod["jmeno"]."</a></span> "; 
       }
     }
    
    echo "</td>";
    
     // sem historii
    echo "<td class=\"tab-objekty\" ><span class=\"objekty-2radka\" style=\"\" > H: ";
    echo "<a href=\"archiv-zmen.php?id=".$id."\" >".$id."</a>";
    echo " </span>";
	
    echo "</td> \n";
	
    // id vlastnika
    echo "<td class=\"tab-objekty\" align=\"center\" ><span class=\"objekty-2radka\" > \n";
     
    $id_cloveka=$data["id_cloveka"];
    
    $vlastnik_dotaz=pg_query("SELECT firma, archiv FROM vlastnici WHERE id_cloveka = '".intval($id_cloveka)."'");
    $vlastnik_radku=pg_num_rows($vlastnik_dotaz);
    while ($data_vlastnik=pg_fetch_array($vlastnik_dotaz))
    { $firma_vlastnik=$data_vlastnik["firma"]; $archiv_vlastnik=$data_vlastnik["archiv"]; }
    
    if ( $archiv_vlastnik == 1)
    { echo "V: <a href=\"vlastnici-archiv.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span> </td> \n"; }
    else
    { echo "V: <a href=\"vlastnici2.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span></td> \n"; }    		
    
    if( $update_mod_vypisu == 2 )
    { echo "<td class=\"tab-objekty\" colspan=\"3\" > <br></td>";  }
    else
    {
     if ( !($co==3 ) )
     {
      echo "<td class=\"tab-objekty\" colspan=\"2\" > <span class=\"objekty-2radka\" >";
       //if( (strlen($data["rb_mac"]) > 0) ){ echo $data["rb_mac"]; }
       echo "&nbsp;";
      echo "</span></td> \n";
    
     //echo "<td><br>b</td>";
    
      echo "<td class=\"tab-objekty\" colspan=\"1\" ><span class=\"objekty-2radka\" >";
       //if( (strlen($data["rb_ip"]) > 0) ){ echo $data["rb_ip"]; }
       echo "&nbsp;";
      echo "</span></td> \n";
     }
    
    }
    // kdo pridal a kdo naposledy upravil 
    echo "<td class=\"tab-objekty\" colspan=\"1\" align=\"center\" ><span class=\"objekty-2radka\" >";
       if( (strlen($data["pridal"]) > 0) ){ echo $data["pridal"]; }
       else{ echo "<span style=\"color: #CC3366;\" >nezadáno</span>"; }
     echo "</span></td> \n";
     
    echo "<td class=\"tab-objekty\" colspan=\"1\" align=\"center\" ><span class=\"objekty-2radka\" >";
       if( (strlen($data["upravil"]) > 0) ){ echo $data["upravil"]; }
       else{ echo "<span style=\"color: #CC3366;\" >nezadáno</span>"; }
    echo "</span></td> \n";
    
    echo "<td class=\"tab-objekty\" >&nbsp;</td> \n";
    
    // kdy se objekty pridal
    //prvne to orezem
    $orez= $pridano; 
    $orezano = explode(':', $orez); 
    $pridano_orez=$orezano[0].":".$orezano[1];
    
    echo "<td class=\"tab-objekty\" colspan=\"3\" ><span class=\"objekty-2radka\" >".$pridano_orez."</span></td>
    <td class=\"tab-objekty\" >
     <form method=\"POST\" action=\"/adminator3/print/reg-form-pdf.php\" >
        <input type=\"hidden\" name=\"id_objektu\" value=\"".intval($data["id_komplu"])."\" >
	<input type=\"submit\" name=\"odeslano_form\" value=\"R.F.\">
     </form>
    </td>\n";
    
     global $odendani_povoleno; 
      
    //sem odendat
    if ( $co==3 ) 
    { 
    
     if ( $odendani_povoleno )
     {
      echo "<td colspan=\"4\" ><a href=\"vlastnici2-obj-erase.php?id_komplu=".$data["id_komplu"]."\">Odendat</a> </td> \n";
     }
     else
     {
	echo "<td colspan=\"4\" style=\"font-size: 10px; font-family: arial; color: gray; \">
	<div style=\"text-align: center; \">odendat</div> </td> \n"; 
     }
    
    }
    elseif( $co==4 ) //opticky rezim
    {

     if ( $odendani_povoleno )
     {
      echo "<td colspan=\"\" ><a href=\"vlastnici2-obj-erase.php?id_komplu=".$data["id_komplu"]."\">Odendat</a> </td> \n";
     }
     else
     {
	echo "<td colspan=\"\" style=\"font-size: 10px; font-family: arial; color: gray; \">
	<div style=\"text-align: center; \">odendat</div> </td> \n"; 
     }
    }
     
    echo "</span>";
    // konec druhyho radku
     echo "</tr> \n";
     
     $verejna=0; 
     $garant=0;
   
     } // konec while
  
    } //konec else
   
   } // konec funkce
   
    
   
// konec class-y objekt  

}

class vlastnik
{
	var $conn_mysql;

	var $conn_pgsql;

   function vypis_tab ($par)
    {
       if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; }
	elseif ($par == 2) { echo "\n".'</table>'."\n"; }
	else	{	echo "chybny vyber"; }
	 
    // konec funkce vypis_tab	 
    }
	

    function vypis ($sql,$co,$mod,$dotaz_source)
    {
    
		$objekt = new objekt_a2();
		$objekt->conn_mysql = $this->conn_mysql;
    
    // co - co hledat, 1- podle dns, 2-podle ip , 3 - dle id_vlastnika
	         
    $dotaz=pg_query($this->conn_pgsql, $dotaz_source);	      
    
	if($dotaz !== false){
		$radku=pg_num_rows($dotaz); 
	}
	
    if ($radku==0) echo "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
	 else
	   {
		   
	while( $data=pg_fetch_array($dotaz) ) {
			  
	echo "<tr><td colspan=\"14\"> <br> </td> </tr>
    
	    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" >[".$data["id_cloveka"]."] ".$data["nick"]."</td>
	    
		<td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"8%\" >"; 
		
		
		echo "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";
		
		// sem mazani
		global $vlastnici_erase_povolen;
		
		if ( ! ( $vlastnici_erase_povolen == "true" ) )
		    { echo "<span style=\"\" > smazat </span> "; }
		else
		{
		
		echo "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
		echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
		echo "<input type=\"submit\" value=\"Smazat\" >";
										    
		echo "</form> \n";
											
		}
		
		echo "</td><td class=\"vlastnici-td-black\" >";
		 
		global $vlastnici_update_povolen;
		 
		 // 6-ta update
				
		 if ( !( $vlastnici_update_povolen =="true") )
		 { echo "<span style=\"color: gray;\" >  upravit  </span> \n"; }
		else
		 { 
		 echo " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
		 echo "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
		 echo "<input type=\"submit\" value=\"update\" >";
					 
		 echo "</form> \n";
		 }
		
		echo "</td> </tr> </table>";
		
	    echo "  </td> 
	    </tr>
	    
	    <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>";
	    
	    echo $data["ulice"]."  ";
	    	    
	    echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
	    
	    echo " <br> ".$data["mesto"]." ".$data["psc"]."</td>";
	    
	    echo "<td colspan=\"11\">icq: ".$data["icq"]." <br>
	    mail: ".$data["mail"]." <br>
	    tel: ".$data["telefon"]." </td>
	    
	    </tr>
    
	    ";


    $id=$data["id_cloveka"];
    $id_f=$data["fakturacni"];
    
    // tady asi bude generovani fakturacnich udaju
    if ( ( $id_f > 0 ) )
    {

	fakturacni::vypis($id_f,$id);
    
    }
    // $sql="%";	    
    $co="3";
	    
    // $id=$data["id_cloveka"];
    
    // print "debug: id: $id";

    
    echo "<tr><td colspan=\"9\" >";

      echo "<table border=\"0\" width=\"100%\" >";
        
	$objekt->vypis($sql, $co, $id, "");

      echo "</table>";
      
    echo "</td></tr>\n\n";
    
    $pocet_wifi_obj = $objekt->zjistipocet(1,$id);
    
    $pocet_fiber_obj = $objekt->zjistipocet(2,$id);

    if( $pocet_wifi_obj > 0 or $pocet_fiber_obj == 0 )
    {
     //objekty wifi
     $co="3";
		
     echo "<tr>
	    <td colspan=\"1\" bgcolor=\"#99FF99\" align=\"center\" >W
	    <td colspan=\"10\" bgcolor=\"#99FF99\" >";
      echo "<table border=\"0\" width=\"100%\" >";
        
      $objekt->vypis($sql,$co,$id,"");
	    
      echo "</table>";
     echo "</td></tr>";
    }
   
    if( $pocet_fiber_obj > 0 )
    {
    
     //objekty fiber
     $co="4";

    echo "<tr><td colspan=\"9\" bgcolor=\"fbbc86\"  >";
		
    // echo "<tr>";
    // echo "<td colspan=\"1\" bgcolor=\"fbbc86\" align=\"center\" >F</td>";
    // echo "<td colspan=\"10\" bgcolor=\"fbbc86\" >";
	   
      echo "<table border=\"0\" width=\"100%\" >";
        
      $objekt->vypis($sql,$co,$id);
	    
      echo "</table>";
    
     echo "</td></tr>";
    }
        	    
    
    //tady dalsi radka asi
/*    
    $voip = new voip();
    
    $id_vlastnika = $data["id_cloveka"];
    
    //$dotaz_sql = "SELECT * FROM voip_cisla WHERE id_vlastnika = '".intval($id_vlastnika)."' ";
      
    //$voip_radku = $voip->vypis_cisla_query($dotaz_sql);
   
    if ( $voip_radku > 0)
    {
	echo "<tr>";
    
	echo "<td colspan=\"14\" ><div style=\"padding-top: 10px; padding-bottom: 10px; \">";
    
	$voip->vypis_cisla("2");
    
	echo "</div></td>";
    
	echo "</tr>\n\n";
    
    }
*/
    
    echo "<tr>\n";
    
    echo "<td colspan=\"14\">";

    echo "<span style=\"margin: 25px; \">další funkce:</span>\n\n";
	
    echo "<a href=\"vlastnici2-add-obj.php?mod=1&id_vlastnika=".$data["id_cloveka"]."\" >přidání objektu</a>";	
	
    echo "<span style=\"margin: 25px; \"></span>";
    
    echo "<a href=\"platby-vypis.php?id_vlastnika=".$data["id_cloveka"]."\" > výpis plateb - starý (do 2/2012)</a>"; 
    
    echo "<span style=\"margin-left: 20px; \">".
            "<a href=pohoda_sql/phd_list_fa.php?id_vlastnika=".$data["id_cloveka"]."\" > výpis plateb - nový (od 3/2012)</a>".
	 "</span>";
                                                      
    echo "<span style=\"margin: 15px; \"></span>";
      
    if ( ( $data["fakturacni"] > 0 ) )
    { echo " přidání fakturační adresy "; }
    else
    { echo "<a href=\"vlastnici2-add-fakt.php?id_vlastnika=".$data["id_cloveka"]."\" > přidání fakt. adresy </a>"; }
    
    echo "<span style=\"margin: 15px; \"></span>";
    
    if ( ( $data["fakturacni"] > 0 ) )
    { echo "<a href=\"vlastnici2-erase-f.php?id=".$data["fakturacni"]."\" > smazání fakt. adresy </a>"; }
    else
    { echo " smazání fakt. adresy " ;}
    
    echo "<span style=\"margin: 15px; \" ></span>";
    
    if ( ( $data["fakturacni"] > 0 ) )
    { echo "<a href=\"vlastnici2-change-fakt.php?id=".$data["fakturacni"]."\" > úprava fakt. adresy </a>"; }
    else
    { echo " úprava fakt. adresy "; }

    echo "<span style=\"margin: 25px; \" ></span>";
    
    echo "<a href=\"vlastnici-gen-xml.php?id_klienta=".$data["id_cloveka"]."\" > import klienta do Pohody (Adresář)</a>";
		
    //konec bunky/radky
    echo "</td></tr>";

    //druha radka
    echo "<tr>";

         echo "<td colspan=\"14\" >";
	    
	    echo "<table border=\"0\" width=\"100%\">";
	    
//h
	    echo "<tr>";

            $orezano = explode(':', $data["pridano"]);
            $pridano=$orezano[0].":".$orezano[1];


            echo "<td colspan=\"1\" >";

            echo "datum přidání: ".$pridano." ";

            echo "</td>";

            echo "<td align=\"center\" >";

        echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
        echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

            echo "</td>";

            echo "<td colspan=\"1\" >";
            
            /*
                echo "<form method=\"POST\" action=\"platby-akce.php\" >";

                echo "<input type=\"hidden\" name=\"firma\" value=\"1\" >";
                echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";
                    
		echo "<input type=\"submit\" name=\"akce\" value=\"Vložení hotovostní platby\" >";

                echo "</form>";
	    */
            echo "</td>";

            echo "<td colspan=\"1\" >";

                echo "<form method=\"POST\" action=\"vypovedi-vlozeni.php\" >";

                echo "<input type=\"hidden\" name=\"firma\" value=\"1\" >";
                echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";

                echo "<input type=\"submit\" name=\"akce\" value=\"Vložení žádosti o výpověď\" >";

                echo "</form>";
            echo "</td>";

            echo "<td colspan=\"1\">";
	    
		// zde dalsi veci
		echo "<span style=\"color: gray; padding-left: 10px; \" >H: </span>";
		echo "<a href=\"archiv-zmen.php?id_cloveka=".$data["id_cloveka"]."\">".$data["id_cloveka"]."</a>";
	    
	    echo "</td>";

	    echo "<td>
		    <form action=\"opravy-vlastnik.php\" method=\"get\" >
		    <input type=\"hidden\" name=\"typ\" value=\"2\" >
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
		    
	    <input type=\"submit\" name=\"ok\" value=\"Zobrazit závady/opravy \" ></form>";
	    echo "</td>";	            


	    echo "<td>
		    <form action=\"opravy-index.php\" method=\"get\" >
		    <input type=\"hidden\" name=\"typ\" value=\"1\" >
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
		    
	    <input type=\"submit\" name=\"ok\" value=\"Vložit závadu/opravu \" ></form>";
	    echo "</td>";	            

	    echo "</tr></table>";    

//h
    
	    echo "</td>";
	
            echo "</tr>";


	//konec while
	}
	
	// konec else
	}
    
    
    // konec funkce vypis
    }

//konec class-y vlastnik
}

class vlastnik2_a2
{
   var $conn_mysql;

   var $logger;

   var $level;
       
   var $export_povolen;

   public static function vypis_tab ($par)
   {
     if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; }
     elseif ($par == 2) { echo "\n".'</table>'."\n"; }
     else    { echo "chybny vyber"; }
    // konec funkce vypis_tab
   }

   // $dotaz_final - for pq_query
   function vypis ($sql,$co,$dotaz_final)
   {
				
    // co - co hledat, 1- podle dns, 2-podle ip					    									    
    $dotaz=pg_query($dotaz_final);

	if($dotaz !== false){
    	$radku=pg_num_rows($dotaz);
	}

    if($radku==0) echo "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
    else
    {

     while( $data=pg_fetch_array($dotaz) ) 
     {
	    echo "<tr><td colspan=\"16\"> <br> </td> </tr>
	    <tr>
		<td class=\"vlastnici-td-black\"><br></td>
		 <td class=\"vlastnici-td-black\" colspan=\"3\" width=\"\" >
	    
	    id: [".$data["id_cloveka"]."]".
	    
	     ", Účetní index: [";
	     
	     if($data["archiv"] == 1)
	     {
	        echo "27VYŘ";
	     }
	     elseif(( ($data["billing_freq"] == 1) and ($data["fakturacni"] > 0) ) )
	     {
	        echo "37";
	     }
	     elseif( $data["billing_freq"] == 1 )
	     { //ctvrtletni fakturace
	        echo "47";											
	     }
	     elseif( ($data["fakturacni"] > 0) )
	     { //faturacni
	           echo "27";
	     }
	     else
	     {  //domaci uzivatel
	           echo "27DM";
	     }
	     
	     echo sprintf("%05d", $data["ucetni_index"]);
	     
	     echo "], Splatnost ke dni: [".$data["splatnost"]."]</td>
	    
	    <td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
	
	    <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
	    <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"\" >";
	
	    echo "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";
	
	// sem mazani
	global $vlastnici_erase_povolen;
	
	if( ! ( $vlastnici_erase_povolen == "true" ) )
	{ echo "<span style=\"\" > smazat </span> "; }
	else
	{
	    echo "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
	    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
	    echo "<input type=\"submit\" value=\"Smazat\" >"."</form> \n";
	}
	
	echo "</td>
	<td class=\"vlastnici-td-black\" >";
	
	global $vlastnici_update_povolen;
	// 6-ta update
	if ( !( $vlastnici_update_povolen =="true") )
	{ echo "<span style=\"\" >  upravit  </span> \n"; }
	else
	{
	 echo " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
	 echo "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
	 echo "<input type=\"submit\" value=\"update\" ></form> \n";
	}
	
	 echo "</td> </tr></table>"; 
	 echo "</td></tr>";
	 
	 echo "<tr>";
	 echo "<td class=\"vlastnici-td-black\" ><br></td>";
	 echo "<td class=\"vlastnici-td-black\" colspan=\"1\">Datum podpisu:  ";
	 
	if ( (strlen($data["datum_podpisu"]) > 0) )
	{
	 list($datum_podpisu_rok,$datum_podpisu_mesic,$datum_podpisu_den) = explode("-",$data["datum_podpisu"]);	 
	  $datum_podpisu=$datum_podpisu_den.".".$datum_podpisu_mesic.".".$datum_podpisu_rok;
	 echo $datum_podpisu;
	}
	  
	 echo "</td>";
	 
	 echo "<td class=\"vlastnici-td-black\" colspan=\"1\">Četnost Fa: ";
	    if( $data["billing_freq"] == 0 )
	    { echo "měsíční"; }
	    elseif( $data["billing_freq"] == 1 )
	    { echo "čtvrtletní"; }
	    else
	    { echo "N/A"; }
	    
	 echo "</td>";
	 
	 echo "<td class=\"vlastnici-td-black\" colspan=\"6\">Fakt. skupina: ";
	 
	 $fakturacni_skupina_id=$data["fakturacni_skupina_id"];
	 
	 $dotaz_fakt_skup=$this->conn_mysql->query("SELECT nazev, typ FROM fakturacni_skupiny WHERE id = '".intval($fakturacni_skupina_id)."' ");
	 $dotaz_fakt_skup_radku=$dotaz_fakt_skup->num_rows;
		 
	 if( ( $dotaz_fakt_skup_radku < 1 ) ){ echo " [žádná fakt. skupina] "; }
	 else
	 { 
	   while( $data_fakt_skup=$dotaz_fakt_skup->fetch_array() )
	   { $nazev_fakt_skup = $data_fakt_skup["nazev"]; $typ_fakt_skup = $data_fakt_skup["typ"]; }  
	 
	 echo " [".$nazev_fakt_skup;
	   if ( $typ_fakt_skup == 2){ echo " (FÚ) "; }
	   else{ echo " (DÚ) "; }
	 echo "] ";
	 
	 }
	  
	 echo " </td>";
	 echo "<td class=\"vlastnici-td-black\" colspan=\"7\">";
	 
	 echo "Smlouva: ";
	 
	   if( $data["typ_smlouvy"] == 0){ echo "[nezvoleno]"; }
	   elseif( $data["typ_smlouvy"] == 1){ echo "[na dobu neurčitou]"; }
	   elseif( $data["typ_smlouvy"] == 2)
	   { 
	    echo "[s min. dobou plnění]"." ( do: ";
	    list($trvani_do_rok,$trvani_do_mesic,$trvani_do_den) = explode("-",$data["trvani_do"]);
	    $trvani_do=$trvani_do_den.".".$trvani_do_mesic.".".$trvani_do_rok;
	    
	    echo $trvani_do." )";    
	   }
	   else{ echo "[nelze zjistit]"; }
	 
	 echo "</td>";
	 echo "</tr>";
	
	 //zde treti radek
	 echo "<tr>\n";
	 echo "<td class=\"vlastnici-td-black\" ><br></td>\n";
	 echo "<td class=\"vlastnici-td-black\" colspan=\"1\">
		<div style=\"float: left; \">Pozastavené fakturace:</div>  ";

	 echo "<div style=\"text-align: right; padding-right: 20px;\">";
	 
	 if( $data["billing_suspend_status"] == 1)
	 { echo "Ano"; }
	 elseif( $data["billing_suspend_status"] == 0)
	 { echo "Ne"; }
	 
	 echo "</div>";
	 echo "</td>";	
	
	 if( $data["billing_suspend_status"] == 1)
	 {
	    //dalsi info o pozast. fakturacich
	    
	    echo "<td class=\"vlastnici-td-black\">od kdy: <span style=\"padding-left: 20px;\">";
		if( (strlen($data["billing_suspend_start"]) > 0) or ($data["billing_suspend_start"] != NULL) )
		{ echo htmlspecialchars($data["billing_suspend_start_f"]); }
		else
		{ echo "není zadáno"; }
		
	    echo "</span></td>";
	    
	    //doba
	    echo "<td class=\"vlastnici-td-black\" colspan=\"3\">do kdy: <span style=\"padding-left: 20px;\">";
	    
	    if( (strlen($data["billing_suspend_stop"]) > 0) or ($data["billing_suspend_stop"] != NULL) )
	    { echo htmlspecialchars($data["billing_suspend_stop_f"]); }
	    else
	    { echo " není zadáno "; }
	 
	    echo "</span></td>";
	    
	    //důvod
	    echo "<td class=\"vlastnici-td-black\" colspan=\"5\">důvod: <span style=\"padding-left: 20px;\">";
	    
	    if( strlen($data["billing_suspend_reason"]) == 0)
	    { echo "není zadáno"; }
	    else
	    { echo htmlspecialchars($data["billing_suspend_reason"]); }
	     
	    echo "</span></td>";
	    
	 } 
	 else
	 {
	    echo "<td class=\"vlastnici-td-black\" colspan=\"9\">&nbsp;</td>";
	 }
	 
	 echo "</tr>";
	  
	 echo " 
		<tr> 
		 <td><br></td>
		 <td colspan=\"3\" >".$data["jmeno"]." ".$data["prijmeni"]."<br>
		 ".$data["ulice"]." ";
		     
	 echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
	    
	 echo "<br>".$data["mesto"]." ".$data["psc"]."</td>
	 <td colspan=\"6\" >";
		 
	 //druhy sloupec - pomyslny
	 echo "icq: ".$data["icq"]." <br>
	 mail: ".$data["mail"]." <br>
	 tel: ".$data["telefon"]." </td>";
		 
	 //treti sloupec - sluzby
	 echo "<td colspan=\"\" valign=\"top\" >";
		 
	  if( $data["sluzba_int"] == 1 )
	  { 
	    echo "<div style=\"\" ><span style=\"font-weight: bold; \"><span style=\"color: #ff6600; \" >Služba Internet</span> - aktivní </span>";
	    if( $data["sluzba_int_id_tarifu"] == 999 )
	    { echo "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
	    else
	    { echo " (<a href=\"admin-tarify.php?id_tarifu=".$data["sluzba_int_id_tarifu"]."\" >tarif)</a></div>"; }
	    
	    $sluzba_int_aktivni = "1"; 
	  }
	  else
	  { $sluzba_int_aktivni = "0"; }
	  
	  if( $data["sluzba_iptv"] == 1 )
	  { 
	    echo "<div style=\"float: left;\" >".
		    "<span style=\"font-weight: bold; \"><span style=\"color: #00cbfc; \" >Služba IPTV</span> - aktivní </span>";
	    
	    if( $data["sluzba_iptv_id_tarifu"] == 999 )
	    { echo "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
	    else
	    { echo " (<a href=\"admin-tarify-iptv.php?id_tarifu=".$data["sluzba_iptv_id_tarifu"]."\" >tarif)</a></div>"; }
	    
	    $sluzba_iptv_aktivni = "1"; 
	  
	    //link portál
	    $mq_prefix = mysql_query("SELECT value FROM settings WHERE name LIKE 'iptv_portal_sub_code_prefix' ");
	    $iptv_prefix_name = mysql_result($mq_prefix, 0, 0);
	    
	    echo "<div style=\"float: left; padding-left: 15px; \" >";
		echo "<a href=\"http://app01.cho01.iptv.grapesc.cz:9080/admin/admin/provisioning/".
		        "subscriber-search.html?type=SUBSCRIBER_CODE&subscriptionNewState=&subscriptionStbAccountState=".
		    	"&localityId=&offerId=&submit=OK&searchText=".urlencode($iptv_prefix_name.$data["prijmeni"])."\" target=\"_new\" >".
			"<img src=\"/img2/Letter-P-icon-small.png\" alt=\"letter-p-small\" width=\"20px\" >".
		    "</a>";
	    echo "</div>";
	    
	    echo "<div style=\"clear: both; \"></div>";
	  
	  }
	  else
	  { $sluzba_iptv_aktivni = "0"; }
		 
	  if( $data["sluzba_voip"] == 1 )
	  { 
	    echo "<div><span style=\"font-weight: bold;\" ><span style=\"color: #e42222; \" >Služba VoIP</span> - aktivní </span>";
	    
	    /*if( $data["sluzba_iptv_id_tarifu"] == 999 )
	    { echo "<span style=\"color: gray; \" >- tarif nezvolen</span></div>"; }
	    else
	    { echo " (<a href=\"\" >tarif)</a></div>"; }
	    */
	    
	    $sluzba_voip_aktivni = "1"; 
	  }
	  else
	  { $sluzba_voip_aktivni = "0"; }
	  
	  if( ( $sluzba_int_aktivni != 1 ) and ( $sluzba_iptv_aktivni != 1 ) and ( $sluzba_voip_aktivni != 1 ) )
	  { echo "<div style=\"color: Navy; font-weight: bold; \" >Žádná služba není aktivovaná</div>"; }
	  else{}
	  
	  //echo "<hr class=\"cara3\" />";
	  echo "<div style=\"border-bottom: 1px solid gray; width: 220px; \" ></div>";
	  
	  if( ( $sluzba_int_aktivni != 1 ) and ( $sluzba_iptv_aktivni != 1 ) and ( $sluzba_voip_aktivni != 1 ) )
	  { 
	   echo "<div style=\"color: #555555; \" >Všechny služby dostupné</div>"; 
	  }
	  else
	  {
	   if( $sluzba_int_aktivni != 1 )
	   { 
	     echo "<div style=\"\" ><span style=\"color: #ff6600; \" >Služba Internet</span>";
	     echo "<span style=\"color: #555555; \"> - dostupné </span></div>"; 
	   }
	   else{}
	   
	   if( $sluzba_iptv_aktivni != 1 )
	   { 
	     echo "<div style=\"\" ><span style=\"color: #27b0db; \" >Služba IPTV</span>";
	     echo "<span style=\"color: #555555; \"> - dostupné </span></div>"; 
	   }
	   else{}
	   
	   if( $sluzba_voip_aktivni != 1 )
	   { 
	     echo "<div style=\"\" ><span style=\"color: #e42222; \" >Služba VoIP</span>";
	     echo "<span style=\"color: #555555; \"> - dostupné </span></div>"; 
	   }
	   else{}
	  
	  }	 
	  
	  echo "</td>";	 
	 echo "</tr>"; //konec radku
		
	 $id=$data["id_cloveka"];
	 $id_v=$id;
	 
	 $id_f=$data["fakturacni"];
	
    // tady asi bude generovani fakturacnich udaju	
    if( ( $id_f > 0 ) ){ fakturacni::vypis($id_f,$id_v); }
    
    $objekt = new objekt_a2(); 
    $objekt->conn_mysql = $this->conn_mysql;
	
    $pocet_wifi_obj = $objekt->zjistipocet(1,$id);
    
    $pocet_fiber_obj = $objekt->zjistipocet(2,$id);
    
    if( $pocet_wifi_obj > 0 or $pocet_fiber_obj == 0 )
    {
     //objekty wifi
     $co="3";
		
     echo "<tr>
	    <td colspan=\"1\" bgcolor=\"#99FF99\" align=\"center\" >W
	    <td colspan=\"10\" bgcolor=\"#99FF99\" >";
      echo "<table border=\"0\" width=\"100%\" >";
        
      $objekt->vypis($sql,$co,$id);
	    
      echo "</table>";
     echo "</td></tr>";
    }
    
    if( $pocet_fiber_obj > 0 )
    {
    
     //objekty fiber
     $co="4";
		
     echo "<tr>";
     echo "<td colspan=\"1\" bgcolor=\"fbbc86\" align=\"center\" >F</td>";
     echo "<td colspan=\"10\" bgcolor=\"fbbc86\" >";
	   
      echo "<table border=\"0\" width=\"100%\" >";
        
      $objekt->vypis($sql, $co, $id);
	    
      echo "</table>";
     echo "</td></tr>";
    }
    
    //stb
    
    $stb = new App\Core\stb($this->conn_mysql, $this->logger);
    
    $stb->level = $this->level;
    
    $pocet_stb = $stb->zjistipocetobj($id);
    
    if( $pocet_stb > 0 )
    {
      echo "<tr>";
      echo "<td colspan=\"1\" bgcolor=\"#c1feff\" align=\"center\" >S</td>\n";
      echo "<td colspan=\"10\" bgcolor=\"#c1feff\" valign=\"center\" >\n";
	   
      echo "<table border=\"0\" width=\"100%\" >\n";
        
      $stb->vypis("1",$id);
	    
      echo "</table>\n";
      echo "</td></tr>\n";
    }
    
    //tady dalsi radka asi
    /*
    $voip = new voip();
    $id_vlastnika = $data["id_cloveka"];
    
    $dotaz_sql = "SELECT * FROM voip_cisla WHERE id_vlastnika = '$id_vlastnika' ";
    $voip_radku = $voip->vypis_cisla_query($dotaz_sql);
   
    if ( $voip_radku > 0)
    {
     echo "<tr>";    
     echo "<td colspan=\"14\" ><div style=\"padding-top: 10px; padding-bottom: 10px; \">";
    
      $voip->vypis_cisla("2");
      
     echo "</div></td>";
     echo "</tr>";
    }
    */
        
    //druha radka			
	    echo "<tr>";
	    echo "<td colspan=\"14\">";
	
	    echo "<table border=\"0\" width=\"100%\" >";
	    echo "<tr>";

	    $orezano = explode(':', $data["pridano"]);
	    $pridano=$orezano[0].":".$orezano[1];
		            
	    echo "<td colspan=\"1\" width=\"250px\" >";
	      echo "<span style=\"margin: 20px; \">datum přidání: ".$pridano." </span>";
	    echo "</td>";
	    
	    echo "<td align=\"center\" >";
		echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
		echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";
	    echo "</td>";
	    
	    echo "<td>
		    <span style=\"\">vyberte akci: </span>
		  </td>";
		
	    echo "<td colspan=\"1\">";

	    
	    echo "<form action=\"vlastnici-cross.php\" method=\"get\" >";
			
		      echo "<select name=\"akce\" size=\"1\" >";
		    
		      echo "<option value=\"0\" class=\"select-nevybrano\" >Nevybráno</option>";
		      
		       echo "<optgroup label=\"objekty\">";
		        echo "<option value=\"1\" "; if( $_GET["akce"] == 1 ) echo " selected "; echo " > přiřadit objekt </option>";
		        echo "<option value=\"15\" "; if( $_GET["akce"] == 15 ) echo " selected "; echo " > přiřadit objekt STB</option>";

		       echo "</optgroup>";
		
		       echo "<optgroup label=\"fakturacni adresa\">";	
		        echo "<option value=\"2\" "; if( $_GET["akce"] == 2) echo " selected "; echo " >přidání fakturační adresy </option>";
		        echo "<option value=\"3\" "; if( $_GET["akce"] == 3) echo " selected "; echo " >smazání fakturační adresy </option>";
		        echo "<option value=\"4\" "; if( $_GET["akce"] == 4) echo " selected "; echo " >úprava fakturační adresy </option>";
		       echo "</optgroup>";
			
		       echo "<optgroup label=\"Závady/opravy\" >";
			echo "<option value=\"5\" "; if( $_GET["akce"] == 5) echo " selected "; echo " >Vložit závadu/opravu</option>";
			echo "<option value=\"6\" "; if( $_GET["akce"] == 6) echo " selected "; echo " >zobrazit závady/opravy</option>";
		       echo "</optgroup>";
		    
		       echo "<optgroup label=\"Smlouvy/výpovědi\" >";
			echo "<option value=\"7\" "; if( $_GET["akce"] == 7) echo " selected "; echo " >Tisk smlouvy</option>";
			echo "<option value=\"8\" "; if( $_GET["akce"] == 8) echo " selected "; echo " >Vložit zádost o výpověď</option>";
		       echo "</optgroup>";
		    
		       echo "<optgroup label=\"Platby/faktury\" >";
		//	echo "<option value=\"9\" "; if( $_GET["akce"] == 9) echo " selected "; echo " >Vložit hotovostní platbu</option>";
			echo "<option value=\"10\" "; if( $_GET["akce"] == 10) echo " selected "; echo " >Výpis plateb za internet</option>";
			echo "<option value=\"11\" "; if( $_GET["akce"] == 11) echo " selected "; echo " >Výpis všech neuhrazených faktur</option>";
		//	echo "<option value=\"12\" "; if( $_GET["akce"] == 12) echo " selected "; echo " >online faktury (XML) - Internet</option>";
		//	echo "<option value=\"14\" "; if( $_GET["akce"] == 14) echo " selected "; echo " >online faktury (XML) - VoIP (hlas)</option>";        
			echo "<option value=\"16\" "; if( $_GET["akce"] == 16) echo " selected "; echo " >Výpis faktur/Plateb (Pohoda SQL)</option>";        
		
		       echo "</optgroup>";
		    
		       echo "<optgroup label=\"Historie\" >";
			echo "<option value=\"13\" "; if( $_GET["akce"] == 13) echo " selected "; echo " >Zobrazení historie</option>";
		       echo "</optgroup>";
		    
		      echo "</select>";
		      
		      echo "<span style=\"padding-left: 20px;\" >
		    	      <input type=\"submit\" name=\"odeslat\" value=\"OK\">
			    </span>";
		      
		      echo "<input type=\"hidden\" name=\"id_cloveka\" value=\"".$data["id_cloveka"]."\">";
		      
		    echo "</form>";

	    echo "</td>";
	 echo "</tr></table>";    
	
	    echo "</td>";	    
	    echo "</tr>";
	
	/*
	    echo "<tr>";
		echo "<td colspan=\"10\" >";
		
		    	
		echo "</td>";
	    echo "</tr>";
	*/
	
	//konec while
	}
	
	// konec else
	}
	
	// konec funkce vypis
	}
    
    function export(){    
    

	// tafy generovani exportu
	if( $this->export_povolen )
	{

    	    $fp=fopen("export/vlastnici-sro.xls","w");   // Otevřeme soubor tabulka.xls, pokud existuje, bude smazán, jinak se vytvoří nový sobor

    	    if( $fp === false)
    	    { echo "<div style=\"color: red; font-weight: bold; \">Chyba: Soubor pro export nelze otevřít </div>\n"; }
    	    else
    	    {
    		fputs($fp,"<table border='1'> \n \n");   // Zapíšeme do souboru začátek tabulky

    		fputs($fp,"<tr>");   // Zapíšeme do souboru začátek řádky, kde budou názvy sloupců (polí)

    		$vysledek_pole=pg_query("SELECT column_name FROM information_schema.columns WHERE table_name ='vlastnici' ORDER BY ordinal_position ");
    		// Vybereme z databáze názvy polí tabulky tabulka a postupně je zapíšeme do souboru

    		// echo "vysledek_pole: $vysledek_pole ";

    		while ($vysledek_array_pole=pg_fetch_row($vysledek_pole) )
    		{  fputs($fp,"<td><b> ".$vysledek_array_pole[0]." </b></td> \n"); }

    		fputs($fp,"<td><b> id_f </b></td> \n");
    		fputs($fp,"<td><b> f. jméno </b></td> \n");
    		fputs($fp,"<td><b> f. ulice </b></td> \n");
    		fputs($fp,"<td><b> f. mesto </b></td> \n");
    		fputs($fp,"<td><b> f. PSČ </b></td> \n");
    		fputs($fp,"<td><b> f. ičo </b></td> \n");
    		fputs($fp,"<td><b> f. dič </b></td> \n");
    		fputs($fp,"<td><b> f. účet </b></td> \n");
    		fputs($fp,"<td><b> f. splatnost </b></td> \n");
    		fputs($fp,"<td><b> f. cetnost </b></td> \n");

    		fputs($fp,"</tr>");   // Zapíšeme do souboru konec řádky, kde jsou názvy sloupců (polí)

    		// $vysledek=pg_query("select * from platby where hotove='1' ");
    		// Vybereme z databáze všechny záznamy v tabulce tabulka a postupě je zapíšeme do souboru

    		$vysledek = pg_query("SELECT * FROM vlastnici WHERE (archiv ='0' OR archiv is NULL) ORDER BY id_cloveka ASC");

    		while ($data=pg_fetch_array($vysledek) )
    		{
        	    fputs($fp,"\n <tr>");

        	    fputs($fp,"<td> ".$data["id_cloveka"]."</td> ");
        	    fputs($fp,"<td> ".$data["nick"]."</td> ");
        	    fputs($fp,"<td> ".$data["jmeno"]."</td> ");
        	    fputs($fp,"<td> ".$data["prijmeni"]."</td> ");
        	    fputs($fp,"<td> ".$data["ulice"]."</td> ");
        	    fputs($fp,"<td> ".$data["mesto"]."</td> ");
        	    fputs($fp,"<td> ".$data["psc"]."</td> ");
        	    fputs($fp,"<td> ".$data["icq"]."</td> ");
        	    fputs($fp,"<td> ".$data["mail"]."</td> ");
        	    fputs($fp,"<td> ".$data["telefon"]."</td> ");
        	    fputs($fp,"<td> ".$data["poznamka"]."</td> ");
        	    fputs($fp,"<td> ".$data["zaplaceno"]."</td> ");
        	    fputs($fp,"<td> ".$data["fakturacni"]."</td> ");
        	    fputs($fp,"<td> ".$data["vs"]."</td> ");
            	    fputs($fp,"<td> ".$data["k_platbe"]."</td> ");
        	    fputs($fp,"<td> ".$data["firma"]."</td> ");

        	    fputs($fp,"<td> ".$data["pridano"]."</td> ");
        	    fputs($fp,"<td> ".$data["ucetni_index"]."</td> ");
        	    fputs($fp,"<td> ".$data["archiv"]."</td> ");
        	    fputs($fp,"<td> ".$data["fakturacni_skupina_id"]."</td> ");

        	    fputs($fp,"<td> ".$data["splatnost"]."</td> ");
        	    fputs($fp,"<td> ".$data["typ_smlouvy"]."</td> ");
        	    fputs($fp,"<td> ".$data["trvani_do"]."</td> ");
        	    fputs($fp,"<td> ".$data["datum_podpisu"]."</td> ");

        	    fputs($fp,"<td> ".$data["sluzba_int"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_iptv"]."</td> ");

        	    fputs($fp,"<td> ".$data["sluzba_voip"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_int_id_tarifu"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_iptv_id_tarifu"]."</td> ");
        	    fputs($fp,"<td> ".$data["sluzba_voip_fa"]."</td> ");

        	    fputs($fp,"<td> ".$data["billing_freq"]."</td> ");

        	    fputs($fp,"<td> ".$data["billing_suspend_status"]."</td> ");
        	    fputs($fp,"<td> ".$data["billing_suspend_length"]."</td> ");
        	    fputs($fp,"<td> ".$data["billing_suspend_reason"]."</td> ");
        	    fputs($fp,"<td> ".$data["billing_suspend_start"]."</td> ");

        	    if ( $data["fakturacni"] > 0 )
        	    {
        		$id_f=$data["fakturacni"];

        		$vysl_f=pg_query("SELECT * FROM fakturacni WHERE id = '".intval($id_f)."' ");

        		while ( $data_f=pg_fetch_array($vysl_f) )
        		{

        		    fputs($fp,"<td> ".$data_f["id"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["ftitle"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["fulice"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["fmesto"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["fpsc"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["ico"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["dic"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["ucet"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["splatnost"]."</td> ");
        		    fputs($fp,"<td> ".$data_f["cetnost"]."</td> ");

        		}

        	    }

        	    fputs($fp,"</tr> \n ");
        	    // echo "vysledek_array: ".$vysledek_array[$i];

    		} //konec while

    		fputs($fp,"</table>");   // Zapíšeme do souboru konec tabulky

    		fclose($fp);   // Zavřeme soubor

    	    } //konec else if fp === true

	} //konec if export_povolen
    
    } //end of function export
    	
 //konec class-y vlastnik2
}
	

class vlastnikarchiv
{

   public static function vypis_tab ($par)
    {
		if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; }
		elseif ($par == 2) { echo "\n".'</table>'."\n"; }
		else    { echo "chybny vyber"; }				
	}
				
				
    public function vypis ($sql,$co,$dotaz_final)
    {
					
    // co - co hledat, 1- podle dns, 2-podle ip
						    
    $dotaz=pg_query($dotaz_final);

	if($dotaz !== false) {
		$radku=pg_num_rows($dotaz);
	}

    if ($radku==0) echo "<tr><td><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle hledaného \"".$sql."\". </span></td></tr>";
    else
        {

        while( $data=pg_fetch_array($dotaz) ) 
	{
	    echo "<tr><td colspan=\"14\"> <br> </td> </tr>
	    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" width=\"\" >id: [".$data["id_cloveka"]."] 
	    nick: [".$data["nick"]."] účetní-index: [".sprintf("%05d", $data["ucetni_index"])."] </td>
	    
	    <td class=\"vlastnici-td-black\" colspan=\"2\">VS: [".$data["vs"]."] ";
	    
	    if ($data["firma"] == 1){ echo " firma: [Simelon, s.r.o.]"; }
	    else{ echo " firma: [M. Lopušný] "; }
	    
	    echo"</td>
	
	    <td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
	    <td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"\" >";
	
	    echo "<table border=\"0\" width=\"70%\" > <tr> <td class=\"vlastnici-td-black\" width=\"\" >";
	
	// sem mazani
	
	global $vlastnici_erase_povolen;
	
	if ( ! ( $vlastnici_erase_povolen == "true" ) )
	{ echo "<span style=\"\" > smazat </span> "; }
	else
	{
	    echo "<form method=\"POST\" action=\"vlastnici2-erase.php\" >";
	    echo "<input type=\"hidden\" name=\"erase_id\" value=\"".$data["id_cloveka"]."\" >";
	    echo "<input type=\"submit\" value=\"Smazat\" >";
	    
	    echo "</form> \n";
	
	}
	    echo "</td><td class=\"vlastnici-td-black\" >";
	
	global $vlastnici_update_povolen;
	
	// 6-ta update
	
	if ( !( $vlastnici_update_povolen =="true") )
	{ echo "<span style=\"\" >  upravit  </span> \n"; }
	
	else
	{
	 echo " <form method=\"POST\" action=\"vlastnici2-change.php\" >";
	 echo "<input type=\"hidden\" name=\"update_id\" value=\"".$data["id_cloveka"]."\" >";
	 echo "<input type=\"submit\" value=\"update\" >";
	
	 echo "</form> \n";
	
	 }
	
	 echo "</td> </tr> </table>";
	
	 echo "  </td>
	        </tr>
		  <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>
		 ".$data["ulice"]." ";
		     
	    echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
	    
	    
		 echo "<br>".$data["mesto"]." ".$data["psc"]."</td>
		 <td colspan=\"12\">icq: ".$data["icq"]." <br>
		 mail: ".$data["mail"]." <br>
		 tel: ".$data["telefon"]." </td>
		 </tr>";
		
		
	 $id=$data["id_cloveka"];
	 $id_v=$id;
	 
	 $id_f=$data["fakturacni"];
	
	// tady asi bude generovani fakturacnich udaju
	
	if ( ( $id_f > 0 ) )
	{
	
	     fakturacni::vypis($id_f,$id_v);
	
	 }
	
	// $sql="%";
	$co="3";
	
	// $id=$data["id_cloveka"];
	// print "debug: id: $id";
	
	 objekt_a2::vypis($sql,$co,$id);



	//tady dalsi radka asi
	
	
	    echo "<tr>";
	    
	    echo "<td>další funkce: </td>
	        <td colspan=\"13\">";
			
	    //echo "<a href=\"vlastnici2-add-obj.php?id_vlastnika=".$data["id_cloveka"]."\">přidání objektu</a>";
	    echo "<span style=\"color: gray; \">přidání objektu</span>";
	    
	    echo "<span style=\"margin: 10px; \"></span>";
	      
	    echo "<a href=\"platby-vypis.php?id_vlastnika=".intval($data["id_cloveka"])."\" > výpis plateb - starý (do 2/2012)</a>";

	    echo "<span style=\"margin-left: 20px; \">
		    <a href=pohoda_sql/phd_list_fa.php?id_vlastnika=".$data["id_cloveka"]."\" > výpis plateb - (od 3/2012)</a>".
		  "</span>";
	
	    echo "<span style=\"margin: 10px; \">fakturační adresa:</span>";
	      
	    /*
	    if ( ( $data["fakturacni"] > 0 ) )
	    { echo " přidání fakturační adresy "; }
	    else
	    { echo "<a href=\"vlastnici2-add-fakt.php?id_vlastnika=".$data["id_cloveka"]."\" > přidání fakturační adresy </a>"; }
	    */
	    echo "<span style=\"color: grey; \"> přidání</span>";
	    
	    echo "<span style=\"margin: 25px; \"></span>";
	    
	    if ( ( $data["fakturacni"] > 0 ) )
	    { echo "<a href=\"vlastnici2-erase-f.php?id=".$data["fakturacni"]."\" > smazání </a>"; }
	    else 
	    { echo " smazání "; }
	    
	    echo "<span style=\"margin: 25px; \" ></span>";
	    
	    if ( ( $data["fakturacni"] > 0 ) )
	    { echo "<a href=\"vlastnici2-change-fakt.php?id=".$data["fakturacni"]."\" > úprava </a>"; }
	    else
	    { echo " úprava "; }

	    echo "</td></tr>";
	    
	    //druha radka			
	    echo "<tr>";
	    
	    // echo "<td><br></td>";
	    	    
	    $orezano = explode(':', $data["pridano"]);
	    $pridano=$orezano[0].":".$orezano[1];
		      
		          
	    echo "<td colspan=\"1\" >";
	    
	    echo "datum přidání: ".$pridano." ";
	        
	    echo "</td>";
	    
	    echo "<td align=\"center\" >";
	    
		echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
		echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";
	    
	    echo "</td>";
	    
	    echo "<td colspan=\"4\">";
		echo "<form method=\"POST\" action=\"platby-akce.php\" >";
		
		echo "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
		echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";
		
		echo "<input type=\"submit\" name=\"akce\" value=\"Vložení hotovostní platby\" >";
	    
		echo "</form>";
	    echo "</td>";


	    echo "<td colspan=\"4\">";
		echo "<form method=\"POST\" action=\"vypovedi-vlozeni.php\" >";
		
		echo "<input type=\"hidden\" name=\"firma\" value=\"2\" >";
		echo "<input type=\"hidden\" name=\"klient\" value=\"".$data["id_cloveka"]."\" >";
		
		echo "<input type=\"submit\" name=\"akce\" value=\"Vložit žádost o výpověď\" >";
	    
		echo "</form>";
	    echo "</td>";
	    
	    //echo "<td colspan=\"3\"><br></td>";
	    
	    echo "<td colspan=\"1\">";
	    // zde dalsi veci
	    echo "<span style=\"color: grey; padding-left: 10px; \" >H: </span>";
	    echo "<a href=\"archiv-zmen.php?id_cloveka=".$data["id_cloveka"]."\">".$data["id_cloveka"]."</a>";
					
	    echo "</td>";
						    
	    echo "<td> ";
	    //tisk smlouvy
	    echo "<form method=\"POST\" action=\"https://tisk.simelon.net/smlouva-pdf.php\" >";
										
	    echo "<input type=\"hidden\" name=\"ec\" value=\"".$data["vs"]."\" >";
	    echo "<input type=\"hidden\" name=\"jmeno\" value=\"".$data["jmeno"]." ".$data["prijmeni"]."\" >";
	    echo "<input type=\"hidden\" name=\"ulice\" value=\"".$data["ulice"]."\" >";
	    echo "<input type=\"hidden\" name=\"mesto\" value=\"".$data["psc"]." ".$data["mesto"]."\" >";
	    echo "<input type=\"hidden\" name=\"telefon\" value=\"".$data["telefon"]."\" >";
	    echo "<input type=\"hidden\" name=\"email\" value=\"".$data["mail"]."\" >";
	
	    if( ( $data["fakturacni"] > 0 ) )
	    {
	        echo "<input type=\"hidden\" name=\"fakturace\" value=\"2\" >";
	        //echo "<input type=\"hidden\" name=\"jmeno\" value=\"".$data["jmeno"]." ".$data["prijmeni"]."\" >";
	        //echo "<input type=\"hidden\" name=\"ulice\" value=\"".$data["ulice"]."\" >";
	        //echo "<input type=\"hidden\" name=\"mesto\" value=\"".$data["psc"]." ".$data["mesto"]."\" >";
	    }
	    if ( $data["k_platbe"] == "250" )
	    { echo "<input type=\"hidden\" name=\"tarif\" value=\"1\" >"; }
	    elseif( $data["k_platbe"] == "420" )
	    { echo "<input type=\"hidden\" name=\"tarif\" value=\"2\" >"; }
	    else
	    { echo "<input type=\"hidden\" name=\"tarif\" value=\"3\" >"; }
	
	    echo "<input type=\"submit\" name=\"akce\" value=\"Tisk smlouvy\" >";
	    
	    echo "</form>";
	
	    // echo "</tr></table>";
	    echo "</td>";
	    
	    
	    echo "<td colspan=\"2\" >
		    <form action=\"opravy-vlastnik.php\" method=\"get\" >
		    <input type=\"hidden\" name=\"typ\" value=\"2\" >
		    <input type=\"hidden\" name=\"id_vlastnika\" value=\"".$data["id_cloveka"]."\" >
										    
		    <input type=\"submit\" name=\"ok\" value=\"Zobrazit závady/opravy \" ></form>";
	    echo "</td>";
	        
	    echo "</tr>";
	
	//konec while
	}
	
	// konec else
	}
	
	// konec funkce vypis
	}
	
	//konec class-y vlastnikarchiv
	}
	
																																																																																																																																																										     
class objektypridani {

    function checkmac ($mac) 
    {
      $mac_check=ereg('^([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})\:([[:xdigit:]]{2,2})$',$mac);
      if ( !($mac_check) )
	{
	global $fail;	$fail="true";
	global $error;  $error .= "<div class=\"objekty-add-fail-mac\"><H4>MAC adresa ( ".$mac." ) není ve správném formátu !!! ( Správný formát je: 00:00:64:65:73:74 ) </H4></div>";
	}
		      
    //konec funkce check-mac
    }

    function checkSikanaCas($sikanacas) 
    {
        global $fail, $error;	
	
	$sikanacas = intval($sikanacas);
	
	if( ($sikanacas > 9) or ($sikanacas < 1) ){
	
	    $fail="true";
	
	    $error .= "<div class=\"objekty-add-fail-mac\">".
			"<H4>Do pole \"Šikana - počet dní\" je třeba vyplnit číslo 1 až 9.</H4></div>";	
	
	}
	 
    } //end of function checkSikanaCas

    function checkSikanaText($sikanatext) 
    {
        global $fail, $error;	

	if( (strlen($sikanatext) > 150) ){
	
	    $fail="true";
	
	    $error .= "<div class=\"objekty-add-fail-mac\">".
			"<H4>Do pole \"Šikana - text\" je možno zadat max. 150 znaků. (aktuálně: ".strlen($sikanatext).")</H4></div>";	
	
	}
	
    } //end of function checkSikanaText
    
    //function to validate ip address format in php by Roshan Bhattarai(http://roshanbh.com.np)
    function validateIpAddress($ip_addr)
    {
	//first of all the format of the ip address is matched
	if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
        {
          //now all the intger values are separated
          $parts=explode(".",$ip_addr);
          //now we need to check each part can range from 0-255
          foreach($parts as $ip_parts)
          {
            if(intval($ip_parts)>255 || intval($ip_parts)<0)
            
            return false; //if number is not within range of 0-255
          }
        
          return true;
        }
        else
          return false; //if format of ip address doesn't matches
    }
                                                      
    function checkip ($ip)
    {
     
      //old
      //$ip_check=ereg('^([[:digit:]]{1,3})\.([[:digit:]]{1,3})\.([[:digit:]]{1,3})\.([[:digit:]]{1,3})$',$ip);
      
      if ( !(objektypridani::validateIpAddress($ip)) )
      {
	global $fail;  $fail="true";
	global $error; $error .= "<div class=\"objekty-add-fail-ip\"><H4>IP adresa ( ".$ip." ) není ve správném formátu !!!</H4></div>";

      }

    } //konec funkce check-ip			 			 
    
    function checkcislo($cislo)
    {
     $rra_check=ereg('^([[:digit:]]+)$',$cislo);
     
     if ( !($rra_check) )
     {
      global $fail;	$fail="true";
      global $error;	$error .= "<H4>Zadaný číselný údaj(e) ( ".$cislo." ) není ve  správném formátu !!! </H4>";
     }			    
    
    } //konec funkce check cislo
    
    function checkdns ($dns)
    {
    $dns_check=ereg('^([[:alnum:]]|\.|-)+$',$dns);
    if ( !($dns_check) )
    {
     global $fail;	$fail="true";
     global $error; 	$error .= "<div class=\"objekty-add-fail-dns\"><H4>DNS záznam ( ".$dns." ) není ve správnem formátu !!! </H4></div>";
    }
    
    } // konec funkce check rra
    
    function check_l2tp_cr($cr)
    {
	$cr_check=ereg('^([[:alnum:]])+$',$cr);
	
	if( !($cr_check) )
	{
    	    global $fail;	
	    $fail="true";
    	
	    global $error; 	
	    $error .= "<div class=\"objekty-add-fail-dns\"><H4>Tunel. login/heslo ( ".$cr." ) není ve správnem formátu !!! </H4></div>";
	}
    
	if( (strlen($cr) <> 4) )
	{
	    global $fail;	
	    $fail="true";
    	
	    global $error; 	
	    $error .= "<div class=\"objekty-add-fail-dns\"><H4>Tunel. login/heslo ( ".$cr." ) musí mít 4 znaky !!! </H4></div>";
	
	}
	
	
    } //konec funkce check_l2tp_cr
    
    function generujdata ($selected_nod, $typ_ip, $dns, $conn_mysql)
    {
     // promenne ktere potrebujem, a ktere budeme ovlivnovat
     global $ip, $mac, $ip_rozsah, $umisteni_aliasu, $tunnel_user, $tunnel_pass, $fail, $error;    
	    
     // skusime ip vygenerovat
	 try {
		$vysl_ip = $conn_mysql->query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($selected_nod)."' ");
	 } catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	 }

     $radku_ip=$vysl_ip->num_rows;

     //print "<div style=\"color: grey;\" >debug sql: "."SELECT ip_rozsah, umisteni_aliasu FROM nod_list WHERE id = '".intval($selected_nod)."' "."</div>";
    	    
     if($radku_ip == 1) 
     {
		while ($data_ip=$vysl_ip->fetch_array() ){
		
			$ip_rozsah=$data_ip["ip_rozsah"];
			
			list($a,$b,$c,$d) =split("[.]",$ip_rozsah);
		}
	
	/*
	if( $ip_rozsah){
	
	       $gen_ip="E_4";
	       
	       if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	       return false;
	}
	*/
	
	 if( $c == 0)
	 {
	    // b-ckova ip
	    $gen_ip_find=$a.".".$b.".".$c.".".$d."/16";
	 
	     $msq_check_ip=pg_query("SELECT ip FROM objekty WHERE ip <<= '$gen_ip_find' order by ip asc");
	     $msq_check_ip_radku=pg_num_rows($msq_check_ip);
								 
	     if ( $msq_check_ip_radku == 0 ) { 
	        $c=10; 
	        $gen_ip=$a.".".$b.".".$c.".".$d;
	     }
	     else 
	     {
										 
	      while (  $data_check_ip=pg_fetch_array($msq_check_ip) ) 
	      { $gen_ip=$data_check_ip["ip"]; }
	
	      list($a,$b,$c,$d) = split("[.]",$gen_ip);
		
	      $limit=250;
	      global $ip_error;
	     
	      if( ($a == "212") and ($b == "80") ){ $gen_ip=$ip_rozsah; $ip_error="1"; }
	      elseif( ( $c >= $limit ) ) { $gen_ip=$ip_rozsah; $ip_error="1"; }
	      else
	      {
	         list($a,$b,$c,$d) = split("[.]",$gen_ip);
	        $c=$c+1;
		$d="3";
		$gen_ip=$a.".".$b.".".$c.".".$d;
		
	      } //konec else gen ip > 255
	      
	     } //konec else msq_check_ip_radku == 0
		
	  } //konec if c == 0
	  elseif( ($a == "212") and ($b == "80") )
	  { //verejny, 2 -- rout. prima, 4 -- tunelovana
	     
	    $sql_src = "SELECT INET_NTOA(ip_address) AS ip_address FROM public_ip_to_use ";
	    
	    if($typ_ip==2)
	    {  $sql_src .= " WHERE mode = '1' "; }
	    elseif($typ_ip==4)
	    {  $sql_src .= " WHERE mode = '0' "; }
	    else
	    {
	       $gen_ip=$ip_rozsah; 
	       
	       if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	       return false;
	    }
	  
	    $sql_src .= " ORDER BY public_ip_to_use.ip_address ASC ";
		// try {
		// 	$ = $conn_mysql->query();
		//  } catch (Exception $e) {
		// 	die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
		//  }
	    $dotaz=mysql_query($sql_src);
	    
	    if( (mysql_num_rows($dotaz) == 0) )
	    {
	       $gen_ip="E_3";
	       
	       if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	       return false;
	    }
	
	
	    while($data = mysql_fetch_array($dotaz))
	    {
		$ip_address = $data["ip_address"];
	    
	        //kontrola :-)
		//if(true){ $gen_ip = $ip_address; }
		
		$dotaz_check = pg_query("SELECT ip FROM objekty WHERE ip <<= '$ip_address' ");
	        $dotaz_check_radku = pg_num_rows($dotaz_check);
		
	        if( ($dotaz_check_radku > 1) )
		{ //chyba, vice adres vyhovelo vyberu
	    	    $gen_ip="E_4";
	       
	    	    if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	    	    return false;
		}
		elseif($dotaz_check_radku == 0)
		{ //ip v DB není, OK
	    	    $gen_ip = $ip_address;
	       
	    	    if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
	    	    break;
		}
		
	     } //end of while data fetch dotaz
	    	
	  } //end of generate public IP address
	  elseif ( ( $d==0 and $c != 0 ) )
	  {
	    // c-ckova ip	
		$gen_ip_find=$a.".".$b.".".$c.".".$d."/24";
		
		$msq_check_ip=pg_query("SELECT * FROM objekty WHERE ip <<= '$gen_ip_find' order by ip asc");
		$msq_check_ip_radku=pg_num_rows($msq_check_ip);
		
		if( $msq_check_ip_radku == 0 ){ $d=10; $gen_ip=$a.".".$b.".".$c.".".$d; }
		else 
		{
		 while( $data_check_ip=pg_fetch_array($msq_check_ip) )
		 { $gen_ip=$data_check_ip["ip"]; }
		     
		 list($a,$b,$c,$d) = split("[.]",$gen_ip);
		    
		 global $ip_error;
		     
		 if( $d >= "254"){ $gen_ip=$a.".".$b.".".$c.".0"; $ip_error="1"; $ip_rozsah=$gen_ip; }
		 else
		 {
		  $d=$d+2;
		  $gen_ip=$a.".".$b.".".$c.".".$d;
		 }
		} // konec else radku == 0
		
	      // konec gen. ceckovy ip
	   }
	   else
	   {
	     $gen_ip = "E1"; //echo "chybnej vyber";
	   }
		
	    // vysledek predame
	    if( ( strlen($ip) <= 0) ){ $ip=$gen_ip; }
		
	 
	 } //end of: if $radku_ip == 1                                     
         else{
        
	    // vysledek predame
	     if( ( strlen($ip) <= 0) ){ 
                $gen_ip = "E2"; //asi neprosel SQL dotaz	  
             }
             
             return false;
         }
         
         //zde generovani dalsich velicin
	 if($typ_ip == 4)
	 {
	   if( (  (strlen($dns)<= 0) and (strlen($tunnel_user) <= 0) and ( strlen($tunnel_pass) <= 0) ) )
	   {
	      $gen_user = "E_DNS";
	      $gen_pass = "E_DNS";
	   }
	   else
	   {
		$dns_trim = substr($dns, 0, 3).rand(0, 9);
		$dns_trim2 = substr($dns, 0, 2).rand(0, 99);
		
		
		$gen_user = $dns_trim;
		$gen_pass = $dns_trim2;
	   }
	   
	   if( ( strlen($tunnel_user) <= 0) ){ $tunnel_user=$gen_user; }
	   if( ( strlen($tunnel_pass) <= 0) ){ $tunnel_pass=$gen_pass; }
	 
	 
	 } //konec if typ_ip == 4
	 
    } // konec funkce generujdata
    
} //konec objketu objekty-pridani

class objektypridanifiber{

  function generujdata( $selected_nod,$id_tarifu )
  {

     global $ip;
     //global $mac;
     //global $rra;
     global $ip_rozsah;

     if($selected_nod < 1 )
     {
       echo "";
       return false;
     }
     	
     // skusime ip vygenerovat
     $vysl_nod=mysql_query("SELECT ip_rozsah FROM nod_list WHERE id = '".intval($selected_nod)."'");
     $radku_nod=mysql_num_rows($vysl_nod);

     if( $radku_nod <> 1 )
     { 
       if( ( strlen($ip) < 1 ) ){ $ip = "E_1"; }
       return false;
     }
     else
     {
     	while( $data_nod=mysql_fetch_array($vysl_nod) ):
	   $ip_rozsah=$data_nod["ip_rozsah"];
	//   $umisteni_aliasu=$data_nod["umisteni_aliasu"];  
	endwhile;
     }
    
     $vysl_tarif = mysql_query("SELECT gen_poradi FROM tarify_int WHERE id_tarifu = '".intval($id_tarifu)."' ");
     $radku_tarif=mysql_num_rows($vysl_tarif);
 
     if( $radku_tarif <> 1 )
     { 
       if( ( strlen($ip) < 1 ) ){ $ip = "E_2"; }
       return false;
     }
     else
     {
     	while( $data_tarif = mysql_fetch_array($vysl_tarif) ):
	   $gen_poradi = $data_tarif["gen_poradi"];
	endwhile;
     }
     
     if( !( $gen_poradi > 0 ) )
     {
       //znama chyba, nechame prazdne...
       //if( ( strlen($ip) < 1 ) ){ $ip = "E_3"; } 
       return false;
     }
     
     list($r_a, $r_b, $r_c, $r_d) =split("[.]",$ip_rozsah);
     
     if( $gen_poradi == 1 )	{ $r_d = $r_d + "0"; }
     elseif( $gen_poradi == 2 ) { $r_d = $r_d + "128"; }
     elseif( $gen_poradi == 3 ) { $r_c = $r_c + "1"; }
     elseif( $gen_poradi == 4 )
     { 	$r_c = $r_c + "1";	$r_d = $r_d + "128"; }
     elseif( $gen_poradi == 5 )
     {  $r_c = $r_c + "2";	$r_d = $r_d + "0";   }
     elseif( $gen_poradi == 6 )
     {  $r_c = $r_c + "2";	$r_d = $r_d + "128";   }
     elseif( $gen_poradi == 7 )
     {  $r_c = $r_c + "3";	$r_d = $r_d + "0";   }
     elseif( $gen_poradi == 8 )
     {  $r_c = $r_c + "3";	$r_d = $r_d + "128";   }
     
     else
     {
       if( ( strlen($ip) < 1 ) ){ $ip = "E_4"; }  
       return false;
     }
     
     $sub_rozsah = $r_a.".".$r_b.".".$r_c.".".$r_d;
     
     $sub_rozsah_d = $r_d;
     
     $r_d = $r_d + "8";
    
     $check_ip = pg_query("SELECT * FROM objekty WHERE ip <<= '$sub_rozsah/26' ORDER BY ip ASC");
     $check_ip_radku = pg_num_rows($check_ip);
     
     //echo "subrozsah: ".$sub_rozsah." xxx";
     
     if( $check_ip_radku == 0 ) // v rozsahu zadna ip, takze generujem prvni..
     { 
       $gen_ip = $r_a.".".$r_b.".".$r_c.".".$r_d; 
       //$gen_ip = "vole...";
     }
     else //v db je vice ip adres ...
     {
      //nacteni predchozi ip adresy ..
      while(  $data_check_ip = pg_fetch_array($check_ip) )
      { $gen_ip2=$data_check_ip["ip"]; }
      	
      list($g_a,$g_b,$g_c,$g_d) = split("[.]",$gen_ip2);

      if( $sub_rozsah_d == "0" ){ $limit = 120; }
      elseif( $sub_rozsah_d == "128" ){ $limit = 250; }
      else
      {
        if( ( strlen($ip) < 1 ) ){ $ip = "E_5"; }  
        return false;
      }
	     
      if( ( $g_d >= $limit ) ){ $gen_ip=$ip_rozsah; $ip_error="1"; }
      else
      {
       //zde tedy pricist udaje a predat ...
       $g_d = $g_d + 2;
       
       //zpetna kontrola jeslti to neni lichy..
       $rs = $g_d % 2;
       
       if( $rs == 1) //je to lichy, chyba ...
       {
         if( ( strlen($ip) < 1 ) ){ $ip = "E_5"; }  
         return false;
       } 
       else //neni to lichy, takze je to spravne, cili finalni predani .
       {
         $gen_ip = $g_a.".".$g_b.".".$g_c.".".$g_d;
       }
      } // konec else if g_d pres limit
     
     } // konec else if  check_ip_radku == 0
     
     	  
     //tady asi cosi neni-li zadana ip, tak gen_ip = ip;
     if( ( strlen($ip) < 1 ) ){ $ip = $gen_ip; }
     
     //return true;  
    } //konec funkce generujdata
    
} // konec objektu objekty pridani fiber

class fakturacni
{

    function vypis($id_f,$id_v)
    {
    // $id="";
    
	$dotaz=pg_query( "SELECT * FROM fakturacni where id='$id_f' ");
	$dotaz_radku=pg_num_rows($dotaz);
    
        
    if ( $dotaz_radku==0 )
    {
    echo "<tr><td> CHYBA! Fakturacni udaje nenalezeny. debug: id=$id_f </td></tr>";
    }
    else
    {
    

	 while( $data=pg_fetch_array($dotaz) ):
			     
	 // echo "<tr><td colspan=\"14\"> <br> </td> </tr>";
	  
	  echo "<tr>";
	  
	  if( $firma == 1)
	  { echo "<td></td>"; }
	  
	  echo " <td colspan=\"2\"> Fakturační údaje: <br>".$data["ftitle"]." ".$data["fadresa"]."<br> ";
	  echo $data["fulice"]." <br> ";
	  
	  echo $data["fmesto"]." ".$data["fpsc"]."</td>";
	  
	  echo "<td colspan=\"12\">ičo: ".$data["ico"].", dič: ".$data["dic"];
	  echo "<br>účet: ".$data["ucet"]." <br> splatnost (dnů): ".$data["splatnost"];
	  echo "<br> četnost: ".$data["cetnost"]."</td>";
			  
	endwhile;
															  
     }
     
    } // konec funkce vypis


} // konec tridy fakturacni

class vlastnici2pridani
{
    
    function checknick ($nick2)
    {
	global $fail, $error;
	
        $nick_check=ereg('^([[:alnum:]]|_|-)+$',$nick2);
	if( !($nick_check) ) {
	    $fail="true";    
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Nick (".$nick2.") není ve správnem formátu!!! (Povoleno alfanumerické znaky, dolní podtržítko, pomlčka)</H4></div>";
	}
	
	if( (strlen($nick2) > 20) ) {
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Nick (".$nick2.") je moc dlouhý! (Maximální délka je 20 znaků)</H4></div>";	        
	}
				
    } // konec funkce check nick

    function checkvs ($vs)
    {
	$vs_check=ereg('^([[:digit:]]+)$',$vs);
	if( !($vs_check) )
	{
	  global $fail;      $fail="true";
	  global $error;
	  $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Variabilní symbol ( ".$vs." ) není ve správnem formátu!!! (Pouze čísla)</H4></div>";
	}
    } // konec funkce check vs															    

    function check_k_platbe ($k_platbe)
    {
	$platba_check=ereg('^([[:digit:]]|\.)+$',$k_platbe);
	   
	if ( !($platba_check) )
	{
	    global $fail;      $fail="true";
	    global $error;
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>K_platbe ( ".$k_platbe." ) není ve správnem formátu !!! </H4></div>";
	}
	
    } // konec funkce check rra    

    function check_uc_index($ucetni_index)
    {
	   $ui_check=ereg('^([[:digit:]]|\.)+$',$ucetni_index);
	   
	   if( !($ui_check) )
	   {
	         global $fail;      
		 $fail="true";
	         global $error;
	         $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$ucetni_index." ) není ve správnem formátu (Povoleny pouze čísla)!!! </H4></div>";
	   }
    
	   $ui_check2 = strlen($ucetni_index);
	     
	   if( $ui_check2 > 5 )
	   {
	         global $fail;      
		 $fail="true";
	         global $error;
	         $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Účetní index ( ".$ucetni_index." ) překračuje povolenou délku (5 znaků) !!! </H4></div>";
	   }
    
    } //konec funkce check_uc_index
    
    function check_splatnost($number)
    {
    	if ( !(ereg('^([[:digit:]])+$',$number)) )
	{
	    global $fail;      $fail="true";
	    global $error;
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Splatnost (".$number.") není ve správnem formátu! (pouze čísla)</H4></div>";
	}
    
    } //end of function check_splatnost

    function check_icq($number)
    {
    	if ( !(ereg('^([[:digit:]])+$',$number)) )
	{
	    global $fail;      $fail="true";
	    global $error;
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>ICQ (".$number.") není ve správnem formátu! (pouze čísla)</H4></div>";
	}
    
    } //end of function check_icq
    
    function check_email($email)
    {
    	if ( !(Aglobal::check_email($email)) )
	{
	    global $fail;      $fail="true";
	    global $error;
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Emailová adresa (".$email.") není ve správnem formátu!</H4></div>";
	}
    
    } //end of function check_icq
    
    function check_tel($number)
    {
	global $fail, $error;
	
    	if( !(ereg('^([[:digit:]])+$',$number)) )
	{
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Telefon (".$number.") není ve správnem formátu! (pouze číslice)</H4></div>";
	}
    
	if( strlen($number) <> 9 ){
	
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Pole Telefon (".$number.") musí obsahovat 9 číslic!</H4></div>";
	}
    } //end of function check_tel
    
    function check_datum($date, $desc)
    {
	global $fail, $error;
	
	$a_date = explode('.', $date);
	    
	$day =   intval($a_date["0"]);
	$month = intval($a_date["1"]);
	$year =  intval($a_date["2"]);
	
	if( !checkdate($month,$day,$year) )
	{
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Datum ".$desc." (".$date.") není ve správném formátu! (dd.mm.rrrr)</H4></div>";
	}
	
    } //end of function check_datum
    
    function check_b_reason($reason)
    {
    	if( (strlen($reason) > 30) )
	{
	    global $fail, $error;
	          
	    $fail="true";
	    $error .= "<div class=\"vlasnici-add-fail-nick\"><H4>Pole \"Důvod pozastavení\" je moc dlouhé! Maximální počet je 30 znaků.</H4></div>";
	}
    
    } //end of function check_b_reason
    
} // konec objektu vlastnici2-pridani

class vlastnikfind
{

   public static function vypis_tab ($par)
         {
	if ($par == 1) { echo "\n".'<table border="1" width="100%">'."\n"; }
	elseif ($par == 2) { echo "\n".'</table>'."\n"; }
	else    { echo "chybny vyber"; }
		  
			
	// konec funkce vypis_tab
	}
			
    function vypis ($sql,$dotaz_source,$co = "")
    {
    
    if ( $co == 2)
    {
    
    // echo "<tr><td>sem fakturacni </td></tr>";
    
    $dotaz_sql = "SELECT t1.id_cloveka,t1.jmeno, t1.prijmeni, t1.mail, t1.telefon, t1.k_platbe, t1.ucetni_index, t1.poznamka,t1.fakturacni,
		    t1.ulice,t1.mesto,t1.psc,t1.vs,t1.icq,t1.pridano,t1.firma, t1.archiv,
                         t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic, t2.ucet, t2.splatnost, t2.cetnost
			 
		    FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id )
		    WHERE 
		    (
		    ( t2.ftitle LIKE '$sql' OR t2.fulice LIKE '$sql' OR t2.fmesto LIKE '$sql' OR t2.fpsc LIKE '$sql'
		     OR t2.ico LIKE '$sql' OR t2.dic LIKE '$sql' OR ucet LIKE '$sql' OR t2.splatnost LIKE '$sql' 
		     OR t2.cetnost LIKE '$sql' )
		     AND  ( archiv = 0 or archiv is null ) )
		     ";
    
    $dotaz=pg_query($dotaz_sql);
		     
    }
    else
    { $dotaz=pg_query($dotaz_source); }
    
    $radku=pg_num_rows($dotaz); 
	
        
    if($radku==0)
    {
	echo "<tr><td colspan=\"9\" ><span style=\"color: red; \" >Nenalezeny žádné odpovídající výrazy dle ";
	echo "hledaného \"".$sql."\".</span></td></tr>";
    }
    elseif( $radku > 25 ) echo "<tr><td><span style=\"color: red; \" >Nalezeno více záznamů než je limit, specifikujte hledaný výraz. </span></td></tr>";	
    else
    {
		   
	while( $data=pg_fetch_array($dotaz) ) {
		
	// if ($co == 2)	  
	
	echo "<tr><td colspan=\"14\"> <br> </td> </tr>
    
	    <tr> <td class=\"vlastnici-td-black\" colspan=\"2\" >[".$data["id_cloveka"]."] ".$data["nick"]."</td>
	    
		<td class=\"vlastnici-td-black\" colspan=\"2\">VS: ".$data["vs"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"4\"> Platit (bez DPH): ".$data["k_platbe"]."</td>
		<td class=\"vlastnici-td-black\" colspan=\"6\" align=\"right\" width=\"8%\" >"; 
		
		
	// tutady update a smazat, takze nic
	    echo "<br>";
		
	    echo "  </td> 
	    </tr>
	    
	    <tr> <td colspan=\"2\">".$data["jmeno"]." ".$data["prijmeni"]."<br>";
	    
	    echo $data["ulice"]."  ";
	    	    
	    echo "<a href=\"http://www.mapy.cz?query=".$data["ulice"].",".$data["mesto"]."\" target=\"_blank\" >ukaž na mapě</a>";
	    
	    echo " <br> ".$data["mesto"]." ".$data["psc"]."</td>";
	    
	    echo "<td colspan=\"11\">icq: ".$data["icq"]." <br>
	    mail: ".$data["mail"]." <br>
	    tel: ".$data["telefon"]." </td>
	    
	    </tr>
    
	    ";


    $id=$data["id_cloveka"];
    $id_f=$data["fakturacni"];
    
    // tady asi bude generovani fakturacnich udaju
    if ( ( $id_f > 0 ) )
    {

	fakturacni::vypis($id_f,$id);
    
    }
    // $sql="%";	    
    $co="3";

    //tady dalsi radka asi
    
    
    echo "<tr>";
    
    echo "<td colspan=\"\" ><span style=\"font-weight: bold; font-size: 20px;  \" >Detail vlastníka: ";
    
    $id_cloveka=$data["id_cloveka"];
    
    $firma_vlastnik=$data["firma"]; 
    $archiv_vlastnik=$data["archiv"];
    
    if ( $archiv_vlastnik == 1)
    { echo "V: <a href=\"vlastnici-archiv.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span> </td> \n"; }
    else
    { echo "V: <a href=\"vlastnici2.php?find_id=".$data["id_cloveka"]."\" >".$data["id_cloveka"]."</a> </span></td> \n"; }    		

    echo "</span></td>";
    
    $orezano = explode(':', $data["pridano"]);
    $pridano=$orezano[0].":".$orezano[1];

    echo "<td colspan=\"2\" width=\"250px\" >datum přidání: ".$pridano." </td>";
    
    echo "<td align=\"center\" width=\"50px\" >";

     echo " <img title=\"poznamka\" src=\"img2/poznamka3.png\" align=\"middle\" ";
     echo " onclick=\"window.alert(' poznámka: ".$data["poznamka"]." ');\" >";

    echo "</td>";

    echo "<td colspan=\"5\" ><br></td>";
            
    echo "</tr>";

	//konec while
	}

	// } //konec else if co == 2
		
	// konec else
	}
    
    
    // konec funkce vypis
    }
	
} // konec class vlastnikfind


