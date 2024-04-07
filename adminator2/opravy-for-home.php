<?

$v_reseni_filtr = $_GET["v_reseni_filtr"];
$vyreseno_filtr = $_GET["vyreseno_filtr"];

global $limit;

$limit=$_GET["limit"];

if ( !isset($v_reseni_filtr) ){ $v_reseni_filtr="99"; }
if ( !isset($vyreseno_filtr) ){ $vyreseno_filtr="0"; }

if ( !isset($limit) ){ $limit="10"; }

// vypis

$pocet_bunek="11";

echo "<table border=\"0\" width=\"800px\" align=\"center\" style=\"font-size: 12px; font-family: Verdana;  \" >";


// $limit="10";

include("opravy-vypis-inc.php");

?>
