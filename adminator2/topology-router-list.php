<?php

require("include/main.function.shared.php");
require_once("include/config.php"); 
require_once("include/check_login.php");
require_once("include/check_level.php");
require("include/class.php");

if ( !( check_level($level,85) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
}
 

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html> 
      <head> ';
      
echo "<script type=\"text/javascript\" src=\"include/js/simelon-global.js\"></script>";

require ("include/charset.php");

?>

<title>Adminator2 - Topology - výpis routerů</title> 

</head>

<body>

<?php require ("head.php"); ?>

<?php require ("category.php"); ?>

 <tr>
    <td colspan="2" bgcolor="silver" >
    <?php require("topology-cat2.php"); ?>
    </td>
 </tr>

  <tr>
    <td colspan="2">
              
    <?php
          
	  $typ=$_GET["typ"];
	  
	  $arr_sql_where = array();
	  
	  if( (strlen($_GET["filtrace"]) > 0) ){
	    $filtrace = intval($_GET["filtrace"]);
	  }
	  else{
	    $filtrace = 99;
	  }

	  if( (strlen($_GET["f_monitoring"]) > 0) ){
	    $f_monitoring = intval($_GET["f_monitoring"]);
	  }
	  else{
	    $f_monitoring = 99;
	  }

	  if( (strlen($_GET["f_alarm"]) > 0) ){
	    $f_alarm = intval($_GET["f_alarm"]);
	  }
	  else{
	    $f_alarm = 99;
	  }

	  if( (strlen($_GET["f_alarm_stav"]) > 0) ){
	    $f_alarm_stav = intval($_GET["f_alarm_stav"]);
	    
	    if($f_alarm_stav == 0 or $f_alarm_stav == 1 or $f_alarm_stav == 2){
		$f_alarm = 1;
	    }
	  }
	  else{
	    $f_alarm_stav = 99;
	  }

	  if( (strlen($_GET["f_id_routeru"]) > 0) ){
	    $f_id_routeru = intval($_GET["f_id_routeru"]);
	  }

	  if( (strlen($_GET["f_search"]) > 0) ){
	    $f_search = $_GET["f_search"];
	  }

	  if( (strlen($_GET["list"]) > 0) ){ 
	    $list = intval($_GET["list"]); 
	  }

	  if( (strlen($_GET["odeslano"]) > 0) ){ 
	    $odeslano = $_GET["odeslano"]; 
	  }

	  if( $_GET["odeslano"] == "OK" ){
	    $display = "visible";
	  }
	  else{
	    $display = "none";
	  }
	  
	  $get_odkazy = "".
		    urlencode("f_monitoring")."=".urlencode($f_monitoring).
	            "&".urlencode("filtrace")."=".urlencode($filtrace).
	            "&".urlencode("f_alarm")."=".urlencode($f_alarm).
	            "&".urlencode("f_alarm_stav")."=".urlencode($f_alarm_stav).
	            "&".urlencode("odeslano")."=".urlencode($odeslano).
	            "&".urlencode("f_search")."=".urlencode($f_search).
	            "&".urlencode("f_id_routeru")."=".urlencode($f_id_routeru).
		    "";
	                                                                                  
	  //priprava filtracnich podminek do pole
	  	  
	  if($filtrace == 0 or $filtrace == 1){
	    $arr_sql_where[] = "router_list.filtrace = '".$filtrace."'";
	  }
	  
	  if($f_monitoring == 0 or $f_monitoring == 1){
	    $arr_sql_where[] = "router_list.monitoring = '".$f_monitoring."'";	  
	  }
	 
	  if($f_alarm == 0 or $f_alarm == 1){
	    $arr_sql_where[] = "router_list.alarm = '".$f_alarm."'";	  
	  }
	  
	  if($f_alarm_stav == 0 or $f_alarm_stav == 1 or $f_alarm_stav == 2){
	    $arr_sql_where[] = " router_list.alarm_stav = '".$f_alarm_stav."'  ";	  
	  }
	  
	  if($f_id_routeru > 0){
	    $arr_sql_where[] = "router_list.id = '".$f_id_routeru."'";	     
	  }

	  if( isset($f_search) ){
	    $f_search_safe = $conn_mysql->real_escape_string($f_search);
	    
	    $arr_sql_where[] = "( router_list.nazev LIKE '%".$f_search_safe."%' OR ".
				" router_list.ip_adresa LIKE '%".$f_search_safe."%' OR ".
				" router_list.mac LIKE '%".$f_search_safe."%' OR ".
				" router_list2.nazev LIKE '%".$f_search_safe."%' OR ".
				" kategorie.jmeno LIKE '%".$f_search_safe."%' ".
				" ) ";	     	  
	  }
	  	  
	  if( (count($arr_sql_where) == 1) ){
	  
	    foreach ($arr_sql_where as $key => $val) {
		$sql_where2 = " WHERE ( ".$val." ) ";
	    }
	  }
	  elseif( (count($arr_sql_where) > 1) ){

	    $sql_where2 = " WHERE ( ";
	    
	    $i = 0;
	    
	    foreach ($arr_sql_where as $key => $val) {
	    
	      if($i == 0){
	        $sql_where2 .= $val;
	      }
	      else{
	        $sql_where2 .= " AND ".$val." ";
	      }
	      
	      $i++;
	    }
	    
	    $sql_where2 .= " ) ";
	    
	  }
	  
	  echo "<div style=\"padding-top: 15px; padding-bottom: 25px; \" >\n";
	  
	  echo "<span style=\" padding-left: 5px; font-size: 16px; font-weight: bold; \" >\n".
		    ".:: Výpis routerů ::. </span>\n";
	  echo "<span style=\"padding-left: 25px; \" >
	      <a href=\"topology-router-add.php\" >přidání nového routeru</a>
	        </span>\n";
	        
	  echo "<span style=\"padding-left: 25px; \" >
	          <a href=\"#\" onclick=\"visible_change(routers_filter)\" >filtr/hledání</a>
	        </span>\n";
	
	  echo "<span style=\"padding-left: 25px; \" >
	          <a href=\"?typ=1\" >hierarchický výpis</a>
	        </span>\n";
	                 	    
	  echo "</div>\n";
	
		
	echo "<form method=\"GET\" action=\"\" >";

	//filtr - hlavni okno
	echo "<div id=\"routers_filter\" style=\"width: 980px; margin: 10px; display: ".$display."; padding: 10px; border: 1px solid gray; \" >";

	//Monitorováno
	echo "<div style=\"width: 150px; float: left;\" >\n".
            "Monitorováno: </div>\n";

	echo "<div style=\"float: left; \">\n".
            "<select size=\"1\" name=\"f_monitoring\" >\n".
                "<option value=\"99\" style=\"color: gray;\" >Nevybráno</option>\n".
                "<option value=\"0\" "; if($f_monitoring == 0) echo " selected "; echo ">Ne</option>\n".
                "<option value=\"1\" "; if($f_monitoring == 1) echo " selected "; echo ">Ano</option>\n".
            "</select>\n".
       "</div>\n";

	//filtrace
	echo "<div style=\"width: 100px; float: left; padding-left: 10px; \" >\n".
            "Filtrace: </div>\n";

	echo "<div style=\"float: left; padding-left: 10px; \">\n".
            "<select size=\"1\" name=\"filtrace\" >\n".
                "<option value=\"99\" style=\"color: gray;\" >nevybráno</option>\n".
                "<option value=\"0\" "; if($filtrace == 0){ echo " selected "; } echo " >Ne</option>\n".
                "<option value=\"1\" "; if($filtrace == 1){ echo " selected "; } echo " >Ano</option>\n";
        echo "</select>\n".
      "</div>\n";

	//alarm
	echo "<div style=\"width: 100px; float: left; padding-left: 10px; \" >\n".
            "Alarm: </div>\n";

	echo "<div style=\"float: left; padding-left: 10px; \">\n".
            "<select size=\"1\" name=\"f_alarm\" >\n".
                "<option value=\"99\" style=\"color: gray;\" >nevybráno</option>\n".
                "<option value=\"0\" "; if($f_alarm == 0){ echo " selected "; } echo " >Ne</option>\n".
                "<option value=\"1\" "; if($f_alarm == 1){ echo " selected "; } echo " >Ano</option>\n";
        echo "</select>\n".
      "</div>\n";

	//alarm stav
	echo "<div style=\"width: 100px; float: left; padding-left: 10px; \" >\n".
            "Stav alarmu: </div>\n";

	echo "<div style=\"float: left; padding-left: 10px; \">\n".
            "<select size=\"1\" name=\"f_alarm_stav\" >\n".
                "<option value=\"99\" style=\"color: gray;\" >nevybráno</option>\n".
                "<option value=\"0\" "; if($f_alarm_stav == 0){ echo " selected "; } echo " >klid</option>\n".
                "<option value=\"1\" "; if($f_alarm_stav == 1){ echo " selected "; } echo " >warning</option>\n".
                "<option value=\"2\" "; if($f_alarm_stav == 2){ echo " selected "; } echo " >poplach</option>\n".
                "";
        echo "</select>\n".
      "</div>\n";

    //tlacitko
    echo "<div style=\"float: left; padding-left: 100%; width: 250px; text-align: right; padding-left: 10px; \" >\n".
            "<input type=\"submit\" name=\"odeslano\" value=\"OK\" ></div>\n";

    //oddelovac
    echo "<div style=\"clear: both; height: 5px; \"></div>\n";

    //druha radka
    echo "<div style=\"float: left; \" >Hledání: </div>\n";

    echo "<div style=\"float: left; padding-left: 20px; \" >".
	"<input type=\"text\" name=\"f_search\" value=\"".htmlspecialchars($f_search)."\" ></div>\n";

    echo "<div style=\"float: left; padding-left: 20px; \" >ID routeru: </div>\n";

    echo "<div style=\"float: left; padding-left: 20px; \" >".
	"<input type=\"text\" name=\"f_id_routeru\" size=\"3\" value=\"".htmlspecialchars($f_id_routeru)."\" ></div>\n";

    //tlacitko
    echo "<div style=\"float: left; padding-left: 10px; \" >".
            "<input type=\"submit\" name=\"odeslano\" value=\"OK\" >".
    	    "<intpu type=\"hidden\" name=\"list\" value=\"".$list."\" ></div>\n";

    //oddelovac
    echo "<div style=\"clear: both; \"></div>\n";

    echo "</div>\n";

    echo "</form>\n";
    
	  if($typ == 1)
	  {
	  
	    $dotaz_router_main=mysql_query("SELECT * FROM router_list WHERE id = 1 order by id");
	    $dotaz_router_main_radku=mysql_num_rows($dotaz_router_main);
	  
	    if( $dotaz_router_main_radku <> 1)
	    { 
		echo "<div style=\"font-size: 16px; font-weight: bold; color: red; \">Nelze vybrat hlavní router</div>\n"; 
		exit;    
	    }
	  
	    while( $data_main=mysql_fetch_array($dotaz_router_main) )
	    {
		//pouze erik
	   
		global $uroven_max; 
	
		$uroven_max=1;
	    	
	        echo "<table border=\"1\" width=\"1000px\" >\n";
		echo "<tr>\n";
	  
	        echo "<td> [".$data_main["id"]."] ".$data_main["nazev"];
	  
	        echo " <span style=\"color:grey; \">( ".$data_main["ip_adresa"]." ) </span>";
	  
		echo "</td>\n</tr>\n";
	
		$dotaz_router_1=mysql_query("SELECT * FROM router_list WHERE parent_router = 1 order by id");
		$dotaz_router_radku_1=mysql_num_rows($dotaz_router_1);
	    
		if( $dotaz_router_radku_1 > 0 )
		{
	    	    require("./include/hierarchy.php");
	         
	    	    //prvni uroven
	    	    while($data_router_1=mysql_fetch_array($dotaz_router_1))
	    	    {    
			global $uroven;
		
	    		$id = $data_router_1["id"];	
	       
	    		vypis_router($id,"0");
	              
	    	    } // while dotaz_router
		} // konec if dotaz_router_radku > 0
	  
		// echo "pokracujem ...";
	  
	    } // konec while
	  
	  //neprirazene rb
	  $uroven_max = $uroven_max + 2;
	  
	  echo "<tr><td><br></td></tr>";
	  
	  echo "<tr><td colspan=\"".$uroven_max."\" ><hr></td></tr>";
	  
	  echo "<tr><td colspan=\"".$uroven_max."\" ><br></td></tr>";
	  
	  echo "<tr><td colspan=\"".$uroven_max."\" >Nepřiřazené routery:  </td></tr>";
	  
	  $dotaz_routery=mysql_query("SELECT * FROM router_list WHERE ( parent_router = 0 and id != 1) order by id");
	  $dotaz_routery_radku=mysql_num_rows($dotaz_routery);
	  
	  if ( $dotaz_routery_radku < 1 )
	  { echo "<tr><td colspan=\"5\" > Žádné routery v databázi. </td></tr>"; }
	  else
	  {
	    while( $data=mysql_fetch_array($dotaz_routery) ):
	    
		echo "<tr>";
	  
		    echo "<td>".$data["id"]."</td>";
		    echo "<td>".$data["nazev"]."</td>";
		    echo "<td>".$data["ip_adresa"]."</td>";
		    
		    // parent router
		    echo "<td>";
		    
		    echo $data["parent_router"];
		    
		    $parent_router=$data["parent_router"];
		    $dotaz_sec=mysql_query("SELECT * FROM router_list WHERE id = '".intval($parent_router)."' ");
		    
		    while($data_sec=mysql_fetch_array($dotaz_sec))
		    { echo "<span style=\"color: grey; font-weight: bold;\"> ( ".htmlspecialchars($data_sec["nazev"])." ) </span>"; }
		    
		    echo "</td>";
		    //konec parent router
		    
		    echo "<td>".$data["mac"]."</td>";
		    
	    endwhile;  
	  
	  }
	  
	  echo "</table>";
	  
	  
	  } // konec if typ == 1
	  else
	  {
	  
	    //vypis routeru normal
	   
	    $sql_base_old = "SELECT router_list.id, nazev, ip_adresa, parent_router, mac, monitoring, monitoring_cat, alarm, alarm_stav, filtrace, ".
			"kategorie.jmeno as kategorie_jmeno FROM `router_list` LEFT JOIN kategorie ON router_list.monitoring_cat = kategorie.id ";
	   
	    $sql_rows = "router_list.id, router_list.nazev, router_list.ip_adresa, router_list.parent_router, ".
			"router_list.mac, router_list.monitoring, router_list.monitoring_cat, router_list.alarm, ".
			" router_list.alarm_stav, router_list.filtrace, router_list.warn, router_list.mail, ".
			" kategorie.jmeno AS kategorie_jmeno, router_list2.nazev AS parent_router_nazev";
	    
	    $sql_base = "SELECT ".$sql_rows." FROM router_list ".
			" LEFT JOIN kategorie ON router_list.monitoring_cat = kategorie.id ".
			" LEFT JOIN router_list AS router_list2 ON router_list.parent_router = router_list2.id ";
	   
	    $sql_final = $sql_base." ".$sql_where2." ORDER BY id";
	    
	    $dotaz_routery=$conn_mysql->query($sql_final);
	    $dotaz_routery_radku=$dotaz_routery->num_rows;
	  
	    if(!$dotaz_routery){
		
		echo "<div style=\"font-weight: bold; color: red; \" >Chyba SQL příkazu.</div>";
		echo "<div style=\"padding: 5px; color: gray; \" >SQL DEBUG: ".$sql_final."</div>";
		// echo "<div style=\"\" >".mysql_error()."</div>";
		
		
	    }
	    elseif( $dotaz_routery_radku < 1 )
	    { 
		echo "<div style=\"margin-left: 10px; padding-left: 10px; padding-right: 10px; ".
			"background-color: #ff8c00; height: 30px; width: 980px; \" >".
		    "<div style=\"padding-top: 5px;\" > Žádné záznamy dle hledaného kritéria. </div>".
		    "</div>"; 

		/*
		    //debug radka
		    echo "<div style=\"padding-top: 15px; padding-bottom: 25px; color: gray; \" >\n";
		    echo $sql_final;
		    echo "</div>\n";
		    //konec debug
		*/
	    }
	    else
	    {
		/*
		    //debug radka
		    echo "<div style=\"padding-top: 15px; padding-bottom: 25px; color: gray; \" >\n";
		    echo $sql_final;
		    echo "</div>\n";
		    //konec debug
		*/
		
		//prvky pro listovaci odkazy
		$paging_url = "?".$get_odkazy;
		    
		$paging = new paging_global($paging_url, 20, $list, "<div class=\"text-listing2\" style=\"width: 1000px; text-align: center; padding-top: 10px; padding-bottom: 10px;\">", "</div>\n", $sql_final);
		     
		$bude_chybet = ( (($list == "")||($list == "1")) ? 0 : ((($list-1) * $paging->interval)) );
		      
		$interval = $paging->interval;
		       
		//uprava sql
		$sql_final = $sql_final . " LIMIT ".$interval." OFFSET ".$bude_chybet." ";
	
		$dotaz_routery=$conn_mysql->query($sql_final);
		$dotaz_routery_radku=$dotaz_routery->num_rows;
	  	
	  	//listovani
	  	echo $paging->listInterval();
	  	         	        
		//hlavní tabulka
		echo "<table border=\"0\" style=\"width: 1000px; margin-left: 10px; \" >";
	  
                $pocet_sloupcu = "8";

                echo "<tr>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"30px\" >id: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"250px\" >název: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"120px\" >IP adresa: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"140px\">mac adresa: </td>\n".

                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"60px\" >alarm: </td>\n".
                        "<td style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"40px\" >filtrace: </td>\n".

                        "<td colspan=\"2\" style=\"border-bottom: 1px dashed gray; font-weight: bold;\" width=\"40px\" >detailní výpis</td>\n".

                      "</tr>\n";

                  //kategorie - druhy radek
                echo "<tr>\n".
                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"30px\" >&nbsp;</td>\n".
                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"250px\" >nadřazený router: </td>\n".
                        "<td colspan=\"2\" style=\"border-bottom: 1px solid black; font-weight: bold;\" >monitorování (kategorie): </td>\n".

                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >&nbsp;</td>\n".

                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >soubory: </td>\n".

                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >úprava: </td>\n".
                        "<td style=\"border-bottom: 1px solid black; font-weight: bold;\" width=\"40px\" >smazání: </td>\n".

                      "</tr>\n";

                echo "<tr>\n<td colspan=\"".$pocet_sloupcu."\" >&nbsp;\n</td>\n</tr>\n";


		while( $data=$dotaz_routery->fetch_array() ):
	    
                  $alarm=$data["alarm"];

                  //1.radek
                  echo "<tr>";

                    echo "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \" >".htmlspecialchars($data["id"])."</td>\n";
                    echo "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \" >".htmlspecialchars($data["nazev"])."</td>\n";
                    echo "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">".htmlspecialchars($data["ip_adresa"])."</td>\n";
                    echo "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">".htmlspecialchars($data["mac"])."</td>";


                    //alarm
                    echo "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">";

                    if ( $alarm==1 ){ echo "<span style=\"font-weight: bold; \">Ano</span>"; }
                    elseif ($alarm==0) { echo "Ne"; }
                    else { echo "N/A"; }

                    if ( $alarm == 1)
                    {
                      if ($data["alarm_stav"]==2){ echo "<span style=\"color: red; \"> (poplach) </span>"; }
                      elseif ($data["alarm_stav"]==1){ echo "<span style=\"color: orange;\"> (warning) </span>"; }
                      elseif ($data["alarm_stav"]==0) { echo "<span style=\"color: green; \"> (klid) </span>"; }
                      else { echo " (N/A) "; }
                    }

                    echo "</td>\n";

                   //konec alarmu

                   //filtrace
                   echo "<td style=\"border-bottom: 1px dashed gray; font-size: 15px; \">\n";
                      if ($data["filtrace"]==1 ){ echo "<span style=\"color: green; font-weight: bold; \">Ano</span>"; }
                      else{ echo "<span style=\"color: orange;\">Ne</span>"; }
                   echo "</td>\n";

                   //detail vypis
                   echo "<td colspan=\"2\" style=\"border-bottom: 1px dashed gray; font-size: 15px; \">\n".
                            "<a href=\"?f_id_routeru=".intval($data["id"])."&list_nodes=yes\">vypsat vysílače/nody</a></td>\n";

                echo "</tr>";

                //2.radek
                echo "<tr>";

                    //2.1 - id
                    echo "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";
            		echo "<a href=\"archiv-zmen.php?id_routeru=".intval($data["id"])."\" >H</a>";
            	    echo "</td>";

                    //2.2 - parent router
                    echo "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";
                        echo $data["parent_router_nazev"].
                        " <span style=\"color: grey; font-weight: bold;\">(".$data["parent_router"].")</span>\n".
                        "</td>\n";

                    //2.3-4 - monitoring
                    echo "<td colspan=\"2\" style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";

                    if( $data["monitoring"] == 1)
                    {
                        echo "<span style=\"font-weight: bold; \">";
                        echo "<a href=\"https://monitoring.simelon.net/mon/www-generated/rb_all_".$data["ip_adresa"].".php\" target=\"_blank\" >Ano</a></span>";
                    }
                    elseif ( $data["monitoring"] == 0) { echo "Ne";}
                    else { echo "N/A"; }

                    echo "<span style=\"color: grey; \"> ( ";
                    if ( $data["monitoring_cat"] > 0 )
                    { echo "<a href=\"https://monitoring.simelon.net/mon/www/rb_all.php\" target=\"_blank\" >"; }

                    echo htmlspecialchars($data["kategorie_jmeno"]." / ".$data["monitoring_cat"]);

                    if ( $data["monitoring_cat"] > 0 ){ echo "</a>"; }
                    echo " ) </span></td>";

                    //2.5 - alarm, 2cast
                    echo "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\">\n";

                        if ( $alarm == 1)
                            echo "( CW: ".$data["warn"]." CM: ".$data["mail"]." )";
                        else
                            echo "&nbsp;";

                    echo "</td>\n";

                    //2.6. - soubory
                    echo "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >\n".
                            "<a href=\"topology-router-mail.php?id=".$data["id"]."\">";
                    echo "<img src=\"img2/icon_files.jpg\" border=\"0\" height=\"20px\" ></a>\n</td>\n";

                   //uprava
                   echo "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >";
                    echo '<form method="POST" action="topology-router-add.php">
                        <input type="hidden" name="update_id" value="'.intval($data["id"]).'">
                        <input type="submit" value="update">
                        </form></span>';
                   echo "</td>\n";

                   //smazat
                   echo "<td style=\"border-bottom: 1px solid black; color: gray; font-size: 14px; padding-bottom: 3px;\" >\n";
                    echo '<form method="POST" action="topology-router-erase.php">
                        <input type="hidden" name="erase_id" value="'.intval($data["id"]).'">
                        <input type="submit" name="smazat" value="smazat" >
                        </form></span>';
                   echo "</td>\n";

                echo "</tr>\n";

	    
		//pokud s kliklo na vypis subnetu 
		
		if( ($_GET["list_nodes"] == "yes" and $f_id_routeru == $data["id"]) )
		{
		
		    echo "<tr><td colspan=\"11\" >";
		
		    $id_routeru = $data["id"];
		
		    $dotaz_top=mysql_query("SELECT * FROM nod_list WHERE router_id = '".intval($f_id_routeru)."' ");
		    $dotaz_top_radku=mysql_num_rows($dotaz_top);
		
	    	    if ( $dotaz_top_radku < 1)
		    { echo "<span style=\"color: teal; font-size: 16px; font-weight: bold;\">
			<p> Žádné aliasy/nody v databázi. </p></span>"; 
		    }
		    else
		    {
		    
		    echo "<table border=\"0\" width=\"100%\" >";
		    
		    while($data_top=mysql_fetch_array($dotaz_top)):
		    
		    echo "<tr>";
		    
		    echo "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">";
			echo $data_top["jmeno"]."</span></td>";
		    
		    echo "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">".$data_top["adresa"]."</span></td>";
		    
		    echo "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">".$data_top["ip_rozsah"]."</span></td>";
		    		    
		    echo "<td class=\"top-router-dolni1\"><span style=\"color: #777777; \">".$data_top["mac"]."</span></td>";
		    	    
		    if ( $data_top["stav"] == 1)
		    {
		     echo "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" bgcolor=\"green\" align=\"center\" >
		         <span style=\"color: white; font-size: 13px; \"> v pořádku </span></td>";
		     }
		     elseif ( $data_top["stav"] == 2)
		     {
		     echo "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" bgcolor=\"orange\" align=\"center\" >
		         <span style=\"color: white; font-size: 13px; \"> vytížen </span></td>";
		     }
		     elseif( $data_top["stav"] == 3 )
		     {
		     echo "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" bgcolor=\"red\" align=\"center\" >
		     <span style=\"color: white; font-size: 13px; \"> přetížen </span></td>";
		     }
		     else
		     {
		     echo "<td class=\"top-router-dolni1\" colspan=\"".$colspan_stav."\" >
		     <span style=\"color: #666666; font-size: 13px; \">".$data_top["stav"]."</span></td>";
		     }
		
		    $typ_vysilace=$data_top["typ_vysilace"];

            	    if ( $typ_vysilace == 1 ){ $typ_vysilace2="Metallic"; }
            	    elseif ( $typ_vysilace == 2 ){ $typ_vysilace2="ap-2,4GHz-OMNI"; }
            	    elseif ( $typ_vysilace == 3 ){ $typ_vysilace2="ap-2,4Ghz-sektor"; }
            	    elseif ( $typ_vysilace == 4 ){ $typ_vysilace2="ap-2.4Ghz-smerovka"; }
            	    elseif ( $typ_vysilace == 5 ){ $typ_vysilace2="ap-5.8Ghz-OMNI"; }
            	    elseif ( $typ_vysilace == 6 ){ $typ_vysilace2="ap-5.8Ghz-sektor"; }
            	    elseif ( $typ_vysilace == 7 ){ $typ_vysilace2="ap-5.8Ghz-smerovka"; }
            	    elseif ( $typ_vysilace == 8 ){ $typ_vysilace2="jiné"; }
            	    else { $typ_vysilace2=$typ_vysilace; }
																																	 
		    echo "<td class=\"top-router-dolni1\"><span style=\"color: grey; font-size: 12px; \">".$typ_vysilace2."</span></td>";
		    echo "<td class=\"top-router-dolni1\">";
		      echo "<a href=\"topology-nod-list.php?find=".$data_top["jmeno"]."\">detail nodu </a>";
		    echo "</td>";
		    
		    echo "</tr>";
		    
		    endwhile;
		
		    echo "</table>";
		
		} // konec else dotaz_top_radku < 1
		
		echo "</td></tr>";
		
		} // konec if get id == data id
		
	    endwhile;
	    
	    echo "</table>";
	  
	    //listovani
	    echo $paging->listInterval();

	  }
	  
	  
	  } // konec else typ == 1
	  
	  ?>
	    
    <!-- konec vlastniho obsahu -->	
  </td>
  </tr>
  
 </table>

</body> 
</html> 

