<?php

//zaklad, vytvoreni objektu a pridani stranky
$pdf=new FPDF("P","mm","A4");
// $pdf->Open();
$pdf->AddPage();

// ceskej arial
$pdf->AddFont('arial','','arial.php');

// autor a podobny hemzy

//Nastaví autora dokumentu.
$pdf->SetAuthor("Simelon Adminator3");

//Nastaví tvůrce dokumentu (většinou název aplikace)
$pdf->SetCreator("Smlouva o poskytování služeb");

//Titulek dokumentu
$pdf->SetTitle("Smlouva");

// vlozeni obrazku na pozadi
// TODO: fix missing background image
// $img="../img2/print/2011-10-24-smlouva-a-podpis.jpg";
// $pdf->Image($img,0,0,210);

$pdf->SetFont('Arial','',10);

$pdf->Cell(0,1,'',0,1);

$pdf->Cell(145); $pdf->Cell(50,3,$ec,0,1);

$pdf->Cell(0,34,'',0,1);

 $pdf->Cell(35); $pdf->Cell(5,5,$jmeno,0,0);
 $pdf->Cell(95); $pdf->Cell(5,5,$nazev_spol,0,1);

 $pdf->Cell(53); $pdf->Cell(5,6,$adresa,0,0);
 $pdf->Cell(77); $pdf->Cell(5,6,$ico_dic,0,1);

 $pdf->Cell(53); $pdf->Cell(5,4,$mesto,0,0);
 $pdf->Cell(77); $pdf->Cell(5,4,$email,0,1);

 $pdf->Cell(53); $pdf->Cell(5,6,$telefon,0,1);

 $pdf->Cell(53); $pdf->Cell(5,4,$kor_adresa,0,1);
 $pdf->Cell(53); $pdf->Cell(5,5,$kor_mesto,0,1);

 $pdf->Cell(0,21,'',0,1);

 $pdf->Cell(66); $pdf->Cell(5,4,$spec_prip_mista,0,1); //prip misto jako trvale bydl.

 $pdf->Cell(52); $pdf->Cell(5,8,$prip_misto_adresa,0,0);
 $pdf->Cell(45); $pdf->Cell(5,8,$prip_misto_cp,0,0);

 $pdf->Cell(22); $pdf->Cell(5,8,$adr_prip_jako_kor,0,1); //pouz. adresu prip. jako kor.

 $pdf->Cell(52); $pdf->Cell(5,2,$prip_misto_mesto,0,0);
 $pdf->Cell(45); $pdf->Cell(5,2,$prip_misto_psc,0,1);

 $pdf->Cell(0,4,'',0,1);

 $pdf->Cell(53); $pdf->Cell(4,4,$prip_tech_1,0,0);
 $pdf->Cell(43); $pdf->Cell(6,4,$prip_tech_2,0,0);
 $pdf->Cell(40); $pdf->Cell(5,4,$prip_tech_3,0,1);

 $pdf->Cell(0,6,'',0,1);

 $pdf->Cell(53); $pdf->Cell(5,6,$int_sluzba_tarif_text,0,0);

 $pdf->Cell(87); $pdf->Cell(5,6,$int_sluzba_tarif_cena,0,0);
 $pdf->Cell(20); $pdf->Cell(5,6,$int_sluzba_tarif_cena_s_dph,0,1);


 $pdf->Cell(52); $pdf->Cell(5,5,$int_sluzba_rychlost,0,0);
 $pdf->Cell(33); $pdf->Cell(5,5,$int_sluzba_tarif_agr,0,0);

 $pdf->Cell(9); $pdf->Cell(5,4,$int_verejna_ip_x,0,0);
 $pdf->Cell(36); $pdf->Cell(5,5,$int_verejna_ip_cena,0,0);
 $pdf->Cell(20); $pdf->Cell(5,5,$int_verejna_ip_cena_s_dph,0,1);

 $pdf->Cell(0,6,'',0,1);

