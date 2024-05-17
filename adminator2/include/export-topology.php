<?php

# Example of using the WriteExcel module to create worksheet panes.
#
# reverse(''), May 2001, John McNamara, jmcnamara@cpan.org

# PHP port by Johann Hanne, 2005-11-01

				
set_time_limit(10);

$fname = tempnam("../export", "export-topology");

// $workbook = &new writeexcel_workbook($fname);

$nazev1=iconv("UTF-8","CP1250",' výpis lokalit - nodů ');

$worksheet1 =& $workbook->addworksheet($nazev1);

# Frozen panes  -predpoklad ze se to hejbat nebude
$worksheet1->freeze_panes(2, 0); // 1 row
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

//$borderleft2 =& $workbook->addformat();
//$borderleft->set_top(1);
//$borderleft2->set_align('left');

$border =& $workbook->addformat();
$border ->set_top(1);

$borderleft2 =& $workbook->addformat( array ( align => 'left', color => 'grey' ) );
																	    
#######################################################################
#
# Sheet 1
#

// nastavení sirek sloupcu
$worksheet1->set_column('A:A', 7); 	//id 

$worksheet1->set_column('B:B', 40);	// jmeno	
$worksheet1->set_column('C:C', 40);	// adresa 
$worksheet1->set_column('D:D', 40);	// poznamka
$worksheet1->set_column('E:E', 15);	// rozsah ip adres

// nevim
$worksheet1->set_row(0, 20);
$worksheet1->set_selection('C3');


    //prvni radek
    $worksheet1->write(0, 0, 'Id', $header);
    $worksheet1->write(0, 1, 'Jmeno', $header);
    $worksheet1->write(0, 2, 'Adresa', $header);
    $worksheet1->write(0, 3, 'Poznamka', $header);
    $worksheet1->write(0, 4, 'Rozsah ip adres', $header);
    
    //druhej
    $worksheet1->write(1, 0, '', $header);
    $worksheet1->write(1, 1, 'umisteni aliasu ( nazev routeru)', $header);
    $worksheet1->write(1, 2, 'typ vysilace', $header);
    $worksheet1->write(1, 3, 'stav', $header);
    $worksheet1->write(1, 4, '', $header);
    
include("./config.php");

 $dotaz1 = $conn_mysql->query("SELECT * FROM nod_list");

// vlastni data

	$i=2;
	
    while( $data=mysql_fetch_array($dotaz1) ):
      
	$worksheet1->write($i, 0, $data["id"], $borderleft);
	$worksheet1->write($i, 1, iconv("UTF-8","CP1250", $data["jmeno"] ), $borderleft);
	$worksheet1->write($i, 2, iconv("UTF-8","CP1250", $data["adresa"] ), $borderleft);
	$worksheet1->write($i, 3, iconv("UTF-8","CP1250", $data["pozn"] ), $borderleft);
	$worksheet1->write($i, 4, iconv("UTF-8","CP1250", $data["ip_rozsah"] ), $borderleft);
	
	$i++;
	
	$worksheet1->write($i, 0, "", $borderleft2);
	
	$router_id = $data["router_id"];
	
	if ($router_id <= 0)
	{ $router_nazev="nelze zjistit "; $router_ip=""; }
	else
	{
	 $vysledek_router=$conn_mysql->query("SELECT * FROM router_list where id = $router_id ");
	 while($data_router=mysql_fetch_array($vysledek_router))
	 { $router_nazev = $data_router["nazev"]; $router_ip = $data_router["ip_adresa"]; }
	}
																			
	$worksheet1->write($i, 1, $router_nazev." (".$router_ip.") " , $borderleft2);
	
	$typ_vysilace=$data["typ_vysilace"];

             if ( $typ_vysilace == 1 ){ $typ_vysilace2="Metallic"; }
             elseif ( $typ_vysilace == 2 ){ $typ_vysilace2="ap-2,4GHz-OMNI"; }
             elseif ( $typ_vysilace == 3 ){ $typ_vysilace2="ap-2,4Ghz-sektor"; }
             elseif ( $typ_vysilace == 4 ){ $typ_vysilace2="ap-2.4Ghz-smerovka"; }
             elseif ( $typ_vysilace == 5 ){ $typ_vysilace2="ap-5.8Ghz-OMNI"; }
             elseif ( $typ_vysilace == 6 ){ $typ_vysilace2="ap-5.8Ghz-sektor"; }
             elseif ( $typ_vysilace == 7 ){ $typ_vysilace2="ap-5.8Ghz-smerovka"; }
             elseif ( $typ_vysilace == 8 ){ $typ_vysilace2="jiné"; }
             else { $typ_vysilace2=$typ_vysilace; }
	

	$worksheet1->write($i, 2, iconv("UTF-8","CP1250", $typ_vysilace2), $borderleft2);
	
	if ( $data["stav"] == 1){ $stav = "v pořádku"; }
        elseif ( $data["stav"] == 2){ $stav = "vytížen"; }
	elseif( $data["stav"] == 3 ){ $stav = "přetížen"; }
	else{ $stav = $data["stav"]; }
	
	$worksheet1->write($i, 3, iconv("UTF-8","CP1250", $stav), $borderleft2);
	$worksheet1->write($i, 4, "", $borderleft2);
	
	$i++;
		
    endwhile;

// konec 

$workbook->close();
 // header("<meta http-equiv=Content-Type content=\"text/html; charset=utf8\">");
    
 header("Content-Type: application/x-msexcel; name=\"export-topology.xls\"");
 header("Content-Disposition: inline; filename=\"export-topology.xls\"");

$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

?>
