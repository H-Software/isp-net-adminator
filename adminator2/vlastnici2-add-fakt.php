<?php

require("include/main.function.shared.php");
require("include/config.php"); 
require_once ("include/check_login.php");
require_once ("include/check_level.php");

if( !( check_level($level,51) ) )
{
 // neni level
 header("Location: nolevelpage.php");
 
 echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
 exit;
   
}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require("include/charset.php"); 

?>

<title>Adminator 2 - vlastníci - fakturační skupiny</title>

</head>

<body>

<?php require("head.php"); ?>

<?php require("category.php"); ?>

  <tr>
  <td colspan="2">
  
  <?php
  
    $odeslano=$_GET["odeslano"]; 
    $id_vlastnika=$_GET["id_vlastnika"];
        
    $ftitle=$_GET["ftitle"];	
    $fulice=$_GET["fulice"];
    $fmesto=$_GET["fmesto"];	
    $fpsc=$_GET["fpsc"];
    $ico=$_GET["ico"];		
    $dic=$_GET["dic"];		
    $ucet=$_GET["ucet"];	
    $splatnost=$_GET["splatnost"];	
    $cetnost=$_GET["cetnost"];
    
  $dotaz1=pg_query("SELECT * FROM vlastnici WHERE id_cloveka='$id_vlastnika' ");
  while ( $data1 =pg_fetch_array($dotaz1) )
  { $id_f = $data1["fakturacni"]; }
  
  if ( $id_f > 0 )
  { 
    echo "<span style=\"font-size: 20px; \" > Vlastník již má vyplněné fakturační údaje. "; 
    exit; 
  }
  
  if( isset ($odeslano) ):
  //budeme ukladat ...
  
  if( !( preg_match('/^([[:digit:]]+)$/',$splatnost) ) )
  {  echo "<span style=\"font-size: 18px; margin: 20px; margin-top: 20px; margin-bottom: 20px; color: red;  \" >
	Pole splatnost musí obsahovat pouze čísla! </span>"; 
    exit; 
  }
  
  if ( !( preg_match('/^([[:digit:]]+)$/',$cetnost) ) )
  {  
    echo "<span style=\"font-size: 18px; margin: 20px; color: red; \">Pole četnost musí obsahovat pouze čísla! </span>"; 
    exit; 
  }
     
//  if ( !( ereg('^([[:digit:]]+)$',$ico) ) )
//  {  echo "<span style=\"font-size: 18px; margin: 20px; margin-top: 20px; margin-bottom: 20px; color: red;  \" >
//            Pole IČO musí obsahovat pouze čísla! </span>"; Exit; }

  $f_add=array( "ico" => $ico, "dic" => $dic, "ucet" => $ucet, "splatnost" => $splatnost, "cetnost" => $cetnost,
		"ftitle" => $ftitle, "fulice" => $fulice, "fmesto" => $fmesto, "fpsc" => $fpsc );
  
  $f_add_r=pg_insert($db_ok2,'fakturacni',$f_add);
  
  
  if ( !($f_add_r) )
  {
    echo "<br><span style=\"color: red; \" > Chyba při přidávání fakturačních údajů <br><br><br>";
    echo pg_last_error($db_ok2);
    echo "</span><br><br>";    
  }
  else
  {
    $dotaz_f=pg_query("SELECT * FROM fakturacni ORDER BY id ASC");
 
     while ( $data=pg_fetch_array($dotaz_f) )
     {  $id_fakturacni=$data["id"];   }
	 
    $vlastnici_upd = array ( "fakturacni" => $id_fakturacni );
  
    $vlastnici_upd_id = array ( "id_cloveka" => $id_vlastnika );
  
    $dotaz_v_r = pg_update($db_ok2,'vlastnici',$vlastnici_upd,$vlastnici_upd_id);
    
  }
    
  // pridame to do archivu zmen
  $pole = "<b>akce: pridani fakturacni adresy;</b><br>";
  $pole .= " [id_vlastnika] => ".$id_vlastnika." , [id_fakturacni] => ".$id_fakturacni;
    
  $pole .= " --= fakturacni udaje =-- ";
    
  foreach ($f_add as $key => $val) { $pole=$pole." [".$key."] => ".$val."\n"; }
    
  //$pole .= " <br> akci provedl: ".$nick." vysledek akce dle databáze: vlastniku: ".$dotaz_v_r." , fakturacni: ".$f_add_r.", datum akce: ".$datum;
       
  if( ( ($f_add_r == 1) and ($dotaz_v_r == 1) ) ){ $vysledek_write=1; }
       
  $add=$conn_mysql->query("INSERT INTO archiv_zmen (akce,provedeno_kym,vysledek) VALUES ('$pole','" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "','$vysledek_write')");

    		
  if( $dotaz_v_r ){ echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze vlastníků. </div></H3>\n"; }
  else{ echo "<div style=\"color: red; \">Chyba! Data do databáze vlastníků nelze uložit. </div><br>\n"; }
	    
  if($f_add_r){ echo "<br><H3><div style=\"color: green; \" >Data úspěšně uloženy do databáze fakturací. </div></H3>\n"; }
  else{ echo "<div style=\"color: red; \">Chyba! Data do databáze fakturací nelze uložit. </div><br>\n"; }
  
  else: 
  
  ?>
      
  <form method="GET" action="" >
  
  <table border="0" width="1024px">
  
     <tr><td colspan="2" > <input type="hidden" name="id_vlastnika" <? echo 'value="'.$id_vlastnika.'"'; ?> ></td></tr>
		  
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

