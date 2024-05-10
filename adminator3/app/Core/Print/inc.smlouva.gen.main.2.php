<?php

//zaklad, vytvoreni objektu a pridani stranky
$pdf = new FPDF("P", "mm", "A4");
// $pdf->Open();
$pdf->AddPage();

// ceskej arial
$pdf->AddFont('arial', '', 'arial.php');

// autor a podobny hemzy

//Nastaví autora dokumentu.
$pdf->SetAuthor("Adminator3");

//Nastaví tvůrce dokumentu (většinou název aplikace)
$pdf->SetCreator("Smlouva o poskytování služeb");

//Titulek dokumentu
$pdf->SetTitle("Smlouva");

// vlozeni obrazku na pozadi
// TODO: fix inserting picture
// $img="../img2/print/2012-05-31-smlouva_1mb.jpg";
// $pdf->Image($img,0,0,210);

$pdf->SetFont('Arial', '', 10);

//$pdf->Cell(0,1,'',0,1);

$pdf->Cell(145);
$pdf->Cell(0, -4, $ec, 0, 1);

$pdf->Cell(0, 47, '', 0, 1);

$pdf->Cell(48);
$pdf->Cell(5, 5, $jmeno, 0, 0);
$pdf->Cell(85);
$pdf->Cell(5, 5, $nazev_spol, 0, 1);

$pdf->Cell(48);
$pdf->Cell(5, 5, $adresa, 0, 0);
$pdf->Cell(85);
$pdf->Cell(5, 6, $f_adresa, 0, 1);

$pdf->Cell(48);
$pdf->Cell(5, 4, $mesto, 0, 0);
$pdf->Cell(85);
$pdf->Cell(5, 4, $f_mesto, 0, 1);

$pdf->Cell(48);
$pdf->Cell(5, 6, $cislo_op, 0, 0);
$pdf->Cell(83);
$pdf->Cell(5, 6, $ico, 0, 0);
$pdf->Cell(25);
$pdf->Cell(5, 6, $dic, 0, 1);

$pdf->Cell(48);
$pdf->Cell(5, 7, $kor_adresa, 0, 0);
$pdf->Cell(85);
$pdf->Cell(5, 7, $telefon, 0, 1);

$pdf->Cell(48);
$pdf->Cell(5, 4, $kor_mesto, 0, 0);
$pdf->Cell(85);
$pdf->Cell(5, 4, $email, 0, 1);

$pdf->Cell(0, 31, '', 0, 1);

$pdf->Cell(18);
$pdf->Cell(5, 6, $int_1_nazev, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 6, $int_1_rychlost, 0, 0);
$pdf->Cell(11);
$pdf->Cell(5, 6, $int_1_cena_1, 0, 0);

$pdf->Cell(22);
$pdf->Cell(5, 6, $int_1_vip, 0, 0);
$pdf->Cell(12);
$pdf->Cell(5, 6, $int_1_cena_2, 0, 0);
$pdf->Cell(11);
$pdf->Cell(5, 6, $int_1_adresa, 0, 1);

//$pdf->Cell(0,2,'',0,1);

$pdf->Cell(18);
$pdf->Cell(5, 6, $int_2_nazev, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 6, $int_2_rychlost, 0, 0);
$pdf->Cell(11);
$pdf->Cell(5, 6, $int_2_cena_1, 0, 0);

$pdf->Cell(22);
$pdf->Cell(5, 6, $int_2_vip, 0, 0);
$pdf->Cell(12);
$pdf->Cell(5, 6, $int_2_cena_2, 0, 0);
$pdf->Cell(11);
$pdf->Cell(5, 6, $int_2_adresa, 0, 1);

$pdf->Cell(0, 16, '', 0, 1);

//IPTV

$pdf->Cell(20);
$pdf->Cell(5, 3, $iptv_tarif_nazev, 0, 0);
$pdf->Cell(35);
$pdf->Cell(5, 3, $iptv_tarif_kanaly, 0, 0);
$pdf->Cell(15);
$pdf->Cell(5, 3, $iptv_tarif_cena, 0, 0);

$pdf->Cell(35);
$pdf->Cell(4, 3, $voip_1_cislo, 0, 0);
$pdf->Cell(39);
$pdf->Cell(4, 2, $voip_1_pre, 0, 0);
$pdf->Cell(15);
$pdf->Cell(4, 2, $voip_1_post, 0, 1);

$pdf->Cell(0, 2, '', 0, 1);

