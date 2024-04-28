-- Adminer 4.8.1 PostgreSQL 12.18 dump

\connect "adminator.new";

DROP TABLE IF EXISTS "fakturacni";
CREATE TABLE "public"."fakturacni" (
    "id" integer DEFAULT GENERATED ALWAYS AS IDENTITY NOT NULL,
    "ftitle" text,
    "fulice" text,
    "fmesto" text,
    "fpsc" text,
    "ico" text,
    "dic" text,
    "ucet" text,
    "splatnost" text,
    "cetnost" text,
    CONSTRAINT "fakturacni_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "fakturacni" ("id", "ftitle", "fulice", "fmesto", "fpsc", "ico", "dic", "ucet", "splatnost", "cetnost") VALUES
(2,	'Hradni kancelar',	'Hrad 1',	'Praha',	'11111',	'',	'',	'',	'15',	'1');

DROP TABLE IF EXISTS "faktury_neuhrazene";
CREATE TABLE "public"."faktury_neuhrazene" (
    "id" integer NOT NULL,
    "cislo" "char",
    "varsym" "char",
    "datum" date,
    "datsplat" date,
    "kccelkem" "char",
    "kclikv" "char",
    "firma" "char",
    "jmeno" "char",
    "ico" "char",
    "dic" "char",
    "par_id_vlastnika" integer,
    "par_stav" "char",
    "datum_vlozeni" date,
    "overeno" integer,
    "aut_email_stav" "char",
    "aut_email_datum" date,
    "aut_sms_stav" "char",
    "aut_sms_datum" date,
    "ignorovat" "char",
    "po_splatnosti_vlastnik" "char",
    CONSTRAINT "faktury_neuhrazene_pkey" PRIMARY KEY ("id"),
    CONSTRAINT "id_unique" UNIQUE ("id", "id")
) WITH (oids = false);

INSERT INTO "faktury_neuhrazene" ("id", "cislo", "varsym", "datum", "datsplat", "kccelkem", "kclikv", "firma", "jmeno", "ico", "dic", "par_id_vlastnika", "par_stav", "datum_vlozeni", "overeno", "aut_email_stav", "aut_email_datum", "aut_sms_stav", "aut_sms_datum", "ignorovat", "po_splatnosti_vlastnik") VALUES
(1,	'1',	'1',	'2024-04-09',	'2024-04-09',	'1',	'1',	'H',	'P',	'1',	'2',	1,	'0',	'2024-04-09',	0,	'0',	'2024-04-09',	NULL,	NULL,	'0',	'');

DROP TABLE IF EXISTS "objekty";
CREATE TABLE "public"."objekty" (
    "dov_net" character(1) DEFAULT 'a' NOT NULL,
    "sikana_status" character(1) DEFAULT 'n' NOT NULL,
    "sikana_text" character(250),
    "id_cloveka" integer,
    "dns_jmeno" character(150),
    "id_tarifu" integer,
    "id_nodu" integer DEFAULT '0',
    "pridano" timestamp,
    "ip" inet,
    "mac" macaddr,
    "id_tridy" integer DEFAULT '0',
    "verejna" integer DEFAULT '99',
    "typ" character(1) DEFAULT '1',
    "poznamka" character(4096),
    "pridal" character(50),
    "sikana_cas" integer DEFAULT '0' NOT NULL,
    "client_ap_ip" inet,
    "id_komplu" integer DEFAULT GENERATED BY DEFAULT AS IDENTITY NOT NULL,
    "port_id" integer DEFAULT '0' NOT NULL,
    "another_vlan_id" integer,
    CONSTRAINT "objekty_id_komplu_pk" PRIMARY KEY ("id_komplu")
) WITH (oids = false);

INSERT INTO "objekty" ("dov_net", "sikana_status", "sikana_text", "id_cloveka", "dns_jmeno", "id_tarifu", "id_nodu", "pridano", "ip", "mac", "id_tridy", "verejna", "typ", "poznamka", "pridal", "sikana_cas", "client_ap_ip", "id_komplu", "port_id", "another_vlan_id") VALUES
('a',	'n',	'                                                                                                                                                                                                                                                          ',	2,	'test-wifi-1                                                                                                                                           ',	1,	1,	NULL,	'10.10.10.4',	'11:22:33:44:55:66',	0,	99,	'1',	'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ',	'admin@admin                                       ',	0,	NULL,	1,	0,	0),
('a',	'n',	'                                                                                                                                                                                                                                                          ',	NULL,	'test-fiber-1                                                                                                                                          ',	3,	2,	NULL,	'10.10.10.12',	'11:22:33:44:55:66',	0,	99,	'1',	'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ',	'admin@admin                                       ',	0,	NULL,	2,	1,	NULL);

DROP TABLE IF EXISTS "vlastnici";
DROP SEQUENCE IF EXISTS vlastnici_id_cloveka_seq;
CREATE SEQUENCE vlastnici_id_cloveka_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 8 CACHE 1;

CREATE TABLE "public"."vlastnici" (
    "id_cloveka" integer DEFAULT nextval('vlastnici_id_cloveka_seq') NOT NULL,
    "billing_suspend_status" integer,
    "archiv" integer,
    "ulice" character(100),
    "mesto" character(100),
    "vs" character(50),
    "icq" character(50),
    "mail" character(50),
    "telefon" character(50),
    "fakturacni_skupina_id" integer,
    "nick" character(150),
    "psc" character(50),
    "firma" integer,
    "k_platbe" integer,
    "ucetni_index" integer,
    "poznamka" text,
    "fakturacni" integer,
    "pridano" date,
    "billing_suspend_start" date,
    "billing_suspend_stop" date,
    "billing_freq" integer DEFAULT '0',
    "jmeno" character(100),
    "prijmeni" character(100),
    "splatnost" integer,
    "trvani_do" date,
    "sluzba_int" integer DEFAULT '0',
    "sluzba_iptv" integer DEFAULT '0',
    "sluzba_voip" integer DEFAULT '0',
    "datum_podpisu" date,
    "typ_smlouvy" integer DEFAULT '0',
    "billing_suspend_reason" character(100),
    CONSTRAINT "nick_unique" UNIQUE ("nick"),
    CONSTRAINT "vlastnici_pkey" PRIMARY KEY ("id_cloveka")
) WITH (oids = false);

INSERT INTO "vlastnici" ("id_cloveka", "billing_suspend_status", "archiv", "ulice", "mesto", "vs", "icq", "mail", "telefon", "fakturacni_skupina_id", "nick", "psc", "firma", "k_platbe", "ucetni_index", "poznamka", "fakturacni", "pridano", "billing_suspend_start", "billing_suspend_stop", "billing_freq", "jmeno", "prijmeni", "splatnost", "trvani_do", "sluzba_int", "sluzba_iptv", "sluzba_voip", "datum_podpisu", "typ_smlouvy", "billing_suspend_reason") VALUES
(3,	NULL,	NULL,	'Hrad 1                                                                                              ',	'Praha                                                                                               ',	'333                                               ',	NULL,	'pavel@hrad.gov.cz                                 ',	'800888882                                         ',	1,	'petrp2                                                                                                                                                ',	'11000                                             ',	1,	2500,	222,	'prezident 2',	NULL,	NULL,	NULL,	NULL,	0,	'Petr                                                                                                ',	'Pavel2                                                                                              ',	15,	NULL,	0,	0,	0,	NULL,	0,	NULL),
(1,	0,	0,	'Nova Ulice 1                                                                                        ',	'Praha                                                                                               ',	'1111                                              ',	'                                                  ',	'hu@hu.hu                                          ',	'123456789                                         ',	NULL,	'petrn                                                                                                                                                 ',	'11111                                             ',	NULL,	0,	111,	'test poznamka',	NULL,	NULL,	NULL,	NULL,	0,	'Petr                                                                                                ',	'Novak                                                                                               ',	15,	NULL,	0,	0,	0,	NULL,	0,	NULL),
(8,	0,	1,	'Hrad 1                                                                                              ',	'Praha                                                                                               ',	'333                                               ',	'                                                  ',	'pavel@hrad.gov.cz                                 ',	'800888882                                         ',	1,	'petrp3                                                                                                                                                ',	'11000                                             ',	1,	2500,	222,	'prezident 2',	NULL,	NULL,	NULL,	NULL,	0,	'Petr                                                                                                ',	'Pavel Archivni                                                                                      ',	15,	NULL,	0,	0,	0,	NULL,	0,	NULL),
(2,	0,	0,	'Hrad 1                                                                                              ',	'Praha                                                                                               ',	'222                                               ',	'                                                  ',	'pavel@hrad.gov.cz                                 ',	'800888888                                         ',	16,	'petrp                                                                                                                                                 ',	'11000                                             ',	1,	250,	222,	'prezident',	2,	NULL,	NULL,	NULL,	0,	'Petr                                                                                                ',	'Pavel Fakturacni                                                                                    ',	15,	NULL,	0,	0,	0,	NULL,	0,	NULL);

-- 2024-04-28 18:44:33.724064+00