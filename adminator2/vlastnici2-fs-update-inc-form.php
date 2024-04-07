<? ?>
<form name="form1" method="post" <? echo 'action="'.$_SERVER["PHP_SELF"].'"'; ?> >
<input type="hidden" name="send" value="true" >
<input type="hidden" name="update_id" <? echo 'value="'.$update_id.'" >'; ?>

<table border="0" width="" cellspacing="5" >

   <tr><td colspan="2" ><br></td></tr>

        <tr>
	    <td colspan="" >&nbsp;</td>
	</tr>

        <tr>
         <td  width="50px" >Název skupiny: </td>
         <td><input type="text" name="nazev" size="30" <? echo "value=\"".$nazev."\""; ?> ></td>
        
	 <td width="50px" >&nbsp;</td>
	 
	 <td width="200px" rowspan="5" valign="top" >
	 <div style="padding-bottom: 10px; " >Fakturační text:</div>
	 
	 <textarea name="fakturacni_text" cols="35" rows="5" ><? echo $fakturacni_text; ?></textarea>
	 
	 </td>
	</tr>

        <tr><td colspan="2" ><br></td></tr>

        <tr>
         <td  width="250px" >Typ: </td>
          <td>
            <select name="typ" size="1" >
                <option value="1" <? if($typ == 1){ echo "selected "; } ?> >DÚ - domácí uživatel</option>
                <option value="2" <? if($typ == 2){ echo "selected "; } ?> >FÚ - firemní uživatel</option>
            </select>
         </td>
        </tr>

        <tr><td colspan="2" ><br></td></tr>

        <tr>
         <td  width="250px" >Typ služby:</td>
          <td>
            <select name="typ_sluzby" size="1" >
                <option value="0" <? if($typ_sluzby == 0){ echo "selected "; } ?> >wifi</option>
                <option value="1" <? if($typ_sluzby == 1){ echo "selected "; } ?> >optika</option>
            </select>
         </td>
        </tr>

        <tr><td colspan="2" ><br></td></tr>
	
	<tr>
	  <? /* sluzba internet */ ?>
          <td>
	    <span style="" ><b>Služba "Internet":</b></span>
	  </td>
	  <td>    
	    <select name="sluzba_int" size="1" onChange="self.document.forms.form1.submit()" >
		<option value="0" 
		    <? if( $sluzba_int == 0 or !isset($sluzba_int) ){ echo " selected "; } ?> >Ne</option>
		<option value="1" <? if( $sluzba_int == 1){ echo " selected "; } ?> >Ano</option>
	    </select>
	  </td>
	</tr>
	
	<tr>
	  <td>
	    <span style="" >Služba Internet :: Vyberte tarif:</span>
	  </td>
	  <td>
	  <?
	   if( $sluzba_int != 1)
	   {
	     echo "<span style=\"color: gray; \" >Není dostupné</span>";
	     echo "<input type=\"hidden\" name=\"sluzba_int_id_tarifu\" value=\"0\" >";
	   }
	   else
	   {
	      //vypis tarifu
	      echo "<select name=\"sluzba_int_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
			     
	     /* echo "<option value=\"0\" ";
	        if($sluzba_int_id_tarifu == 0 or !isset($sluzba_int_id_tarifu) ){ echo " selected "; }
	      echo " style=\"color: gray; \">Nevybráno</option>";
	     */							   
	      $dotaz_tarify_id_tarifu = mysql_query("SELECT * FROM tarify_int ORDER BY id_tarifu ");
										
	      while( $data_tarify = mysql_fetch_array($dotaz_tarify_id_tarifu) )
	      {
	          echo "<option value=\"".$data_tarify["id_tarifu"]."\" ";
	          if( $sluzba_int_id_tarifu == $data_tarify["id_tarifu"] ){ echo " selected "; }
	          echo " >".$data_tarify["jmeno_tarifu"]." (".$data_tarify["zkratka_tarifu"].")</option>";
	      }
																						                echo "</select>";
	    }// konec else if sluzba_int != 1
	   
	  ?>
	  </td>
	</tr>

	<tr><td colspan="2" ><br></td></tr>
	
	<tr>
	  <? /* sluzba iptv */ ?>
          <td>
	    <span style="" ><b>Služba "IPTV" (televize):</b></span>
	  </td>
	  <td>    
	    <select name="sluzba_iptv" size="1" onChange="self.document.forms.form1.submit()" >
		<option value="0" 
		    <? if( $sluzba_iptv == 0 or !isset($sluzba_iptv) ){ echo " selected "; } ?> >Ne</option>
		<option value="1" <? if( $sluzba_iptv == 1){ echo " selected "; } ?> >Ano</option>
	    </select>
	  </td>
	</tr>
	
	<tr>
	  <td>
	    <span style="" >Služba IPTV :: Vyberte tarif:</span>
	  </td>
	  <td>
	  <?
	   if( $sluzba_iptv != 1)
	   {
	     echo "<span style=\"color: gray; \" >Není dostupné</span>";
	   }
	   else
	   {
	      //vypis tarifu
	      echo "<select name=\"sluzba_iptv_id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";
				      
	      echo "<option value=\"0\" ";
               if($sluzba_iptv_id_tarifu == 0 or !isset($sluzba_iptv_id_tarifu) ){ echo " selected "; }
	      echo " style=\"color: gray; \">Nevybráno</option>";
	      
	      $dotaz_iptv_id_tarifu = mysql_query("SELECT * FROM tarify_iptv ORDER BY id_tarifu ");
	
	      while( $data_iptv = mysql_fetch_array($dotaz_iptv_id_tarifu) )
	      {
	         echo "<option value=\"".$data_iptv["id_tarifu"]."\" ";
	           if( $sluzba_iptv_id_tarifu == $data_iptv["id_tarifu"] ){ echo " selected "; }
	         echo " >".$data_iptv["jmeno_tarifu"]." (".$data_iptv["zkratka_tarifu"].")</option>";
	      }
	 
	      echo "</select>";
	      
	    }// konec else if sluzba_iptv != 1
	   
	  ?>
	  </td>
	</tr>
	
	<tr><td colspan="2" ><br></td></tr>
	
	<tr>
	  <? /* sluzba voip */ ?>
          <td>
	    <span style="" ><b>Služba "VoIP":</b></span>
	  </td>
	  <td>    
	    <select name="sluzba_voip" size="1" onChange="self.document.forms.form1.submit()" >
		<option value="0" 
		    <? if( $sluzba_voip == 0 or !isset($sluzba_voip) ){ echo " selected "; } ?> >Ne</option>
		<option value="1" <? if( $sluzba_voip == 1 ){ echo " selected "; } ?> >Ano</option>
	    </select>
	  </td>
	</tr>
	
	<tr><td colspan="2" ><br></td></tr>
	
   <tr><td colspan="4" ><br></td></tr>
		     
 <tr><td colspan="4" align="center" >
  <input type="submit" value="OK / Odeslat / Uložit .... " name="odeslano" style="width: 400px; background-color: green; color: white; " >
 </td></tr>
	
</table>
</form>
