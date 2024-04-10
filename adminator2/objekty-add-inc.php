<?php ?>
<form name="form1" method="post" action="" >
<input type="hidden" name="send" value="true" >
<input type="hidden" name="update_id" <?php echo 'value="'.intval($update_id).'" >'; ?>

<table border="0" width="100%" >
    
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
	<td width="170px" >dns záznam:</td>
	<td width="380px" ><input type="Text" name="dns" size="30" maxlength="50" <?php echo 'value="'.$dns.'"'; ?> ></td>

	<td width="" >Přípojný bod - hledání:</td>
	<td width="" ><input type="Text" name="nod_find" size="30" <?php echo 'value="'.$nod_find.'"'; ?> ></td>

    </tr>

    <tr><td colspan="4" ><br></td></td>

   </tr>

    <tr>
      <td>typ ip adresy:</td>
      <td width="" >
    	     <table border="0">
	     <tr>
	      <td>
	       <input type="radio" name="typ_ip" onChange="self.document.forms.form1.submit()" value="1" 
	       <?php if ( ( $typ_ip==1 or (!isset($typ_ip)) ) ) { echo "checked"; } ?> >
	       <label>Neveřejná </label>
	       
	       <!--
	       <input type="radio" name="typ_ip" onchange="self.document.forms.form1.submit()" value="2" 
	       <?php if($typ_ip==2 ) { echo " checked "; } ?> >
	       -->
	       
	       <span style="padding-left: 5px; padding-right: 5px;"> | </span>
		<span style="padding-right: 10px;">Veřejná </span>
 	      </td>
	      <td> 
	       <select size="1" name="typ_ip" onchange="self.document.forms.form1.submit()" >
	         <option value="1" class="select-nevybrano" <?php if($typ_ip==1 ) { echo " selected "; } ?> >vyberte typ</option>
	 	 <option value="2" <?php if($typ_ip==2 ) { echo " selected "; } ?> >default - routovaná</option>
	    <?php		    
		if( ($update_id > 0) and ($typ_ip==3) )
		{
		  echo "<option value=\"3\"";
		   if($typ_ip==3 ) { echo " selected "; }
		  echo " >překládaná - snat/dnat</option> "; 
		}
	    ?>
		 <option value="4" <?php if($typ_ip==4 ) { echo " selected "; } ?> >tunelovaná - l2tp tunel</option>
	       </select>
	      </td>
	     </tr>
	    </table>
	                 		   
	    <input type="hidden" name="vip_rozsah" value="1" >

	</td>
	    
	<td><label> Přípojný bod: </label></td>
        <td>
<?php
	$sql_nod = "SELECT * from nod_list WHERE ( jmeno LIKE '%$nod_find%' ";
	$sql_nod .= " OR ip_rozsah LIKE '%$nod_find%' OR adresa LIKE '%$nod_find%' ";
	$sql_nod .= " OR pozn LIKE '%$nod_find%' ) AND ( typ_nodu = '1' ) ORDER BY jmeno ASC ";
	
	  try {
		$vysledek = $conn_mysql->query($sql_nod);
		$radku=$vysledek->num_rows;
	  } catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	  }
       
       print '<select size="1" name="selected_nod" onChange="self.document.forms.form1.submit()" >';

       if($typ_ip==4)
       {
    	 echo "<option value=\"572\" selected > verejne_ip_tunelovane ( 212.80.82.160 ) </option>"; 
       }	
       elseif( ($radku==0) )
       { 
    	 echo "<option value=\"0\" style=\"color: gray; \" selected >nelze zjistit / žádný nod nenalezen </option>"; 
       }
       else
       {
         echo '<option value="0" style="color: gray; font-style: bold; "';
          if( ( $_POST["selected"] == 0 ) or ( (!isset($selected_nod)) ) ) { echo "selected"; }
         echo ' > Není vybráno</option>';

          while ($zaznam2=$vysledek->fetch_array() )
			{
				echo '<option value="'.$zaznam2["id"].'"';
				if ( ( $selected_nod == $zaznam2["id"]) ){ echo " selected "; }
				echo '>'." ".$zaznam2["jmeno"]." ( ".$zaznam2["ip_rozsah"]." )".'</option>'." \n";
			} //konec while
        } //konec else
        
	print '</select>';
