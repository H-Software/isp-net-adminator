<?php

require("include/main.function.shared.php");
require_once("include/config.php");
require_once("include/check_login.php");
require_once("include/check_level.php");


$ag = new Aglobal();
$ag->conn_mysql = $conn_mysql;
$ag->conn_pgsql = $db_ok2;

echo "<div style=\"padding-bottom: 10px; padding-top: 10px; font-size: 18px; \">Přidání/úprava routeru </div>";

if(($odeslat == "OK") and ($error != "1")) {
    //proces ukladani ..

    //vypsat co se vlozilo
    // removed

    // ulozeni do DB
    // removed

} // konec odeslat == OK
else {
    //nechceme ukladat, tj. zobrazit form

    //pokud update, tak zjistit predchozi hodnoty
    if($update_id > 0 and ($odeslat != "OK")) {
        // nacteni promennych, pokud se nedna o upravu a neodeslal sem form

        // removed
    }

    //zobrazime formular
    // removed
}

?>

    <!-- konec vlastniho obsahu -->	
  </td>
  </tr>
  
 </table>

</body> 
</html> 

<?php

//funkce

//function to validate ip address format in php by Roshan Bhattarai(http://roshanbh.com.np)
function validateIpAddress($ip_addr)
{
    //first of all the format of the ip address is matched
    if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
        //now all the intger values are separated
        $parts = explode(".", $ip_addr);
        //now we need to check each part can range from 0-255
        foreach($parts as $ip_parts) {
            if(intval($ip_parts) > 255 || intval($ip_parts) < 0) {

                return false;
            } //if number is not within range of 0-255
        }

        return true;
    } else {
        return false;
    } //if format of ip address doesn't matches
}

?>
