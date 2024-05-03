<?php

include ("include/config.php"); 
include ("include/check_login.php");

include ("include/check_level.php");

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

include ("include/charset.php"); 

    $klient=$_GET["klient"];
    
    include("include/config.pg.php");
    
 $vysledek=pg_exec($db_ok2,"SELECT * FROM vlastnici WHERE id_cloveka='$klient' " );
 $radku=pg_num_rows($vysledek);

 if ($radku==0) { $error="1"; }
 else
   {

     while ($zaznam=pg_fetch_array($vysledek)):
     
        $id_cloveka=$zaznam["id_cloveka"];
	$jmeno=$zaznam["jmeno"];
	$prijmeni=$zaznam["prijmeni"];
	$ulice=$zaznam["ulice"];
	$mesto=$zaznam["mesto"];
	$psc=$zaznam["psc"];
					
    endwhile;
    
   }				   
   
   $vysl_user=$conn_mysql->query("SELECT * FROM users_old WHERE login LIKE '" . \Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email . "' ");
   $radku_user = $vysl_user->num_rows();
   
   if ( $radku_user == 0){ }
   else
   {
    while ($data_user=mysql_fetch_array($vysl_user) ):
    
    $vytvoril=$data_user["name"];
    
    endwhile;
   
   }
   
?>

<title>Adminator 2</title>

</head> 

<body> 

<? if ( $error == 1 )
    {
    
    echo "CHYBA! Nepodarilo se najit vlastnika! ";
    
    echo "</body></html>";
    exit;
    
    }

?>

<table border="0" width="100%">
    
<!-- <tr>
	<td colspan="4">&nbsp;</td>
    </tr>
