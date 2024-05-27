<?php

//skript co se pousti v urcitych intervalech a provadi pozadavky na restart z adminatoru

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

// boostrap
//
require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . "/../include/main.function.shared.php";

require __DIR__ ."/../boostrap/netteLoader.php";

// require __DIR__ ."/../boostrap/database.php";
$conn_mysql = init_mysql("Adminator2");

$conn_pgsql = init_postgres("Adminator2");

// require __DIR__ ."/../boostrap/containerBuilder.php";

// old style DI stuff
// require __DIR__ ."/../boostrap/containerAfter.php";

// require __DIR__ ."/../boostrap/appFactory.php";

// require __DIR__ ."/../boostrap/dependencies.php";

// end of bootstrap

$html_tags = 1;

echo "work-diff.php start [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
$output_main .= "work-diff.php start [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";

$sql = "SELECT id, number_request FROM workitems ORDER BY id";

$sql = "SELECT workitems.id, workitems_names.name, workitems.number_request, workitems_names.priority ".
   " FROM workitems, workitems_names ".
   " WHERE workitems.number_request = workitems_names.id ".
   " ORDER BY priority ";

$rs = $conn_mysql->query($sql);
$num_rows = $rs->num_rows;

if($num_rows == 0) {
    echo " INFO: no requests on the system \n";
    $output_main .= " INFO: no requests on the system \n";
} else {
    while($data = $rs->fetch_array()) {
        $id = $data["id"];
        $number_request = $data["number_request"];

        execute_action($number_request, $id);

    } //end of while

} // end of else if num_rows == 0


echo "work-diff.php stop [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";
$output_main .= "work-diff.php stop [".strftime("%d/%m/%Y %H:%M:%S", time())."] \n";

if(preg_match("/.*<span.*>.*/", $output_main)) {
    $soubor = fopen(__DIR__ . "/../logs/reinhard.remote.log", "w");
} else {
    $output_main = "- - - - - - - - - - - - - -\n".$output_main;
    $soubor = fopen(__DIR__ . "/../logs/reinhard.remote.log", "a");
}

fwrite($soubor, $output_main);
fclose($soubor);

//vlozit vysledek do DB

if((strlen($output_main) > 150)) {
    $set = array();
    $set["akce"] = "'" . $conn_mysql->real_escape_string($output_main) . "'";
    //$set["provedeno_kym"] = "'" . $conn_mysql->real_escape_string(\Cartalyst\Sentinel\Native\Facades\Sentinel::getUser()->email) . "'";

    // a další spolu s případným ošetřením hodnot
    $rs_archiv = $conn_mysql->query("INSERT INTO archiv_zmen_work (" . implode(", ", array_keys($set)) . ") VALUES (" . implode(", ", $set) . ")");
}
