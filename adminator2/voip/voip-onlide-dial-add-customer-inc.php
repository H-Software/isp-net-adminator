<?php

echo "

<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" >
  
<tr>
	<td width=\"\">Výběr vlastníka: </td>
        <td colspan=\"1\" >

            <select size=\"1\" name=\"id_vlastnika\" onchange=\"self.document.forms.form3.submit()\" >";

          $sql="%".$vlastnik_hledani."%";

          $select1=" WHERE ( nick LIKE '$sql' OR jmeno LIKE '$sql' OR prijmeni LIKE '$sql' OR ulice LIKE '$sql' OR mesto LIKE '$sql' ";
          $select2=" OR psc LIKE '$sql' OR icq LIKE '$sql' OR mail LIKE '$sql' OR telefon LIKE '$sql' OR vs LIKE '$sql' OR id_cloveka LIKE '$sql' ) ";

         $dotaz_klienti = pg_query("SELECT * FROM vlastnici ".$select1.$select2." order by prijmeni ");

         $radku_klienti = pg_num_rows($dotaz_klienti);

         if ( $radku_klienti == 0 )
         { echo "<option value=\"0\" style=\"color: gray; font-style: bold; \">Žádný klient neodpovídá výběru/ není v databazi! </option>"; }
         else
         {

              echo "<option value=\"0\" style=\"color: gray; font-style: bold; \"> Není vybráno </option>";

              while( $data2=pg_fetch_array($dotaz_klienti) ):

              echo "<option value=\"".$data2["id_cloveka"]."\" ";

              if ( ( $id_vlastnika == $data2["id_cloveka"]) ){ echo " selected  "; }

              echo " >";

              echo " ".$data2["prijmeni"].", ".$data2["jmeno"]."  ( ".$data2["nick"]."  )  ";
              echo " ".$data2["ulice"].", VS: ".$data2["vs"];

              echo "</option> \n ";

              endwhile;

        }

    echo "</select>
        </td>
</tr>

<tr><td><br></td><tr>

<tr>
        <td colspan=\"\">Výběr vlastníka - hledání:</td>
        <td><input type=\"text\" size=\"20\" name=\"vlastnik_hledani\" value=\"".$vlastnik_hledani."\" >
            <span style=\"color: grey; font-weight: bold; \" >
             <span style=\"padding-left: 10px; \" ><input type=\"submit\" name=\"hledat\" value=\"Hledat\" ></span>
             <span style=\"padding-left: 10px; \" >hledaný výraz:</span>
	     <span style=\"color: #555555\">".$sql." </span>
	    </span>
	</td>
</tr>
											    
<tr><td colspan=\"2\" height=\"20px\" ><hr style=\"color: grey;\" ></td><tr>

  <tr valign=\"top\">
    <td class=\"verticalDividerCompanion\">



<!--- left cell  -->
<table cellspacing=\"0\" cellpadding=\"4\" border=\"0\" >

<tr>
     
    <td class=\"fieldName\"><b>Jméno klienta ( popis )</b></td>
    <td style=\"padding-right: 1cm;\">
      <input type=\"text\" size='30' name=\"name\" value=\"".$name."\"  maxlength=\"41\" >
    </td>
</tr>

<tr>
    <td class=\"fieldName\" style=\"border-bottom: 1px solid grey; padding-bottom: 7px; \" ><b>Blokováno</b></td>
    <td style=\"border-bottom: 1px solid grey; \" >
        <input type=\"checkbox\" name=\"blocked\" value=\"Y\" >
    </td>
</tr>

<tr style=\"margin-top: 10px; \">
	
	<td style=\"padding-top: 10px; \">Křestní jméno</td>
	<td style=\"padding-top: 10px; \"><input type=\"text\" size='20' name=\"firstname\" value=\"".$firstname."\" ></td>
</tr>
<tr>
	<td>Příjmení</td>
	<td><input type=\"text\"  name=\"lastname\" value=\"".$lastname."\" ></td>
</tr>
<tr class=\"lightGrey\" valign=\"top\">
	<td>Adresa</td>
	<td><input type=\"text\" name=\"address\" value=\"".$address."\" ></td>
</tr>

<tr class=\"lightGrey\">
    <td>Město</td>
   <td><input type=\"text\" size='25' name=\"city\" value=\"".$city."\" ></td>
</tr>

</tr></table>
</td><!--- left cell end  -->

<td>
<!--- right cell -->
<table cellspacing=\"0\" cellpadding=\"4\">

<tr class=\"lightGrey\">
    <td><b>Popis ( id vlastníka )</b></td>
    <td><input type=\"text\"size='25' name=\"note\" value=\"".$note."\" maxlength=\"32\" ></td>
</tr>

<tr>
    <td style=\"border-bottom: 1px solid grey; \"><b>Počáteční stav účtu</b></td>
    <td style=\"border-bottom: 1px solid grey; \">
	<input type=\"text\" size='25' name=\"balance\" value=\"0\" maxlength=\"41\" >
    </td>
</tr>

<tr>
	<td style=\"padding-top: 10px; \">Jméno firmy</td>
	<td style=\"padding-top: 10px; \">
	    <input type=\"text\" size='30' name=\"companyname\" value=\"".$companyname."\" maxlength=\"41\" ></td>
</tr>

<tr>
    <td>Telefon</td>
    <td><input  type=\"text\" size='30' name=\"phone\" value=\"".$phone."\" maxlength=\"21\" ></td>
</tr>

<tr>
    <td>E-mail</td>
    <td><input  type=\"text\" size='30' name=\"email\" value=\"".$email."\" maxlength=\"99\" ></td>
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
