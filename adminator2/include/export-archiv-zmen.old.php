<?php

# Example of using the WriteExcel module to create worksheet panes.
#
# reverse(''), May 2001, John McNamara, jmcnamara@cpan.org

# PHP port by Johann Hanne, 2005-11-01

				
set_time_limit(0);

require_once "class.writeexcel_workbook.inc.php";
require_once "class.writeexcel_worksheet.inc.php";

$fname = tempnam("/export", "export-archiv-zmen.xls");

$workbook = &new writeexcel_workbook($fname);

$jmeno_listu= iconv("UTF-8","CP1250", "export položek");

$worksheet1 =& $workbook->addworksheet($jmeno_listu);

# Frozen panes  -predpoklad ze se to hejbat nebude
$worksheet1->freeze_panes(2, 0); // 1 row

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

$center =& $workbook->addformat();
$center->set_align('center');

$left =& $workbook->addformat();
$left->set_align('left');

$bordercenter =& $workbook->addformat();
$bordercenter->set_top(1);
$bordercenter->set_align('center');

$borderleft =& $workbook->addformat();
$borderleft->set_top(1);
$borderleft->set_align('left');

$borderleftcolor =& $workbook->addformat();
$borderleftcolor->set_top(1);
$borderleftcolor->set_align('left');
$borderleftcolor->set_fg_color('yellow');

$text_color1 =& $workbook->addformat( array(
                                            bold    => 0,
					    // italic  => 0,
					    color   => 'grey',
					    // align    => 18,
					    // font    => 'Comic Sans MS'
					    ) );


$bordercenter2 =& $workbook->addformat();
$bordercenter2->set_top(1);
$bordercenter2->set_align('center');
$bordercenter2->set_fg_color('yellow');
	
$center_bold =& $workbook->addformat( array( bold => 1, align => 'center', top => '1' ) );

$center_bold2 =& $workbook->addformat( array( bold => 1, align => 'center', top => '1', fg_color => 'yellow' ) );
		
$leftpozn =& $workbook->addformat( array ( color => 'blue' ) );
																				    
#######################################################################
#
# Sheet 1 - export
#

// nastavení sirek sloupcu
$worksheet1->set_column('A:A', 4); //id

$worksheet1->set_column('B:B', 400);	// akce	
$worksheet1->set_column('C:C', 23);	// pozn

// nevim
$worksheet1->set_row(0, 20);
$worksheet1->set_selection('C3');

//prvni radek

    $worksheet1->write(0, 0, 'id', $header);
    $worksheet1->write(0, 1, 'akce', $header);
    $worksheet1->write(0, 2, 'pozn', $header);
    
include("config.php");

$dotaz=mysql_query(" SELECT * FROM archiv_zmen "); // WHERE ( id > 5011 and id < 7920 ) ");
$dotaz_radku=mysql_num_rows($dotaz);

// vlastni data

	$i=2;
	
    while( $data=mysql_fetch_array($dotaz) ):

        //jednotlive promenne
	$id=$data["id"];
	
	  $akce_conv = iconv("UTF-8","CP1250", $data["akce"]);
	  $pozn_conv = iconv("UTF-8","CP1250", $data["pozn"]);
	
	// $akce_conv = $data["akce"];
	// $pozn_conv = $data["pozn"];
	
	//prvne orezem nezaudouci znaky	
	//$akce_conv = htmlspecialchars($akce_conv, ENT_QUOTES);
	
//	$akce_conv = str_replace (" ", ".", $akce_conv );
	$akce_conv = str_replace (">", ".", $akce_conv );
	$akce_conv = str_replace ("<", ".", $akce_conv );

	$akce_conv = str_replace ("=", "-", $akce_conv );
	
//	$akce_conv = trim ($akce_conv);
	
//	$akce_conv = htmlentities($akce_conv);
	
//	$akce_conv = htmlspecialchars($akce_conv, ENT_QUOTES);

//	 $akce_conv = strtr($akce_conv, 
//	 "áäčďéěëíľňôóöŕřšťúůüýžÁÄČĎÉĚËÍĽŇÓÖÔŘŔŠŤÚŮÜÝŽ" ,"aacdeeeilnooorrstuuuyzAACDEEELINOOORRSTUUUYZ");

//	v pripade ze se export nebude delat, tak pouzit tutu funkci
//	$akce_conv = na_text($akce_conv);
	
	$pocet_znaku=strlen($akce_conv);
	
	
	$pocet_cyklu = (int)$pocet_znaku/(int)160;
	
	$zbytek = $pocet_znaku % 160;
	
	if ( $zbytek > 0){ $pocet_cyklu++; }
	
	for($y=0,$x=1; $y < $pocet_cyklu; $y++,$x++ )
	{
	    
	    $pocatek = $y*160;

	    $tmp = substr ($akce_conv, $pocatek, 160);
	    
	    if ( $y == 0)
	    {
	    $worksheet1->write($i, 0, $id, $borderleft);
	    $worksheet1->write($i, 1, $tmp , $borderleft);
	    $worksheet1->write($i, 2, $pozn_conv, $borderleft);
	    }
	    else
	    {
	    $worksheet1->write($i, 1, $tmp , $left);
	    }
	    
	    $i++;
	}
	
	
    endwhile;

// konec 

$workbook->close();
 // header("<meta http-equiv=Content-Type content=\"text/html; charset=utf8\">");
    
 header("Content-Type: application/x-msexcel; name=\"export-archiv-zmen.xls\"");
 header("Content-Disposition: inline; filename=\"export-archiv-zmen.xls\"");

$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

function na_text($vstup)
{

for($i=0;$i<strlen($vstup);$i++)
{
    if ( ( ord(substr($vstup,$i,1))> 43 ) and ( ord(substr($vstup,$i,1))< 59 ) ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ( ( ord(substr($vstup,$i,1)) > 65 ) and ( ord(substr($vstup,$i,1))< 91 )  ) ) { $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ( ( ord(substr($vstup,$i,1))> 96 ) and ( ord(substr($vstup,$i,1))< 123 ) ) ) { $vystup = $vystup.substr($vstup,$i,1); }
    
    elseif ( ord(substr($vstup,$i,1)) ==  91 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  93 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  138 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  141 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  142 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  154 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  158 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  193 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  142 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  200 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  216 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  225 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  232 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  233 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  236 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  237 ){ $vystup = $vystup.substr($vstup,$i,1); }
    
    elseif ( ord(substr($vstup,$i,1)) ==  242 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  243 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  248 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  249 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  250 ){ $vystup = $vystup.substr($vstup,$i,1); }
    elseif ( ord(substr($vstup,$i,1)) ==  253 ){ $vystup = $vystup.substr($vstup,$i,1); }
    
    
    elseif ( ord(substr($vstup,$i,1)) ==  61 ) { $vystup = $vystup."-"; }
    else { $vystup = $vystup." "; }
}

    return $vystup; 
    
}
	    
?>
