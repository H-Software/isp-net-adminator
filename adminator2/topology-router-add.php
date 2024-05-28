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

?>
