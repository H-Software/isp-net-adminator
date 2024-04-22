<?php

require("include/main.function.shared.php");
require_once("include/config.php");
require_once("include/check_login.php");
require_once("include/check_level.php");

if ( !( check_level($level,68) ) )
{
  // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Nepravneny pristup /chyba pristupu. STOP <br>";
 exit;
   
}	

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      <html> 
      <head> ';

require("include/charset.php");

?>

<title>Adminator 2 - vlastníci - úprava fakt. adresy</title> 

</head>

<body>

<?php require("head.php"); ?>

<?php include ("category.php"); ?>

 <tr>
  <td colspan="2">
  
  <?php
  
    $odeslano=$_POST["odeslano"]; 
    $id=$_GET["id"];
    
    if ( isset($odeslano) )
    {
	// odeslano, do promenych najeleme data z formulare
	$id=$_POST["id"];
        
	$ftitle=$_POST["ftitle"];	
	$fulice=$_POST["fulice"];
	$fmesto=$_POST["fmesto"];
	$fpsc=$_POST["fpsc"];
    
	$ico=$_POST["ico"];			
	$dic=$_POST["dic"];
	$ucet=$_POST["ucet"];
	$splatnost=$_POST["splatnost"];
	$cetnost=$_POST["cetnost"];
	
    }
    else
    {
	$dotaz=pg_query("SELECT * FROM fakturacni WHERE id='$id' ");
	$dotaz_radku=pg_num_rows($dotaz);
	
	if( $dotaz_radku <> 1)
	{
	    echo "Chyba v načítání původních údajů. STOP! ";
	    exit;
	}
	
	while ( $data = pg_fetch_array($dotaz) )
	{ 
	
	    $ftitle = $data["ftitle"]; 
	    $fulice = $data["fulice"];
	    $fmesto = $data["fmesto"];
	    $fpsc = $data["fpsc"];
	
	    $ico = $data["ico"];
	    $dic = $data["dic"];
	
	    $ucet = $data["ucet"];
	    $splatnost = $data["splatnost"];
	    $cetnost = $data["cetnost"];
	
	}
  
    }
    

    if ( isset($odeslano) ):
    	 
    $vlastnici_upd = array ( "ftitle" => $ftitle, "fulice" => $fulice, "fmesto" => $fmesto, "fpsc" => $fpsc, "ico" => $ico, "dic" =>$dic,
				"ucet" => $ucet, "splatnost" => $splatnost, "cetnost" => $cetnost  );
  
    $vlastnici_upd_id = array ( "id" => $id );
  
    $dotaz_v_r = pg_update($db_ok2,'fakturacni',$vlastnici_upd,$vlastnici_upd_id);
    
    // pridame to do archivu zmen
    $pole = "<b>akce</b>: uprava fakturacni adresy;<br>";
    $pole .= "[id] => ".$id.", nové data:";
    
    foreach ($vlastnici_upd as $key => $val) { $pole=$pole." <b>[".$key."]</b> => ".$val."\n"; }
    
    if( $dotaz_v_r === true){ $vysledek_write = 1; }
    
    $add=mysql_query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','$nick','$vysledek_write')");
   
    if( $dotaz_v_r ) { 
	echo "<br><H3><div style=\"color: green; \" >Data úspěšně upraveny.</div></H3>\n"; 
    }
    else
    {
        echo "<div style=\"color: red; \">Chyba! Data nelze upravit. </div><br>\n";
    }
	      
  else: ?>
      
  <form method="POST" action="" >
  
  <table border="0" width="1000px">
  
     <tr><td colspan="2" > <input type="hidden" name="id" <? echo 'value="'.intval($id).'"'; ?> ></td></tr>
		  
      <tr>
          <td colspan="2" >Fakturační adresa: </td>
      </tr>

      <tr>
          <td> <label>Jméno: </label> </td>
          <td><input type="text" name="ftitle" <? echo 'value="'.$ftitle.'"'; ?> > </td>
      </tr>
					      
      <tr>
          <td> <label>Ulice: <label>  </td>
	  <td> <input type="text" name="fulice" <? echo 'value="'.$fulice.'"'; ?> > </td>
      </tr>

      <tr>
          <td> <label> Město a PSČ: </label> </td>
          <td> <input type="text" name="fmesto" <? echo 'value="'.$fmesto.'"'; ?> >
           <input type="text" name="fpsc" <? echo 'value="'.$fpsc.'"'; ?> size="10" >
	   </td>
       </tr>
    
       <tr><td colspan="2"><br></td></tr>
	
       <tr>
           <td> <label>IČO a DIČ: </label> </td>
           <td> <input type="text" name="ico" <? echo 'value="'.$ico.'"'; ?> >
            <input type="text" name="dic" <? echo 'value="'.$dic.'"'; ?> >
    	 </td>
	</tr>
	
	<tr>
	    <td> <label>Účet: </label> </td>
	    <td> <input type="text" name="ucet" <? echo 'value="'.$ucet.'"'; ?> > </td>
	</tr>
	
	<tr><td colspan="2"><hr  align="left" width="50%" ></td></tr>
	
	<tr>
	    <td> <label> Splatnost: ( dní )</label> </td>
	    <td> <input type="text" name="splatnost" <? echo 'value="'.$splatnost.'"'; ?> > </td>
	</tr>
	
	<tr>
	    <td> <label> Četnost zasílání: </label> </td>
	    <td> <input type="text" name="cetnost" <? echo 'value="'.$cetnost.'"'; ?> > </td>
	</tr>
	
	<tr>
	    <td colspan="2"><hr align="left" width="50%" > </td>
	</tr>
	
	<tr>
	    <td colspan="2" align="center">
	    <hr>
	
	    <input name="odeslano" type="submit" value="OK" >
	    </td>
	</tr>
	
    </table>
    	
    <? endif; ?>	
  
  <!-- zbytek vnejsi tabulky -->  
  </td>
  </tr>
  
 </table>

</body> 
</html> 

