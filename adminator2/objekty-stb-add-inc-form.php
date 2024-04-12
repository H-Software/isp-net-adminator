<?php ?>
<form name="form1" method="post" action="" >
<input type="hidden" name="send" value="true" >
<input type="hidden" name="update_id" <?php echo 'value="'.intval($update_id).'" >'; ?>

<table border="0" width="1000px" cellspacing="5" >
    
    <tr>
	<td><span style="font-weight: bold; font-size: 18px; color: teal;" >Mód:</span></td>
	<td >
	    <span style="color: #e37d2b; font-weight: bold;" >Optická síť</span>
	</td>
    </tr>

    <tr><td colspan="4" ><br></td></td>
    
    <tr>
	<td width="20%" >Popis objektu:</td>
	<td width="" ><input type="Text" name="popis" size="30" maxlength="50" <?php echo "value=\"".htmlspecialchars($popis)."\""; ?> ></td>

	<td width="" >Přípojný bod - hledání:</td>
	<td width="" ><input type="Text" name="nod_find" size="30" <?php echo 'value="'.htmlspecialchars($nod_find).'"'; ?> ></td>

    </tr>

    <tr><td colspan="4" ><br></td></tr>

    <tr>
      <td>ip adresa:</td>
      <td>
	<input type="Text" name="ip" size="30" maxlength="20" <?php echo 'value="'.htmlspecialchars($ip).'"'; ?> >
      </td>
	    
	<td><label> Přípojný bod: </label></td>
        <td>
<?php
	$sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$nod_find%' ";
	$sql_nod .= " OR ip_rozsah LIKE '%$nod_find%' OR adresa LIKE '%$nod_find%' ";
	$sql_nod .= " OR pozn LIKE '%$nod_find%' ) AND ( typ_nodu = '2' ) ORDER BY jmeno ASC ";
	
       $vysledek=$conn_mysql->query($sql_nod);
       $radku=$vysledek->num_rows;

       print "<select size=\"1\" name=\"id_nodu\" onChange=\"self.document.forms.form1.submit()\" >";

       if( ($radku==0) )
       { 
    	 echo "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>"; 
       }
       else
       {
         echo "<option value=\"0\" style=\"color: gray; font-style: bold; \"";
          if( (!isset($id_nodu)) ){ echo "selected"; }
         echo " > Není vybráno</option> \n";

          while ($zaznam2=$vysledek->fetch_array() )
	  {
            echo '<option value="'.$zaznam2["id"].'"';
              if ( ( $id_nodu == $zaznam2["id"]) ){ echo " selected "; }
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
	  <td>mac adresa:</td>
	  <td><input type="text" name="mac" size="30" maxlength="17" <?php echo "value=\"".htmlspecialchars($mac)."\""; ?> ></td>
	  <td colspan="2" align="center" >
	  <input type="button" value="Generovat údaje ...." name="G" 
	    style="width: 300px; background-color: red; color: white; " onClick="self.document.forms.form1.submit()" >
	</td></tr>

	<tr><td colspan="4" ><br></td></tr>
	
	<tr>

	 <td>Puk:</td>
	 <td>
	    <input type="text" name="puk" size="30" maxlength="20" <?php echo 'value="'.htmlspecialchars($puk).'"'; ?> >	 
	 </td>
	
	
	 <td>Pin1:</td>
	 <td>wip</td>
	    
	</tr>
	
    <tr><td colspan="4" ><br></td></tr>
	
   <tr>
        <td>Číslo portu (ve switchi): </td>
	<td>
	  <select name="port_id" onChange="self.document.forms.form1.submit()" >
	  
	  <?php
	  
	   $pocet_portu = 48;
	   
	   for($i=1;$i<=$pocet_portu;$i++)
	   {
	     echo "<option value=\"".$i."\" ";
	      
	      if( $port_id == $i){ echo " selected "; }
	      
	     echo " >".$i."</option> \n";
	   }
	   
	  ?>
	  </select>
	</td>
	
	    
	<td>Pin2:</td>
	<td>wip</td>
    </tr>

   <tr><td colspan="4" ><br></td></tr>

   <tr>
     <td><label> poznámka:  </label></td>
     <td>
         <textarea name="pozn" cols="30" rows="6" wrap="soft" ><?php echo htmlspecialchars($pozn); ?></textarea>
     </td>
     
     <td><label> tarif: </label></td>
     <td>
        <select size="1" name="id_tarifu" >
    	    <option value="0" class="nevybrano" >nevybráno</option>
    	    
    	    <?php
    	    
    		$tarif_q = $conn_mysql->query("SELECT id_tarifu, jmeno_tarifu FROM tarify_iptv ORDER by jmeno_tarifu ASC");
    	    
    		while( $data_tarif = $tarif_q->fetch_array()){
    		
    		    echo "<option value=\"".intval($data_tarif["id_tarifu"])."\" ";
    		    
    		    if( $data_tarif["id_tarifu"] == $id_tarifu ){ echo " selected "; }
    		    
    		    echo " >";
    			echo htmlspecialchars($data_tarif["jmeno_tarifu"]);
    		    echo "</option>\n";
    		
    		}
    	    ?>
    	    
        </select>
     </td>
     
   </tr>		     

   <tr><td colspan="4" ><br></td></tr>
		     
 <tr><td colspan="4" align="center" >
  <input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" style="width: 400px; background-color: green; color: white; " >
 </td></tr>
	
</table>
</form>
