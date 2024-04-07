<?php ?>
<form name="form1" method="post" action="" >
<input type="hidden" name="send" value="true">
<input type="hidden" name="update_id" <?php echo 'value="'.intval($update_id).'" >'; ?>
<input type="hidden" name="fakturacni" <?php echo 'value="'.intval($fakturacni).'" >'; ?>

<table border="1" width="100%">
    <tr>
	<td width="70">nick:
	<input type="Text" name="nick2" size="10" maxlength="20" <?php echo 'value="'.$nick2.'"'; ?> ></td>
    
	<td colspan="3" width="80" align="left" >
	
	
	vs: <input type="Text" name="vs" size="" maxlength="" <?php echo 'value="'.$vs.'"'; ?> >
	
	<span style="padding-left: 10px; padding-right: 10px; ">
	k platbě: </span><input type="text" name="k_platbe" size="" maxlength="" <?php echo 'value="'.$k_platbe.'"'; ?> >

	<span style="padding-left: 10px; padding-right: 10px; ">Splatnost (ke dni):
	<?php
	if ( $firma == 1)
	{ echo '<input type="text" name="splatnost" size="8" maxlength="" value="'.$splatnost.'" >'; } 
	else
	{ echo "<span style=\"color: grey; \" > není dostupné </span>"; }
	?>
	 </span> 
	
	
	</td>	
    </tr>

    <tr><td><br></td></tr>
		
       <tr>
	<td> jméno a příjmení: </td>
	   <td colspan="" >
	       <input type="text" name="jmeno" <?php echo 'value="'.$jmeno.'"';  ?> >
 	       <input type="text" name="prijmeni" <?php echo 'value="'.$prijmeni.'"'; ?> >    	   
	     </td>

	    <td>účetní index: <span style="padding-left: 10px; "></span>
	    <?php
	    //if ( $firma == 1)
	    { echo '<input type="text" name="ucetni_index" value="'.$ucetni_index.'" >'; }
	    //else
	    //{ echo "<span style=\"color: grey; \" >není dostupné</span>"; }
	    
	    ?>
	    </td>
	</tr>
	
	<tr><td><br></td></tr>
												 
	 <tr>
	     <td>Ulice a čp. :</td>
	     <td colspan="1" ><input type="text" name="ulice" size="35" maxlength="" <?php echo 'value="'.$ulice.'"'; ?> ></td>
	     <td>Fakturační skupina: 
	     
	     <?php
	     
	     if($firma == 1)
	     {
	     
	     echo ' <span style="padding-left: 10px;" >
	     
	     <select name="fakt_skupina" size="1" >
	     
	     '."\t\t".'<option value="0" class="vlastnici2-fakt-skupina" ';
	     if ($fakt_skupina == 0){ echo " selected "; }
	     echo ' > žádná </option> '."\n";
	     
	      if ( $fakturacni > 0){ $sql= "SELECT * FROM fakturacni_skupiny WHERE typ = 2 order by nazev DESC"; }
	      else { $sql = "SELECT * FROM fakturacni_skupiny WHERE typ = 1 order by nazev DESC"; }
	     	     
	      $dotaz_fakt_skup=mysql_query($sql);
	      $dotaz_fakt_skup_radku=mysql_num_rows($dotaz_fakt_skup);
		      
	       if( $dotaz_fakt_skup_radku > 0 )
	       {
	        while( $data_fakt_skup=mysql_fetch_array($dotaz_fakt_skup) )
	        {
						      
	          echo "\t\t<option value=\"".$data_fakt_skup["id"]."\" ";
	            if ($fakt_skupina == $data_fakt_skup["id"] ){ echo " selected "; }
		  echo " > ".$data_fakt_skup["nazev"];
		
		  if( $data_fakt_skup["typ"] == 1 ){ echo " (DÚ) "; }
		    elseif( $data_fakt_skup["typ"] == 2 ){ echo " (FÚ) "; }
		  else{ echo $data_fakt_skup["typ"]; }
					
		  echo " </option>\n";
												  
	        } // konec while
	       } // kone if dotaz > 0
	      
	      } // konec if firma == 1
	      else
	      { echo "<span style=\"color: grey; \" >není dostupné</span>"; }
	      
	     ?>
	     
	     </select>
	     </td>
	</tr>
				 
	
	<tr><td><br></td></tr>
	
	<tr>
		 <td>Město , PSČ: </td>
		<td colpsan="2" >
		    <input type="text" name="mesto" size="" maxlength="" <?php echo 'value="'.$mesto.'">'; ?> 
		    <input type="text" name="psc" size="10" <?php echo 'value="'.$psc.'">'; ?> 
		</td>
	 
	 <td valign="top" rowspan="7" >
	    Poznámka: <br>    
	    <textarea rows="10" name="poznamka" cols="40"><?php echo $poznamka; ?></textarea>
	 </td>
	 
	</tr>
												 
	<tr><td><br></td></tr>


	<tr>
	    <td>Email: </td>
	    <td colspan="3" ><input type="text" name="email" size="30" <?php echo 'value="'.$email.'"'; ?> ></td>
	</tr>
	
    <tr><td><br></td></tr>
    							 
     <tr>
	 <td>ICQ:</td>
	 <td colspan="3" >
	
	    <input type="text" name="icq" size="30" <?php echo 'value="'.$icq.'">'; ?>
	    
	 </td>
	
	
	
    </tr>

   <tr><td><br></td></tr>
														       
   <tr>
    <td>Telefon: </td>
   
    <td> <input type="text" name="tel" size="30" <?php echo 'value="'.$tel.'">'; ?> 
    </td>
    
    </tr>

    <tr><td colspan="2"><br></td></tr>

    <tr>
      <td> Firma: </td>
      <td colspan="1" >
      
      <select name="firma" size="1" onChange="self.document.forms.form1.submit()" >
      <option value="" <?php if ( ( $firma == "") ){ echo " selected "; } ?> >Fyzická os. ( vlastníci )</option>
      <option value="1" <?php if ( ( $firma == 1 ) ){ echo " selected "; } ?> >Simelon, s.r.o. ( vlastníci2 ) </option>
      </select>
      
      </td>
      
      <td>
      <?php
      
      if ( $update_status == "1")
      {
       echo "<span style=\"padding-right: 20px; \" >Archivovat: </span>"; 
        
       echo " <select name=\"archiv\" size=\"1\" >
         <option value=\"0\""; if ( ( $archiv != "1" ) ){ echo " selected "; } echo " > Ne </option>
         <option value=\"1\""; if ( ( $archiv == "1" ) ){ echo " selected "; } echo " > Ano </option>";
        
      }
      else
      { echo "<br>"; }
      
    echo ' </td>
    </tr>
    
    <tr><td colspan="3"><br></td><tr>

    <tr>
	<td colspan="" >Smlouva na dobu: </td>
	<td colspan="" >';

    if( $firma == 1 )
    {
	echo '<select name="typ_smlouvy" size="1" onChange="self.document.forms.form1.submit()" >    
		<option value="0"'; 
		    if( ($typ_smlouvy == 0) or ( !isset($typ_smlouvy) ) ){ echo " selected "; } 
		    
		    echo 'class="vlastnici-nezvoleno" >Nevybráno</option>
		<option value="1"'; if( $typ_smlouvy == 1){ echo " selected "; } echo ' >Neurčitou</option>
		<option value="2"'; if( $typ_smlouvy == 2){ echo " selected "; } echo ' >Určitou</option>
	    </select>';
    }
    else
    { echo "<span style=\"color: gray; \" >Není dostupné</span>"; }

    echo "</td>";
    
    echo "<td><span style=\"font-weight: bold;\" >Aktivace / deaktivace služeb:</span></td>";
    
    ?>
	
     </td>
    <tr>

    <tr><td colspan="3" ><br></td></tr>

    <tr>
	<td>Trvání do:</td>
    <?php
        echo "<td colspan=\"\" >";

	if( ( ($typ_smlouvy == 2) and ($firma == 1) ) )
	{ 
         echo "<input type=\"text\" name=\"trvani_do\" value=\"".$trvani_do."\" >"; 
         echo "<span style=\"padding-left: 15px; \" >formát: ( dd.mm.rrrr )</span>";
	}
	else 
	{ echo "<span style=\"color: grey; \">Není dostupné</span>"; }
    
	if( $firma == 1 )
	{
	  //sluzba internet
	  echo "<td><span style=\"padding-right: 40px; \" ><b>Internet:</b></span>";
	
	    echo "<select name=\"sluzba_int\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
	      echo "<option value=\"0\" "; if( $sluzba_int == 0 or !isset($sluzba_int) ){ echo " selected "; } echo " >Ne</option>";
	      echo "<option value=\"1\" "; if( $sluzba_int == 1 ){ echo " selected "; } echo " >Ano</option>";
	
	   echo "</select>";
	    
	  echo "</td>";
	
	}
	else
	{ echo "<td><span style=\"color: grey; \">Není dostupné</span></td>"; }
    
    ?>
    </tr>

    <?php
      if( $sluzba_int == 1 )
      {
        echo "<tr>
    		<td colspan=\"2\" >&nbsp;</td>";
	    echo "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";
	    
	     //vypis tarifu
	     echo "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
	     
	     echo "<option value=\"999\" "; 
	    	    if($sluzba_int_id_tarifu == 999 or !isset($sluzba_int_id_tarifu) ){ echo " selected "; }
	     echo " style=\"color: gray; \">Nevybráno</option>";
	     
	     $dotaz_tarify_id_tarifu = mysql_query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
	     
	     while( $data_tarify = mysql_fetch_array($dotaz_tarify_id_tarifu) )
	     {
	        echo "<option value=\"".$data_tarify["id_tarifu"]."\" ";
		    if( $sluzba_int_id_tarifu == $data_tarify["id_tarifu"] ){ echo " selected "; }
		echo " >".$data_tarify["jmeno_tarifu"]." (".$data_tarify["zkratka_tarifu"].")</option>";
	     
	     }
	     	     
	     echo "</select>";
	    echo "</td>";	
		
        echo "</tr>";
      
      }
    ?>
    <tr><td colspan="3" ><br></td></tr>
    
    <tr>
	<td colspan="" >Datum podpisu: </td>
	<td>

    <?php
	if ( $firma == 1 )
	{
	  echo '<input type="text" name="datum_podpisu" size="10" class=tcal value='."\"".$datum_podpisu."\" > (formát: dd.mm.yyyy)";
	}
	else
	{ echo "<span style=\"color: grey; \">Není dostupné</span>"; }

	echo "</td>";
	
	if( $firma == 1 )
	{
	  //sluzba iptv
	  echo "<td><span style=\"padding-right: 5px; \" ><b>IPTV</b> (televize): </span>";
	
	    echo "<select name=\"sluzba_iptv\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
	      echo "<option value=\"0\" "; 
	    		if( $sluzba_iptv == 0 or !isset($sluzba_iptv) ){ echo " selected "; } 
	      echo ">Ne</option>";
	      echo "<option value=\"1\" "; if( $sluzba_iptv == 1){ echo " selected "; }
	      echo ">Ano</option>";
	
	    echo "</select>";
	    
	  echo "</td>";
	}
	else
	{ echo "<td><span style=\"color: grey; \">Není dostupné</span></td>"; }

       echo "</tr>";

       if( $sluzba_iptv == 1 )
       {
          echo "<tr>
    		<td colspan=\"2\" >&nbsp;</td>";
	    echo "<td><span style=\"padding-right: 17px; \" >Vyberte tarif: </span>";
	    
	     //vypis tarifu
	     echo "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
	     
	     echo "<option value=\"999\" "; 
	    	    if($sluzba_iptv_id_tarifu == 999 or !isset($sluzba_iptv_id_tarifu) ){ echo " selected "; }
	     echo " style=\"color: gray; \">Nevybráno</option>";
	     
	     $dotaz_iptv_id_tarifu = mysql_query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
	     
	     while( $data_iptv = mysql_fetch_array($dotaz_iptv_id_tarifu) )
	     {
	        echo "<option value=\"".$data_iptv["id_tarifu"]."\" ";
		    if( $sluzba_iptv_id_tarifu == $data_iptv["id_tarifu"] ){ echo " selected "; }
		echo " >".$data_iptv["jmeno_tarifu"]." (".$data_iptv["zkratka_tarifu"].")</option>";
	     }
	     
	     echo "</select>";
	    echo "</td>";	
		
           echo "</tr>";
      
       }

       echo "<tr><td colspan=\"3\" ><br></td></tr>";
       
       echo "<tr>";
          echo "<td>Frekvence fakturování:</td>";      
          
       if( $firma == 1 )
       {
         echo "<td>".
		"<select size=\"1\" name=\"billing_freq\">";
	
	     echo "<option value=\"0\" "; if( $billing_freq == 0 or empty($billing_freq) )echo "selected"; echo " >Měsíční</option>";
	     echo "<option value=\"1\" "; if( $billing_freq == 1 )echo "selected"; echo " >Čtvrtletní</option>";
	    	    	
	   echo "</select>".
	    "</td>";
       }
       else
       {
          echo"<td><span style=\"color: grey; \">Není dostupné</span></td>";
       }
       
	if( $firma == 1 )
	{
	  //sluzba VoIP
	  echo "<td><span style=\"padding-right: 10px; \" ><b>VoIP</b> (telefon): </span>";
	
	    echo "<select name=\"sluzba_voip\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
	      echo "<option value=\"0\" ";
	    	    if( $sluzba_voip == 0 or !isset($sluzba_voip) ){ echo " selected "; }
	      echo " >Ne</option>";
	      echo "<option value=\"1\" ";
	    	    if( $sluzba_voip == 1 ){ echo " selected "; }
	      echo ">Ano</option>";
	
	    echo "</select>";
	    
	  echo "</td>";
	}
	else
	{ echo "<td><span style=\"color: grey; \">Není dostupné</span></td>"; }
    
    ?>

    <tr><td colspan="3" ><br></td></tr>

    <tr>
	<td>Pozastavené fakturace:</td>
	<td>
	    <?php

		echo "<select size=\"1\" name=\"billing_suspend_status\" onChange=\"self.document.forms.form1.submit()\">";
		    
		    echo "<option value=\"0\" "; if(($billing_suspend_status == 0) or (!isset($billing_suspend_status)) ) echo " selected "; echo ">Ne</option>";
		    echo "<option value=\"1\" "; if($billing_suspend_status == 1) echo " selected "; echo ">Ano</option>";
		    
		echo "</select>";
	    ?>
	</td>
	<td>Důvod pozastavení:</td>
    </tr>

    <tr>
	<td colspan="2" ><br></td>
    <?php
    
      if($billing_suspend_status == 1)
      {
	echo "<td rowspan=\"3\">
		<textarea type=\"text\" name=\"billing_suspend_reason\" cols=\"40\" rows=\"4\" >".htmlspecialchars($billing_suspend_reason)."</textarea>
	      </td>";
      }
      else
      {  
    	echo "<td rowspan=\"3\"><span style=\"color: grey; \">Není dostupné</span></td>"; 
        echo "<input type=\"hidden\" name=\"billing_suspend_reason\" value=\"".htmlspecialchars($billing_suspend_reason)."\" >";

      }

    ?>
    </tr>

    <tr>
        <td>Poz. fakturace - od kdy:</td>
	<td>
	
    <?php

    if($billing_suspend_status == 1)
    {
	 echo "<input type=\"text\" name=\"billing_suspend_start\" size=\"10\" class=\"tcal\" value=\"".
	    htmlspecialchars($billing_suspend_start)."\" > datum (formát: dd.mm.yyyy)";
    }
    else
    {  
	echo "<span style=\"color: grey; \">Není dostupné</span>"; 
        echo "<input type=\"hidden\" name=\"billing_suspend_start\" value=\"".htmlspecialchars($billing_suspend_start)."\" >";
    }
    
    
    ?>
	</td>
    </tr>
	
    <tr>
	<td>Poz. fakturace - do kdy:</td>
	<td>
	<?php
	if($billing_suspend_status == 1)
	{	
	    echo "<input type=\"text\" name=\"billing_suspend_stop\" size=\"10\" value=\"".
		htmlspecialchars($billing_suspend_stop)."\" class=\"tcal\"> datum (formát: dd.mm.yyyy)";
	}
	else
	{  
	    echo "<span style=\"color: grey; \">Není dostupné</span>"; 
	    echo "<input type=\"hidden\" name=\"billing_suspend_stop\" value=\"".htmlspecialchars($billing_suspend_stop)."\" >";
	}
	
	?>
	</td>
    </tr>
    
    <tr><td colspan="3" ><br></td></tr>
    
    <tr>
	<td colspan="2" align="center">
	
	<hr>
	<input name="odeslano" type="submit" value="OK" >
	</td>
	<td><br></td>
	
    </tr>

    <tr><td colspan="3" ><br></td></tr>

  </table>
 </form>

