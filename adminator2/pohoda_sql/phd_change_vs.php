<?php

$cesta = "../";

//zde vyresit prepinani roku/db jednotek
if(!isset($_GET["rok"]))
{ $rok = date("Y");  }
else
{ $rok = intval($_GET["rok"]); }

$print_items = intval($_GET["print_items"]);

$update_items = intval($_GET["update_items"]);

//$mssql_db = "StwPh_26109824_".$rok;

$level_col = "lvl_phd_change_vs";

//ini_set("mssql.charset", "UTF-8");

require($cesta."include/main.function.shared.php");
require_once($cesta."include/config.php"); 

require_once($cesta."include/config.ms.php"); 

require($cesta."include/check_login.php");

require($cesta."include/check_level.php");

if( !( check_level2($level,$level_col) ) )
{ // neni level
  header("Location: ".$cesta."nolevelpage.php");
    
  echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
  exit;
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head>';

require($cesta."include/charset.php"); 

?>

<title>Adminator 2 :: PohodaSQL :: Change VS</title> 

</head> 

<body> 

<?php include ($cesta."head.php"); ?> 

<?php include ($cesta."category.php"); ?> 


 <tr>
   <td colspan="2" height="20" bgcolor="silver" ><? include("../fn-cat.php"); ?> </td>
 </tr>
        
 <tr>
  <td colspan="2">
  
<?php
  
    echo "<div style=\"padding-left: 10px; padding-top: 10px;\" >";
    
    echo "<div style=\"padding-bottom: 40px;\">".
    
	"<span style=\"font-family: Arial; font-weight: bold; \" >Změna Variabilního Symbolu</span>
	     u faktur z jednotky: <b>".$mssql_db."</b>, rok: <b>".$rok."</b>
	  </div>";
	
    echo "<form action=\"\" method=\"get\" >".
	  
	  "<div style=\"float: left; padding-right: 40px; \">Vyberte rok: </div>".
	  "<div style=\"float: left; padding-right: 40px; \" >
		<select name=\"rok\" size=\"1\" >";
		
		//vycet roku
		$start = 2006;
		$cil = date("Y");	
		
		for($i = $start; $i <= $cil; $i++)
		{
		    echo "<option value=\"".$i."\" ";
		    if( $i == $rok){ echo " selected "; }
		    echo " >".$i."</option>";
		}
		
    echo "	</select>
	  </div>";
	  
    echo " <div style=\"float: left; padding-right: 40px;\">
	    <input type=\"submit\" name=\"OK\" value=\"Vybrat\">
	  </div>";

    //oprava SQL views	
    $rs_check = check_required_sql_views();

    //zjistime pocet FA
    $num = marked_items_count();
    	  
    echo  "<div style=\"\" >Počet označených faktur: <b>".$num."</b></div>".
	 
          "<div style=\"padding-top: 5px; clear: both; width: 600px; height: 1px; color: #BBBBBB;\"><hr></div>".
	  
	  "<div style=\"margin-top: 5px; height: 40px; \">".
	    "<div style=\"float: left; font-size: 18px; width: 100px; height: 20px; margin-top: 10px; \">Akce: </div>".
	    
	    "<div style=\"float: left; \">".
		"<div style=\"float: left; width: 200px; text-align: center; \" >vypsat položky/faktury</div>".
	    
		"<div style=\"width: 300px; text-align: center; \" >upravit variabilní symboly (!NEVRATNÉ!)</div>".
	
		"<div style=\"float: left; width: 200px; text-align: center;\" >
		    <input type=\"checkbox\" name=\"print_items\" value=\"1\" "; if($print_items == 1)echo " checked "; echo ">
	        </div>".
		
		"<div style=\"width: 300px; text-align: center;\" >
		    <input type=\"checkbox\" name=\"update_items\" value=\"1\" "; if($update_items == 1)echo " checked "; echo ">
		</div>".
	
	    "</div>".
	    
	    "<div style=\"width: 100px; height: 20px; padding-top: 10px; padding-left: 40px; \">
		<input type=\"submit\" name=\"OK2\" value=\"PROVÉST\" >
	    </div>".
	    
	  "</div>".
	   
	  "</form>".
	  
	  "<div style=\"width: 99%; height: 1px; color: #BBBBBB; \" ><hr></div>";
	
    echo "<div style=\"padding-top: 10px; \"></div>"; 	 
    
    if($print_items == 1)
    {
	print_marked_items();
    }
    	  
    if($update_items==1)
    {
	update_marked_items();
    }
    
    echo "</div> ";   
 
 //
 // functions
 //
 
 function marked_items_count()
 {
    $q = mssql_query("SELECT id FROM qSTWFA_upd ");
    $num = mssql_num_rows($q);
    
    return $num;
 } //end of function marked_items_count
 
 function print_marked_items()
 {
    echo "<div style=\"padding-top: 0px; padding-bottom: 10px; font-size: 18px; font-weight: bold; \">".
	 "Výpis označených položek .... </div>";
    
    $q = mssql_query("SELECT id, Cislo, VarSym, Jmeno, Firma FROM qSTWFA_upd ");
    $num = mssql_num_rows($q);

    $sl1 = 80;
    $sl2 = 120;
    $sl3 = 120;
    $sl4 = 400;
    
    if($num > 0)
    { 
	echo "<div style=\"font-weight: bold; float: left; width: ".$sl1."px; \">ID</div>";
	echo "<div style=\"font-weight: bold; float: left; width: ".$sl2."px; \">Cislo</div>";
	echo "<div style=\"font-weight: bold; float: left; width: ".$sl3."px;\">VarSym</div>";
	echo "<div style=\"font-weight: bold; width: ".$sl4."px;\">Jméno / Firma</div>";            
	echo "<div style=\"padding-top: 10px; \"></div>";
    }
    else
    {
      echo "<div>Žádné označené položky (faktury) ve zvolené účetní jednotce.</div>";
      return false;
    }
    
    while($data=mssql_fetch_array($q))
    {
	$jmeno_a_firma = iconv('CP1250', 'UTF-8', $data["Jmeno"]." (".$data["Firma"].")");
	//$jmeno_a_firma = $data["Jmeno"]." (".$data["Firma"].")";
        
	echo "<div style=\"float: left; width: ".$sl1."px;\">".$data["id"]."</div>";
	echo "<div style=\"float: left; width: ".$sl2."px;\">".$data["Cislo"]."</div>";
	echo "<div style=\"float: left; width: ".$sl3."px;\">".$data["VarSym"]."</div>";
	echo "<div style=\"width: ".$sl4."px;\">".$jmeno_a_firma."</div>";
	echo "<div style=\"padding-top: 5px; border-top: 1px dashed #BBBBBB; \"></div>";    
    }
    
 } //end of function print_marked_items
 
 function check_required_sql_views(){
     
    global $mssql_spojeni;
 
    echo "<div style=\"clear: both; float: left; padding-top: 10px; padding-bottom: 10px; font-size: 14px; font-weight: bold; \">".
	 "Kontrola potřebný SQL pohledů... </div>";
    
    $sql_query_1 = "select count(*) from INFORMATION_SCHEMA.VIEWS ".
		" where table_schema = 'dbo' AND table_name = 'qSTWAD' ";

    $sql_query_2 = "select count(*) from INFORMATION_SCHEMA.VIEWS ".
		" where table_schema = 'dbo' AND table_name = 'qSTWFA_upd' ";
                    
    $upd_check_1 = mssql_query($sql_query_1);

    $upd_check_2 = mssql_query($sql_query_2);
    	
    $upd_check_rs_1 = mssql_result($upd_check_1, 0, 0);
    
    $upd_check_rs_2 = mssql_result($upd_check_2, 0, 0);
    
    if($upd_check_rs_1 == 1 and $upd_check_rs_2 == 1 ){

        echo "<div style=\"float: left; padding-top: 10px; padding-bottom: 10px; font-size: 18px; font-weight: bold; color: green;\">".
	 "OK</div>\n";
    
    }
    else{
    
        echo "<div style=\"float: left; padding-top: 10px; padding-bottom: 10px; font-size: 18px; font-weight: bold; color: orange;\">\n".
	 "Pohledy nenalezeny</div>\n";
    
	echo "<div style=\"clear: both; float: left; padding-top: 10px; padding-bottom: 10px; font-size: 14px; font-weight: bold; \">".
	 "Vytvářím SQL Pohled(y)... </div>";
	
	$sql_create_views =
	
	"CREATE VIEW dbo.qSTWAD\n".
	"AS \n".
	"\n".
	"SELECT AD.* \n".
	"FROM dbo.AD \n".
	"WHERE (Smlouva IS NOT NULL) OR (Smlouva <> '');\n".
	"\n";

	$sql_create_views2 =
	
	"CREATE VIEW dbo.qSTWFA_upd\n".
	"AS \n".
	"\n".
	"SELECT dbo.FA.*, qSTWAD.Smlouva ".
	"FROM FA LEFT JOIN qSTWAD ON FA.RefAD = qSTWAD.ID ".
	"WHERE (((qSTWAD.Smlouva) Is Not Null) AND ((FA.Sel)=1) AND ((FA.RelTpFak)=1)) OR ".
	" (((qSTWAD.Smlouva)<>'') AND ((FA.Sel)=1) AND ((FA.RelTpFak)=1));\n".
	"\n";

	if($upd_check_rs_1 <> 1){
	    $upd_create_views = mssql_query($sql_create_views);
	}
	
	if($upd_check_rs_2 <> 1){
	    $upd_create_views2 = mssql_query($sql_create_views2);
	}
	
	if($upd_create_views === false or $upd_create_views2 === false){
	
	    echo "<div style=\"float: left; padding-top: 10px; padding-bottom: 10px; font-size: 18px; font-weight: bold; color: red;\">".
	    "Chyba";
	    
	    echo " <span>MSSQL error: ". mssql_get_last_message()."</span>\n";    	    
//    	    echo "<pre>".$sql_create_views."</pre>";
    	    
	    echo "</div>\n";
        
	}
        else{
    	
    	    echo "<div style=\"float: left; padding-top: 10px; padding-bottom: 10px; font-size: 18px; font-weight: bold; color: green;\">".
	    "OK</div>\n";
    	
    	} //konec else if upd_create ..
    	
    } //konec else if mssql_query
        
 } //end of function
 
  
 function update_marked_items()
 {
    global $mssql_spojeni;
    
    echo "<div style=\"padding-top: 10px; padding-bottom: 10px; font-size: 18px; font-weight: bold; \">".
	 "Úprava označených položek .... </div>";
    
    $upd = mssql_query("UPDATE dbo.qSTWFA_upd SET VarSym = qstwfa_upd.Smlouva;");
    //$upd = mssql_query("XXXqstwfa_upd.Smlouva;");
  
    if($upd === false)
    {
      //echo "ERROR update query <br>\n";
      echo "<div><span style=\"color: red; \">CHYBA! Úprava položek nebyla úspešná. </span>";
      echo " <span>MSSQL error: ". mssql_get_last_message()."</span></div>\n";
    }
    else
    {   
      $num_aff = mssql_rows_affected($mssql_spojeni);
      
      echo "<div><span style=\"color: green; \">OK! Položky úspěšně opraveny. </span>"; 
      echo "upraveno záznamů: ".$num_aff." </div>\n";
    
    }   
       
 } //end of function update_marked_items
 
?>
  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

