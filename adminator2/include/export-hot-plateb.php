<?php

# Example of using the WriteExcel module to create worksheet panes.
#
# reverse(''), May 2001, John McNamara, jmcnamara@cpan.org

# PHP port by Johann Hanne, 2005-11-01
			
set_time_limit(20);

require_once "class.writeexcel_workbook.inc.php";
require_once "class.writeexcel_worksheet.inc.php";

//global $rok;
//echo "sekund. rok: ".$rok."<br>";
if( ereg('^([[:digit:]]+)$',$_GET["rok"]) )
{
 $rok = $_GET["rok"];
}

$fname = tempnam("/export", "export-hot-plateb-".$rok.".xls");

$workbook = &new writeexcel_workbook($fname);

$nazev1=iconv("UTF-8","CP1250",' všechny platby - '.$rok);
$nazev2=iconv("UTF-8","CP1250",' platby na f.o. - '.$rok);
$nazev3=iconv("UTF-8","CP1250",' platby na s.r.o. - '.$rok);

$worksheet1 =& $workbook->addworksheet($nazev1);
$worksheet2 =& $workbook->addworksheet($nazev2);
$worksheet3 =& $workbook->addworksheet($nazev3);

# Frozen panes  -predpoklad ze se to hejbat nebude
$worksheet1->freeze_panes(1, 0); // 1 row
// $worksheet2->freeze_panes(2, 0); 

#######################################################################
#
# Set up some formatting and text to highlight the panes
#

$header =& $workbook->addformat();
$header->set_color('white');
$header->set_align('center');
$header->set_align('vcenter');
$header->set_pattern();
$header->set_fg_color('green');

$bordercenter =& $workbook->addformat();
$bordercenter->set_top(1);
$bordercenter->set_align('center');

$borderleft =& $workbook->addformat();
$borderleft->set_top(1);
$borderleft->set_align('left');

$border =& $workbook->addformat();
$border ->set_top(1);
																				    
#######################################################################
#
# Sheet 1 - vsechny platby
#

// nastavení sirek sloupcu
$worksheet1->set_column('A:A', 7); 	//id

$worksheet1->set_column('B:B', 18);	// zaplaceno_dne	
$worksheet1->set_column('C:C', 9);	// castka 
$worksheet1->set_column('D:D', 6);	// dan
$worksheet1->set_column('E:E', 6);	// ucet

$worksheet1->set_column('F:F', 8);	// id 
$worksheet1->set_column('G:G', 12);	// zaplaceno_za
$worksheet1->set_column('H:H', 6);	// z_vypisu
$worksheet1->set_column('I:I', 6);	// hotove
$worksheet1->set_column('J:J', 6);	// firma
$worksheet1->set_column('K:K', 12);	// jmeno
$worksheet1->set_column('L:L', 12);	// prijmeni

$worksheet1->set_column('M:M', 15);	// ico

// nevim
$worksheet1->set_row(0, 20);
$worksheet1->set_selection('C3');

