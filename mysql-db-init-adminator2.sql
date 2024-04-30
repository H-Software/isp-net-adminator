-- Adminer 4.8.1 MySQL 8.0.36 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `adminator2`;
CREATE DATABASE `adminator2` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `adminator2`;

DROP TABLE IF EXISTS `archiv_zmen`;
CREATE TABLE `archiv_zmen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `akce` varchar(12000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `provedeno_kdy` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `provedeno_kym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `vysledek` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `archiv_zmen` (`id`, `akce`, `provedeno_kdy`, `provedeno_kym`, `vysledek`) VALUES
(1,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 5, [mac_adresa]=> 43:22:33:44:55:66, [ip_adresa]=> 10.10.10.4, [puk]=> , [popis]=> stb4, [id_nodu]=> 2, [sw_port]=> 4 [pozn]=> pozn 4, [id_tarifu]=> 1',	'2024-04-12 09:34:49',	'admin',	1),
(2,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 6, [mac_adresa]=> 55:22:33:44:55:66, [ip_adresa]=> 10.10.10.5, [puk]=> 5555, [popis]=> stb5, [id_nodu]=> 2, [sw_port]=> 5 [pozn]=> pozn 5, [id_tarifu]=> 1',	'2024-04-12 09:42:30',	'admin',	1),
(3,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 7, [mac_adresa]=> 66:22:33:44:55:66, [ip_adresa]=> 10.10.10.6, [puk]=> 666, [popis]=> stb6, [id_nodu]=> 2, [sw_port]=> 6 [pozn]=> pozn 6, [id_tarifu]=> 1',	'2024-04-12 09:54:46',	'admin',	1),
(4,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 8, [mac_adresa]=> 77:22:33:44:55:66, [ip_adresa]=> 10.10.10.7, [puk]=> 777, [popis]=> stb 7, [id_nodu]=> 2, [sw_port]=> 7 [pozn]=> pozn 7, [id_tarifu]=> 1',	'2024-04-12 10:00:24',	'admin',	1),
(5,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 9, [mac_adresa]=> 88:22:33:44:55:66, [ip_adresa]=> 10.10.10.8, [puk]=> 888, [popis]=> stb 8, [id_nodu]=> 2, [sw_port]=> 8 [pozn]=> pozn 8, [id_tarifu]=> 1',	'2024-04-12 10:03:37',	'admin',	1),
(6,	'<b>akce: uprava stb objektu; </b><br>[id_stb]=> 2, diferencialni data: změna pole: <b>puk</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">111</span>, změna pole: <b>pozn</b> z: <span class=\"az-s1\" >xxx222</span> na: <span class=\"az-s2\">xxx111</span>, ',	'2024-04-12 12:29:43',	'admin',	1),
(10,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 14, [mac_adresa]=> 11:22:33:44:55:66, [ip_adresa]=> 10.10.10.4, [puk]=> , [popis]=> xxxx, [id_nodu]=> 0, [sw_port]=> 1 [pozn]=> test, [id_tarifu]=> 0',	'2024-04-14 21:14:10',	'admin@admin',	1),
(11,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 15, [mac_adresa]=> xx, [ip_adresa]=> xx, [puk]=> , [popis]=> xxx, [id_nodu]=> 0, [sw_port]=> 1 [pozn]=> test, [id_tarifu]=> 0',	'2024-04-14 21:17:57',	'admin@admin',	1),
(12,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 16, [mac_adresa]=> 11:22:33:44:55:66, [ip_adresa]=> 10.10.10.42, [puk]=> 1, [popis]=> xxx, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 1',	'2024-04-14 21:59:44',	'admin@admin',	1),
(13,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 17, [mac_adresa]=> 11:22:33:45:55:66, [ip_adresa]=> 10.10.10.1, [puk]=> 1, [popis]=> xx-5, [id_nodu]=> 2, [sw_port]=> 1 [pozn]=> test 1, [id_tarifu]=> 1',	'2024-04-14 22:01:56',	'admin@admin',	1),
(14,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 18, [mac_adresa]=> 11:22:33:44:55:11, [ip_adresa]=> 10.10.10.11, [puk]=> 1, [popis]=> stb-11, [id_nodu]=> 2, [sw_port]=> 2 [pozn]=> test 2, [id_tarifu]=> 4',	'2024-04-14 22:08:15',	'admin@admin',	1),
(15,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 19, [mac_adresa]=> 11:22:33:44:55:33, [ip_adresa]=> 10.10.10.12, [puk]=> 1, [popis]=> stb-12, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 4',	'2024-04-14 22:10:17',	'admin@admin',	1),
(16,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 20, [mac_adresa]=> 22:22:33:44:55:15, [ip_adresa]=> 10.10.10.15, [puk]=> 15, [popis]=> stb-1t, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 4',	'2024-04-14 22:32:35',	'admin@admin',	1),
(17,	'<b>akce: pridani zmeny pro ucetni; </b><br>[typ_id]=> 1, [text]=> NULL',	'2024-04-15 17:16:05',	'admin@admin',	1),
(18,	'<b>akce: pridani zmeny pro ucetni; </b><br>[typ_id]=> 1, [text]=> ucetni zmena 1',	'2024-04-15 17:16:26',	'admin@admin',	1),
(19,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 1 diferencialni data: změna pole: <b>fakturacni_text</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">text 1</span>, ',	'2024-04-18 17:55:19',	'admin',	1),
(20,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 1 diferencialni data: změna pole: <b>fakturacni_text</b> z: <span class=\"az-s1\" >text 1</span> na: <span class=\"az-s2\">text fakturace 1</span>, ',	'2024-04-18 17:55:34',	'admin',	1),
(21,	'<b> akce: pridani fakt. skupiny; </b><br>[nazev]=> fakturacni skupina 2, [typ]=> 1, [sluzba_int]=> 1, [sluzba_int_id_tarifu]=> 1, [sluzba_iptv]=> 1, [sluzba_iptv_id_tarifu]=> 1, [sluzba_voip]=> 0 [fakturacni_text]=> text 2, [typ_sluzby]=> 1',	'2024-04-18 17:57:04',	'admin',	1),
(22,	'<b> akce: pridani fakt. skupiny; </b><br>[nazev]=> fakt skupina - wifi - FU - sc, [typ]=> 2, [sluzba_int]=> 1, [sluzba_int_id_tarifu]=> 1, [sluzba_iptv]=> 0, [sluzba_iptv_id_tarifu]=> 0, [sluzba_voip]=> 0 [fakturacni_text]=> small city pro fakturacni skupiny, [typ_sluzby]=> 0',	'2024-04-19 09:44:30',	'admin',	1),
(23,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 21, [mac_adresa]=> 14:22:33:44:55:66, [ip_adresa]=> 10.10.10.41, [puk]=> , [popis]=> pokus-2, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> test poznamka, [id_tarifu]=> 4',	'2024-04-20 12:13:01',	'admin@admin',	1),
(24,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 3 diferencialni data: změna pole: <b>id</b> z: <span class=\"az-s1\" >3</span> na: <span class=\"az-s2\"></span>, změna pole: <b>vlozil_kdo</b> z: <span class=\"az-s1\" >admin</span> na: <span class=\"az-s2\"></span>, změna pole: <b>sluzba_int</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">1</span>, změna pole: <b>sluzba_int_id_tarifu</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">1</span>, ',	'2024-04-20 15:42:24',	'admin@admin',	1),
(25,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 1 diferencialni data: změna pole: <b>sluzba_int</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">1</span>, změna pole: <b>sluzba_int_id_tarifu</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">2</span>, ',	'2024-04-20 16:17:52',	'admin@admin',	1),
(26,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 1 diferencialni data: změna pole: <b>sluzba_int</b> z: <span class=\"az-s1\" >1</span> na: <span class=\"az-s2\">0</span>, změna pole: <b>sluzba_int_id_tarifu</b> z: <span class=\"az-s1\" >2</span> na: <span class=\"az-s2\">0</span>, ',	'2024-04-20 16:21:06',	'admin@admin',	1),
(27,	'<b> akce: pridani fakt. skupiny; </b><br>[nazev]=> fakturacni skupina 11, [fakturacni_text]=> text fakturacni skupiny 11, [typ]=> 1, [typ_sluzby]=> 1, [sluzba_int]=> 1, [sluzba_int_id_tarifu]=> 0, [sluzba_iptv]=> 0, [sluzba_iptv_id_tarifu]=> 0, [sluzba_voip]=> 0, [sluzba_voip_id_tarifu]=> 0, [vlozil_kdo]=> admin@admin, ',	'2024-04-20 16:22:50',	'admin@admin',	1),
(28,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 2 diferencialni data: ',	'2024-04-20 16:28:58',	'admin@admin',	0),
(29,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 2 diferencialni data: změna pole: <b>fakturacni_text</b> z: <span class=\"az-s1\" >text 2</span> na: <span class=\"az-s2\">text 22</span>, ',	'2024-04-20 16:29:11',	'admin@admin',	1),
(30,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 4 diferencialni data: ',	'2024-04-20 16:30:11',	'admin@admin',	0),
(31,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 4 diferencialni data: změna pole: <b>fakturacni_text</b> z: <span class=\"az-s1\" >text fakturacni skupiny 11</span> na: <span class=\"az-s2\">text fakturacni skupiny 111</span>, ',	'2024-04-20 16:30:23',	'admin@admin',	1),
(32,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 1 diferencialni data: změna pole: <b>sluzba_int</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">1</span>, změna pole: <b>sluzba_int_id_tarifu</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">2</span>, ',	'2024-04-20 17:02:39',	'admin@admin',	1),
(33,	'<b> akce: pridani fakt. skupiny; </b><br>[nazev]=> test12, [fakturacni_text]=> test12, [typ]=> 1, [typ_sluzby]=> 0, [sluzba_int]=> 0, [sluzba_int_id_tarifu]=> 0, [sluzba_iptv]=> 0, [sluzba_iptv_id_tarifu]=> 0, [sluzba_voip]=> 0, [sluzba_voip_id_tarifu]=> 0, [vlozil_kdo]=> admin@admin, ',	'2024-04-20 18:30:21',	'admin@admin',	1),
(34,	'<b> akce: pridani fakt. skupiny; </b><br>[nazev]=> test13, [fakturacni_text]=> test13, [typ]=> 1, [typ_sluzby]=> 0, [sluzba_int]=> 0, [sluzba_int_id_tarifu]=> 0, [sluzba_iptv]=> 0, [sluzba_iptv_id_tarifu]=> 0, [sluzba_voip]=> 0, [sluzba_voip_id_tarifu]=> 0, [vlozil_kdo]=> admin@admin, ',	'2024-04-20 18:32:04',	'admin@admin',	1),
(35,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 15 diferencialni data: změna pole: <b>nazev</b> z: <span class=\"az-s1\" >test13-4</span> na: <span class=\"az-s2\">test13-5</span>, změna pole: <b>fakturacni_text</b> z: <span class=\"az-s1\" >test13-4</span> na: <span class=\"az-s2\">test13-5</span>, změna pole: <b>sluzba_int</b> z: <span class=\"az-s1\" >1</span> na: <span class=\"az-s2\">0</span>, změna pole: <b>sluzba_int_id_tarifu</b> z: <span class=\"az-s1\" >2</span> na: <span class=\"az-s2\">0</span>, ',	'2024-04-20 23:48:53',	'admin@admin',	1),
(36,	'<b>akce: uprava fakturacni skupiny; </b><br>[id_fs] => 15 diferencialni data: změna pole: <b>sluzba_int</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">1</span>, změna pole: <b>sluzba_int_id_tarifu</b> z: <span class=\"az-s1\" >0</span> na: <span class=\"az-s2\">1</span>, ',	'2024-04-21 00:02:05',	'admin@admin',	1),
(37,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 22, [mac_adresa]=> 14:14:33:44:55:66, [ip_adresa]=> 10.10.10.122, [puk]=> , [popis]=> xxx-14, [id_nodu]=> 2, [sw_port]=> 1 [pozn]=> test, [id_tarifu]=> 1',	'2024-04-21 11:56:06',	'admin@admin',	1),
(38,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 23, [mac_adresa]=> 11:22:33:11:11:11, [ip_adresa]=> 10.10.15.15, [puk]=> , [popis]=> xxx-15, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 4',	'2024-04-21 12:02:06',	'admin@admin',	1),
(39,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 24, [mac_adresa]=> 11:23:33:44:33:66, [ip_adresa]=> 10.10.14.15, [puk]=> , [popis]=> stb-16, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 1',	'2024-04-21 12:02:56',	'admin@admin',	1),
(40,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 25, [mac_adresa]=> 16:23:33:44:33:66, [ip_adresa]=> 10.10.14.16, [puk]=> , [popis]=> xxx-16, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 1',	'2024-04-21 12:04:01',	'admin@admin',	1),
(41,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 26, [mac_adresa]=> 11:22:33:44:17:17, [ip_adresa]=> 10.10.10.17, [puk]=> , [popis]=> xxx-17, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 1',	'2024-04-21 12:08:00',	'admin@admin',	1),
(42,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 27, [mac_adresa]=> 11:22:33:44:18:18, [ip_adresa]=> 10.10.10.18, [puk]=> , [popis]=> stb-18, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 4',	'2024-04-21 12:09:57',	'admin@admin',	1),
(43,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 28, [mac_adresa]=> 11:22:33:44:19:19, [ip_adresa]=> 10.10.10.19, [puk]=> , [popis]=> stb-19, [id_nodu]=> 3, [sw_port]=> 1 [pozn]=> , [id_tarifu]=> 1',	'2024-04-21 12:12:26',	'admin@admin',	1),
(53,	'[id_stb]=> 4, diferencialni data: změna pole: <b>popis</b> z: <span class=\"az-s1\" >stb3</span> na: <span class=\"az-s2\">stb3a</span>, změna <b>Přípojného bodu</b> z: <span class=\"az-s1\">opticky nod 1</span> na: <span class=\"az-s2\">opticky nod 2</span>, změna <b>Čísla portu (ve switchi)</b> z: <span class=\"az-s1\">3</span> na: <span class=\"az-s2\">1</span>, změna <b>Poznámky</b> z: <span class=\"az-s1\">pozn 3</span> na: <span class=\"az-s2\">pozn 3a</span>, změna pole: <b>upravil_kdo</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-21 15:09:39',	'admin@admin',	1),
(54,	'[id_stb]=> 2, diferencialni data: změna pole: <b>popis</b> z: <span class=\"az-s1\" >stb-1acba</span> na: <span class=\"az-s2\">stb-1acbaa</span>, změna <b>Tarifu</b> z: <span class=\"az-s1\">tarif iptv 2</span> na: <span class=\"az-s2\">tarif iptv 1</span>, změna pole: <b>upravil_kdo</b> z: <span class=\"az-s1\" >pavel</span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-21 15:10:10',	'admin@admin',	1),
(55,	'<b>akce: odrazeni objektu; </b><br> [id_komplu]=> 1 , [id_vlastnika] => 1',	'2024-04-21 16:12:44',	'admin',	1),
(59,	'<b>akce: prirazeni objektu k vlastnikovi; </b><br> [id_komplu]=> 1, [id_vlastnika] => 1',	'2024-04-21 22:27:06',	'admin',	1),
(65,	'<b>akce: uprava vlastnika; </b><br> diferencialni data:  [id_cloveka] => 1 , změna pole: <b>nick</b> z: <span class=\"az-s1\" >petrn                                                                                                                                                 </span> na: <span class=\"az-s2\">petrn</span>, změna pole: <b>jmeno</b> z: <span class=\"az-s1\" >Petr                                                                                                </span> na: <span class=\"az-s2\">Petr</span>, změna pole: <b>prijmeni</b> z: <span class=\"az-s1\" >Novak                                                                                               </span> na: <span class=\"az-s2\">Novak</span>, změna pole: <b>ulice</b> z: <span class=\"az-s1\" >Nova Ulice 1                                                                                        </span> na: <span class=\"az-s2\">Nova Ulice 1</span>, změna pole: <b>mesto</b> z: <span class=\"az-s1\" >Praha                                                                                               </span> na: <span class=\"az-s2\">Praha</span>, změna pole: <b>icq</b> z: <span class=\"az-s1\" >                                                  </span> na: <span class=\"az-s2\"></span>, změna pole: <b>mail</b> z: <span class=\"az-s1\" >hu@hu.hu                                          </span> na: <span class=\"az-s2\">hu@hu.hu</span>, ',	'2024-04-22 08:18:31',	'admin',	1),
(66,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp\n [vs] => 222\n [k_platbe] => 250\n [jmeno] => Petr\n [prijmeni] => Pavel\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888888\n [poznamka] => prezident\n',	'2024-04-22 13:15:42',	'admin',	0),
(67,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp2\n [vs] => 333\n [k_platbe] => 2500\n [jmeno] => Petr\n [prijmeni] => Pavel2\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888882\n [poznamka] => prezident 2\n',	'2024-04-22 13:19:27',	'admin',	0),
(68,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp2\n [vs] => 333\n [k_platbe] => 2500\n [jmeno] => Petr\n [prijmeni] => Pavel 3\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888882\n [poznamka] => prezident 2\n',	'2024-04-22 13:20:25',	'admin',	0),
(69,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp2\n [vs] => 333\n [k_platbe] => 2500\n [jmeno] => Petr\n [prijmeni] => Pavel 4\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888882\n [poznamka] => prezident 2\n',	'2024-04-22 13:21:16',	'admin',	0),
(70,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp2\n [vs] => 333\n [k_platbe] => 2500\n [jmeno] => Petr\n [prijmeni] => Pavel 5\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888882\n [poznamka] => prezident 2\n',	'2024-04-22 13:25:05',	'admin',	1),
(71,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp2\n [vs] => 333\n [k_platbe] => 2500\n [jmeno] => Petr\n [prijmeni] => Pavel 5\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888882\n [poznamka] => prezident 2\n',	'2024-04-22 13:30:36',	'admin',	0),
(72,	'<b>akce: pridani vlastnika ; </b><br> [nick] => petrp3\n [vs] => 333\n [k_platbe] => 2500\n [jmeno] => Petr\n [prijmeni] => Pavel 5\n [ulice] => Hrad 1\n [mesto] => Praha\n [psc] => 11000\n [ucetni_index] => 222\n [fakturacni_skupina_id] => 1\n [splatnost] => 15\n [typ_smlouvy] => 0\n [sluzba_int] => 0\n [sluzba_iptv] => 0\n [sluzba_voip] => 0\n [billing_freq] => 0\n [firma] => 1\n [mail] => pavel@hrad.gov.cz\n [telefon] => 800888882\n [poznamka] => prezident 2\n',	'2024-04-22 13:31:10',	'admin',	1),
(73,	'<b>akce: uprava vlastnika; </b><br> diferencialni data:  [id_cloveka] => 8 , změna pole: <b>nick</b> z: <span class=\"az-s1\" >petrp3                                                                                                                                                </span> na: <span class=\"az-s2\">petrp3</span>, změna pole: <b>jmeno</b> z: <span class=\"az-s1\" >Petr                                                                                                </span> na: <span class=\"az-s2\">Petr</span>, změna pole: <b>prijmeni</b> z: <span class=\"az-s1\" >Pavel 5                                                                                             </span> na: <span class=\"az-s2\">Pavel Archivni</span>, změna pole: <b>ulice</b> z: <span class=\"az-s1\" >Hrad 1                                                                                              </span> na: <span class=\"az-s2\">Hrad 1</span>, změna pole: <b>mesto</b> z: <span class=\"az-s1\" >Praha                                                                                               </span> na: <span class=\"az-s2\">Praha</span>, změna pole: <b>mail</b> z: <span class=\"az-s1\" >pavel@hrad.gov.cz                                 </span> na: <span class=\"az-s2\">pavel@hrad.gov.cz</span>, změna <b>Archivu</b> z: <span class=\"az-s1\"></span> na: <span class=\"az-s2\">1</span>, ',	'2024-04-22 13:32:35',	'admin',	1),
(74,	'<b> akce: pridani fakt. skupiny; </b><br>[nazev]=> fakt. skupina wifi FU, [fakturacni_text]=> fakt. skupina FU test, [typ]=> 2, [typ_sluzby]=> 0, [sluzba_int]=> 1, [sluzba_int_id_tarifu]=> 1, [sluzba_iptv]=> 0, [sluzba_iptv_id_tarifu]=> 0, [sluzba_voip]=> 0, [sluzba_voip_id_tarifu]=> 0, [vlozil_kdo]=> admin@admin, ',	'2024-04-22 14:25:44',	'admin@admin',	1),
(75,	'<b>akce: uprava vlastnika; </b><br> diferencialni data:  [id_cloveka] => 2 , změna pole: <b>nick</b> z: <span class=\"az-s1\" >petrp                                                                                                                                                 </span> na: <span class=\"az-s2\">petrp</span>, změna pole: <b>jmeno</b> z: <span class=\"az-s1\" >Petr                                                                                                </span> na: <span class=\"az-s2\">Petr</span>, změna pole: <b>prijmeni</b> z: <span class=\"az-s1\" >Pavel Fakturacni                                                                                    </span> na: <span class=\"az-s2\">Pavel Fakturacni</span>, změna pole: <b>ulice</b> z: <span class=\"az-s1\" >Hrad 1                                                                                              </span> na: <span class=\"az-s2\">Hrad 1</span>, změna pole: <b>mesto</b> z: <span class=\"az-s1\" >Praha                                                                                               </span> na: <span class=\"az-s2\">Praha</span>, změna pole: <b>icq</b> z: <span class=\"az-s1\" >                                                  </span> na: <span class=\"az-s2\"></span>, změna pole: <b>mail</b> z: <span class=\"az-s1\" >pavel@hrad.gov.cz                                 </span> na: <span class=\"az-s2\">pavel@hrad.gov.cz</span>, ',	'2024-04-22 14:34:18',	'admin',	1),
(76,	'<b>akce</b>: uprava fakturacni adresy;<br>[id] => 1, nové data: <b>[ftitle]</b> => x\n <b>[fulice]</b> => x\n <b>[fmesto]</b> => x\n <b>[fpsc]</b> => x\n <b>[ico]</b> => \n <b>[dic]</b> => \n <b>[ucet]</b> => x\n <b>[splatnost]</b> => 15\n <b>[cetnost]</b> => 1\n',	'2024-04-22 15:04:37',	'admin',	1),
(77,	'<b>akce</b>: uprava fakturacni adresy;<br>[id] => 1, nové data: <b>[ftitle]</b> => Hradni Kancelar\n <b>[fulice]</b> => Hrad 1\n <b>[fmesto]</b> => Praha\n <b>[fpsc]</b> => 11111\n <b>[ico]</b> => 1\n <b>[dic]</b> => CZ1\n <b>[ucet]</b> => \n <b>[splatnost]</b> => 15\n <b>[cetnost]</b> => 1\n',	'2024-04-22 15:05:40',	'admin',	1),
(78,	'<b>akce: prirazeni objektu typu STB k vlastnikovi; </b><br> [id_stb]=> 3, [id_vlastnika]=> 2',	'2024-04-22 15:15:20',	'admin',	1),
(79,	' akce: smazani fakturacni adresy ;  [id] => 1\n, akci provedl: admin, vysledek akce dle postgre: 1, datum akce: 22/04/2024 15:24:57',	'2024-04-22 15:24:57',	NULL,	0),
(80,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.12\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 15:18:54',	'admin@admin',	0),
(81,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> =>  test-fiber-1\n <b>[ip]</b> => 10.10.10.12\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 15:24:02',	'admin@admin',	0),
(82,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-2\n <b>[ip]</b> => 10.10.10.122\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[poznamka]</b> => test 1\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:01:24',	'admin@admin',	0),
(83,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.12\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:15:15',	'admin@admin',	0),
(84,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.122\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:23:06',	'admin@admin',	0),
(85,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.4\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:28:22',	'admin@admin',	0),
(86,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.4\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:29:44',	'admin@admin',	0),
(87,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.12\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:31:29',	'admin@admin',	0),
(88,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.4\n <b>[id_tarifu]</b> => 3\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 3\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:34:41',	'admin@admin',	0),
(89,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.4\n <b>[id_tarifu]</b> => 3\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:35:31',	'admin@admin',	0),
(90,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.5\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:41:13',	'admin@admin',	0),
(91,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.122\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => n\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 22:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 16:45:50',	'admin@admin',	0),
(92,	'<b>akce: prirazeni objektu k vlastnikovi; </b><br> [id_komplu]=> 1, [id_vlastnika] => 2',	'2024-04-28 16:46:58',	'admin',	1),
(93,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 2  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.10.12\n <b>[id_tarifu]</b> => 3\n <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>[id_nodu]</b> => 2\n <b>[sikana_status]</b> => n\n <b>[sikana_cas]</b> => 0\n <b>[port_id]</b> => 1\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 17:11:53',	'admin@admin',	0),
(94,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 0  <b>[dns_jmeno]</b> => test-wifi-3 , <b>[ip]</b> => 10.10.10.122 , <b>tarif</b> => metropolitni linka , <b>[dov_net]</b> => a , <b>Typ</b> => poc (platici) , <b>[poznamka]</b> => test 1 , <b>Veřejná IP</b> => Ne , <b>[pridal]</b> => admin@admin , <b>přípojný bod</b> => prvni nod , <b>[sikana_status]</b> => n , <b>[sikana_cas]</b> => 0 , <b>[mac]</b> => 11:22:33:44:55:66 ,',	'2024-04-28 19:31:30',	'admin@admin',	1),
(95,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 5  <b>[dns_jmeno]</b> => test-fiber-1\n <b>[ip]</b> => 10.10.100.5\n <b>tarif</b> => fiber tarif 1 , <b>[dov_net]</b> => a\n <b>Typ</b> => poc (platici) , <b>[poznamka]</b> => test fiber 1\n <b>[mac]</b> => 11:22:33:44:55:66\n <b>[pridal]</b> => admin@admin\n <b>přípojný bod</b> => opticky nod 1 , <b>[sikana_status]</b> => n\n <b>[sikana_cas]</b> => 0\n <b>[port_id]</b> => 10\n <b>Veřejná IP</b> => Ne ,',	'2024-04-28 19:46:03',	'admin@admin',	1),
(96,	'[id_komplu]=> 1, diferencialni data: změna pole: <b>client_ap_ip</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">10.10.10.4</span>, změna pole: <b>upravil</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">admin@admin</span>, změna pole: <b>tunnelling_ip</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">0</span>, ',	'2024-04-28 21:35:50',	'admin@admin',	0),
(97,	'[id_komplu]=> 2, diferencialni data: změna pole: <b>upravil</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-28 21:38:29',	'admin@admin',	1),
(98,	'[id_komplu]=> 2, diferencialni data: změna pole: <b>dns_jmeno</b> z: <span class=\"az-s1\" >test-fiber-1                                                                                                                                          </span> na: <span class=\"az-s2\">test-fiber-1</span>, změna pole: <b>poznamka</b> z: <span class=\"az-s1\" >test-fiber-1                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </span> na: <span class=\"az-s2\">test-fiber-1</span>, změna <b>MAC adresy</b> z: <span class=\"az-s1\">11:22:33:44:55:66</span> na: <span class=\"az-s2\">11:22:33:44:55:622</span>, změna pole: <b>upravil</b> z: <span class=\"az-s1\" >admin@admin                                       </span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-28 21:41:18',	'admin@admin',	0),
(99,	'[id_komplu]=> 2, diferencialni data: změna pole: <b>dns_jmeno</b> z: <span class=\"az-s1\" >test-fiber-1                                                                                                                                          </span> na: <span class=\"az-s2\">test-fiber-1</span>, změna pole: <b>poznamka</b> z: <span class=\"az-s1\" >test-fiber-1                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </span> na: <span class=\"az-s2\">test-fiber-1 </span>, změna pole: <b>upravil</b> z: <span class=\"az-s1\" >admin@admin                                       </span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-28 21:41:49',	'admin@admin',	1),
(100,	'[id_komplu]=> 1, diferencialni data: změna pole: <b>dns_jmeno</b> z: <span class=\"az-s1\" >test-wifi-1                                                                                                                                           </span> na: <span class=\"az-s2\">test-wifi-1</span>, změna pole: <b>client_ap_ip</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">10.10.10.111</span>, změna pole: <b>poznamka</b> z: <span class=\"az-s1\" >                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </span> na: <span class=\"az-s2\">test 1</span>, změna pole: <b>upravil</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">admin@admin</span>, změna pole: <b>tunnelling_ip</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">0</span>, ',	'2024-04-28 23:16:52',	'admin@admin',	0),
(101,	'[id_komplu]=> 1, diferencialni data: změna pole: <b>dns_jmeno</b> z: <span class=\"az-s1\" >test-wifi-1                                                                                                                                           </span> na: <span class=\"az-s2\">test-wifi-1</span>, změna pole: <b>client_ap_ip</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">10.10.10.4</span>, změna pole: <b>poznamka</b> z: <span class=\"az-s1\" >                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </span> na: <span class=\"az-s2\">test</span>, změna pole: <b>upravil</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-28 23:25:17',	'admin@admin',	1),
(102,	'[id_komplu]=> 1, diferencialni data: změna pole: <b>dns_jmeno</b> z: <span class=\"az-s1\" >test-wifi-1                                                                                                                                           </span> na: <span class=\"az-s2\">test-wifi-1</span>, změna <b>Tarifu</b> z: <span class=\"az-s1\">small city</span> na: <span class=\"az-s2\">metropolitni linka</span>, změna pole: <b>poznamka</b> z: <span class=\"az-s1\" >test                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </span> na: <span class=\"az-s2\">test</span>, ',	'2024-04-29 07:17:32',	'admin@admin',	1),
(103,	'[id_komplu]=> 2, diferencialni data: změna pole: <b>dns_jmeno</b> z: <span class=\"az-s1\" >test-fiber-1                                                                                                                                          </span> na: <span class=\"az-s2\">test-fiber-1</span>, změna pole: <b>poznamka</b> z: <span class=\"az-s1\" >test-fiber-1                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </span> na: <span class=\"az-s2\">test-fiber-1  update 2</span>, změna <b>MAC adresy</b> z: <span class=\"az-s1\">11:22:33:44:55:66</span> na: <span class=\"az-s2\">22:22:33:44:55:66</span>, změna pole: <b>upravil</b> z: <span class=\"az-s1\" >admin@admin                                       </span> na: <span class=\"az-s2\">admin@admin</span>, ',	'2024-04-29 07:42:24',	'admin@admin',	1),
(104,	'<b> akce: pridani objektu ; </b><br>[id_komplu]=> 6  <b>[dns_jmeno]</b> => test-wifi-mp-2 , <b>[ip]</b> => 10.10.10.123 , <b>tarif</b> => small city , <b>[dov_net]</b> => a , <b>Typ</b> => poc (platici) , <b>Veřejná IP</b> => Ne , <b>[pridal]</b> => admin@admin , <b>přípojný bod</b> => prvni nod , <b>[sikana_status]</b> => n , <b>[sikana_cas]</b> => 0 , <b>[client_ap_ip]</b> => 10.10.10.4 , <b>[mac]</b> => 11:22:33:44:55:66 ,',	'2024-04-29 07:44:03',	'admin@admin',	1);

DROP TABLE IF EXISTS `archiv_zmen_work`;
CREATE TABLE `archiv_zmen_work` (
  `id` int NOT NULL AUTO_INCREMENT,
  `akce` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `provedeno_kdy` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `provedeno_kym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vysledek` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `autorizace`;
CREATE TABLE `autorizace` (
  `id` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nick` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `autorizace` (`id`, `date`, `nick`, `level`) VALUES
('21232f297a57a5a743894a0e4a801fc3',	'1714395018',	'admin',	'100');

DROP TABLE IF EXISTS `az_ucetni`;
CREATE TABLE `az_ucetni` (
  `zu_id` int NOT NULL AUTO_INCREMENT,
  `zu_text` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_typ` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_akceptovano` int unsigned NOT NULL DEFAULT '0',
  `zu_akceptovano_kdy` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `zu_akceptovano_kym` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `zu_akceptovano_pozn` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `zu_vlozeno_kdy` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_vlozeno_kym` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`zu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `az_ucetni` (`zu_id`, `zu_text`, `zu_typ`, `zu_akceptovano`, `zu_akceptovano_kdy`, `zu_akceptovano_kym`, `zu_akceptovano_pozn`, `zu_vlozeno_kdy`, `zu_vlozeno_kym`) VALUES
(1,	'NULL',	'1',	0,	NULL,	NULL,	NULL,	'2024-04-15 17:16:05',	'admin@admin'),
(2,	'ucetni zmena 1',	'1',	0,	NULL,	NULL,	NULL,	'2024-04-15 17:16:26',	'admin@admin');

DROP TABLE IF EXISTS `az_ucetni_typy`;
CREATE TABLE `az_ucetni_typy` (
  `zu_id_typ` int NOT NULL AUTO_INCREMENT,
  `zu_nazev_typ` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`zu_id_typ`),
  UNIQUE KEY `zu_nazev_typ` (`zu_nazev_typ`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `az_ucetni_typy` (`zu_id_typ`, `zu_nazev_typ`) VALUES
(1,	'ucetni typ 1');

DROP TABLE IF EXISTS `board`;
CREATE TABLE `board` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `subject` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `body` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `board` (`id`, `author`, `email`, `from_date`, `to_date`, `subject`, `body`) VALUES
(5,	'admin',	'test',	'2024-04-08',	'2024-04-19',	'subject',	'text'),
(6,	'admin',	'mail',	'2024-04-09',	'2024-04-16',	'sub',	'hahaha'),
(7,	'1',	'',	'2024-04-13',	'2024-04-27',	'test',	'rffff'),
(8,	'1',	'',	'2024-04-13',	'2024-04-27',	'test',	'rffff'),
(9,	'1',	'',	'2024-04-13',	'2024-04-27',	'test4',	'rffff4'),
(10,	'1',	'x@xx',	'2024-04-20',	'2024-04-20',	'hu@hu',	'hu');

DROP TABLE IF EXISTS `core__user_roles`;
CREATE TABLE `core__user_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fk_user_id` int NOT NULL,
  `role` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_idx` (`fk_user_id`,`role`),
  KEY `IDX_E086BB8D5741EEB9` (`fk_user_id`),
  CONSTRAINT `FK_E086BB8D5741EEB9` FOREIGN KEY (`fk_user_id`) REFERENCES `core__users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `core__user_roles` (`id`, `fk_user_id`, `role`) VALUES
(1,	1,	'member');

DROP TABLE IF EXISTS `core__users`;
CREATE TABLE `core__users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `passwordHash` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `level` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_idx` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `core__users` (`id`, `username`, `passwordHash`, `level`) VALUES
(1,	'admin@admin',	'$2y$10$haYN5Ng5BG2oFt5SPMgCUeiXU5c2ZVMOHnZ2oaiC9B5TXg7Hg7KNi',	101);

DROP TABLE IF EXISTS `fakturacni_skupiny`;
CREATE TABLE `fakturacni_skupiny` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nazev` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `typ` int unsigned NOT NULL DEFAULT '0',
  `typ_sluzby` int unsigned NOT NULL DEFAULT '0',
  `fakturacni_text` varchar(4096) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `vlozil_kdo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sluzba_int` int NOT NULL DEFAULT '0',
  `sluzba_int_id_tarifu` int NOT NULL DEFAULT '0',
  `sluzba_iptv` int NOT NULL DEFAULT '0',
  `sluzba_iptv_id_tarifu` int NOT NULL DEFAULT '0',
  `sluzba_voip` int NOT NULL DEFAULT '0',
  `sluzba_voip_id_tarifu` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazev` (`nazev`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `fakturacni_skupiny` (`id`, `nazev`, `typ`, `typ_sluzby`, `fakturacni_text`, `vlozil_kdo`, `sluzba_int`, `sluzba_int_id_tarifu`, `sluzba_iptv`, `sluzba_iptv_id_tarifu`, `sluzba_voip`, `sluzba_voip_id_tarifu`) VALUES
(1,	'fakturacni skupina 1',	1,	0,	'text fakturace 1',	NULL,	1,	2,	0,	0,	0,	0),
(2,	'fakturacni skupina 2',	1,	1,	'text 22',	'admin',	1,	1,	1,	1,	0,	0),
(3,	'fakt skupina - wifi - FU - sc',	1,	0,	'small city pro fakturacni skupiny',	'admin',	1,	1,	0,	0,	0,	0),
(4,	'fakturacni skupina 11',	1,	1,	'text fakturacni skupiny 111',	'admin@admin',	1,	0,	0,	0,	0,	0),
(5,	'test',	1,	0,	'test',	'admin@admin',	0,	0,	0,	0,	0,	0),
(6,	'test2',	1,	0,	'test2',	'admin@admin',	0,	0,	1,	0,	0,	0),
(7,	'test3',	1,	0,	'test3',	'admin@admin',	0,	0,	0,	0,	0,	0),
(8,	'test4',	1,	0,	'test4',	'admin@admin',	0,	0,	0,	0,	0,	0),
(9,	'test5',	1,	0,	'test5',	'admin@admin',	0,	0,	0,	0,	0,	0),
(10,	'test6',	1,	0,	'test6',	'admin@admin',	0,	0,	0,	0,	0,	0),
(11,	'test7',	1,	0,	'test7',	'admin@admin',	0,	0,	0,	0,	0,	0),
(12,	'test8',	1,	0,	'test8',	'admin@admin',	0,	0,	0,	0,	0,	0),
(13,	'test11',	1,	0,	'test11',	'admin@admin',	0,	0,	0,	0,	0,	0),
(14,	'test12',	1,	0,	'test12',	'admin@admin',	0,	0,	0,	0,	0,	0),
(15,	'test13-5',	1,	0,	'test13-5',	'admin@admin',	1,	1,	0,	0,	0,	0),
(16,	'fakt. skupina wifi FU',	2,	0,	'fakt. skupina FU test',	'admin@admin',	1,	1,	0,	0,	0,	0);

DROP TABLE IF EXISTS `faktury_neuhrazene`;
CREATE TABLE `faktury_neuhrazene` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Cislo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `VarSym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Datum` date NOT NULL,
  `DatSplat` date NOT NULL,
  `KcCelkem` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `KcLikv` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Firma` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Jmeno` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ICO` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `DIC` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `par_id_vlastnika` int NOT NULL,
  `par_stav` int NOT NULL,
  `datum_vlozeni` date NOT NULL,
  `overeno` int NOT NULL,
  `aut_email_stav` int NOT NULL,
  `aut_email_datum` date NOT NULL,
  `ignorovat` int NOT NULL,
  `po_splatnosti_vlastnik` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_unique` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `faktury_neuhrazene` (`id`, `Cislo`, `VarSym`, `Datum`, `DatSplat`, `KcCelkem`, `KcLikv`, `Firma`, `Jmeno`, `ICO`, `DIC`, `par_id_vlastnika`, `par_stav`, `datum_vlozeni`, `overeno`, `aut_email_stav`, `aut_email_datum`, `ignorovat`, `po_splatnosti_vlastnik`) VALUES
(1,	'121212',	'12112',	'2024-04-09',	'2024-04-09',	'1000',	'1000',	'H-Software',	'Patrik',	'1111',	'2222',	1,	0,	'2024-04-09',	0,	0,	'2024-04-09',	0,	'');

DROP TABLE IF EXISTS `fn_import_log`;
CREATE TABLE `fn_import_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE `kategorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `sablona` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `leveling`;
CREATE TABLE `leveling` (
  `id` int NOT NULL AUTO_INCREMENT,
  `level` int unsigned NOT NULL DEFAULT '0',
  `popis` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=304 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `leveling` (`id`, `level`, `popis`) VALUES
(1,	6,	'a2: objekty'),
(2,	10,	'a2: objekty-add'),
(4,	10,	'topology-nod-list'),
(5,	10,	'a2: topology-nod-list'),
(6,	10,	'topolog-user-list'),
(13,	10,	'vlastnici / vlastnici-gen-xml'),
(14,	20,	'a2: platby'),
(15,	20,	'maily'),
(16,	10,	'a2: work'),
(17,	10,	'a2: admin'),
(20,	88,	'a2: admin-level-add'),
(21,	82,	'a2: admin-level-list'),
(23,	10,	'admin level action'),
(25,	50,	'topology-nod-update'),
(27,	30,	'objekty-vypis-ip'),
(28,	30,	'soubory'),
(29,	70,	'objekty update'),
(30,	10,	'archiv-zmen, vlastnici update'),
(31,	10,	'a2: automatika'),
(32,	40,	'a2: automatika-sikana-odpocet'),
(33,	70,	'objekty erase'),
(34,	80,	'objekty garant akce'),
(36,	40,	'a2: automatika-sikana-zakazani'),
(37,	50,	'platby-akce'),
(38,	100,	'a3: home.php, vlastnici2'),
(40,	30,	'vlastnici2: pridani vlastnika'),
(41,	50,	'platby-soucet'),
(43,	40,	'stats-objekty'),
(45,	80,	'vlastnici erase'),
(48,	40,	'vlastnici2-add-obj'),
(49,	50,	'objekty - odendani od vlastnika'),
(50,	50,	'platby-vypis'),
(51,	30,	'vlastnici-add-fakt'),
(53,	53,	'monitoring-control'),
(55,	50,	'monitoring grafy'),
(59,	70,	'objekty list - export'),
(63,	40,	'vlastnici export'),
(67,	60,	'vlastnici erase fakturacni'),
(68,	50,	'vlastnici2-change-fakt'),
(75,	10,	'a2: partner-cat'),
(76,	20,	'partner-vypis'),
(77,	20,	'partner-vyrizeni'),
(78,	10,	'a2: vypovedi'),
(79,	30,	'a2: vypovedi vlozeni'),
(80,	20,	'vypovedi plaintisk'),
(81,	80,	'platby-hot-stats'),
(82,	10,	'a3: vlastnici-archiv'),
(84,	20,	'a2: opravy'),
(85,	30,	'topology-router-list'),
(86,	30,	'topology-router-add'),
(87,	100,	'a2: board-header, others-board'),
(90,	10,	'vlastnici-cat'),
(91,	10,	'a2: admin-subcat'),
(92,	10,	'a3: platby-cat'),
(93,	20,	'a2: objekty-subcat'),
(94,	99,	'objekty-lite'),
(95,	10,	'a3: others-cat'),
(96,	10,	'a2: about-map.php'),
(99,	10,	'a2: vlastnici2-fakt-skupiny'),
(101,	10,	'opravy a zavady vypis (homepage)'),
(102,	10,	'a2: vlastnici hledani'),
(103,	49,	'stats.php'),
(104,	40,	'stats-vlastnici'),
(105,	20,	'opravy-vlastnik'),
(106,	20,	'opravy-zacit-resit'),
(107,	10,	'fn.php'),
(108,	33,	'faktury: fn-index'),
(110,	20,	'faktury: fn-aut-sms'),
(111,	10,	'partner-pripojeni'),
(115,	50,	'a2: automatika-fn-check-vlastnik'),
(116,	40,	'topology-nod-erase'),
(117,	20,	'voip index'),
(119,	20,	'partner-pozn-update'),
(125,	60,	'voip online-dial-cust-list'),
(128,	60,	'topology-router-erase'),
(131,	40,	'admin tarify list'),
(132,	20,	'topology-router-mail'),
(135,	20,	'a2: objekty-stb'),
(136,	10,	'objekty-stb-add'),
(137,	20,	'stb uprava'),
(138,	44,	'vlastnici2-add-obj'),
(139,	10,	'objekty test'),
(140,	30,	'vlastnici2-fs-update'),
(141,	20,	'vlastnici2-fs-erase'),
(142,	2,	'about.php'),
(143,	10,	'a2: archiv-zmen-cat.php'),
(144,	10,	'a3: about-changes-old.php'),
(145,	10,	'a3: about-changes.php'),
(146,	10,	'a3: other-print'),
(147,	10,	'a3: archiv-zmen-ucetni.php'),
(148,	10,	'a3: archiv-zmen-ucetni.php : add'),
(149,	10,	'a3: fn-kontrola-omezeni.php'),
(150,	40,	'objekty stb unpair'),
(151,	10,	'a3: others-web-simelon'),
(301,	30,	'fakturacni-skupiny add'),
(302,	44,	'vlastnici-cross'),
(303,	49,	'admin-tarify action');

DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nick` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `login_log` (`id`, `nick`, `date`, `ip`) VALUES
(34,	'admin',	'1713601589',	'172.18.0.1'),
(35,	'admin',	'1713715907',	'172.18.0.1'),
(36,	'admin',	'1713740348',	'172.18.0.1'),
(37,	'admin',	'1713792013',	'172.18.0.1'),
(38,	'admin',	'1713798122',	'172.18.0.1'),
(39,	'admin',	'1713819772',	'172.18.0.1'),
(40,	'admin',	'1713880109',	'172.18.0.1'),
(41,	'admin',	'1714211930',	'172.18.0.1');

DROP TABLE IF EXISTS `mon_grafy`;
CREATE TABLE `mon_grafy` (
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `nod_list`;
CREATE TABLE `nod_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jmeno` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `adresa` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pozn` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_rozsah` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `typ_nodu` int unsigned NOT NULL,
  `typ_vysilace` int unsigned NOT NULL DEFAULT '0',
  `stav` int unsigned NOT NULL,
  `router_id` int unsigned NOT NULL,
  `vlan_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `nod_list` (`id`, `jmeno`, `adresa`, `pozn`, `ip_rozsah`, `typ_nodu`, `typ_vysilace`, `stav`, `router_id`, `vlan_id`) VALUES
(1,	'prvni nod',	'u me doma',	'test pozn',	'10.10.10.0/24',	1,	0,	0,	1,	0),
(2,	'opticky nod 1',	'',	'',	'10.10.100.0/24',	2,	0,	0,	0,	0),
(3,	'opticky nod 2',	'kdesi 2',	'',	'10.10.200.0/24',	2,	0,	0,	0,	0),
(370,	'optika - neco special',	'',	'',	'',	2,	0,	0,	0,	0),
(371,	'druhy nod',	'',	'',	'10.20.20.20/24',	1,	0,	0,	3,	0);

DROP TABLE IF EXISTS `objekty_stb`;
CREATE TABLE `objekty_stb` (
  `id_stb` int NOT NULL AUTO_INCREMENT,
  `id_cloveka` int DEFAULT NULL,
  `mac_adresa` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `puk` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pin1` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pin2` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_adresa` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `popis` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_nodu` int NOT NULL,
  `sw_port` int NOT NULL,
  `pozn` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vlozil_kdo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `upravil_kdo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `datum_vytvoreni` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_tarifu` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stb`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `objekty_stb` (`id_stb`, `id_cloveka`, `mac_adresa`, `puk`, `pin1`, `pin2`, `ip_adresa`, `popis`, `id_nodu`, `sw_port`, `pozn`, `vlozil_kdo`, `upravil_kdo`, `datum_vytvoreni`, `id_tarifu`) VALUES
(2,	NULL,	'11:22:33:44:55:66',	'111',	'',	'',	'10.10.10.1',	'stb-1acbaa',	2,	1,	'xxx111uacba',	'admin',	'admin@admin',	'2024-04-21 15:10:10',	1),
(3,	2,	'33:22:33:44:55:63',	'',	'',	'',	'10.10.10.22',	'stb2-ua',	2,	1,	'pozn xxx 23a',	'admin',	'admin@admin',	'2024-04-22 15:15:20',	1),
(4,	0,	'33:22:33:44:55:66',	'',	'',	'',	'10.10.10.3',	'stb3a',	3,	1,	'pozn 3a',	'admin',	'admin@admin',	'2024-04-21 15:09:39',	1),
(5,	0,	'43:22:33:44:55:66',	'',	'',	'',	'10.10.10.4',	'stb4',	2,	4,	'pozn 4',	'admin',	'',	'2024-04-12 09:34:49',	1),
(6,	0,	'55:22:33:44:55:66',	'5555',	'',	'',	'10.10.10.5',	'stb5',	2,	5,	'pozn 5',	'admin',	'',	'2024-04-12 09:42:30',	1),
(7,	0,	'66:22:33:44:55:66',	'666',	'',	'',	'10.10.10.6',	'stb6',	2,	6,	'pozn 6',	'admin',	'',	'2024-04-12 09:54:46',	1),
(8,	0,	'77:22:33:44:55:66',	'777',	'',	'',	'10.10.10.7',	'stb 7',	2,	7,	'pozn 7',	'admin',	'',	'2024-04-12 10:00:24',	1),
(9,	0,	'88:22:33:44:55:66',	'888',	'',	'',	'10.10.10.8',	'stb 8',	2,	8,	'pozn 8',	'admin',	'',	'2024-04-12 10:03:37',	1),
(10,	0,	'00:00:64:65:73:74',	'1111',	'',	'',	'1.1.1.1',	'xxxxx',	1,	1,	'',	'admin@admin',	NULL,	'2024-04-13 20:51:58',	1),
(11,	0,	'00:00:64:65:73:73',	'1111',	'',	'',	'1.1.1.1',	'xxxz',	1,	1,	'',	'admin@admin',	NULL,	'2024-04-13 20:53:47',	1),
(12,	0,	'32:22:33:44:55:66',	'111',	'',	'',	'10.10.10.4',	'xxeee',	1,	1,	'',	'admin@admin',	NULL,	'2024-04-13 20:56:56',	1),
(13,	0,	'11:23:33:44:55:66',	'1111',	'',	'',	'10.10.10.5',	'stbx',	1,	1,	'',	'admin@admin',	NULL,	'2024-04-13 20:58:06',	1),
(18,	0,	'11:22:33:44:55:11',	'1',	'',	'',	'10.10.10.11',	'stb-11',	2,	2,	'test 2',	'admin@admin',	NULL,	'2024-04-14 22:08:15',	4),
(19,	0,	'11:22:33:44:55:33',	'1',	'',	'',	'10.10.10.12',	'stb-12',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-14 22:10:17',	4),
(20,	0,	'22:22:33:44:55:15',	'15',	'',	'',	'10.10.10.15',	'stb-1t',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-14 22:32:35',	4),
(21,	0,	'14:22:33:44:55:66',	'',	'',	'',	'10.10.10.41',	'pokus-2',	3,	1,	'test poznamka',	'admin@admin',	NULL,	'2024-04-20 12:13:01',	4),
(22,	0,	'14:14:33:44:55:66',	'',	'',	'',	'10.10.10.122',	'xxx-14',	2,	1,	'test',	'admin@admin',	NULL,	'2024-04-21 11:56:06',	1),
(23,	0,	'11:22:33:11:11:11',	'',	'',	'',	'10.10.15.15',	'xxx-15',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-21 12:02:06',	4),
(24,	0,	'11:23:33:44:33:66',	'',	'',	'',	'10.10.14.15',	'stb-16',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-21 12:02:56',	1),
(25,	0,	'16:23:33:44:33:66',	'',	'',	'',	'10.10.14.16',	'xxx-16',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-21 12:04:01',	1),
(26,	0,	'11:22:33:44:17:17',	'',	'',	'',	'10.10.10.17',	'xxx-17',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-21 12:08:00',	1),
(27,	0,	'11:22:33:44:18:18',	'',	'',	'',	'10.10.10.18',	'stb-18',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-21 12:09:57',	4),
(28,	0,	'11:22:33:44:19:19',	'',	'',	'',	'10.10.10.19',	'stb-19',	3,	1,	'',	'admin@admin',	NULL,	'2024-04-21 12:12:26',	1);

DROP TABLE IF EXISTS `opravy`;
CREATE TABLE `opravy` (
  `id_opravy` int unsigned NOT NULL AUTO_INCREMENT,
  `id_predchozi_opravy` int unsigned NOT NULL DEFAULT '0',
  `id_vlastnika` int unsigned NOT NULL,
  `datum_vlozeni` date NOT NULL,
  `v_reseni` int unsigned NOT NULL DEFAULT '0',
  `v_reseni_kym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vlozil` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `priorita` int unsigned NOT NULL DEFAULT '0',
  `vyreseno` int unsigned NOT NULL DEFAULT '0',
  `vyreseno_kym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id_opravy`),
  UNIQUE KEY `id_opravy` (`id_opravy`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `opravy` (`id_opravy`, `id_predchozi_opravy`, `id_vlastnika`, `datum_vlozeni`, `v_reseni`, `v_reseni_kym`, `vlozil`, `priorita`, `vyreseno`, `vyreseno_kym`, `text`) VALUES
(2,	0,	1,	'2024-04-10',	0,	'patrik',	'nevime',	0,	0,	'',	'fakt mi neco nejde, a uz to nejde asi sto let, nekolikrat jsem si volal s vasim technikem a nic'),
(3,	2,	1,	'2024-04-10',	0,	'pavel',	'lucka',	0,	0,	'',	'');

DROP TABLE IF EXISTS `partner_klienti`;
CREATE TABLE `partner_klienti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tel` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `jmeno` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `adresa` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `poznamky` varchar(450) COLLATE utf8mb3_unicode_ci NOT NULL,
  `prio` int NOT NULL DEFAULT '0',
  `vlozil` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `datum_vlozeni` datetime NOT NULL,
  `pripojeno` int NOT NULL DEFAULT '0',
  `pripojeno_linka` int NOT NULL DEFAULT '0',
  `typ_balicku` int NOT NULL DEFAULT '0',
  `typ_linky` int NOT NULL DEFAULT '0',
  `akceptovano` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `partner_klienti` (`id`, `tel`, `jmeno`, `adresa`, `email`, `poznamky`, `prio`, `vlozil`, `datum_vlozeni`, `pripojeno`, `pripojeno_linka`, `typ_balicku`, `typ_linky`, `akceptovano`) VALUES
(1,	'123456789',	'Petr Pavel',	'Hrad 1',	'me@hrad.gov.cz',	'celej areal + wifi',	0,	'backoffice',	'2024-04-27 17:21:54',	1,	0,	0,	0,	0),
(2,	'608608608',	'Vratislav Mynar',	'Hrad 666',	'podatelna@hrad.gov.cz',	'presna lokace bude vyresena na miste',	0,	'admin',	'2024-04-27 20:40:33',	0,	0,	0,	0,	1),
(3,	'608608608',	'Milan Balak',	'Obora 1',	'',	'',	0,	'hrad',	'2024-04-28 07:51:09',	0,	0,	0,	0,	0);

DROP TABLE IF EXISTS `partner_klienti_servis`;
CREATE TABLE `partner_klienti_servis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tel` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `jmeno` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `adresa` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `poznamky` varchar(4096) COLLATE utf8mb3_unicode_ci NOT NULL,
  `prio` int NOT NULL DEFAULT '0',
  `vlozil` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `akceptovano` int NOT NULL DEFAULT '0',
  `akceptovano_kym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `akceptovano_pozn` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `datum_vlozeni` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `partner_klienti_servis` (`id`, `tel`, `jmeno`, `adresa`, `email`, `poznamky`, `prio`, `vlozil`, `akceptovano`, `akceptovano_kym`, `akceptovano_pozn`, `datum_vlozeni`) VALUES
(1,	'112233222',	'xx,  V:0',	'kdesi 2',	'hu@hu.hu',	'nic',	2,	'admin',	0,	NULL,	NULL,	'2024-04-18 09:32:03');

DROP TABLE IF EXISTS `router_list`;
CREATE TABLE `router_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nazev` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_adresa` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `parent_router` int NOT NULL DEFAULT '0',
  `mac` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `monitoring` int NOT NULL DEFAULT '0',
  `monitoring_cat` int NOT NULL DEFAULT '0',
  `alarm` int NOT NULL DEFAULT '0',
  `alarm_stav` int NOT NULL DEFAULT '0',
  `filtrace` int NOT NULL DEFAULT '0',
  `warn` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `mail` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=445 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `router_list` (`id`, `nazev`, `ip_adresa`, `parent_router`, `mac`, `monitoring`, `monitoring_cat`, `alarm`, `alarm_stav`, `filtrace`, `warn`, `mail`) VALUES
(1,	'reinhard-wifi',	'10.10.10.10',	0,	'',	0,	0,	0,	0,	0,	'',	''),
(2,	'router 2',	'192.168.1.213',	1,	'22:33:22:33:44:44',	0,	0,	0,	0,	0,	'',	''),
(3,	'child of router 2',	'10.20.10.10',	2,	'',	0,	0,	0,	0,	0,	'',	''),
(444,	'reinhard-fiber',	'10.128.0.1',	1,	'',	0,	0,	0,	0,	0,	'',	'');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` int NOT NULL,
  `value` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `tarify_int`;
CREATE TABLE `tarify_int` (
  `id_tarifu` int NOT NULL AUTO_INCREMENT,
  `typ_tarifu` int unsigned NOT NULL,
  `zkratka_tarifu` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `jmeno_tarifu` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `speed_dwn` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `speed_upl` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `agregace` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `agregace_smlouva` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cena_bez_dph` float NOT NULL DEFAULT '0',
  `cena_s_dph` float NOT NULL DEFAULT '0',
  `gen_poradi` int NOT NULL DEFAULT '0',
  `barva` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `garant` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tarifu`),
  UNIQUE KEY `id_tarifu` (`id_tarifu`),
  UNIQUE KEY `zkratka_tarifu` (`zkratka_tarifu`),
  UNIQUE KEY `jmeno_tarifu` (`jmeno_tarifu`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `tarify_int` (`id_tarifu`, `typ_tarifu`, `zkratka_tarifu`, `jmeno_tarifu`, `speed_dwn`, `speed_upl`, `agregace`, `agregace_smlouva`, `cena_bez_dph`, `cena_s_dph`, `gen_poradi`, `barva`, `garant`) VALUES
(1,	0,	'cs',	'small city',	'2048',	'2048',	'1:20',	'1:50',	249,	298.5,	0,	'',	0),
(2,	0,	'mp',	'metropolitni linka',	'',	'',	'',	'',	0,	0,	0,	'',	0),
(3,	1,	'fiber 1',	'fiber tarif 1',	'60000',	'60000',	'1:4',	'1:',	399,	422,	0,	'',	0);

DROP TABLE IF EXISTS `tarify_iptv`;
CREATE TABLE `tarify_iptv` (
  `id_tarifu` int NOT NULL AUTO_INCREMENT,
  `jmeno_tarifu` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `zkratka_tarifu` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_tarifu`),
  UNIQUE KEY `id_tarifu_unique` (`id_tarifu`),
  UNIQUE KEY `jmeno_tarifu` (`jmeno_tarifu`),
  UNIQUE KEY `zkratka_tarifu` (`zkratka_tarifu`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `tarify_iptv` (`id_tarifu`, `jmeno_tarifu`, `zkratka_tarifu`) VALUES
(1,	'tarif iptv 1',	NULL),
(4,	'tarif iptv 2',	NULL);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` int unsigned NOT NULL DEFAULT '0',
  `lvl_admin_login_iptv` int unsigned NOT NULL DEFAULT '0',
  `lvl_objekty_stb_add_portal` int unsigned NOT NULL DEFAULT '0',
  `lvl_objekty_stb_erase` int unsigned NOT NULL DEFAULT '0',
  `lvl_partner_servis_add` int unsigned NOT NULL DEFAULT '0',
  `lvl_partner_servis_list` int unsigned NOT NULL DEFAULT '0',
  `lvl_partner_servis_accept` int unsigned NOT NULL DEFAULT '0',
  `lvl_partner_servis_pozn_update` int unsigned NOT NULL DEFAULT '0',
  `lvl_phd_adresar` int unsigned NOT NULL DEFAULT '0',
  `lvl_phd_list_fa` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `users` (`id`, `login`, `password`, `level`, `lvl_admin_login_iptv`, `lvl_objekty_stb_add_portal`, `lvl_objekty_stb_erase`, `lvl_partner_servis_add`, `lvl_partner_servis_list`, `lvl_partner_servis_accept`, `lvl_partner_servis_pozn_update`, `lvl_phd_adresar`, `lvl_phd_list_fa`) VALUES
(1,	'admin',	'1a1dc91c907325c69271ddf0c944bc72',	100,	1,	1,	1,	1,	1,	1,	1,	1,	1);

DROP TABLE IF EXISTS `vypovedi`;
CREATE TABLE `vypovedi` (
  `id_vypovedi` int NOT NULL AUTO_INCREMENT,
  `id_vlastnika` int NOT NULL,
  `datum_vlozeni` date NOT NULL,
  `datum_uzavreni` date NOT NULL,
  `datum_vypovedi` date NOT NULL,
  `duvod` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `uhrazeni_vypovedni_lhuty` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vypovedni_lhuta` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_vypovedi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `workitems`;
CREATE TABLE `workitems` (
  `id` int NOT NULL AUTO_INCREMENT,
  `number_request` int NOT NULL DEFAULT '0',
  `in_progress` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `workitems_names`;
CREATE TABLE `workitems_names` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `workitems_names` (`id`, `name`, `priority`) VALUES
(1,	'work item 1',	0),
(2,	'work item 2',	0);

DROP TABLE IF EXISTS `workzamek`;
CREATE TABLE `workzamek` (
  `id` int NOT NULL AUTO_INCREMENT,
  `zamek` varchar(10) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `workzamek` (`id`, `zamek`) VALUES
(1,	'ne');

-- 2024-04-30 07:36:50