//IPTV
 $pdf->Cell(25); $pdf->Cell(5,3,$iptv_sluzba_tarif_1,0,0);
 $pdf->Cell(28); $pdf->Cell(5,3,$iptv_sluzba_tarif_2,0,0);
 $pdf->Cell(82); $pdf->Cell(5,4,$iptv_sluzba_cena,0,0);
 $pdf->Cell(20); $pdf->Cell(5,4,$iptv_sluzba_cena_s_dph,0,1);

 $pdf->Cell(0,3,'',0,1);

//tem. balicky
 $pdf->Cell(47); $pdf->Cell(7,4,$tb1_x,0,0);

 $pdf->Cell(25); $pdf->Cell(4,5,$tb1,0,0);
 $pdf->Cell(63); $pdf->Cell(5,5,$tb_cena_1,0,0);
 $pdf->Cell(20); $pdf->Cell(5,5,$tb_cena_s_dph_1,0,1);

 $pdf->Cell(47); $pdf->Cell(7,4,$tb2_x,0,0);

 $pdf->Cell(25); $pdf->Cell(4,5,$tb2,0,0);
 $pdf->Cell(63); $pdf->Cell(5,5,$tb_cena_2,0,0);
 $pdf->Cell(20); $pdf->Cell(5,5,$tb_cena_s_dph_2,0,1);

// $pdf->Cell(47); $pdf->Cell(5,4,$tb3_x,0,0);

// $pdf->Cell(25); $pdf->Cell(5,5,$tb3,0,0);
// $pdf->Cell(63); $pdf->Cell(5,5,$tb_cena_3,0,0);
// $pdf->Cell(20); $pdf->Cell(5,5,$tb_cena_s_dph_3,0,1);

 $pdf->Cell(0,4,'',0,1);

//voip
 $pdf->Cell(55); $pdf->Cell(5,5,$voip_cislo,0,0);

 $pdf->Cell(41); $pdf->Cell(6,5,$voip_postpaid,0,0);
 $pdf->Cell(32); $pdf->Cell(5,5,$voip_prepaid,0,1);

 $pdf->Cell(0,5,'',0,1);

//sleva
 $pdf->Cell(148); $pdf->Cell(13,5,$sleva_on,0,0);
 $pdf->Cell(12); $pdf->Cell(5,5,$sleva_hodnota,0,1);

 $pdf->Cell(0,8,'',0,1);

//placeni
 $pdf->Cell(5); $pdf->Cell(5,4,$zpusob_placeni_1,0,0); //bank prikaz
 $pdf->Cell(81); $pdf->Cell(5,4,$vs,0,1);

 //$pdf->Cell(4); $pdf->Cell(5,5,$zpusob_placeni_2,0,1);

 //$pdf->Cell(4); $pdf->Cell(5,4,$zpusob_placeni_3,0,0);
 $pdf->Cell(100); $pdf->Cell(5,5,$splatnost_ke_dni,0,1);

 $pdf->Cell(88); $pdf->Cell(5,5,$platba_mes,0,0);
 $pdf->Cell(21); $pdf->Cell(5,5,$platba_ctvrt,0,0);

 $pdf->Cell(25); $pdf->Cell(5,5,$celk_cena,0,0);
 $pdf->Cell(21); $pdf->Cell(5,5,$celk_cena_s_dph,0,1);

//MIN DOBA PLNENI

 $pdf->Cell(0,23,'',0,1);

 $pdf->Cell(77); $pdf->Cell(5,5,$min_plneni_on,0,0);
 $pdf->Cell(28); $pdf->Cell(5,5,$min_plneni_doba,0,1);

 $datum_nz = date('Y-m-d-H-i-s');

 if($id_cloveka > 0 )
 { $nazev_souboru = "print/temp/smlouva-fiber-pdf-id-".$id_cloveka."-".$datum_nz.".pdf"; }
 else
 { $nazev_souboru = "print/temp/smlouva-fiber-pdf-ec-".$ec."-".$datum_nz.".pdf"; }

 $rs = $pdf->Output($nazev_souboru,"F");

?>
