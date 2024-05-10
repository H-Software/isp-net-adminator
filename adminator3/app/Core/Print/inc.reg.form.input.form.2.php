<?php

echo "\n\n<form method=\"POST\" name=\"form1\" >\n";

echo $this->csrf_html;

echo "<table border=\"1\" width=\"1000px\">

    <tr>
        <td colspan=\"5\"><span style=\"font-size: 18px; font-weight: bold; \" >
	Průvodce tiskem registračního formuláře:
	</span></td>

    </tr>

        <tr>
                <td align=\"center\" colspan=\"5\"><br></td>
        </tr>\n";

echo "<tr>
                <td width=\"220px\" align=\"center\" class=\"label-font\" ><label>Evidenční číslo smlouvy: </label></td>
                <td width=\"220px\"><input type=\"text\" name=\"ec\" size=\"30\" class=\"input1\" value=\"".$ec."\"></td>
    		<td colspan=\"3\"><br></td>
	</tr>";

echo "<tr>
                <td colspan=\"5\" >&nbsp;</td>
        </tr>\n";

//
//   ZAKAZNIK
//
echo "<tr>
		<td colspan=\"5\">
		
		    <fieldset>
			<legend>Zákazník</legend>";

echo "</fieldset>
		</td>
	      </tr>";

echo "<tr><td colspan=\"5\"><br></td></tr>\n";

echo "<tr>
                <td colspan=\"5\" >
            	    <div style=\"padding-left: 20px; padding-bottom: 5px; font-weight: bold;\">HARDWARE A JEHO KONFIGURACE</div>
            	</td>
        </tr>";

echo "<tr>
                <td class=\"label-font\" style=\"padding-left: 20px;\"><label>Připojená technologie: </label></td>
                <td colspan=\"4\" >
                    <span style=\"margin-left: 10px; \" ></span>

                    <input type=\"radio\" name=\"prip_tech\" value=\"1\" onChange=\"self.document.forms.form1.submit()\" ";
if($prip_tech == 1) {
    echo " checked=\"checked\" ";
} echo " >
                    <span style=\"margin-left: 10px; \" >Optiká síť</span>
                    |
                    <input type=\"radio\" name=\"prip_tech\" value=\"2\" onChange=\"self.document.forms.form1.submit()\" ";
if($prip_tech == 2) {
    echo " checked=\"checked\" ";
} echo " >
                    <span style=\"margin-left: 10px; \" >Metalický okruh</span>
                    |
                    <input type=\"radio\" name=\"prip_tech\" value=\"3\" onChange=\"self.document.forms.form1.submit()\" ";
if($prip_tech == 3 or !isset($prip_tech)) {
    echo " checked=\"checked\" ";
} echo " >
                    <span style=\"margin-left: 10px; \" >Bezdrátová síť</span>
                </td>
          </tr>";

echo "</tr>";

echo "<tr><td colspan=\"5\" ><div style=\"height: 10px;\"></div></td></tr>";

echo "<tr>
		<td colspan=\"5\">
		    <div class=\"label-font\" style=\"float: left; padding-left: 20px; padding-right: 20px; \">Číslo portu:</div>
		    
		    <div style=\"float:left; \">
			<input type=\"text\" name=\"cislo_portu\" value=\"".$cislo_portu."\" size=\"5\" >
		    </div>
		
		    <div class=\"label-font\" style=\"float: left; padding-left: 20px; padding-right: 20px; \">Poznámka:</div>
		    
		    <div style=\"\">
			<input type=\"text\" name=\"poznamka\" value=\"".$poznamka."\" size=\"75\" >
		    </div>
		    
		</td>
	       </tr>";

echo "<tr><td colspan=\"5\"><br></td></tr>";

//INTERNET

echo "<tr>
		<td colspan=\"5\">
		
		    <fieldset>
			<legend>Internet</legend>";


echo   "<div style=\"float: left; padding-right: 15px;\"><label>Instalované zařízení - počet: </label></div>
			<select name=\"int_pocet_zarizeni\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			    <option value=\"0\" style=\"color: gray;\" ";
if(!isset($int_pocet_zarizeni)) {
    echo "selected ";
} echo" >Žádné</option>
			    
			    <option value=\"1\" ";
if($int_pocet_zarizeni == 1) {
    echo "selected ";
} echo " >1</option>
			    <option value=\"2\" ";
if($int_pocet_zarizeni == 2) {
    echo "selected ";
} echo " >2</option>
			    <option value=\"3\" ";
