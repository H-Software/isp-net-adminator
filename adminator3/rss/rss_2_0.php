<?php

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ ."/../boostrap/netteLoader.php";

// require __DIR__ . "/../include/main.function.shared.php";
require __DIR__ . "/../boostrap/database.php";

//prvne pokus o autorizaci
$rss = new rss($conn_mysql, $logger);

$rs_check_login = $rss->check_login_rss($_GET["sid"]);

if($rs_check_login == false) {
    $row = new rss_wrong_login("spatny login", "Špatný login, prosím přihlašte se do administračního systému.", "System");

    $rss->putHeader();
    $rss->putItem($row);
    $rss->putEnd();
} else {
    $rss->exportRSS();
}
