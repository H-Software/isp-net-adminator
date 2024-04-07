<?php

#  WriteExcel module to create worksheet panes.
#
# reverse(''), May 2001, John McNamara, jmcnamara@cpan.org
#
# PHP port by Johann Hanne, 2005-11-01

set_time_limit(45);

require("config.php");
//include("config.pg.php");

require_once "class.writeexcel_workbook.inc.php";
require_once "class.writeexcel_worksheet.inc.php";

$fname = tempnam("/export", "export-ucetni.xls");

$workbook = &new writeexcel_workbook($fname);

require_once "export_ucetni.inc.function.php";

#######################################################################
#
# Set up some formatting and text to highlight the panes
#

$header =& $workbook->addformat( array(color=>'white', align=>'center', fg_color=>'green') );
$header->set_align('vcenter');
$header->set_pattern();

$center =& $workbook->addformat( array(align=>'center') );

$fu_center =& $workbook->addformat( array(top=>1, align=>'center') );

$fu_center_fs =& $workbook->addformat( array(top=>1, align=>'center', color=>'gray', size=>'8') );

$du_tel =& $workbook->addformat( array(align=>'center', color=>'16') );

$fu_2_line =& $workbook->addformat( array(align=>'center', color=>'16') );

$du_pozs_fa =& $workbook->addformat( array(align=>'center', color=>'16') );
$du_pozs_fa->set_bold();

$fu_pozs_fa =& $workbook->addformat( array(align=>'center', color=>'16') );
$fu_pozs_fa->set_bold();
   
$left =& $workbook->addformat( array(align=>'left') );

$leftpozn2 =& $workbook->addformat( array(align=>'center', color=>'16') );

$bordercenter =& $workbook->addformat( array(top=>1, align=>'center') );
$bordercenter->set_align('vcenter');

$border5 =& $workbook->addformat( array(top=>1, align=>'vcenter', color=>'gray') );
$border5->set_align('center');

$border5_fs =& $workbook->addformat( array(top=>1, align=>'vcenter', color=>'gray', size=>'8' ) );
$border5_fs->set_align('center');

$borderleft =& $workbook->addformat( array(top=>1, text_wrap=>1, align=>'vcenter') );
$borderleft->set_align('left'); 

$borderleftpolozka =& $workbook->addformat( array(top=>1, text_wrap=>1, align=>'vcenter') );
$borderleftpolozka->set_align('left');

$du_fs_wifi =& $workbook->addformat( array(top=>1, text_wrap=>1, align=>'vcenter', color=>'gray', size=>'8') );
$du_fs_wifi->set_align('center');

$du_fs_wifi_color =& $workbook->addformat( 
		    array(top=>1, text_wrap=>1, align=>'vcenter', color=>'gray', fg_color=>'yellow', size=>'8') );
$du_fs_wifi_color->set_align('center');

$borderleftcolor =& $workbook->addformat( array (top=>1, fg_color=>'yellow', align =>'vcenter') );
$borderleftcolor->set_align('left');

$text_color1 =& $workbook->addformat( array(bold => 0, color => 'grey', align => 'center') );

$bordercenter2 =& $workbook->addformat( array(top=>1, align=>'center', fg_color=>'yellow') );

$center_bold =& $workbook->addformat( array( bold=>1, top=>'1', align =>'vcenter' ) );
$center_bold->set_align('center');

$center_bold2 =& $workbook->addformat( array( bold=>1, top=>'1', fg_color=>'yellow', align=>'vcenter' ) );
$center_bold2->set_align('center');

$leftpozn =& $workbook->addformat( array ( color => 'blue' ) );

$fs_text =& $workbook->addformat( array(align=>'left', color=>'16') );

$fs_id =& $workbook->addformat( array(align=>'center', top=>'1') );
$fs_id->set_bold();

$fs_s_int =& $workbook->addformat( array(top=>1, text_wrap=>1, align=>'vcenter') );

$pocet_klientu =& $workbook->addformat( array ( align => 'center', bold => 1 ) );
$pocet_klientu2 =& $workbook->addformat( array ( align => 'center' ) );

$pocet_klientu3 =& $workbook->addformat( array ( align => 'left' ) );

