<?php

echo "

<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" >
  
<tr>
	<td width=\"\">Výběr klienta ( customer ): </td>
        <td colspan=\"1\" >

            <select size=\"1\" name=\"id_customer\" >"; //onchange=\"self.document.forms.form3.submit()\" >";

              echo "<option value=\"0\" style=\"color: gray; font-style: bold; \"> Není vybráno </option>";

	      if( isset($_GET["id_customer"]) )
	      {
	       echo "<option value=\"".$id_customer."\" style=\"color: grey; \" selected >zvoleno fixně - id: ".$id_customer."</option>";
	      }
	      else
	      {
	       //zbytek zaridime skriptem
	       
	        ob_flush();
		flush();
		      
	       system("/var/www/cgi-bin/cgi-adm2/customer_list_for_select.pl \"".$id_customer."\" ");
	      }
	      // echo $vysl;
	      
     echo "</select>
        </td>
</tr>";

 echo "<tr><td colspan=\"2\" height=\"20px\" ><hr style=\"color: grey;\" ></td><tr>

  <tr valign=\"top\">
    <td class=\"verticalDividerCompanion\">

<!--- left cell  -->
<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\" >

<tr>
     
    <td class=\"fieldName\"><b>Identifikace účtu ( číslo )</b></td>
    <td style=\"padding-right: 1cm;\">
      <select size=\"1\" name=\"id\" onchange=\"self.document.forms.form3.submit()\" >
        <option value=\"0\" style=\"color: gray; font-style: bold; \"> Není vybráno </option>";
	
	//zbytek zaridime skriptem
	ob_flush();
	flush();
	       
	system("/var/www/cgi-bin/cgi-adm2/did_list_for_select.pl \"".$id."\" ");
    
echo "</select>
    </td>
</tr>

<tr>
    <td class=\"fieldName\" style=\"border-bottom: 1px solid grey; padding-bottom: 7px; \" ><b>Blokováno</b></td>
    <td style=\"border-bottom: 1px solid grey; \" >
        <input type=\"checkbox\" name=\"blocked\" value=\"Y\" >
    </td>
</tr>

<tr style=\"margin-top: 10px; \">
	<td style=\"padding-top: 10px; \">Kreditní limit</td>
	<td style=\"padding-top: 10px; \"><input type=\"text\" size='20' name=\"credit_limit\" value=\"".$credit_limit."\" ></td>
</tr>


<tr style=\"margin-top: 10px; \">
	<td style=\"padding-top: 10px; \">VoIP heslo</td>
	<td style=\"padding-top: 10px; \">
	    <input type=\"text\" size='20' name=\"h323_password\" value=\"".$h323_password."\" >
	    <span><input type=button value=\"Auto\" onClick=\"javascript:document.forms.form3.h323_password.value=gen()\" ></span>
	</td>
</tr>

<tr style=\"margin-top: 10px; \">
	<td style=\"padding-top: 10px; \">Časové pásmo</td>
	<td style=\"padding-top: 10px; \">
	    <select name=\"i_time_zone\" size=\"1\" >";
	    
	    require("voip-onlide-dial-account-add-time-inc.php");
	    
	echo "</select>
	</td>
</tr>

<tr style=\"margin-top: 10px; \">	
	<td style=\"padding-top: 10px; \">Jazyk weboveho rozhraní: </td>
	<td style=\"padding-top: 10px; \">
	    <select name=\"i_lang\" size=\"1\" >
		<option value=\"\">Základní jazyk</option>
		<option value=\"ar\">ar - Arabic</option>
		<option value=\"zh\">zh - Chinese Simplified</option>
		<option value=\"zh-tw\">zh-tw - Chinese Traditional</option>
		<option value=\"cs\">cs - Czech</option>
		<option value=\"en\" selected>en - English</option>
		<option value=\"et\">et - Estonian</option>
		<option value=\"fr\">fr - French</option>
		<option value=\"de\">de - German</option>
		<option value=\"hu\">hu - Hungarian</option>
		<option value=\"it\">it - Italian</option>
		<option value=\"lv\">lv - Latvian</option>
		<option value=\"lt\">lt - Lithuanian</option>
		<option value=\"no\">no - Norwegian</option>
		<option value=\"pl\">pl - Polish</option>
		<option value=\"pt\">pt - Portuguese</option>
		<option value=\"pt-br\">pt-br - Portuguese Brazilian</option>
		<option value=\"ru\">ru - Russian</option>
		<option value=\"es\">es - Spanish</option>
		<option value=\"sv\">sv - Swedish</option>
	    </select>
	</td>
</tr>

</tr></table>
</td><!--- left cell end  -->

<td>
<!--- right cell -->
<table cellspacing=\"0\" cellpadding=\"4\">

<tr class=\"lightGrey\">
    <td><b>Produkt: </b></td>
    <td>
	<select name=\"i_produkt\" size=\"1\" >
	  <option value=\"0\" style=\"color: gray; font-style: bold; \"> Není vybráno </option>
    	  <option value=\"16\">CZK - Simelon pro Simelon</option>
	  <option value=\"17\">CZK - Simelon pro Zákazníky</option>	
        </select>
    </td>
</tr>

<tr>
    <td style=\"border-bottom: 1px solid grey; \"><b>Počáteční stav účtu</b></td>
    <td style=\"border-bottom: 1px solid grey; \">
	<input type=\"text\" size='25' name=\"balance\" value=\"0\" maxlength=\"41\" >
    </td>
</tr>

<tr>
	<td style=\"padding-top: 10px; \">Login:</td>
	<td style=\"padding-top: 10px; \">
	    <input type=\"text\" size='30' name=\"login\" value=\"".$login."\" maxlength=\"41\" ></td>
</tr>

<tr>
	<td style=\"padding-top: 10px; \">Heslo:</td>
	<td style=\"padding-top: 10px; \">
	    <input type=\"text\" size='30' name=\"password\" value=\"".$password."\" maxlength=\"41\" >
	    <span><input type=button value=\"Auto\" onClick=\"javascript:document.forms.form3.password.value=gen()\" ></span>    
	</td>
</tr>

<tr>
	<td style=\"padding-top: 10px; \">Úroveň přístupů:</td>
	<td style=\"padding-top: 10px; \">
	    <select size=\"1\" name=\"i_acl\" >
		<option value=\"155\" selected>Account self-care</option>
		<option value=\"10005\">Uživatel</option>
	    </select>
	</td>
</tr>

</table>
<!--- right cell end -->
</td>
</tr>

<tr>
    <td colspan=\"2\" style=\"padding-top: 10px; \" align=\"center\" >
	<input type=\"hidden\" name=\"item\" value=\"".$item."\" >
	<input type=\"submit\" name=\"odeslano\" value=\"OK\"></td>
</tr>

</table>

";

?>
