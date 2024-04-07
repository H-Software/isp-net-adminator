<?php

 // konverze promennych
 $ec = iconv("UTF-8","CP1250", $ec);
 
 $jmeno = iconv("UTF-8","CP1250", $jmeno );                                                                                                                    
                                                                                                                     
 if( (strlen($nazev_spol) > 1 ) )
 { $nazev_spol = iconv("UTF-8","CP1250", $nazev_spol); }
 else
 { $nazev_spol = "- - - - -"; }

 if( (strlen($adresa) > 1 ) )
 { $adresa = iconv("UTF-8","CP1250", $adresa ); }
 else
 { $adresa = "- - - - -"; }

 if( (strlen($f_adresa) > 1 ) )
 { $f_adresa = iconv("UTF-8","CP1250", $f_adresa ); }
 else
 { $f_adresa = "- - - - -"; }

 if( (strlen($mesto) > 1 ) )
 { $mesto = iconv("UTF-8","CP1250", $mesto ); }
 else
 { $mesto = "- - - - -"; }

 if( (strlen($f_mesto) > 1 ) )
 { $f_mesto = iconv("UTF-8","CP1250", $f_mesto ); }
 else
 { $f_mesto = "- - - - -"; }

 if( (strlen($ico) > 1 ) )
 { $ico = iconv("UTF-8","CP1250", $ico ); }
 else
 { $ico = "- - - - -"; }

 if( (strlen($dic) > 1 ) )
 { $dic = iconv("UTF-8","CP1250", $dic ); }
 else
 { $dic = "- - - - -"; }

 if( (strlen($email) > 1 ) )
 { $email = iconv("UTF-8","CP1250", $email ); }
 else
 { $email = "- - - - -"; }

 if( (strlen($telefon) > 1 ) )
 { $telefon = iconv("UTF-8","CP1250", $telefon ); }
 else
 { $telefon = "- - - - -"; }

 if( (strlen($kor_adresa) > 1) )
 { $kor_adresa = iconv("UTF-8","CP1250", $kor_adresa); }
 else
 { $kor_adresa = "- - - - -"; }
 
 if( (strlen($kor_mesto) > 1) )
 { $kor_mesto = iconv("UTF-8","CP1250", $kor_mesto); }
 else
 { $kor_mesto = "- - - - -"; }

 //
 // internet
 //
 
 //1. tarif
 if( (strlen($int_1_nazev) >= 1 ) )
 { $int_1_nazev = iconv("UTF-8","CP1250", $int_1_nazev); }
 else
 { $int_1_nazev = "- - - - - - - - - - - -"; }
 
 if( (strlen($int_1_rychlost) >= 1 ) )
 { $int_1_rychlost = iconv("UTF-8","CP1250", $int_1_rychlost); }
 else
 { $int_1_rychlost = "- -"; }
 
 if( (strlen($int_1_cena_1) >= 1 ) )
 { $int_1_cena_1 = iconv("UTF-8","CP1250", $int_1_cena_1); }
 else
 { $int_1_cena_1 = "- -"; }
 
 if( (strlen($int_1_vip) >= 1 ) )
 { $int_1_vip = iconv("UTF-8","CP1250", $int_1_vip); }
 else
 { $int_1_vip = "- -"; }
 
 if( (strlen($int_1_cena_2) >= 1 ) )
 { $int_1_cena_2 = iconv("UTF-8","CP1250", $int_1_cena_2); }
 else
 { $int_1_cena_2 = "- -"; }
 
 if( (strlen($int_1_adresa) >= 1 ) )
 { $int_1_adresa = iconv("UTF-8","CP1250", $int_1_adresa); }
 else
 { $int_1_adresa = "- - - - - -"; }
 
 //2. tarif
 if( (strlen($int_2_nazev) >= 1 ) )
 { $int_2_nazev = iconv("UTF-8","CP1250", $int_2_nazev); }
 else
 { $int_2_nazev = "- - - - - - - - - - - -"; }
 
 if( (strlen($int_2_rychlost) >= 1 ) )
 { $int_2_rychlost = iconv("UTF-8","CP1250", $int_2_rychlost); }
 else
 { $int_2_rychlost = "- -"; }
 
 if( (strlen($int_2_cena_1) >= 1 ) )
 { $int_2_cena_1 = iconv("UTF-8","CP1250", $int_2_cena_1); }
 else
 { $int_2_cena_1 = "- -"; }
 
 if( (strlen($int_2_vip) >= 1 ) )
 { $int_2_vip = iconv("UTF-8","CP1250", $int_2_vip); }
 else
 { $int_2_vip = "- -"; }
 
 if( (strlen($int_2_cena_2) >= 1 ) )
 { $int_2_cena_2 = iconv("UTF-8","CP1250", $int_2_cena_2); }
 else
 { $int_2_cena_2 = "- -"; }
 
 if( (strlen($int_2_adresa) >= 1 ) )
 { $int_2_adresa = iconv("UTF-8","CP1250", $int_2_adresa); }
 else
 { $int_2_adresa = "- - - - - -"; }
 
 
 //
 // iptv 
 //
 
 if( (strlen($iptv_tarif_nazev) >= 1 ) )
 { $iptv_tarif_nazev = iconv("UTF-8","CP1250", $iptv_tarif_nazev); }
 else
 { $iptv_tarif_nazev = "- - - - - -"; }
 
 if( (strlen($iptv_tarif_kanaly) >= 1 ) )
 { $iptv_tarif_kanaly = iconv("UTF-8","CP1250", $iptv_tarif_kanaly); }
 else
 { $iptv_tarif_kanaly = "- - -"; }
 
 if( (strlen($iptv_tarif_cena) >= 1 ) )
 { $iptv_tarif_cena = iconv("UTF-8","CP1250", $iptv_tarif_cena); }
 else
 { $iptv_tarif_cena = "- - -"; }
 
 //dalsi radek
 if( (strlen($iptv_tema_nazev) >= 1 ) )
 { $iptv_tema_nazev = iconv("UTF-8","CP1250", $iptv_tema_nazev); }
 else
 { $iptv_tema_nazev = "- - - - - -"; }
 
 if( (strlen($iptv_tema_kanaly) >= 1 ) )
 { $iptv_tema_kanaly = iconv("UTF-8","CP1250", $iptv_tema_kanaly); }
 else
 { $iptv_tema_kanaly = "- - -"; }
 
 if( (strlen($iptv_tema_cena) >= 1 ) )
 { $iptv_tema_cena = iconv("UTF-8","CP1250", $iptv_tema_cena); }
 else
 { $iptv_tema_cena = "- - -"; }
  
 if( (strlen($stb) >= 1) )
 { $stb = iconv("UTF-8","CP1250", $stb); }
 else
 { $stb = "- - - - - - - - - - - - - - -"; }
 
 if( (strlen($stb_sn) >= 1) )
 { $stb_sn = iconv("UTF-8","CP1250", $stb_sn); }
 else
 { $stb_sn = "- - - - - - - - - - - - - - -"; }
 
 if( (strlen($stb_kauce) >= 1) )
 { $stb_kauce = iconv("UTF-8","CP1250", $stb_kauce); }
 else
 { $stb_kauce = "- - - -"; }
 
 //             
 //VOIP 
 //
 
 if( (strlen($voip_1_cislo) >= 1 ) )
 { $voip_1_cislo = iconv("UTF-8","CP1250", $voip_1_cislo); }
 else
 { $voip_1_cislo = "- - - - - - - - - - - -"; }
 
 if( (strlen($voip_1_pre) >= 1 ) )
 { $voip_1_pre = iconv("UTF-8","CP1250", $voip_1_pre); }
 else
 { $voip_1_pre = "-"; }

 if( (strlen($voip_1_post) >= 1 ) )
 { $voip_1_post = iconv("UTF-8","CP1250", $voip_1_post); }
 else
 { $voip_1_post = "-"; }

 if( (strlen($voip_2_cislo) >= 1 ) )
 { $voip_2_cislo = iconv("UTF-8","CP1250", $voip_2_cislo); }
 else
 { $voip_2_cislo = "- - - - - - - - - - - -"; }
 
 if( (strlen($voip_2_pre) >= 1 ) )
 { $voip_2_pre = iconv("UTF-8","CP1250", $voip_2_pre); }
 else
 { $voip_2_pre = "-"; }

 if( (strlen($voip_2_post) >= 1 ) )
 { $voip_2_post = iconv("UTF-8","CP1250", $voip_2_post); }
 else
 { $voip_2_post = "-"; }

 //ostatni
 
 if( (strlen($ostatni_nazev) >= 1 ) )
 { $ostatni_nazev = iconv("UTF-8","CP1250", $ostatni_nazev); }
 else
 { $ostatni_nazev = "- - - - - -"; }

 if( (strlen($ostatni_cena) >= 1 ) )
 { $ostatni_cena = iconv("UTF-8","CP1250", $ostatni_cena); }
 else
 { $ostatni_cena = "- - -"; }

 //bonus
 
 if($bonus_select_1 == 1)
 {
    $bonus_select_jmeno = "má";
 }
 else
 {
    $bonus_select_jmeno = "nemá";
 }
 
 $bonus_select_jmeno = iconv("UTF-8","CP1250", $bonus_select_jmeno);
 
 
 if( (strlen($bonus_1_tarif) >= 1 ) )
 { $bonus_1_tarif = iconv("UTF-8","CP1250", $bonus_1_tarif); }
 else
 { $bonus_1_tarif = "- - - - - - - -"; }
 
 if( (strlen($bonus_1_cena1) >= 1 ) )
 { $bonus_1_cena1 = iconv("UTF-8","CP1250", $bonus_1_cena1); }
 else
 { $bonus_1_cena1 = "- - - -"; }
 
 if( (strlen($bonus_1_cena2) >= 1 ) )
 { $bonus_1_cena2 = iconv("UTF-8","CP1250", $bonus_1_cena2); }
 else
 { $bonus_1_cena2 = "- - - -"; }
 
 
 if( (strlen($bonus_2_tarif) >= 1 ) )
 { $bonus_2_tarif = iconv("UTF-8","CP1250", $bonus_2_tarif); }
 else
 { $bonus_2_tarif = "- - - - - - - -"; }
 
 if( (strlen($bonus_2_cena1) >= 1 ) )
 { $bonus_2_cena1 = iconv("UTF-8","CP1250", $bonus_2_cena1); }
 else
 { $bonus_2_cena1 = "- - - -"; }
 
 if( (strlen($bonus_2_cena2) >= 1 ) )
 { $bonus_2_cena2 = iconv("UTF-8","CP1250", $bonus_2_cena2); }
 else
 { $bonus_2_cena2 = "- - - -"; }
 
 //platebni predpis
 
 if( (strlen($platba_1_od) >= 1 ) )
 { $platba_1_od = iconv("UTF-8","CP1250", $platba_1_od); }
 else
 { $platba_1_od = "- - - -"; }
 
 if( (strlen($platba_1_do) >= 1 ) )
 { $platba_1_do = iconv("UTF-8","CP1250", $platba_1_do); }
 else
 { $platba_1_do = "- - - -"; }
 
 if( (strlen($platba_1_cena) >= 1 ) )
 { $platba_1_cena = iconv("UTF-8","CP1250", $platba_1_cena); }
 else
 { $platba_1_cena = "- - - -"; }

 if( (strlen($platba_1_pozn) >= 1 ) )
 { $platba_1_pozn = iconv("UTF-8","CP1250", $platba_1_pozn); }
 else
 { $platba_1_pozn = "- - - -"; }
 
 if( (strlen($platba_2_od) >= 1 ) )
 { $platba_2_od = iconv("UTF-8","CP1250", $platba_2_od); }
 else
 { $platba_2_od = "- - - -"; }
 
 if( (strlen($platba_2_do) >= 1 ) )
 { $platba_2_do = iconv("UTF-8","CP1250", $platba_2_do); }
 else
 { $platba_2_do = "- - - -"; }
 
 if( (strlen($platba_2_cena) >= 1 ) )
 { $platba_2_cena = iconv("UTF-8","CP1250", $platba_2_cena); }
 else
 { $platba_2_cena = "- - - -"; }

 if( (strlen($platba_2_pozn) >= 1 ) )
 { $platba_2_pozn = iconv("UTF-8","CP1250", $platba_2_pozn); }
 else
 { $platba_2_pozn = "- - - -"; }
 
 
 if( (strlen($platba_3_od) >= 1 ) )
 { $platba_3_od = iconv("UTF-8","CP1250", $platba_3_od); }
 else
 { $platba_3_od = "- - - -"; }
 
 if( (strlen($platba_3_do) >= 1 ) )
 { $platba_3_do = iconv("UTF-8","CP1250", $platba_3_do); }
 else
 { $platba_3_do = "- - - -"; }
 
 if( (strlen($platba_3_cena) >= 1 ) )
 { $platba_3_cena = iconv("UTF-8","CP1250", $platba_3_cena); }
 else
 { $platba_3_cena = "- - - -"; }

 if( (strlen($platba_3_pozn) >= 1 ) )
 { $platba_3_pozn = iconv("UTF-8","CP1250", $platba_3_pozn); }
 else
 { $platba_3_pozn = "- - - -"; }
 
 if( $zpusob_placeni == 1)
 { $zpusob_placeni = iconv("UTF-8","CP1250", "BANKOVNÍ PŘÍKAZ"); }
 else
 { $zpusob_placeni = "- - - - - - - -"; }
 
 if( $platba == 1 )
 { $platba = iconv("UTF-8","CP1250", "MĚSÍČNÍ"); }
 elseif( $platba == 2 )
 { $platba = iconv("UTF-8","CP1250", "ČTVRTLETNÍ"); }
 else
 { $platba = "- - - - - - - -"; }
 
 if( (strlen($celk_cena) >= 1 ) )
 { $celk_cena = iconv("UTF-8","CP1250", $celk_cena); }
 else
 { $celk_cena = "- - -"; }
 
 if( (strlen($celk_cena_s_dph) >= 1 ) )
 { $celk_cena_s_dph = iconv("UTF-8","CP1250", $celk_cena_s_dph); }
 else
 { $celk_cena_s_dph = "- - -"; }
 
 
 
 if( (strlen($celk_cena) > 1) )
 {
  $celk_cena = $celk_cena.",-";
 }

 if( (strlen($celk_cena_s_dph) > 1) )
 {
  $celk_cena_s_dph = $celk_cena_s_dph.",-";
 }	

// konec pripravy promennych

?>