if($int_pocet_zarizeni == 3) {
    echo "selected ";
} echo " >3</option>
			    
			</select>";

echo "<div style=\"height: 5px;\"></div>";

//instalovani zarizeni

for($i = 1; $i <= $int_pocet_zarizeni; $i++) {

    if($i == 1) {
        echo "<div style=\"padding-bottom: 15px; padding-top: 10px; \">
				
				<div style=\"width: 297px; float: left; padding-top: 20px; margin-right: 20px; border-bottom: 1px solid gray; \" >INSTALOVANÉ ZAŘÍZENÍ</div>
				<div style=\"width: 218px; float: left; padding-top: 20px; margin-right: 20px; border-bottom: 1px solid gray;\" >IP/MAC</div>
				<div style=\"width: 260px; float: left; padding-top: 20px; margin-right: 12px; border-bottom: 1px solid gray;\" >POZNÁMKA</div>
				
				<div style=\"width: 130px; border-bottom: 1px solid gray; \" >Zařízení ve vlastnictví poskytovatele</div>
				
			      </div>\n";
    }

    if($i > 1) {
        echo "\t\t<div style=\"clear: both; height: 5px;\" ></div>\n\n";
    }

    echo "\t\t<div style=\"clear: both; float: left; \">záznam č.".$i."</div>\n";

    $value = "int_zarizeni_".$i;

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"int_zarizeni_".$i."\" size=\"29\" maxlength=\"26\" value=\"".$$value."\"></div>\n";

    $value = "int_zarizeni_".$i."_ip";

    echo "\t\t<div style=\"float: left; padding-left: 20px; padding-right: 20px; \">".
        "<input type=\"text\" name=\"int_zarizeni_".$i."_ip\" size=\"29\" maxlength=\"25\" value=\"".$$value."\" >".
      "</div>\n";

    $value = "int_zarizeni_".$i."_pozn";

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"int_zarizeni_".$i."_pozn\" size=\"35\" maxlength=\"26\" value=\"".$$value."\" >".
      "</div>\n";

    $value = "int_zarizeni_".$i."_vlastnik";

    echo "\t\t<div style=\"float: left; padding-left: 50px;\" >".
        "<input type=\"checkbox\" name=\"int_zarizeni_".$i."_vlastnik\" value=\"1\" ";
    if($$value == 1) {
        echo " checked ";
    }
    echo " >".
     "</div>";

}

//ip adresa - popisky a DHCP chlívek
echo "<div style=\"clear: both; padding-top: 20px;\"></div>";

echo "<div style=\"float: left; padding-right: 20px; font-size: 16px; font-weight: bold;\">".
    "WAN IP (adresa)</div>";

echo "<div style=\"float: left;\"><input type=\"checkbox\" name=\"ip_dhcp\" value=\"1\" ";
if($ip_dhcp == 1) {
    echo " checked ";
}
echo " > DHCP</div>";

echo "<div style=\"float: left; padding-left: 30px; \">MASKA</div>";

echo "<div style=\"float: left; padding-left: 140px; \">BRÁNA</div>";

echo "<div style=\"float: left; padding-left: 125px; \">DNS 1</div>";

echo "<div style=\"float: left; padding-left: 55px; \">DNS 2</div>";

//konec radku
echo "<div style=\"clear: both; height: 5px;\"></div>";

//IP - policka
echo "<div style=\"float: left; padding-right: 25px; \">
			    <input type=\"text\" name=\"ip_adresa\" value=\"".$ip_adresa."\" size=\"27\"></div>";

echo "<div style=\"float: left; padding-right: 40px;\" >".
      "<input type=\"text\" name=\"ip_maska\" value=\"".$ip_maska."\"></div>";

echo "<div style=\"float: left; padding-right: 20px;\" >".
    "<input type=\"text\" name=\"ip_brana\" value=\"".$ip_brana."\"></div>";

echo "<div style=\"float: left; padding-right: 10px;\" >".
    "<input type=\"text\" name=\"ip_dns1\" size=\"10\" value=\"".$ip_dns1."\"></div>";

echo "<div style=\"float: left;\" ><input type=\"text\" name=\"ip_dns2\" size=\"10\" value=\"".$ip_dns2."\"></div>";

echo "</fieldset>
		</td>
	      </tr>";

echo "<tr><td colspan=\"5\"><br></td></tr>";

//IPTV

echo "<tr>
		<td colspan=\"5\">
		
		    <fieldset>
			<legend>IPTV</legend>";

