<?php ?>
<form name="form1" method="post" action="" >
<input type="hidden" name="send" value="true" >
<input type="hidden" name="update_id" <?php echo 'value="'.$update_id.'" >'; ?>

<table border="0" width="100%" cellspacing="5" >
    
    <tr>
	<td><span style="font-weight: bold; font-size: 18px; color: teal;" >Mód:</span></td>
	<td >
	  <select size="1" name="mod_objektu" onChange="self.document.forms.form1.submit()" >
	    <option value="1" style="color: #CC0033;" 
	    <?php if($mod_objektu == 1) echo " selected "; ?> >Bezdrátová síť</option>
	    <option value="2" style="color: #e37d2b; font-weight: bold;" 
	    <?php if($mod_objektu == 2) echo " selected "; ?> >Optická síť</option>
	  </select>  
	</td>
    </tr>

    <tr><td colspan="4" ><br></td></td>
    
    <tr>
	<td width="20%" >dns záznam:</td>
	<td width="" ><input type="Text" name="dns" size="30" maxlength="50" <?php echo 'value="'.$dns.'"'; ?> ></td>

	<td width="" >Přípojný bod - hledání:</td>
	<td width="" ><input type="Text" name="nod_find" size="30" <?php echo 'value="'.$nod_find.'"'; ?> ></td>

    </tr>

    <tr><td colspan="4" ><br></td></tr>

    <tr>
      <td>typ ip adresy:</td>
      <td>
	       <input type="radio" name="typ_ip" onChange="self.document.forms.form1.submit()" value="1" 
	       <?php if ( ( $typ_ip==1 or (!isset($typ_ip)) ) ) { echo "checked"; } ?> >
	       <label>Neveřejná | </label>
	       
	       <input type="radio" name="typ_ip" onchange="self.document.forms.form1.submit()" value="2" 
	       <?php if ($typ_ip==2 ) { echo " checked "; } ?> >
	       <label>Veřejná </label>
       </td>
	    
	<td><label> Přípojný bod: </label></td>
        <td>
<?php
	$sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$nod_find%' ";
	$sql_nod .= " OR ip_rozsah LIKE '%$nod_find%' OR adresa LIKE '%$nod_find%' ";
	$sql_nod .= " OR pozn LIKE '%$nod_find%' ) AND ( typ_nodu = '2' ) ORDER BY jmeno ASC ";
	
       $vysledek=mysql_query($sql_nod);
       $radku=mysql_num_rows($vysledek);

       print '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

       if( ($radku==0) )
       { 
    	 echo "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>"; 
       }
       else
       {
         echo '<option value="0" style="color: gray; font-style: bold; "';
          if( (!isset($selected_nod)) ){ echo "selected"; }
         echo ' > Není vybráno</option>';

          while ($zaznam2=mysql_fetch_array($vysledek) )
	  {
            echo '<option value="'.$zaznam2["id"].'"';
              if ( ( $selected_nod == $zaznam2["id"]) ){ echo " selected "; }
            echo '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
	  } //konec while
        } //konec else
        
	print '</select>';
