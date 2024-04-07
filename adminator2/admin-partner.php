<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

if ( !( check_level($level,112) ) )
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

include ("include/charset.php"); 

?>

<title>Adminator 2</title> 

</head> 

<body> 

<? include ("head.php"); ?> 

<? include ("category.php"); ?> 

 
 <tr>
 <td colspan="2" height="20" bgcolor="silver" >
 <? include("admin-subcat-inc.php"); ?>

  </td>
 </tr>
  
 <tr>
  <td colspan="2">
  <!-- zacatek vlastniho obsahu -->
  
  <?
    
   $update_id=$_GET["update_id"];
       
   $update_id2=$_POST["update_id2"];
   $nove_heslo=$_POST["nove_heslo"];
    
    $jmeno=$_POST["jmeno"];
    $heslo=$_POST["heslo"];
    $odeslat=$_POST["odeslat"];
    
    echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px; font-weight: bold; font-size: 18px; \">
              <span style=\"border-bottom: 1px solid grey; \" >Administrace uživatelů externí sekce partner programu</span>
            </div>";

    if( isset($update_id2) )
    {
	 echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px;
        font-weight: bold; font-size: 18px; \">Změna hesla</div>";

      if ( !($update_id2 > 0) )
      {
	echo "<div style=\"padding-left: 40px; font-size: 14px; weight-bold; \">Heslo nelze upravit! Chyba vstupních dat.</div>";

	exit;	
      }
      
     $nove_heslo_crypt=md5($nove_heslo);
     
     $add=mysql_query("UPDATE partner_login SET password='$nove_heslo_crypt' WHERE id='$update_id2' ");

    echo "  <div style=\"padding-left: 40px; \">";
      
    if ($add){ echo "<br><span style=\"color: green; font-size: 16px; font-weight: bold; \">Heslo upraveno.</span><br><br>"; }
    else { echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Heslo nelze upravit. </span>"; }  
    
    echo "</div>";
    
     echo "  <div style=\"padding-left: 40px; padding-top: 20px; \">
    		<a href=\"admin-partner.php\" >Zpět</a>
	    </div>";
    
    }
    elseif( isset($odeslat) )
    {
     // ulozeni noveho uzivatele
     echo "  <div style=\"padding-left: 40px; padding-bottom: 20px; padding-top: 20px;
     font-weight: bold; font-size: 18px; \">Přidání nového uživatele</div>";
    
     $heslo_crypt=md5($heslo);
     
     $add=mysql_query("INSERT INTO partner_login (login, password) VALUES ('$jmeno','$heslo_crypt') ");

    echo "  <div style=\"padding-left: 40px; \">";
      
    if ($add){ echo "<br><span style=\"color: green; font-size: 16px; font-weight: bold; \">Admin externí partner sekce úspěšně vložen.</span><br><br>"; }
    else { echo "<span style=\"color: red; font-weight: bold; font-size: 16px; \">Admina pro ext. partner program nelze vložit. </span>"; }  
    
    echo "</div>";
    
     echo "  <div style=\"padding-left: 40px; padding-top: 20px; \">
    		<a href=\"admin-partner.php\" >Zpět</a>
	    </div>";
    
    }	       
    elseif( isset($update_id) )
    {
    
     echo "<div style=\"padding-left: 40px; padding-bottom: 20px; \">";

	if ( $update_id > 0 )
	{
	   echo "<div style=\"font-weight: bold; \" >Změna hesla</div>";
	   
	   echo "<div style=\"padding-top: 20px; \">Zadejte nové heslo: </div>";
	    
	   echo "<div style=\"padding-top: 20px; \">
	   
		<form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >	   
		    <input type=\"text\" name=\"nove_heslo\" value=\"\" >
		    <input type=\"hidden\" name=\"update_id2\" value=\"".$update_id."\" >
	   
		<span style=\"padding-left: 20px; \">
		    <input type=\"submit\" name=\"odeslano2\" value=\"OK\" ></span>
	   
	   </div>
	   
	   
	   </form>";

	}
	else
	{ 
	    echo "<div style=\"color: red; font-weight: bold; \" >";
	    echo "Heslo nelze změnit. Chyba vstupních dat.</div>"; 
	}
	
     //echo "<div style=\"font-weight: bold; \" >Změna hesla ...work in progress</div>";  
     
     echo "</div>";
    }
    else
    {
    
    echo "<div style=\"padding-left: 40px; padding-bottom: 20px; \">

	    <div style=\"font-weight: bold; padding-bottom: 20px; \" >Přidání:</div>  
        
	    <form action=\"".$_SERVER["PHP_SELF"]."\" method=\"POST\" >
	    
	    <div style=\"padding-bottom: 20px; \" >
	    
	    <span style=\"padding-right: 20px;\" >Přihl. jméno: </span>
		<input type=\"text\" name=\"jmeno\" value=\"\" >
	    
	    <span style=\"padding-right: 20px; padding-left: 20px; \" >Přihl. heslo: </span>
		<input type=\"text\" name=\"heslo\" value=\"\" >
	
	    <span style=\"padding-right: 20px; padding-left: 20px; \" >
		<input type=\"submit\" name=\"odeslat\" value=\"OK\" >
	    </span>
	    
	    </form>
	    
	    </div>  
        
	    <div style=\"font-weight: bold; padding-bottom: 20px; \" >Výpis</div>  
    
	    <table border=\"0\" width=\"\" cellspacing=\"10\" >
	      <tr>
		<td class=\"admin-partner-vypis1\" >
		    <span style=\"font-weight: bold; \" >id: </span></td>
		<td class=\"admin-partner-vypis1\" >
		    <span style=\"font-weight: bold; \" >přihlašovací jméno: </span></td>
	        <td class=\"admin-partner-vypis1\" >
		    <span style=\"font-weight: bold; \" >heslo: </span></td>
		<td class=\"admin-partner-vypis1\" >
		    <span style=\"font-weight: bold; \" >změnit heslo: </span></td>	                
	      </tr>
	      
	      <tr>
	        <td colspan=\"4\" ><br></td>	      
	      </tr>";
	      
	$dotaz=mysql_query("SELECT * FROM partner_login ORDER BY id");
	$dotaz_radku = mysql_num_rows($dotaz);
	
	if( $dotaz_radku == 0)
	{
	    echo "<tr><td colspan=\"4\" >V databázi nejsou žádné záznamy. </td></tr>";
	}
	else
	{
	    while( $data=mysql_fetch_array($dotaz) )
	    {
	    
	     echo "
	      <tr>
		<td><span style=\"\" >".$data["id"]."</span></td>
		<td><span style=\"\" >".$data["login"]."</span></td>
	        <td><span style=\"\" >".$data["password"]."</span></td>
		<td><span style=\"\" >
		    <a href=\"".$_SERVER["PHP_SELF"]."?update_id=".$data["id"]."\">změnit heslo</span>
		</td>	                
	      </tr>";
	
	    } // konec while
	
	} // konec else if doraz_radku == 0
	      
	echo "</table>";
      
	echo "</div>"; 

      } //konec else if isset update_id
      
		     
  ?>
  
  <!-- konec vlasniho obsahu -->
  </td>
  </tr>
  
 </table>

</body> 
</html> 