echo   "<div style=\"float: left; padding-right: 15px;\"><label>Instalované zařízení - počet: </label></div>
			<select name=\"iptv_pocet_zarizeni\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			    <option value=\"0\" style=\"color: gray;\" ";
if(!isset($iptv_pocet_zarizeni)) {
    echo "selected ";
} echo" >Žádné</option>
			    
			    <option value=\"1\" ";
if($iptv_pocet_zarizeni == 1) {
    echo "selected ";
} echo " >1</option>
			    <option value=\"2\" ";
if($iptv_pocet_zarizeni == 2) {
    echo "selected ";
} echo " >2</option>
			    <option value=\"3\" ";
if($iptv_pocet_zarizeni == 3) {
    echo "selected ";
} echo " >3</option>
			    
			</select>";

echo "<div style=\"height: 5px;\"></div>";

//instalovani zarizeni

for($i = 1; $i <= $iptv_pocet_zarizeni; $i++) {

    if($i == 1) {
        echo "<div style=\"padding-bottom: 15px; padding-top: 10px; \">
				
				<div style=\"width: 297px; float: left; padding-top: 20px; margin-right: 20px; border-bottom: 1px solid gray; \" >INSTALOVANÉ ZAŘÍZENÍ</div>
				<div style=\"width: 218px; float: left; padding-top: 20px; margin-right: 20px; border-bottom: 1px solid gray;\" >IP/MAC</div>
				<div style=\"width: 260px; float: left; padding-top: 20px; margin-right: 12px; border-bottom: 1px solid gray;\" >POZNÁMKA</div>
				
				<div style=\"width: 130px; border-bottom: 1px solid gray; \" >Zařízení ve vlastnictví poskytovatele</div>
				
			      </div>\n";
    }

    if($i > 1) {
        echo "\t\t<div style=\"clear: both; height: 5px;\" ></div>\n\n";
    }

    echo "\t\t<div style=\"clear: both; float: left; \">záznam č.".$i."</div>\n";

    $value = "iptv_zarizeni_".$i;

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"iptv_zarizeni_".$i."\" size=\"29\" maxlength=\"26\" value=\"".$$value."\"></div>\n";

    $value = "iptv_zarizeni_".$i."_ip";

    echo "\t\t<div style=\"float: left; padding-left: 20px; padding-right: 20px; \">".
        "<input type=\"text\" name=\"iptv_zarizeni_".$i."_ip\" size=\"29\" maxlength=\"25\" value=\"".$$value."\" >".
      "</div>\n";

    $value = "iptv_zarizeni_".$i."_pozn";

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"iptv_zarizeni_".$i."_pozn\" size=\"35\" maxlength=\"26\" value=\"".$$value."\" >".
      "</div>\n";

    $value = "iptv_zarizeni_".$i."_vlastnik";

    echo "\t\t<div style=\"float: left; padding-left: 50px;\" >".
        "<input type=\"checkbox\" name=\"iptv_zarizeni_".$i."_vlastnik\" value=\"1\" ";
    if($$value == 1) {
        echo " checked ";
    }
    echo " >".
     "</div>";


}

echo "</fieldset>
		</td>
	      </tr>";

//VOIP
echo "<tr><td colspan=\"5\"><br></td></tr>";

echo "<tr>
		<td colspan=\"5\">
		
		    <fieldset>
			<legend>VOIP</legend>";

echo   "<div style=\"float: left; padding-right: 15px;\"><label>Instalované zařízení - počet: </label></div>
			<select name=\"voip_pocet_zarizeni\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			    <option value=\"0\" style=\"color: gray;\" ";
if(!isset($voip_pocet_zarizeni)) {
    echo "selected ";
} echo" >Žádné</option>
			    
			    <option value=\"1\" ";
if($voip_pocet_zarizeni == 1) {
    echo "selected ";
} echo " >1</option>
			    <option value=\"2\" ";
if($voip_pocet_zarizeni == 2) {
    echo "selected ";
} echo " >2</option>
			    
			</select>";

echo "<div style=\"height: 5px;\"></div>";

//instalovani zarizeni

