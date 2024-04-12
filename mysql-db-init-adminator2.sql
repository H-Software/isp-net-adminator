-- Adminer 4.8.1 MySQL 8.0.36 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `archiv_zmen`;
CREATE TABLE `archiv_zmen` (
  `id` int NOT NULL AUTO_INCREMENT,
  `akce` varchar(4096) COLLATE utf8mb3_unicode_ci NOT NULL,
  `provedeno_kdy` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `provedeno_kym` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vysledek` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `archiv_zmen` (`id`, `akce`, `provedeno_kdy`, `provedeno_kym`, `vysledek`) VALUES
(1,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 5, [mac_adresa]=> 43:22:33:44:55:66, [ip_adresa]=> 10.10.10.4, [puk]=> , [popis]=> stb4, [id_nodu]=> 2, [sw_port]=> 4 [pozn]=> pozn 4, [id_tarifu]=> 1',	'2024-04-12 09:34:49',	'admin',	1),
(2,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 6, [mac_adresa]=> 55:22:33:44:55:66, [ip_adresa]=> 10.10.10.5, [puk]=> 5555, [popis]=> stb5, [id_nodu]=> 2, [sw_port]=> 5 [pozn]=> pozn 5, [id_tarifu]=> 1',	'2024-04-12 09:42:30',	'admin',	1),
(3,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 7, [mac_adresa]=> 66:22:33:44:55:66, [ip_adresa]=> 10.10.10.6, [puk]=> 666, [popis]=> stb6, [id_nodu]=> 2, [sw_port]=> 6 [pozn]=> pozn 6, [id_tarifu]=> 1',	'2024-04-12 09:54:46',	'admin',	1),
(4,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 8, [mac_adresa]=> 77:22:33:44:55:66, [ip_adresa]=> 10.10.10.7, [puk]=> 777, [popis]=> stb 7, [id_nodu]=> 2, [sw_port]=> 7 [pozn]=> pozn 7, [id_tarifu]=> 1',	'2024-04-12 10:00:24',	'admin',	1),
(5,	'<b> akce: pridani stb objektu ; </b><br>[id_stb]=> 9, [mac_adresa]=> 88:22:33:44:55:66, [ip_adresa]=> 10.10.10.8, [puk]=> 888, [popis]=> stb 8, [id_nodu]=> 2, [sw_port]=> 8 [pozn]=> pozn 8, [id_tarifu]=> 1',	'2024-04-12 10:03:37',	'admin',	1),
(6,	'<b>akce: uprava stb objektu; </b><br>[id_stb]=> 2, diferencialni data: změna pole: <b>puk</b> z: <span class=\"az-s1\" ></span> na: <span class=\"az-s2\">111</span>, změna pole: <b>pozn</b> z: <span class=\"az-s1\" >xxx222</span> na: <span class=\"az-s2\">xxx111</span>, ',	'2024-04-12 12:29:43',	'admin',	1);

DROP TABLE IF EXISTS `archiv_zmen_work`;
CREATE TABLE `archiv_zmen_work` (
  `id` int NOT NULL AUTO_INCREMENT,
  `akce` varchar(4096) COLLATE utf8mb3_unicode_ci NOT NULL,
  `provedeno_kdy` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `provedeno_kym` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `vysledek` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `autorizace`;
CREATE TABLE `autorizace` (
  `id` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nick` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `autorizace` (`id`, `date`, `nick`, `level`) VALUES
('21232f297a57a5a743894a0e4a801fc3',	'1712925762',	'admin',	'100');

DROP TABLE IF EXISTS `az_ucetni`;
CREATE TABLE `az_ucetni` (
  `zu_id` int NOT NULL AUTO_INCREMENT,
  `zu_text` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_typ` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_akceptovano` int unsigned NOT NULL,
  `zu_akceptovano_kdy` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_akceptovano_kym` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_akceptovano_pozn` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_vlozeno_kdy` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `zu_vlozeno_kym` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`zu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `az_ucetni_typy`;
CREATE TABLE `az_ucetni_typy` (
  `zu_id_typ` int NOT NULL AUTO_INCREMENT,
  `zu_nazev_typ` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`zu_id_typ`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `board`;
CREATE TABLE `board` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `subject` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `body` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `board` (`id`, `author`, `email`, `from_date`, `to_date`, `subject`, `body`) VALUES
(5,	'admin',	'test',	'2024-04-08',	'2024-04-19',	'subject',	'text'),
(6,	'admin',	'mail',	'2024-04-09',	'2024-04-16',	'sub',	'hahaha');

DROP TABLE IF EXISTS `fakturacni_skupiny`;
CREATE TABLE `fakturacni_skupiny` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nazev` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `typ` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `fakturacni_skupiny` (`id`, `nazev`, `typ`) VALUES
(1,	'fakturacni skupina 1',	1);

DROP TABLE IF EXISTS `faktury_neuhrazene`;
CREATE TABLE `faktury_neuhrazene` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Cislo` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `VarSym` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Datum` date NOT NULL,
  `DatSplat` date NOT NULL,
  `KcCelkem` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `KcLikv` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Firma` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `Jmeno` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ICO` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `DIC` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `par_id_vlastnika` int NOT NULL,
  `par_stav` int NOT NULL,
  `datum_vlozeni` date NOT NULL,
  `overeno` int NOT NULL,
  `aut_email_stav` int NOT NULL,
  `aut_email_datum` date NOT NULL,
  `ignorovat` int NOT NULL,
  `po_splatnosti_vlastnik` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `faktury_neuhrazene` (`id`, `Cislo`, `VarSym`, `Datum`, `DatSplat`, `KcCelkem`, `KcLikv`, `Firma`, `Jmeno`, `ICO`, `DIC`, `par_id_vlastnika`, `par_stav`, `datum_vlozeni`, `overeno`, `aut_email_stav`, `aut_email_datum`, `ignorovat`, `po_splatnosti_vlastnik`) VALUES
(1,	'121212',	'12112',	'2024-04-09',	'2024-04-09',	'1000',	'1000',	'H-Software',	'Patrik',	'1111',	'2222',	1,	0,	'2024-04-09',	0,	0,	'2024-04-09',	0,	'');

DROP TABLE IF EXISTS `fn_import_log`;
CREATE TABLE `fn_import_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


DROP TABLE IF EXISTS `leveling`;
CREATE TABLE `leveling` (
  `id` int NOT NULL AUTO_INCREMENT,
  `level` int NOT NULL DEFAULT '0',
  `popis` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `leveling` (`id`, `level`, `popis`) VALUES
(1,	5,	'a2: objekty'),
(2,	10,	'a2: objekty-add'),
(5,	10,	'a2: topology-nod-list'),
(13,	10,	'a2: vlastnici'),
(14,	20,	's2: platby'),
(16,	10,	'a2: work'),
(17,	10,	'a2: admin'),
(20,	88,	'a2: admin-level-add'),
(21,	82,	'a2: admin-level-list'),
(23,	10,	'admin level action'),
(28,	30,	'soubory'),
(30,	10,	'a3: archiv-zmen-cat.php, a2: archiv-zmen.php'),
(31,	10,	'a2: automatika'),
(32,	40,	'a2: automatika-sikana-odpocet'),
(36,	40,	'a2: automatika-sikana-zakazani'),
(38,	100,	'a3: home.php, vlastnici2'),
(40,	30,	'vlastnici2: pridani vlastnika'),
(63,	40,	'vlastnici export'),
(75,	10,	'a2: partner-cat'),
(78,	10,	'a2: vypovedi'),
(79,	30,	'a2: vypovedi vlozeni'),
(80,	20,	'vypovedi plaintisk'),
(82,	10,	'a3: vlastnici-archiv'),
(84,	20,	'a2: opravy'),
(87,	100,	'a2: board-header, others-board'),
(90,	10,	'vlastnici-cat'),
(91,	10,	'a2: admin-subcat'),
(92,	10,	'a3: platby-cat'),
(93,	10,	'a2: objekty-subcat'),
(94,	99,	'objekty-lite'),
(95,	10,	'a3: others-cat'),
(96,	10,	'a2: about-map.php'),
(99,	10,	'a2: vlastnici2-fakt-skupiny'),
(101,	10,	'opravy a zavady vypis (homepage)'),
(102,	10,	'a2: vlastnici hledani'),
(107,	10,	'fn.php'),
(115,	50,	'a2: automatika-fn-check-vlastnik'),
(131,	40,	'admin tarify'),
(135,	20,	'a2: objekty-stb'),
(136,	10,	'a2: objekty-stb-add'),
(137,	20,	'stb uprava'),
(139,	10,	'objekty test'),
(140,	30,	' a2: vlastnici2-fs-update'),
(142,	2,	'about.php'),
(143,	10,	'a2: archiv-zmen-cat.php'),
(144,	10,	'a3: about-changes-old.php'),
(145,	10,	'a3: about-changes.php'),
(146,	10,	'a3: other-print'),
(147,	10,	'a3: archiv-zmen-ucetni.php'),
(148,	10,	'a3: archiv-zmen-ucetni.php : add'),
(149,	10,	'a3: fn-kontrola-omezeni.php'),
(151,	10,	'a3: others-web-simelon');

DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nick` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `login_log` (`id`, `nick`, `date`, `ip`) VALUES
(1,	'admin',	'1712529296',	'192.168.65.1'),
(2,	'admin',	'1712565285',	'192.168.65.1'),
(3,	'admin',	'1712589532',	'192.168.65.1'),
(4,	'admin',	'1712605764',	'192.168.65.1'),
(5,	'admin',	'1712649618',	'192.168.65.1'),
(6,	'admin',	'1712652699',	'192.168.65.1'),
(7,	'admin',	'1712656346',	'192.168.65.1'),
(8,	'admin',	'1712664928',	'192.168.65.1'),
(9,	'admin',	'1712665482',	'192.168.65.1'),
(10,	'admin',	'1712924537',	'192.168.65.1');

DROP TABLE IF EXISTS `nod_list`;
CREATE TABLE `nod_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jmeno` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `adresa` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pozn` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_rozsah` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `typ_nodu` int unsigned NOT NULL,
  `stav` int unsigned NOT NULL,
  `router_id` int unsigned NOT NULL,
  `vlan_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `nod_list` (`id`, `jmeno`, `adresa`, `pozn`, `ip_rozsah`, `typ_nodu`, `stav`, `router_id`, `vlan_id`) VALUES
(1,	'prvni nod',	'u me doma',	'test pozn',	'10.10.10.0/24',	0,	0,	0,	0),
(2,	'opticky nod 1',	'',	'',	'',	2,	0,	0,	0),
(370,	'cosi specialniho pro stb',	'',	'',	'',	0,	0,	0,	0);

DROP TABLE IF EXISTS `objekty_stb`;
CREATE TABLE `objekty_stb` (
  `id_stb` int NOT NULL AUTO_INCREMENT,
  `id_cloveka` int NOT NULL DEFAULT '0',
  `mac_adresa` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `puk` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `ip_adresa` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `popis` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_nodu` int NOT NULL,
  `sw_port` int NOT NULL,
  `pozn` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `vlozil_kdo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `upravil_kdo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `datum_vytvoreni` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_tarifu` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_stb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `objekty_stb` (`id_stb`, `id_cloveka`, `mac_adresa`, `puk`, `ip_adresa`, `popis`, `id_nodu`, `sw_port`, `pozn`, `vlozil_kdo`, `upravil_kdo`, `datum_vytvoreni`, `id_tarifu`) VALUES
(2,	0,	'11:22:33:44:55:66',	'111',	'10.10.10.1',	'stb-1',	2,	1,	'xxx111',	'admin',	'admin',	'2024-04-12 12:29:43',	1),
(3,	0,	'22:22:33:44:55:66',	'',	'10.10.10.2',	'stb2',	2,	2,	'pozn xxx 2',	'admin',	'admin',	'2024-04-12 12:25:07',	1),
(4,	0,	'33:22:33:44:55:66',	'',	'10.10.10.3',	'stb3',	2,	3,	'pozn 3',	'admin',	'',	'2024-04-12 09:33:13',	1),
(5,	0,	'43:22:33:44:55:66',	'',	'10.10.10.4',	'stb4',	2,	4,	'pozn 4',	'admin',	'',	'2024-04-12 09:34:49',	1),
(6,	0,	'55:22:33:44:55:66',	'5555',	'10.10.10.5',	'stb5',	2,	5,	'pozn 5',	'admin',	'',	'2024-04-12 09:42:30',	1),
(7,	0,	'66:22:33:44:55:66',	'666',	'10.10.10.6',	'stb6',	2,	6,	'pozn 6',	'admin',	'',	'2024-04-12 09:54:46',	1),
(8,	0,	'77:22:33:44:55:66',	'777',	'10.10.10.7',	'stb 7',	2,	7,	'pozn 7',	'admin',	'',	'2024-04-12 10:00:24',	1),
(9,	0,	'88:22:33:44:55:66',	'888',	'10.10.10.8',	'stb 8',	2,	8,	'pozn 8',	'admin',	'',	'2024-04-12 10:03:37',	1);

DROP TABLE IF EXISTS `opravy`;
CREATE TABLE `opravy` (
  `id_opravy` int unsigned NOT NULL AUTO_INCREMENT,
  `id_predchozi_opravy` int unsigned NOT NULL DEFAULT '0',
  `id_vlastnika` int unsigned NOT NULL,
  `datum_vlozeni` date NOT NULL,
  `v_reseni` int unsigned NOT NULL DEFAULT '0',
  `v_reseni_kym` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `vlozil` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `priorita` int unsigned NOT NULL DEFAULT '0',
  `vyreseno` int unsigned NOT NULL DEFAULT '0',
  `vyreseno_kym` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` varchar(4096) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id_opravy`),
  UNIQUE KEY `id_opravy` (`id_opravy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `opravy` (`id_opravy`, `id_predchozi_opravy`, `id_vlastnika`, `datum_vlozeni`, `v_reseni`, `v_reseni_kym`, `vlozil`, `priorita`, `vyreseno`, `vyreseno_kym`, `text`) VALUES
(2,	0,	1,	'2024-04-10',	0,	'patrik',	'nevime',	0,	0,	'',	'fakt mi neco nejde, a uz to nejde asi sto let, nekolikrat jsem si volal s vasim technikem a nic'),
(3,	2,	1,	'2024-04-10',	0,	'pavel',	'lucka',	0,	0,	'',	'');

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
  `zkratka_tarifu` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `jmeno_tarifu` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `speed_down` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `speed_up` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `gen_poradi` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_tarifu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `tarify_int` (`id_tarifu`, `typ_tarifu`, `zkratka_tarifu`, `jmeno_tarifu`, `speed_down`, `speed_up`, `gen_poradi`) VALUES
(1,	0,	'cs',	'small city',	'2048',	'2048',	0);

DROP TABLE IF EXISTS `tarify_iptv`;
CREATE TABLE `tarify_iptv` (
  `id_tarifu` int NOT NULL AUTO_INCREMENT,
  `jmeno_tarifu` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id_tarifu`),
  CONSTRAINT `tarify_iptv_ibfk_1` FOREIGN KEY (`id_tarifu`) REFERENCES `tarify_int` (`id_tarifu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `tarify_iptv` (`id_tarifu`, `jmeno_tarifu`) VALUES
(1,	'tarif iptv 1');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb3_unicode_ci NOT NULL,
  `level` int unsigned NOT NULL DEFAULT '0',
  `lvl_admin_login_iptv` int unsigned NOT NULL DEFAULT '0',
  `lvl_objekty_stb_add_portal` int unsigned NOT NULL DEFAULT '0',
  `lvl_objekty_stb_erase` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `users` (`id`, `login`, `password`, `level`, `lvl_admin_login_iptv`, `lvl_objekty_stb_add_portal`, `lvl_objekty_stb_erase`) VALUES
(1,	'admin',	'1a1dc91c907325c69271ddf0c944bc72',	100,	1,	1,	1);

DROP TABLE IF EXISTS `users_slim`;
CREATE TABLE `users_slim` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `level` int unsigned NOT NULL DEFAULT '0',
  `lvl_admin_login_iptv` int unsigned DEFAULT '0',
  `lvl_objekty_stb_add_portal` int unsigned DEFAULT '0',
  `lvl_objekty_stb_erase` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `users_slim` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`, `level`, `lvl_admin_login_iptv`, `lvl_objekty_stb_add_portal`, `lvl_objekty_stb_erase`) VALUES
(1,	'admin',	'admin@admin',	'$2y$10$8ccRNXzVcyArzcbiBT813u18fBagOvhEjN9YW8hx98neQKH32h24i',	'2024-04-10 20:26:07',	'2024-04-10 20:26:07',	100,	0,	0,	0);

DROP TABLE IF EXISTS `vypovedi`;
CREATE TABLE `vypovedi` (
  `id_vypovedi` int NOT NULL AUTO_INCREMENT,
  `id_vlastnika` int NOT NULL,
  `datum_vlozeni` date NOT NULL,
  `datum_uzavreni` date NOT NULL,
  `datum_vypovedi` date NOT NULL,
  `duvod` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `uhrazeni_vypovedni_lhuty` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `vypovedni_lhuta` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_vypovedi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- 2024-04-12 13:04:32