// $jmeno_uzivatele=iconv("UTF-8","CP1250",'jméno uživatele');

    //prvni radek
    $worksheet1->write(0, 0, 'id_cloveka', $header);
    $worksheet1->write(0, 1, 'zaplaceno_dne', $header);
    $worksheet1->write(0, 2, 'castka', $header);
    $worksheet1->write(0, 3, 'dan', $header);
    $worksheet1->write(0, 4, 'ucet', $header);
    $worksheet1->write(0, 5, 'id', $header);
    $worksheet1->write(0, 6, 'zaplaceno_za', $header);
    $worksheet1->write(0, 7, 'zvypisu', $header);
    $worksheet1->write(0, 8, 'hotove', $header);
    $worksheet1->write(0, 9, 'firma', $header);
    $worksheet1->write(0, 10, 'jmeno', $header);
    $worksheet1->write(0, 11, 'prijmeni', $header);
    
    $worksheet1->write(0, 12, 'ICO', $header);
    
 //include("config.pg.php");

 $dotaz1 = pg_query("SELECT t1.id_cloveka, t1.zaplaceno_dne, t1.castka, t1.dan, t1.ucet, t1.id, t1.zaplaceno_za,
                        t1.zvypisu, t1.hotove, t1.firma, t2.jmeno, t2.prijmeni,t2.fakturacni,t3.id,t3.ico
			     
			FROM (platby AS t1 LEFT JOIN vlastnici AS t2
			 ON t1.id_cloveka=t2.id_cloveka LEFT JOIN fakturacni AS t3 ON t2.fakturacni=t3.id)
			  WHERE ( hotove='1' AND zaplaceno_za LIKE '$rok-%' ) ORDER BY zaplaceno_dne  ASC
			");

// vlastni data

	$i=1;
	
    while( $data=pg_fetch_array($dotaz1) ):

	$orezano = split(':', $data["zaplaceno_dne"]);
	$pridano=$orezano[0].":".$orezano[1];
		      
	$worksheet1->write($i, 0, $data["id_cloveka"], $bordercenter);
	$worksheet1->write($i, 1, iconv("UTF-8","CP1250", $pridano) , $bordercenter);
	$worksheet1->write($i, 2, iconv("UTF-8","CP1250", $data["castka"]) , $bordercenter);
	$worksheet1->write($i, 3, iconv("UTF-8","CP1250", $data["dan"]) , $bordercenter);
	$worksheet1->write($i, 4, iconv("UTF-8","CP1250", $data["ucet"]) , $bordercenter);
	$worksheet1->write($i, 5, iconv("UTF-8","CP1250", $data["id"]) , $bordercenter);
	$worksheet1->write($i, 6, iconv("UTF-8","CP1250", $data["zaplaceno_za"]) , $bordercenter);
	$worksheet1->write($i, 7, iconv("UTF-8","CP1250", $data["zvypisu"]) , $bordercenter);
	$worksheet1->write($i, 8, iconv("UTF-8","CP1250", $data["hotove"]) , $bordercenter);
		
	$worksheet1->write($i, 9, iconv("UTF-8","CP1250", $data["firma"]) , $bordercenter);
	$worksheet1->write($i, 10, iconv("UTF-8","CP1250", $data["jmeno"]) , $borderleft);
	$worksheet1->write($i, 11, iconv("UTF-8","CP1250", $data["prijmeni"]) , $borderleft);

	$worksheet1->write($i, 12, iconv("UTF-8","CP1250", $data["ico"]) , $borderleft);
	
	$i++;
		
    endwhile;


#######################################################################
#
# Sheet 2 - platby na F.O.
#

// nastavení sirek sloupcu
$worksheet2->set_column('A:A', 7);      //id

$worksheet2->set_column('B:B', 18);     // zaplaceno_dne
$worksheet2->set_column('C:C', 9);      // castka
$worksheet2->set_column('D:D', 6);      // dan
$worksheet2->set_column('E:E', 6);      // ucet

$worksheet2->set_column('F:F', 8);      // id
$worksheet2->set_column('G:G', 12);     // zaplaceno_za
$worksheet2->set_column('H:H', 6);      // z_vypisu
$worksheet2->set_column('I:I', 6);      // hotove
$worksheet2->set_column('J:J', 6);      // firma
$worksheet2->set_column('K:K', 12);     // jmeno
$worksheet2->set_column('L:L', 12);     // prijmeni

// nevim
$worksheet2->set_row(0, 20);
$worksheet2->set_selection('C3');

// $jmeno_uzivatele=iconv("UTF-8","CP1250",'jméno uživatele');

    //prvni radek
    $worksheet2->write(0, 0, 'id_cloveka', $header);
    $worksheet2->write(0, 1, 'zaplaceno_dne', $header);
    $worksheet2->write(0, 2, 'castka', $header);
    $worksheet2->write(0, 3, 'dan', $header);
    $worksheet2->write(0, 4, 'ucet', $header);
    $worksheet2->write(0, 5, 'id', $header);
    $worksheet2->write(0, 6, 'zaplaceno_za', $header);
    $worksheet2->write(0, 7, 'zvypisu', $header);
    $worksheet2->write(0, 8, 'hotove', $header);
    $worksheet2->write(0, 9, 'firma', $header);
    $worksheet2->write(0, 10, 'jmeno', $header);
    $worksheet2->write(0, 11, 'prijmeni', $header);

 $dotaz2 = pg_query("SELECT t1.id_cloveka, t1.zaplaceno_dne, t1.castka, t1.dan, t1.ucet, t1.id, t1.zaplaceno_za,
                        t1.zvypisu, t1.hotove, t1.firma, t2.jmeno, t2.prijmeni

                        FROM (platby AS t1 LEFT JOIN vlastnici AS t2
                         ON t1.id_cloveka=t2.id_cloveka) 
			 
			WHERE ( hotove='1' AND t1.firma is null AND zaplaceno_za LIKE '$rok-%' ) 
			
			ORDER BY zaplaceno_dne ASC
                        ");

// vlastni data

        $i=1;

    while( $data=pg_fetch_array($dotaz2) ):

        $orezano = split(':', $data["zaplaceno_dne"]);
        $pridano=$orezano[0].":".$orezano[1];

        $worksheet2->write($i, 0, $data["id_cloveka"], $bordercenter);
        $worksheet2->write($i, 1, iconv("UTF-8","CP1250", $pridano) , $bordercenter);
        $worksheet2->write($i, 2, iconv("UTF-8","CP1250", $data["castka"]) , $bordercenter);
        $worksheet2->write($i, 3, iconv("UTF-8","CP1250", $data["dan"]) , $bordercenter);
        $worksheet2->write($i, 4, iconv("UTF-8","CP1250", $data["ucet"]) , $bordercenter);
        $worksheet2->write($i, 5, iconv("UTF-8","CP1250", $data["id"]) , $bordercenter);
        $worksheet2->write($i, 6, iconv("UTF-8","CP1250", $data["zaplaceno_za"]) , $bordercenter);
        $worksheet2->write($i, 7, iconv("UTF-8","CP1250", $data["zvypisu"]) , $bordercenter);
        $worksheet2->write($i, 8, iconv("UTF-8","CP1250", $data["hotove"]) , $bordercenter);

        $worksheet2->write($i, 9, iconv("UTF-8","CP1250", $data["firma"]) , $bordercenter);
        $worksheet2->write($i, 10, iconv("UTF-8","CP1250", $data["jmeno"]) , $borderleft);
        $worksheet2->write($i, 11, iconv("UTF-8","CP1250", $data["prijmeni"]) , $borderleft);

        $i++;

    endwhile;


#######################################################################
#
# Sheet 3 - platby s.r.o.
#

// nastavení sirek sloupcu
$worksheet3->set_column('A:A', 7);      //id

$worksheet3->set_column('B:B', 18);     // zaplaceno_dne
$worksheet3->set_column('C:C', 9);      // castka
$worksheet3->set_column('D:D', 6);      // dan
$worksheet3->set_column('E:E', 6);      // ucet

$worksheet3->set_column('F:F', 8);      // id
$worksheet3->set_column('G:G', 12);     // zaplaceno_za
$worksheet3->set_column('H:H', 6);      // z_vypisu
$worksheet3->set_column('I:I', 6);      // hotove
$worksheet3->set_column('J:J', 6);      // firma
$worksheet3->set_column('K:K', 12);     // jmeno
$worksheet3->set_column('L:L', 12);     // prijmeni
$worksheet3->set_column('M:M', 15);     // vs

$worksheet3->set_column('N:N', 15);     // ico

// nevim
$worksheet3->set_row(0, 20);
$worksheet3->set_selection('C3');

// $jmeno_uzivatele=iconv("UTF-8","CP1250",'jméno uživatele');

    //prvni radek
    $worksheet3->write(0, 0, 'id_cloveka', $header);
    $worksheet3->write(0, 1, 'zaplaceno_dne', $header);
    $worksheet3->write(0, 2, 'castka', $header);
    $worksheet3->write(0, 3, 'dan', $header);
    $worksheet3->write(0, 4, 'ucet', $header);
    $worksheet3->write(0, 5, 'id', $header);
    $worksheet3->write(0, 6, 'zaplaceno_za', $header);
    $worksheet3->write(0, 7, 'zvypisu', $header);
    $worksheet3->write(0, 8, 'hotove', $header);
    $worksheet3->write(0, 9, 'firma', $header);
    $worksheet3->write(0, 10, 'jmeno', $header);
    $worksheet3->write(0, 11, 'prijmeni', $header);
    $worksheet3->write(0, 12, 'var. symbol', $header);
    $worksheet3->write(0, 13, 'ico', $header);

 $dotaz3 = pg_query("SELECT t1.id_cloveka, t1.zaplaceno_dne, t1.castka, t1.dan, t1.ucet, t1.id, t1.zaplaceno_za,
                        t1.zvypisu, t1.hotove, t1.firma, t2.jmeno, t2.prijmeni, t2.vs,t3.id,t3.ico

                        FROM (platby AS t1 LEFT JOIN vlastnici AS t2
                         ON t1.id_cloveka=t2.id_cloveka LEFT JOIN fakturacni AS t3 ON t2.fakturacni=t3.id)
			  WHERE ( hotove='1' AND t1.firma is not null AND zaplaceno_za LIKE '$rok-%' )
			  
			ORDER BY zaplaceno_dne ASC
                        ");

// vlastni data

        $i=1;

    while( $data=pg_fetch_array($dotaz3) ):

        $orezano = split(':', $data["zaplaceno_dne"]);
        $pridano=$orezano[0].":".$orezano[1];

        $worksheet3->write($i, 0, $data["id_cloveka"], $bordercenter);
        $worksheet3->write($i, 1, iconv("UTF-8","CP1250", $pridano) , $bordercenter);
        $worksheet3->write($i, 2, iconv("UTF-8","CP1250", $data["castka"]) , $bordercenter);
        $worksheet3->write($i, 3, iconv("UTF-8","CP1250", $data["dan"]) , $bordercenter);
        $worksheet3->write($i, 4, iconv("UTF-8","CP1250", $data["ucet"]) , $bordercenter);
        $worksheet3->write($i, 5, iconv("UTF-8","CP1250", $data["id"]) , $bordercenter);
        $worksheet3->write($i, 6, iconv("UTF-8","CP1250", $data["zaplaceno_za"]) , $bordercenter);
        $worksheet3->write($i, 7, iconv("UTF-8","CP1250", $data["zvypisu"]) , $bordercenter);
        $worksheet3->write($i, 8, iconv("UTF-8","CP1250", $data["hotove"]) , $bordercenter);

        $worksheet3->write($i, 9, iconv("UTF-8","CP1250", $data["firma"]) , $bordercenter);
        $worksheet3->write($i, 10, iconv("UTF-8","CP1250", $data["jmeno"]) , $borderleft);
        $worksheet3->write($i, 11, iconv("UTF-8","CP1250", $data["prijmeni"]) , $borderleft);
	$worksheet3->write($i, 12, iconv("UTF-8","CP1250", $data["vs"]) , $borderleft);

	$worksheet3->write($i, 13, iconv("UTF-8","CP1250", $data["ico"]) , $borderleft);

        $i++;

    endwhile;



 // konec 

 $workbook->close();
 // header("<meta http-equiv=Content-Type content=\"text/html; charset=utf8\">");
    
 header("Content-Type: application/x-msexcel; name=\"export-hot-plateb-".$rok.".xls\"");
 header("Content-Disposition: inline; filename=\"export-hot-plateb-".$rok.".xls\"");

$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

?>