for($i = 1; $i <= $voip_pocet_zarizeni; $i++) {

    if($i == 1) {
        echo "<div style=\"padding-bottom: 15px; padding-top: 10px; \">
				
				<div style=\"width: 297px; float: left; padding-top: 20px; margin-right: 20px; border-bottom: 1px solid gray; \" >INSTALOVANÉ ZAŘÍZENÍ</div>
				<div style=\"width: 218px; float: left; padding-top: 20px; margin-right: 20px; border-bottom: 1px solid gray;\" >IP/MAC</div>
				<div style=\"width: 260px; float: left; padding-top: 20px; margin-right: 12px; border-bottom: 1px solid gray;\" >POZNÁMKA</div>
				
				<div style=\"width: 130px; border-bottom: 1px solid gray; \" >Zařízení ve vlastnictví poskytovatele</div>
				
			      </div>\n";
    }

    if($i > 1) {
        echo "\t\t<div style=\"clear: both; height: 5px;\" ></div>\n\n";
    }

    echo "\t\t<div style=\"clear: both; float: left; \">záznam č.".$i."</div>\n";

    $value = "voip_zarizeni_".$i;

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"voip_zarizeni_".$i."\" size=\"29\" maxlength=\"26\" value=\"".$$value."\"></div>\n";

    $value = "voip_zarizeni_".$i."_ip";

    echo "\t\t<div style=\"float: left; padding-left: 20px; padding-right: 20px; \">".
        "<input type=\"text\" name=\"voip_zarizeni_".$i."_ip\" size=\"29\" maxlength=\"24\" value=\"".$$value."\" >".
      "</div>\n";

    $value = "voip_zarizeni_".$i."_pozn";

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"voip_zarizeni_".$i."_pozn\" size=\"35\" maxlength=\"26\" value=\"".$$value."\" >".
      "</div>\n";

    $value = "voip_zarizeni_".$i."_vlastnik";

    echo "\t\t<div style=\"float: left; padding-left: 50px;\" >".
        "<input type=\"checkbox\" name=\"voip_zarizeni_".$i."_vlastnik\" value=\"1\" ";
    if($$value == 1) {
        echo " checked ";
    }
    echo " >".
     "</div>";


}

echo "</fieldset>
		</td>
	      </tr>";

//MATERIAL
echo "<tr><td colspan=\"5\"><br></td></tr>";

echo "<tr>
		<td colspan=\"5\">
		
		    <fieldset>
			<legend>Instalovaný materiál</legend>";

echo   "<div style=\"float: left; padding-right: 15px;\"><label>počet položek: </label></div>
			<select name=\"mat_pocet\" size=\"1\" onChange=\"self.document.forms.form1.submit()\" >
			    
			    <option value=\"0\" ";
if(($mat_pocet == 0) or (!isset($mat_pocet))) {
    echo "selected ";
} echo " >Žádné</option>
			    <option value=\"1\" ";
if($mat_pocet == 1) {
    echo "selected ";
} echo " >1</option>
			    <option value=\"2\" ";
if($mat_pocet == 2) {
    echo "selected ";
} echo " >2</option>
			    <option value=\"3\" ";
if($mat_pocet == 3) {
    echo "selected ";
} echo " >3</option>
			    
			</select>";

echo "<div style=\"height: 5px;\"></div>";

for($i = 1; $i <= $mat_pocet; $i++) {

    echo "\t\t<div style=\"clear: both; float: left; \">záznam č.".$i."</div>\n";

    $value = "mat_".$i;

    echo "\t\t<div style=\"float: left; padding-left: 10px;\">".
        "<input type=\"text\" name=\"mat_".$i."\" size=\"100\" value=\"".$$value."\"></div>\n";


    echo "\t\t<div style=\"clear: both; height: 5px;\" ></div>\n\n";
}

echo "</fieldset>
		</td>
	      </tr>\n";

//POZNAMKA
echo "<tr><td colspan=\"5\"><br></td></tr>\n";

echo "<tr>
		<td colspan=\"5\">
		
		    <fieldset>
			<legend>Poznámka</legend>\n";

echo   "<div style=\"float: left; padding-right: 15px; width: 50px;\">&nbsp;</div>\n";

echo   "<div style=\"float: left; padding-right: 15px;\"><textarea name=\"poznamka2\" rows=\"4\" cols=\"100\" >".
    $poznamka2."</textarea>\n";

echo "</fieldset>
		</td>
	      </tr>\n";

//PODPISY
echo "<tr><td colspan=\"5\"><br></td></tr>\n";

echo "<tr><td colspan=\"5\"><br></td></tr>\n";

echo " <tr>
                <td align=\"center\" colspan=\"2\" ><input type=\"submit\" name=\"odeslano\" value=\"OK -- VYGENEROVAT\" ></td>
                <td>&nbsp;</td>
                <td align=\"center\" colspan=\"2\" >&nbsp;</td>
        </tr>

</table>
</form>\n";
