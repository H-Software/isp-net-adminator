<?php

//zaklad, vytvoreni objektu a pridani stranky
$pdf = new FPDF("P", "mm", "A4");
// $pdf->Open();
$pdf->AddPage();

// ceskej arial
$pdf->AddFont('arial', '', 'arial.php');

// autor a podobny hemzy

//Nastaví autora dokumentu.
$pdf->SetAuthor("Simelon Adminator3");

//Nastaví tvůrce dokumentu (většinou název aplikace)
$pdf->SetCreator("Registrační formulář");

//Titulek dokumentu
$pdf->SetTitle("Reg. Formulář");

// vlozeni obrazku na pozadi
// TODO: fix missing background image
// $img="../img2/print/2009_technicka_sekce_01.jpg";
// $pdf->Image($img,0,0,210);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(0, 1, '', 0, 1);

$pdf->Cell(145);
$pdf->Cell(50, 3, $ec, 0, 1);

$pdf->Cell(0, 34, '', 0, 1);

$pdf->Cell(130);
$pdf->Cell(5, 5, $vas_technik, 0, 1);
$pdf->Cell(150);
$pdf->Cell(5, 9, $vas_technik_tel, 0, 1);

$pdf->Cell(0, 26, '', 0, 1);

$pdf->Cell(59);
$pdf->Cell(5, 5, $prip_tech_1, 0, 0);
$pdf->Cell(42);
$pdf->Cell(5, 5, $prip_tech_2, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $prip_tech_3, 0, 1);

$pdf->Cell(64);
$pdf->Cell(5, 5, $cislo_portu, 0, 0);
$pdf->Cell(37);
$pdf->Cell(5, 5, $poznamka, 0, 1);

$pdf->Cell(0, 17, '', 0, 1);

//internet

$pdf->Cell(7);
$pdf->Cell(5, 5, $int_zarizeni_1, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $int_zarizeni_1_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $int_zarizeni_1_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $int_zarizeni_1_vlastnik_x, 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $int_zarizeni_2, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $int_zarizeni_2_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $int_zarizeni_2_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $int_zarizeni_2_vlastnik_x, 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $int_zarizeni_3, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $int_zarizeni_3_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $int_zarizeni_3_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $int_zarizeni_3_vlastnik_x, 0, 1);

$pdf->Cell(0, 3, '', 0, 1);

$pdf->Cell(32);
$pdf->Cell(5, 5, $ip_dhcp_x, 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $ip_adresa, 0, 0);
$pdf->Cell(39);
$pdf->Cell(5, 5, $ip_maska, 0, 0);
$pdf->Cell(23);
$pdf->Cell(5, 5, $ip_brana, 0, 0);

$pdf->Cell(39);
$pdf->Cell(5, 5, $ip_dns1, 0, 0);
$pdf->Cell(23);
$pdf->Cell(5, 5, $ip_dns2, 0, 1);

$pdf->Cell(0, 16, '', 0, 1);

//IPTV

$pdf->Cell(7);
$pdf->Cell(5, 5, $iptv_zarizeni_1, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $iptv_zarizeni_1_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $iptv_zarizeni_1_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $iptv_zarizeni_1_vlastnik_x, 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $iptv_zarizeni_2, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $iptv_zarizeni_2_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $iptv_zarizeni_2_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $iptv_zarizeni_2_vlastnik_x, 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $iptv_zarizeni_3, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $iptv_zarizeni_3_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $iptv_zarizeni_3_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $iptv_zarizeni_3_vlastnik_x, 0, 1);

//VOIP
$pdf->Cell(0, 16, '', 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $voip_zarizeni_1, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $voip_zarizeni_1_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $voip_zarizeni_1_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $voip_zarizeni_1_vlastnik_x, 0, 1);

$pdf->Cell(7);
$pdf->Cell(5, 5, $voip_zarizeni_2, 0, 0);
$pdf->Cell(41);
$pdf->Cell(5, 5, $voip_zarizeni_2_ip, 0, 0);
$pdf->Cell(40);
$pdf->Cell(5, 5, $voip_zarizeni_2_pozn, 0, 0);
$pdf->Cell(54);
$pdf->Cell(5, 5, $voip_zarizeni_2_vlastnik_x, 0, 1);

//MATERIAL
$pdf->Cell(0, 9, '', 0, 1);

$pdf->Cell(8);
$pdf->Cell(5, 5, $mat_1, 0, 1);
$pdf->Cell(8);
$pdf->Cell(5, 5, $mat_2, 0, 1);
$pdf->Cell(8);
$pdf->Cell(5, 5, $mat_3, 0, 1);

//POZNAMKA 2
$pdf->Cell(0, 10, '', 0, 1);

$pdf->Cell(8);
$pdf->MultiCell(175, 7, $poznamka2, 0, 1);
// $pdf->Cell(21); $pdf->Cell(5,5,$celk_cena_s_dph,0,1);

$datum_nz = date('Y-m-d-H-i-s');

if ($id_cloveka > 0) {
    $nazev_souboru = "print/temp/reg-form-pdf-id-".$id_cloveka."-".$datum_nz.".pdf";
} else {
    $nazev_souboru = "print/temp/reg-form-pdf-ec-".$ec."-".$datum_nz.".pdf";
}

$rs = $pdf->Output($nazev_souboru, "F");