//$fs_s_int->set_align('center'); 

//set_size()

###
#
#   zde novy zpusob generovani listu pres funkci
#
###

#######################################################################
#
# Sheet 1 - domaci uziv.
#

//prvne vyberem lidi
#$dotaz_du = pg_query("SELECT * FROM vlastnici 
#			WHERE ( firma = '1' AND fakturacni is NULL AND ( archiv = '0' OR archiv is null ) )
#			ORDER BY billing_freq, ucetni_index ");

$dotaz_du = pg_query("SELECT * FROM vlastnici 
			WHERE ( fakturacni is NULL AND ( archiv = '0' OR archiv is null ) )
			ORDER BY billing_freq, ucetni_index ");

$pole_id_du = array();

while( $data_du = pg_fetch_array($dotaz_du))
{
 $pole_id_du[] = $data_du["id_cloveka"];
}

createsheet("1","1"," domácí uživ. ",$pole_id_du);

#######################################################################
#
# Sheet 2 - firemni uziv.
#

$dotaz_fu=pg_query("SELECT t1.id_cloveka,t1.jmeno, t1.prijmeni, t1.mail, t1.telefon, t1.k_platbe, t1.ucetni_index, t1.poznamka,
			    t1.fakturacni_skupina_id, t1.billing_freq, t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic
			    
	    FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id ) 
	    WHERE ( fakturacni IS NOT NULL AND ( archiv = '0' or archiv is null ) ) ORDER BY billing_freq, ucetni_index 
		");

$pole_id_fu = array();

while( $data_fu = pg_fetch_array($dotaz_fu) )
{
 $pole_id_fu[] = $data_fu["id_cloveka"];
}

createsheet("2","2"," firemní uživ. ",$pole_id_fu);

#######################################################################
#
# Sheet 3 - domaci archiv
#

$dotaz_du_a = pg_query("SELECT * FROM vlastnici WHERE ( fakturacni is NULL AND archiv = '1' ) order by ucetni_index ");

$pole_id_du_a = array();

while( $data_du_a = pg_fetch_array($dotaz_du_a) )
{
 $pole_id_du_a[] = $data_du_a["id_cloveka"];
}

createsheet("1","3"," archiv dú ",$pole_id_du_a);

#######################################################################
#
# Sheet 4 - firemni uzivatele archiv
#

$dotaz_fu_a = pg_query("SELECT t1.id_cloveka,t1.jmeno, t1.prijmeni, t1.mail, t1.telefon, t1.k_platbe, t1.ucetni_index, t1.poznamka,
			    t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic

	    FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id ) 
	    WHERE ( fakturacni IS NOT NULL AND archiv = '1' ) ORDER BY ucetni_index 
		");

$pole_id_fu_a = array();

while( $data_fu_a = pg_fetch_array($dotaz_fu_a) )
{
 $pole_id_fu_a[] = $data_fu_a["id_cloveka"];
}

createsheet("2","4"," archiv fú ",$pole_id_fu_a);

#######################################################################
#
# Sheet 5 - pozastavene fakturace DU
#

$dotaz_du_pz_fa = pg_query("SELECT * FROM vlastnici WHERE ( fakturacni IS NULL AND ( archiv = '0' or archiv is null ) AND billing_suspend_status = 1 ) ORDER BY billing_freq, ucetni_index");

$pole_id_du_pz_fa = array();

while( $data_du_pz_fa = pg_fetch_array($dotaz_du_pz_fa) )
{
 $pole_id_du_pz_fa[] = $data_du_pz_fa["id_cloveka"];
}

createsheet("1","5"," pozast. fa. dú ",$pole_id_du_pz_fa);

#######################################################################
#
# Sheet 6 - pozastavene fakturace FU
#

$dotaz_fu_pz_fa = pg_query("SELECT * FROM vlastnici WHERE ( fakturacni IS NOT NULL AND ( archiv = '0' or archiv is null ) AND billing_suspend_status = 1 ) ORDER BY billing_freq, ucetni_index");

$pole_id_fu_pz_fa = array();

while( $data_fu_pz_fa = pg_fetch_array($dotaz_fu_pz_fa) )
{
 $pole_id_fu_pz_fa[] = $data_fu_pz_fa["id_cloveka"];
}

createsheet("2","5"," pozast. fa. fú ",$pole_id_fu_pz_fa);

#######################################################################
#
# Sheet X - dynamicke listy
#

$dotaz_fakt_skup=mysql_query("SELECT * FROM fakturacni_skupiny order by nazev DESC");

//zde prvne zjistovani jestli ve FS jsou naky lidi ..
$fakt_skupiny_pole = array();
     
while( $data_fakt_skup=mysql_fetch_array($dotaz_fakt_skup) )
{
    $fakturacni_skupina_id = $data_fakt_skup["id"];

    $dotaz_fs_lidi = pg_query("SELECT * FROM vlastnici WHERE fakturacni_skupina_id = '$fakturacni_skupina_id' ");
    $dotaz_fs_lidi_radku = pg_num_rows($dotaz_fs_lidi);
    
    if( ($dotaz_fs_lidi_radku > 0 ))
    {
     $fakt_skupiny_pole[] = $fakturacni_skupina_id;
    }
}

$x=5; //prvni dyn. list uz je celkem 5ty ...

for ($p = 0; $p < count($fakt_skupiny_pole); $p++)
{

    $fakturacni_skupina_id = $fakt_skupiny_pole[$p];
    
    $dotaz_fakt_skup=mysql_query("SELECT * FROM fakturacni_skupiny WHERE id = '$fakturacni_skupina_id' ");
    $dotaz_fakt_skup_radku=mysql_num_rows($dotaz_fakt_skup);
    
    while( $data_fakt_skup=mysql_fetch_array($dotaz_fakt_skup) )
    {
     $worksheet = 'worksheet'.$x;
     
     $fakturacni_skupina_id=$data_fakt_skup["id"];
    
     if( $data_fakt_skup["typ"] == 2 )
     { 
	//firemni sablona ..
	
	$dotaz_fu_d = pg_query("SELECT t1.id_cloveka,t1.jmeno, t1.prijmeni, t1.mail, t1.telefon, t1.k_platbe, t1.ucetni_index, t1.poznamka,
			    t2.ftitle, t2.fulice, t2.fmesto, t2.fpsc, t2.ico, t2.dic

	    FROM ( vlastnici AS t1 LEFT JOIN fakturacni AS t2 ON t1.fakturacni=t2.id ) 
	    WHERE ( fakturacni IS NOT NULL AND ( archiv = '0' or archiv is null ) 
	    and fakturacni_skupina_id = '$fakturacni_skupina_id' ) ORDER BY ucetni_index 
		");
		
	$pole_id_fu_d = array();

	while( $data_fu_d = pg_fetch_array($dotaz_fu_d) )
	{
	    $pole_id_fu_d[] = $data_fu_d["id_cloveka"];
	}

	createsheet("2",$x,"fu-fs-".$data_fakt_skup["id"],$pole_id_fu_d);     
     }
     else 
     { 
	$dotaz_du_d = pg_query("SELECT * FROM vlastnici 
		WHERE ( fakturacni is NULL AND ( archiv = '0' OR archiv is null ) 
		 AND fakturacni_skupina_id = '$fakturacni_skupina_id' ) order by ucetni_index 
		 ");

	$pole_id_du_d = array();

	while( $data_du_d = pg_fetch_array($dotaz_du_d) )
	{
	    $pole_id_du_d[] = $data_du_d["id_cloveka"];
	}

	createsheet("1",$x,"du-fs-".$data_fakt_skup["id"],$pole_id_du_d);      
     }
     
    $x++;
    
   } // konec while
    
}// konec for dynamicke listy ..

// konec dynamickeho obsahu

###
#
#  fakturacni skupiny
#
##

 $worksheet = 'worksheet'.$x;
 
 $$worksheet =& $workbook->addworksheet("fakturacni-skupiny");
 $$worksheet->freeze_panes(2, 0); // zmrazeni prvnich 2 radek

 $$worksheet->set_column('A:A', 7);     //id
 $$worksheet->set_column('B:B', 25);    //jmeno tarifu
 
 $$worksheet->set_column('C:C', 8);     //typ FS
 $$worksheet->set_column('D:D', 10);    //typ služby

 $$worksheet->set_column('E:E', 16);    //sluzba int
 $$worksheet->set_column('F:F', 16);    //sluzba iptv
 
 //toto nevim
 $$worksheet->set_row(0, 20);
 $$worksheet->set_selection('C3');
 
 //prvni radek
 $$worksheet->write(0, 0, 'FS-id', $header);
 $$worksheet->write(0, 1, iconv("UTF-8","CP1250", " název "), $header);
 $$worksheet->write(0, 2, " typ FS ", $header);
 $$worksheet->write(0, 3, iconv("UTF-8","CP1250", " typ služby "), $header);

 $$worksheet->write(0, 4, iconv("UTF-8","CP1250", " služba internet "), $header);
 $$worksheet->write(0, 5, iconv("UTF-8","CP1250", " služba iptv "), $header);

 //druhej radek
 $$worksheet->write(1, 0, '', $header);
 $$worksheet->write(1, 1, iconv("UTF-8","CP1250", " fakturační text "), $header);
 
 $$worksheet->write(1, 4, iconv("UTF-8","CP1250", " id/zkratka tarifu "), $header);
 $$worksheet->write(1, 5, iconv("UTF-8","CP1250", " id/zkratka tarifu "), $header);
 
 $i=2;
  
 $dotaz_fs = mysql_query("SELECT * FROM fakturacni_skupiny ORDER BY id ");
 $dotaz_fs_radku = mysql_num_rows($dotaz_fs);
 
  while( $data_fs = mysql_fetch_array($dotaz_fs) )
  {
      
    $$worksheet->write($i, 0, $data_fs["id"], $fs_id);
    $$worksheet->write($i, 1, $data_fs["nazev"], $borderleft4);

    if( $data_fs["typ"] == 1 )
    { $typ = iconv("UTF-8","CP1250", " DÚ "); }
    elseif( $data_fs["typ"] == 2 )
    { $typ = iconv("UTF-8","CP1250", " FÚ "); }
    else
    { $typ = "Nelze zjistit"; }
    
    $$worksheet->write($i, 2, $typ , $borderleft4);
    
    if( $data_fs["typ_sluzby"] == 0 )
    { $$worksheet->write($i, 3, "wifi", $borderleft4); }
    elseif( $data_fs["typ_sluzby"] == 1 )
    { $$worksheet->write($i, 3, "optika", $borderleft4); }
    else
    { $$worksheet->write($i, 3, "Nelze zjistit", $borderleft4); }

    if( $data_fs["sluzba_int"] == 0 )
    { $sluzba_int = "Ne"; }
    elseif( $data_fs["sluzba_int"] == 1 )
    { $sluzba_int = "Ano"; }
    else
    { $sluzba_int = "N/Z"; }
    
    $$worksheet->write($i, 4, $sluzba_int, $fs_s_int);
    
    if( $data_fs["sluzba_iptv"] == 0 )
    { $sluzba_iptv = "Ne"; }
    elseif( $data_fs["sluzba_iptv"] == 1 )
    { $sluzba_iptv = "Ano"; }
    else
    { $sluzba_iptv = "N/Z"; }
    
    $$worksheet->write($i, 5, $sluzba_iptv, $fs_s_int);
    
    $i++;
    
    $$worksheet->write($i, 1, iconv("UTF-8","CP1250", $data_fs["fakturacni_text"]), $fs_text);
    $$worksheet->write($i, 4, $data_fs["sluzba_int_id_tarifu"]." ()", $fs_text);
    
    $$worksheet->write($i, 5, $data_fs["sluzba_iptv_id_tarifu"]." ()", $fs_text);
    
    $i++;
  }
   
#
#  konec vypisu fakturovacnich skupin
#

$workbook->close();
// header("<meta http-equiv=Content-Type content=\"text/html; charset=utf8\">");

header("Content-Type: application/x-msexcel; name=\"export-ucetni.xls\"");
header("Content-Disposition: inline; filename=\"export-ucetni.xls\"");

$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

?>