//2. radka IPTV
$pdf->Cell(20);
$pdf->Cell(5, 4, $iptv_tema_nazev, 0, 0);
$pdf->Cell(35);
$pdf->Cell(5, 4, $iptv_tema_kanaly, 0, 0);
$pdf->Cell(15);
$pdf->Cell(5, 4, $iptv_tema_cena, 0, 0);

$pdf->Cell(35);
$pdf->Cell(5, 4, $voip_2_cislo, 0, 0);
$pdf->Cell(38);
$pdf->Cell(5, 4, $voip_2_pre, 0, 0);
$pdf->Cell(14);
$pdf->Cell(5, 4, $voip_2_post, 0, 1);

$pdf->Cell(0, 6, '', 0, 1);

//Stb 1.radka
$pdf->Cell(20);
$pdf->Cell(5, 4, $stb, 0, 1);

$pdf->Cell(0, 1, '', 0, 1);

//stb 2.radka /ostatní
$pdf->Cell(20);
$pdf->Cell(20, 3, $stb_sn, 0, 0);
$pdf->Cell(38);
$pdf->Cell(5, 3, $stb_kauce, 0, 0);

$pdf->Cell(38);
$pdf->Cell(5, 3, $ostatni_nazev, 0, 0);
$pdf->Cell(50);
$pdf->Cell(5, 3, $ostatni_cena, 0, 1);

$pdf->Cell(0, 11, '', 0, 1);

//sleva
$pdf->Cell(20);
$pdf->Cell(13, 5, $bonus_select_jmeno, 0, 1);

//plateb. predpis č.1
$pdf->SetFont('Arial', '', 8);

$pdf->Cell(101);
$pdf->Cell(13, 4, $platba_1_od, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 4, $platba_1_do, 0, 0);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(10);
$pdf->Cell(10, 4, $platba_1_cena, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 4, $platba_1_pozn, 0, 1);

$pdf->Cell(0, 1, '', 0, 1);

//bonus č.1
$pdf->Cell(20);
$pdf->Cell(13, 3, $bonus_1_tarif, 0, 0);
$pdf->Cell(27);
$pdf->Cell(13, 3, $bonus_1_cena1, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 3, $bonus_1_cena2, 0, 0);

//plateb. predpis č.2
$pdf->SetFont('Arial', '', 8);

$pdf->Cell(10);
$pdf->Cell(13, 3, $platba_2_od, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 3, $platba_2_do, 0, 0);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(10);
$pdf->Cell(10, 3, $platba_2_cena, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 3, $platba_2_pozn, 0, 1);

//bonus č.2
$pdf->Cell(20);
$pdf->Cell(13, 6, $bonus_2_tarif, 0, 0);
$pdf->Cell(27);
$pdf->Cell(13, 6, $bonus_2_cena1, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 6, $bonus_2_cena2, 0, 0);

//plateb. predpis č.3
$pdf->SetFont('Arial', '', 8);

$pdf->Cell(10);
$pdf->Cell(13, 6, $platba_3_od, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 6, $platba_3_do, 0, 0);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(10);
$pdf->Cell(10, 6, $platba_3_cena, 0, 0);
$pdf->Cell(5);
$pdf->Cell(13, 6, $platba_3_pozn, 0, 1);

$pdf->Cell(0, 15, '', 0, 1);

//platby
$pdf->Cell(95);
$pdf->Cell(13, 4, $vs, 0, 1);

$pdf->Cell(0, 8, '', 0, 1);

$pdf->Cell(20);
$pdf->Cell(13, 4, $platba, 0, 0);
$pdf->Cell(45);
$pdf->Cell(13, 4, $zpusob_placeni, 0, 0);

$pdf->Cell(55);
$pdf->Cell(13, 4, $celk_cena, 0, 0);
$pdf->Cell(15);
$pdf->Cell(13, 4, $celk_cena_s_dph, 0, 1);

//min plneni
$pdf->Cell(0, 9, '', 0, 1);

$pdf->Cell(105);
$pdf->Cell(10, 5, $min_plneni_doba, 0, 1);

$pdf->Cell(60);
$pdf->Cell(10, 3, $aut_prodlouzeni, 0, 1);


$datum_nz = date('Y-m-d-H-i-s');

if($id_cloveka > 0) {
    $nazev_souboru = "print/temp/smlouva-v3-pdf-id-".$id_cloveka."-".$datum_nz.".pdf";
} else {
    $nazev_souboru = "print/temp/smlouva-v3-pdf-ec-".$ec."-".$datum_nz.".pdf";
}

$rs = $pdf->Output($nazev_souboru, "F");

$this->logger->info("inc.smlouva.gen.main.2.php: dump var nazev_souboru: ".var_export($nazev_souboru, true));