?>                                                                                                                                                         
	     <input type="button" value="Generovat / hledat (nody)" name="G" onClick="self.document.forms.form1.submit()" >
            </td>
		    
	</tr>
	
	<tr><td colspan="4" ><br></td></tr>
												 
	 <tr>
	     <td>ip adresa:</td>
	     <td><input type="Text" name="ip" size="30" maxlength="20" <?php echo 'value="'.$ip.'"'; ?> >
	     <?php 
	     //global $ip_error;
	     
	     if($ip_error == 1) 
	     { 
	      echo "<img title=\"error\" width=\"20px\" src=\"img2/warning.gif\" align=\"middle\" ";
	      echo "onclick=\" window.open('objekty-vypis-ip.php?id_rozsah=".$ip_rozsah."'); "."\">";
	     } 
	     ?>
	     
	     </td>
	    <td>
	     <?php
	        if($typ_ip == 3)
		{
		  echo "<label> Lokální adresa k veřejné: </label>";	
		}
		elseif($typ_ip==4)
		{
		  echo "Přihlašovací údaje 
			<span style=\"font-size: 11px;\">(k tunelovacímu serveru): </span>";
		}
		else
		{ echo "<span style=\"color: gray; \" >Není dostupné </span>"; }
	    
	      ?>
	    </td>
	    <td>
		
	    <?php
	    
	    /*
	    if ( $typ_ip == 3)
	    {
		$vysledek2=pg_query("select * from objekty where typ != 3 AND verejna=99 ORDER BY dns_jmeno ASC" );
                $radku2=pg_num_rows($vysledek2);

                if ($radku==0) { echo "žádné objekty v databázi "; }
                else
                {
                 print '<select size="1" name="vip_snat_lip" onChange="self.document.forms.form1.submit()" >';
                 print '<option value="0" style="color: gray; font-style: bold; "';

                 if ( ( $_POST["vip_snat_lip"] == 0 ) or ( (!isset($vip_snat_lip)) ) ) { echo "selected"; }
                 echo ' > Není vybráno</option>';

                  while ($zaznam3=pg_fetch_array($vysledek2) ):

                      echo '<option value="'.$zaznam3["ip"].'"';
                      if( ( $vip_snat_lip == $zaznam3["ip"]) ){ echo " selected "; }
                      echo '>'." ".$zaznam3["dns_jmeno"]." ( ".$zaznam3["ip"]." )".'</option>'." \n";

                  endwhile;
    
		}
	    }
	    else
	    */
	    
	    if($typ_ip==4)
	    {
	      echo "<span style=\"padding-right: 10px; padding-left: 5px;\">login:</span>".
	    	   "<input type=\"text\" name=\"tunnel_user\" size=\"6\" maxlength=\"4\" value=\"".$tunnel_user."\" >".
		   
		   "<span style=\"padding-left: 10px; padding-right: 5px\">heslo: </span>".
		   
		   "<input type=\"text\" name=\"tunnel_pass\" size=\"6\" maxlength=\"4\" value=\"".$tunnel_pass."\" >";
	    
	    }	
	    else
	    { echo "<span style=\"color: gray; \" >Není dostupné </span>"; }
	    
	    ?>
	    
	    </td>
	
	
	</tr>
				 
	
	<tr><td colspan="4" ><br></td></tr>
	
	<tr>
	    <td>mac adresa: <div style="font-size: 12px;">(prouze pro DHCP server/y)</div></td>
	    <td>
	      <?php
	        if($typ_ip==4)
		{ echo "<span style=\"color: gray; \" >Není dostupné </span>"; }
		else
		{ echo "<input type=\"text\" name=\"mac\" maxlength=\"17\" value=\"".$mac."\">"; }
	     ?>
	    </td>
	 
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>	
	 </tr>
												 
	<tr><td colspan="4"><br></td></tr>

	<tr>
	 <td>ip klientského zařízení: </td>
	 <td>
	    <?php
	      if( ($typ_ip <> 3) and ($typ_ip != 4) )
	      { echo "<input type=\"text\" name=\"client_ap_ip\" value=\"".$client_ap_ip."\" > "; }
	      else
	      { echo "<span style=\"color: gray; \">není dostupné</span>"; }
	    ?>
		
	 
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
      <td>Typ:</td>
      <td>
      
	 <select name="typ" onChange="self.document.forms.form1.submit()" >
	        <option value="1" <?php if ( $typ == 1) { echo " selected "; } ?> >poc (platici)</option>
	        <option value="2" <?php if ( $typ == 2) { echo " selected "; } ?> >poc (free)</option>
	        <option value="3" <?php if ( $typ == 3) { echo " selected "; } ?> >AP</option>
	 </select>
      
    </td>
    <td>Šikana: </td>
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
    
    ?>
    
    </td>
</tr>


<tr><td colspan="4" ><br></td></tr>

   <tr>	
    <td style="" >Tarif:</td>
    <td>
    
    <?php
    
    if( !isset($id_tarifu) )
    {
     if( $typ==3 ){ $find_tarif = "2"; } //ap-cko ...
     elseif( $typ_ip==3 ) //snat/dnat verejka ...
     { $find_tarif = "2"; }
     
      elseif( $garant == 2 ) //garant linka ...
      { } //.
      elseif( $tarif == 1 )  // asi SmallCity
      {  $find_tarif = "1"; }
      elseif( $tarif == 2 )  // Mp linka
      { $find_tarif = "0"; }
      else
      { $find_tarif = "0"; }
     }
     
     echo "<select name=\"id_tarifu\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >";

     //echo "<option value=\"\" class=\"select-nevybrano\" >Nevybráno</option>";
	try {
		$dotaz_t2 = $conn_mysql->query("SELECT * FROM tarify_int WHERE typ_tarifu = '0' ORDER BY zkratka_tarifu ");
	} catch (Exception $e) {
		die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
	}
    
     while( $data_t2 = $dotaz_t2->fetch_array() )
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
    ?>
    
    </td>
    
    <?php

    echo "<td>Šikana - počet dní: </td>
	    <td>";
       
    if( ( $typ==3 or ($sikana_status!=2) ) )
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
    	<textarea name="pozn" cols="30" rows="6" wrap="soft"><?php echo $pozn; ?></textarea>
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
    <td colspan="2" align="center">	
      <hr>
      <input name="odeslano" type="submit" value="OK">
    </td>
    <td colspan="2" >
     <br>
    </td>
</tr>
	
</table>
</form>
	
