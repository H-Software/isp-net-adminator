<?php

require("include/main.function.shared.php");
require("include/config.php");
require("include/check_login.php");
require("include/check_level.php");

if(!(check_level($level, 44))) {
    // neni level

    $stranka = 'nolevelpage.php';
    header("Location: ".$stranka);

    echo "<br>Neopravneny pristup /chyba pristupu. STOP <br>";
    exit;

}

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"> 
      <html> 
      <head> ';

require("include/charset.php");

?>

<title>Adminator2 - platby výpis</title> 

</head>

<body>

<?php require("head.php"); ?>

<?php require("category.php"); ?>

 <tr>
   <td colspan="2" ><?php require("platby-subcat-inc2.php"); ?></td>
 </tr>
     
 <tr>
  <td colspan="2" >
  
<?php
// vlastni obsah
$list = $_GET["list"];

$sql_base = "SELECT t1.id, t1.zaplaceno_dne, t2.prijmeni, t2.jmeno, t2.id_cloveka,
                t1.firma, t1.zaplaceno_za, t1.castka, t1.id_cloveka 
                FROM (platby AS t1 LEFT JOIN vlastnici AS t2 
                ON t1.id_cloveka=t2.id_cloveka) WHERE hotove='1' ";

//vytvoreni objektu
$listovani = new c_Listing(
    "./platby-hot-vypis.php?menu=1",
    30,
    $list,
    "<center><div class=\"text-listing\">\n",
    "</div></center>\n",
    $sqbl_base . " ORDER BY id",
    $db_ok2
);

if (($list == "") || ($list == "1")) {    //pokud není list zadán nebo je první
    $bude_chybet = 0;                  //bude ve výběru sql dotazem chybet 0 záznamů
} else {
    $bude_chybet = (($list - 1) * $listovani->interval);    //jinak jich bude chybet podle závislosti na listu a intervalu
}

$sql_listing = "";
if($listovani->interval > 0 and $bude_chybet > 0 ){
    $sql_listing = " LIMIT ".$listovani->interval." OFFSET ".$bude_chybet;
}

//provedení sql dotazu a výběr záznamů
try {
    $vyber = pg_query($db_ok2, $sql_base . " ORDER BY id " . $sql_listing);
} catch (Exception $e) {
    echo "<div style=\"color: red; \" >Chyba! Data nelze načíst! </div>";
    echo "<div style=\"color: red; \" >Database Error: ". $e->getMessage() . "</div>";
}

if($vyber) {
    $listovani->listInterval();    //zobrazení stránkovače

    echo "<table border=\"1\" width=\"100%\" >
      <tr>
      
      <td><b>id platby: </b></td>
      <td><b>zaplaceno za: </b></td>
      <td><b>částka: </b></td>
      <td><b>datum placení: </b></td> 
          
      <td><b>id vlastníka: </b></td>
      <td><b>firma</b></td>
      
      <td><b>Příjmení: </b></td>
      <td><b>Jméno: </b></td>
      </tr>
      ";

    //výpis záznamů dokud nějaké jsou
    while ($zaznam = pg_fetch_array($vyber)) {
        $orez = $zaznam["zaplaceno_dne"];
        $orezano = explode(':', $orez);
        $pridano_orez = $orezano[0].":".$orezano[1];

        // $id_cloveka=$id_cloveka=["id_cloveka"];
        echo "<tr>";

        echo "<td> ".$zaznam["id"]."</td>"."<td>".$zaznam["zaplaceno_za"]."</td>"."<td>".$zaznam["castka"]."</td> ";
        echo "<td>".$pridano_orez." </td>";

        echo "<td>".$zaznam["id_cloveka"]."</td>";
        echo "<td>".$zaznam["firma"]." </td> "."<td> ".$zaznam["prijmeni"]." </td> "." <td>".$zaznam["jmeno"]." </td> ";

        // , ".$zaznam["t1.zaplaceno_dne"]."<br><br>\n";

        echo "</tr>";
    }

    echo "</table>";

    $listovani->listInterval();    //zobrazení stránkovače
} else {
    echo "<div style=\"color: red; \" >Chyba! Data nelze načíst! </div>";
    echo "<div style=\"color: red; \" >Error: No database handler.</div>";
    echo "<div style=\"color: red; \" >" . pg_last_error() . "</div>";
}

?>
  
  </td>
  </tr>
  
 </table>

</body>
</html>
