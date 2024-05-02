<?php

# Example of using the WriteExcel module to create worksheet panes.
#
# reverse(''), May 2001, John McNamara, jmcnamara@cpan.org

# PHP port by Johann Hanne, 2005-11-01

				
set_time_limit(10);

require_once "../include/class.writeexcel_workbook.inc.php";
require_once "../include/class.writeexcel_worksheet.inc.php";

$fname = tempnam("../export", "faktury-neuhrazene-export");

$workbook = &new writeexcel_workbook($fname);

$nazev1=iconv("UTF-8","CP1250",' všechny faktury ');

$worksheet1 =& $workbook->addworksheet($nazev1);

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
# Sheet 1 - vsechny neuh. faktury
#

// nastavení sirek sloupcu
$worksheet1->set_column('A:A', 16); 	//cislo 

$worksheet1->set_column('B:B', 18);	// vs	
$worksheet1->set_column('C:C', 14);	// datum 
$worksheet1->set_column('D:D', 14);	// splatnost
$worksheet1->set_column('E:E', 25);	// firma

$worksheet1->set_column('F:F', 10);	// celkem
$worksheet1->set_column('G:G', 10);	// k likvidaci
$worksheet1->set_column('H:H', 20);	// jmeno

// nevim
$worksheet1->set_row(0, 20);
$worksheet1->set_selection('C3');


    //prvni radek
    $worksheet1->write(0, 0, 'Cislo', $header);
    $worksheet1->write(0, 1, 'Var. sym.', $header);
    $worksheet1->write(0, 2, 'Datum', $header);
    $worksheet1->write(0, 3, 'Splatnost', $header);
    $worksheet1->write(0, 4, 'Firma', $header);
    $worksheet1->write(0, 5, 'Celkem', $header);
    $worksheet1->write(0, 6, 'K likvidaci', $header);
    $worksheet1->write(0, 7, 'Jmeno', $header);
    
include("../include/config.php");

 $dotaz1 = $conn_mysql->query("SELECT *,DATE_FORMAT(Datum, '%d.%c.%Y') as Datum,
			 DATE_FORMAT(DatSplat, '%d.%c.%Y') as DatSplat FROM faktury_neuhrazene 
			order by Jmeno,Firma ");

// vlastni data

	$i=1;
	
    while( $data=mysql_fetch_array($dotaz1) ):
      
	$worksheet1->write($i, 0, $data["Cislo"], $bordercenter);
	$worksheet1->write($i, 1, $data["VarSym"], $bordercenter);
	$worksheet1->write($i, 2, $data["Datum"]  , $bordercenter);
	$worksheet1->write($i, 3, $data["DatSplat"] , $bordercenter);
	$worksheet1->write($i, 4, iconv("UTF-8","CP1250", $data["Firma"] ), $borderleft);
	$worksheet1->write($i, 5, $data["KcCelkem"] , $bordercenter);
	$worksheet1->write($i, 6, $data["KcLikv"] , $bordercenter);
	// $worksheet1->write($i, 7, iconv("UTF-8","CP1250", $data["zvypisu"]) , $bordercenter);
	$worksheet1->write($i, 7, iconv("UTF-8","CP1250", $data["Jmeno"]) , $borderleft);
		
	/*
	$worksheet1->write($i, 9, iconv("UTF-8","CP1250", $data["firma"]) , $bordercenter);
	$worksheet1->write($i, 10, iconv("UTF-8","CP1250", $data["jmeno"]) , $borderleft);
	$worksheet1->write($i, 11, iconv("UTF-8","CP1250", $data["prijmeni"]) , $borderleft);

	$worksheet1->write($i, 12, iconv("UTF-8","CP1250", $data["ico"]) , $borderleft);
	*/
	
	$i++;
		
    endwhile;

// konec 

$workbook->close();
 // header("<meta http-equiv=Content-Type content=\"text/html; charset=utf8\">");
    
 header("Content-Type: application/x-msexcel; name=\"faktury-neuhrazene-export.xls\"");
 header("Content-Disposition: inline; filename=\"faktury-neuhrazene-export.xls\"");

$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

?>