-->

    <tr>
        <td width="" colspan="4">
	
	<!-- hlavickova tabulka -->
	<table border="0" width="100%" >
	 <tr>
	
	<td align="center" > <H3> Hotovostní<span style="padding: 5px; "></span> platba </H3> </td>
	
	<td align="right" > <img src="img2/logo.png" width="150px" ></td>
    
	</tr>
    
	</table>
	<!-- konec hlavickovy tabulky -->
    
	</td>
    </tr>

    <tr>
        <td colspan="4" ><hr></td>	<!-- vodorovna cara -->
    </tr>

    <tr>
        <td>&nbsp;</td>			<!-- radka co dela merezu -->
	<td width="47%" >&nbsp;</td>
	<td width="47%" >&nbsp;</td>
	<td>&nbsp;</td>
    </tr>

    <tr>				
	<td colspan="2" >			<!-- wokno dodavatele -->
	 
	
	<span style="font-size: 12px; " >Dodavatel:</span>
	
	<table >
	<tr><td>
	<div style="border: 1px solid #000000; 
	padding-left: 30px; padding-right: 100px; padding-top: 10px; padding-bottom: 10px" >
	<b>
	<?
	
	if ( $_GET["firma"] == 2)
	{ 
	    echo 'Simelon, s.r.o. <br>
		  Žižkova 247 <br>
		  397 01 Písek
		    </b>
		    ';
	}
	else
	{
	    
	    echo 'Martin Lopušný <br>
		  Truhlářská 2161 <br>
		  397 01 Písek
		    </b>
		    ';
	}
	
	?>

	</div>
	</td></tr></table>
	
	</td>
	
	<td>
	
	<!-- wokno kontaktu -->
	
	<?
	
	if ( $_GET["firma"] == 2)
	{
	
	  echo '<span style="font-size: 8px;" >
	
		    <table border="0" width="100%" >
		    <tr>
	    
			<td style="font-size: 12px; " >IČ: </td>
			<td style="font-size: 12px; " > 26109824  </td>
	
			<td style="font-size: 12px; " >DIČ: </td>
			<td style="font-size: 12px; " > CZ26109824 </td>
		
		    </tr>
	    
		    <tr>
			<td style="font-size: 12px; " > tel.: </td>
			<td style="font-size: 12px; " > provozovna: 391 009 400 </td>
		
			<td colspan="2" style="font-size: 12px; " > obchod: 723 393 188 </td>
		
		    </tr>
	        
		    </table>
	
		</span>';
	
	}
	else
	{
	// udaje na F.O.
	
	  echo '<span style="font-size: 8px;" >
	
		    <table border="0" width="100%" >
		    <tr>
	    
			<td style="font-size: 12px; " >IČ: </td>
			<td style="font-size: 12px; " > 73525111  </td>
	
			<td style="font-size: 12px; " >DIČ: </td>
			<td style="font-size: 12px; " > CZ8204151582 </td>
		
		    </tr>
	    
		    <tr>
			<td style="font-size: 12px; " > tel.: </td>
			<td style="font-size: 12px; " > provozovna: 391 009 400 </td>
		
			<td colspan="2" style="font-size: 12px; " > obchod: 723 393 188 </td>
		
		    </tr>
	        
		    </table>
	
		</span>';
	
	}
	
	?>
	
	<!-- konec wokna kontaktu -->
	
	</td>
	
	<td>&nbsp;</td>
    </tr>

    <tr>
	<td></td>
        <td></td>
        
	<td>&nbsp;</td>
	<td>&nbsp;</td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td valign="top" >
	
	 <?
	    $datum2 = strftime("%d/%m/%Y %H:%M", time());
		  
	    echo "<span style=\"color: grey; \"> Vystavil: ".$vytvoril."  <br>";
	
	    echo "<br>";
	    		   
	    echo "datum: ".$datum2;
	?>
					     
	
	</td>
        
	<td colspan="2" >
	
	<span style="font-size: 12px; " >Odběratel: </span> 
	<table><tr><td>
	<div style="border: 1px solid #000000; 
	    padding-left: 20px; padding-right: 150px; 
	    padding-top: 10px; padding-bottom: 10px"
	    
	    >
	    
	 <?
		 
	 echo "<H4> ";
	    echo $prijmeni."  ".$jmeno." <br> ";
		 
	    echo $ulice." <br> ".$mesto." ".$psc;
		
	echo "</H4> ";	 
	
	 ?>
	
	</div>
	</td></tr></table>
	
	 </td>
	 
	 
    </tr>

    <tr>
	<td>&nbsp;</td>
        <td > </td>
	 <td >	</td>
        <td>&nbsp;</td>
    </tr>

    
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
    </tr>
    
    <tr>
    <td colspan="4"> <br ></td>
    </tr>

    <tr>
    <td colspan="4"> <br></td>
    </td>
    
     <tr>
         <td colspan="4"> 
	 
	 <table border="0" width="100%">
	    
	 <tr><td colspan="4"> <hr color="#000000" size="2" > </td></tr>
	 <tr>
	 <td><b>Název </b></td>
	 <td><b>Za období  </b></td>
	 <td><b>Cena </b></td>
	
	 </tr>
	
	 
	 <tr><td colspan="4" > <hr color="#000000" size="3"> </td></tr>
	 
	 <tr>
	 <td colspan="3"> <br> </td>
	 </tr>
	 
	 <?
	 
	 if ( ( $_GET["dalsi"] == 1 ) )
	 {
	    $zaplaceno_za=$_GET["zaplaceno_za"];
	    $zaplaceno_do=$_GET["zaplaceno_do"];
	    
	     for ($i=$zaplaceno_za;$i<=$zaplaceno_do;$i++)
	    {
			
		if ( ($i < 10) and ( strlen($i) < 2) ){ $i="0".$i; }
		
		$obdobi=$_GET["rok"]."-".$i;     
		
		echo ' <tr>
		     <td> Měsíční konektivita ( internet ) </td>
		    <td> '.$obdobi.'</td>
		    <td> '.$_GET["castka"].' Kč </td>
		    </tr>';
	 
	    } // konec for-u
	 } // konec if get[dalsi] == 1
	 else
	 {
	     echo '
	    <tr>
	    <td> Měsíční konektivita ( internet ) </td>
	    <td> '.$_GET["zaplaceno_za"].'</td>
	    <td> '.$_GET["castka"].' Kč </td>
	    </tr>';
	 }
	 
	 ?>
	 
	 </table>
	 
	 
	 </td>
    </tr>
    
    <tr>
    <td colspan="4" >
    
    <div style="padding: 140px; ">
    </div>
    
    <span style="font-size: 18px; ">
    podpis:<span style="padding: 15px"></span>	. . . . . . . . . . . . . . . . . . .
    
    </span>
    
    </td></tr>
					 

</table>


</body> 
</html> 