?>                                                                                                                                                         
	    <input type="button" value="Hledat (nody)" name="G" onClick="self.document.forms.form1.submit()" >
          </td>
	</tr>
	
	<tr><td colspan="4" ><br></td></tr>
												 
	<tr>
	   <td>ip adresa:</td>
	   <td><input type="Text" name="ip" size="30" maxlength="20" <?php echo 'value="'.$ip.'"'; ?> >
	     <?php 
	     //global $ip_error;
	     
	     if ($ip_error == 1) 
	     { 
	      echo "<img title=\"error\" width=\"20px\" src=\"img2/warning.gif\" align=\"middle\" ";
	      echo "onclick=\" window.open('objekty-vypis-ip.php?id_rozsah=".$ip_rozsah."'); "."\">";
	     } 
	     ?>
	     
	     </td>
	    <td>Linka: </td>
	    
	    <td>
    <?php
    
     if( !isset($id_tarifu) ){ $id_tarifu = "0"; }
     
     echo "<select name=\"id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

     //echo "<option value=\"\" class=\"select-nevybrano\" >Nevybráno</option>";
    
     $dotaz_t2 = mysql_query("SELECT * FROM tarify_int WHERE typ_tarifu = '1' ORDER BY gen_poradi ");
    
     while( $data_t2 = mysql_fetch_array($dotaz_t2) )
     { 
      echo "<option value=\"".$data_t2["id_tarifu"]."\" ";
             
      if( isset($find_tarif) )
      { if( ( $find_tarif == $data_t2["id_tarifu"] ) ){ echo " SELECTED "; } }
      else
      { 
        if( $id_tarifu == $data_t2["id_tarifu"] ){ echo " SELECTED "; } 
      }
       
       echo " >".$data_t2["zkratka_tarifu"];
       echo " (".$data_t2["jmeno_tarifu"]." :: ".$data_t2["speed_dwn"]."/".$data_t2["speed_upl"]." )</option> \n"; 
     }      
     
     echo "</select>";
    echo "</td>";

    	
    ?>	    
	  </td>
	</tr>
				 
	
	<tr><td colspan="4" ><br></td></tr>

	<tr>
	  <td>mac adresa:</td>
	  <td><input type="text" name="mac" size="30" <?php echo "value=\"".$mac."\""; ?> ></td>
	  <td colspan="2" align="center" >
	  <input type="button" value="Generovat údaje ...." name="G" 
	    style="width: 300px; background-color: red; color: white; " onClick="self.document.forms.form1.submit()" >
	</td></tr>

	<tr><td colspan="4" ><br></td></tr>
	
	<tr>

	 <td>Typ:</td>
	 <td>
		
	 <select name="typ" onChange="self.document.forms.form1.submit()" >
	        <option value="1" <?php if ( $typ == 1) { echo " selected "; } ?> >poc (platici)</option>
	        <option value="2" <?php if ( $typ == 2) { echo " selected "; } ?> >poc (free)</option>
	 </select>
	 
	 </td>
	
	    
	 <td>Povolen NET:</td>
	 <td>
		
	<?php
	    
	 if( ($typ==3) or ($typ_ip == 3) )
	 { 
	   if( $typ_ip ==3){ echo "<input type=\"hidden\" name=\"dov_net\" value=\"2\" >"; }
	   echo "<div class=\"objekty-not-allow\">není dostupné</div>"; 
	 }
	 else
	 {
    
	   echo "<input type=\"radio\" name=\"dov_net\" value=\"2\""; if ( ( $dov_net==2 or (!isset($dov_net)) ) ) { echo "checked"; } echo ">";
	   echo "<label>Ano | </label>";
				    
	   echo "<input type=\"radio\" name=\"dov_net\" value=\"1\""; if ( $dov_net==1 ) { echo "checked"; } echo ">";
	   echo "<label> Ne</label>";
			    
	  }
	  echo "</td>";
	    
	?>    
	</tr>
	
    <tr><td colspan="4" ><br></td></tr>
	
   <tr>

        <td>Číslo portu (ve switchi): </td>
	<td>
	  <select name="port_id" onChange="self.document.forms.form1.submit()" >
	  <?php
	   $pocet_portu = 24;
	   
	   for($i=1;$i<=$pocet_portu;$i++)
	   {
	     echo "<option value=\"".$i."\" ";
	      
	      if( $port_id == $i){ echo " selected "; }
	      
	     echo " >".$i."</option>";
	   }
	   
	  ?>
	  </select>
	</td>
	
	    
	<td>Šikana:</td>
	<td>
	<?php
	
	if ($typ==3 or $typ_ip==3 )
        { 
    	    echo "<div class=\"objekty-not-allow\">není dostupné</div>"; 
        }
        else
        {
    	    echo "<select name=\"sikana_status\" size=\"1\" onChange=\"self.document.forms.form1.submit()\"> \n";
    	    echo "<option value=\"1\" "; if ( ( $sikana_status==1 or (!isset($sikana_status) ) ) ) { echo " selected "; } echo ">Ne</option> \n";	    
    	    echo "<option value=\"2\" "; if ( $sikana_status==2 ) { echo " selected "; } echo ">Ano</option> \n";
    	    echo "</select>";
        }
    
        echo "</td>";

      ?>
    </tr>

    <tr><td colspan="4" ><br></td></tr>
     
    <tr> 
    
     <td> </td>
     <td> </td>
     
     <?php
    
      echo "<td>Šikana - počet dní: </td><td>";
       
      if ( ( $typ==3 or ($sikana_status!=2) ) )
      { 
       echo "<div class=\"objekty-not-allow\" >není dostupné</div>"; 
       echo "<input type=\"hidden\" name=\"sikana_cas\" value=\"".$sikana_cas."\">";
      }
      else
      { echo "<input type=\"text\" name=\"sikana_cas\" size=\"5\" value=\"".$sikana_cas."\" >"; }

    ?>
    
    </td>
  </tr>


 <tr><td colspan="4" ><br></td></tr>

 <tr>
    <td><label> poznámka:  </label></td>
    <td>
    	<textarea name="pozn" cols="30" rows="6" wrap="soft" ><? echo $pozn; ?></textarea>
    </td>
    
    <td><label>Šikana - text: </label></td>
    <td>
    <?php
     if( ( $typ ==3 or ($sikana_status!=2) ) ) 
     { 
      echo "<div class=\"objekty-not-allow\" >není dostupné</div>"; 
      echo "<input type=\"hidden\" name=\"sikana_text\" value=\"".$sikana_text."\" >";
     }
     else 
     { echo "<textarea name=\"sikana_text\" cols=\"30\" rows=\"4\" wrap=\"soft\" >".$sikana_text."</textarea>";  }
    ?>
    </td>
 </tr>

 <tr><td colspan="4" ><br></td></tr>

 <tr>
    <td><label> příslušnost MAC do jiné vlany <br>(v domovním switchi):  </label></td>
    <td colspan="2">
	<select name="another_vlan_id" size="1">
	<?php
	    echo "<option value=\"0\" style=\"color: grey;\">Nevybráno</option>";

	    $dotaz_a_vlan = mysql_query("SELECT jmeno, vlan_id FROM nod_list WHERE typ_nodu = '2' ORDER BY vlan_id ");

    	    while( $data_vlan = mysql_fetch_array($dotaz_a_vlan) )
    	    {	 
    		echo "<option value=\"".$data_vlan["vlan_id"]."\" ";

    		if( $another_vlan_id == $data_vlan["vlan_id"] ){ echo " SELECTED "; }
		
		echo " >".$data_vlan["jmeno"];
		echo " ( vlan_id: ".$data_vlan["vlan_id"]." )		
		    </option>";
	    }
	    
    /*
     $dotaz_t2 = mysql_query("SELECT * FROM tarify_int WHERE typ_tarifu = '1' ORDER BY gen_poradi ");
    
     while( $data_t2 = mysql_fetch_array($dotaz_t2) )
     { 
      echo "<option value=\"".$data_t2["id_tarifu"]."\" ";
             
      if( isset($find_tarif) )
      { if( ( $find_tarif == $data_t2["id_tarifu"] ) ){ echo " SELECTED "; } }
      else
      { 
        if( $id_tarifu == $data_t2["id_tarifu"] ){ echo " SELECTED "; } 
      }
       
       echo " >".$data_t2["zkratka_tarifu"];
       echo " (".$data_t2["jmeno_tarifu"]." :: ".$data_t2["speed_dwn"]."/".$data_t2["speed_upl"]." )</option> \n"; 
     }      
     */
	    
	?>
	</select>
    </td>

    <td></td>
 </tr>

 <tr><td colspan="4" ><br></td></tr>

 <tr><td colspan="4" align="center" >
  <input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" style="width: 400px; background-color: green; color: white; " >
 </td></tr>
	
</table>
</form>
