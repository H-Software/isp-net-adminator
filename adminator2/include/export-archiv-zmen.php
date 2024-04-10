<?php

# Example of using the WriteExcel module to create worksheet panes.
# reverse(''), May 2001, John McNamara, jmcnamara@cpan.org
# PHP port by Johann Hanne, 2005-11-01
				
set_time_limit(0);

error_reporting(E_ERROR | E_CORE_ERROR | E_PARSE);

require ("main.function.shared.php");
require ("config.php");

require_once "class.writeexcel_biffwriter.inc.php";
require_once "class.writeexcel_format.inc.php";
require_once "class.writeexcel_formula.inc.php";
require_once "class.writeexcel_olewriter.inc.php";
require_once "class.writeexcel_workbook.inc.php";
require_once "class.writeexcel_workbookbig.inc.php";
require_once "class.writeexcel_worksheet.inc.php";

global $delic; 

$fname = tempnam("/export", "export-archiv-zmen.xls");

$workbookbig = &new writeexcel_workbookbig($fname);

try {
    $dotaz = $conn_mysql->query("SELECT * FROM archiv_zmen ");
    $dotaz_radku = $dotaz->num_rows;
} catch (Exception $e) {
    die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
}

$delic='6000';

$pocet_listu = (int)$dotaz_radku / (int)$delic;

$pocet_listu = $pocet_listu + 1;

if ( $dotaz_radku > 1 )
{

    for ($z=1; $z <= $pocet_listu; $z++)
    {

        $worksheet = 'worksheet'.$z;
                    
        // $$
        $$worksheet =& $workbookbig->addworksheet("list-".$z);

        $$worksheet ->freeze_panes(2, 0);

        #######################################################################
        #
        # Set up some formatting and text to highlight the panes
        #

        $header =& $workbookbig->addformat();
        $header->set_color('white');
        $header->set_align('center');
        $header->set_align('vcenter');
        $header->set_pattern();
        $header->set_fg_color('green');

        $center =& $workbookbig->addformat();
        $center->set_align('center');

        $left =& $workbookbig->addformat();
        $left->set_align('left');

        $bordercenter =& $workbookbig->addformat();
        $bordercenter->set_top(1);
        $bordercenter->set_align('center');

        $borderleft =& $workbookbig->addformat();
        $borderleft->set_top(1);
        $borderleft->set_align('left');

        $borderleftcolor =& $workbookbig->addformat();
        $borderleftcolor->set_top(1);
        $borderleftcolor->set_align('left');
        $borderleftcolor->set_fg_color('yellow');

        $text_color1 =& $workbookbig->addformat( array( bold    => 0, color   => 'grey') );

        $bordercenter2 =& $workbookbig->addformat();
        $bordercenter2->set_top(1);
        $bordercenter2->set_align('center');
        $bordercenter2->set_fg_color('yellow');
            
        $center_bold =& $workbookbig->addformat( array( bold => 1, align => 'center', top => '1' ) );

        $center_bold2 =& $workbookbig->addformat( array( bold => 1, align => 'center', top => '1', fg_color => 'yellow' ) );
                
        $leftpozn =& $workbookbig->addformat( array ( color => 'blue' ) );


        #######################################################################
        #
        # Sheet  - export
        #

        // nastavení sirek sloupcu
        $$worksheet->set_column('A:A', 6); //id

        $$worksheet->set_column('B:B', 400);	// akce	
        $$worksheet->set_column('C:C', 23);	// pozn

        $$worksheet->set_column('D:D', 30);	// provedeno_kdy
        $$worksheet->set_column('E:E', 30);	// provedeno_kym
        $$worksheet->set_column('F:F', 30);	// vysledek

        // nevim
        $$worksheet->set_row(0, 20);
        $$worksheet->set_selection('C3');

        //prvni radek

        $$worksheet->write(0, 0, 'id', $header);
        $$worksheet->write(0, 1, 'akce', $header);
        $$worksheet->write(0, 2, 'pozn', $header);

        $$worksheet->write(0, 3, 'provedeno_kdy', $header);
        $$worksheet->write(0, 4, 'provedeno_kym', $header);
        $$worksheet->write(0, 5, 'vysledek', $header);

        // vlastni data

        $i=2;

        $zac_id = $delic * ( $z - 1);

        $sql2="SELECT * FROM archiv_zmen LIMIT $delic OFFSET $zac_id ";

        try {
            $dotaz2=$conn_mysql->query($sql2);
            $dotaz_radku2=$dotaz2->num_rows;
        } catch (Exception $e) {
            die ("<h2 style=\"color: red; \">Error: Database query failed! Caught exception: " . $e->getMessage() . "\n" . "</h2></body></html>\n");
        }

        while( $data=$dotaz2->fetch_array() ):

            //jednotlive promenne
            $id=$data["id"];
            
            $akce_conv = iconv("UTF-8","CP1250", $data["akce"]);
            $pozn_conv = iconv("UTF-8","CP1250", $data["pozn"]);
            
            $provedeno_kdy_conv = iconv("UTF-8","CP1250", $data["provedeno_kdy"]);
            $provedeno_kym_conv = iconv("UTF-8","CP1250", $data["provedeno_kym"]);
            $vysledek_conv = iconv("UTF-8","CP1250", $data["vysledek"]);
            
            $akce_conv = str_replace (">", ".", $akce_conv );
            $akce_conv = str_replace ("<", ".", $akce_conv );

            $akce_conv = str_replace ("=", "-", $akce_conv );
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
                $$worksheet->write($i, 0, $id, $borderleft);
                $$worksheet->write($i, 1, $tmp , $borderleft);
                $$worksheet->write($i, 2, $pozn_conv, $borderleft);
                
                $$worksheet->write($i, 3, $provedeno_kdy_conv , $borderleft);
                $$worksheet->write($i, 4, $provedeno_kym_conv, $borderleft);
                $$worksheet->write($i, 5, $vysledek_conv, $borderleft);
                
                }
                else
                { $$worksheet->write($i, 1, $tmp , $left); }
            
                $i++;
            }
        
        endwhile;
        
    } // konec for
 
} // konec if radku > 1

$workbookbig->close();
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
