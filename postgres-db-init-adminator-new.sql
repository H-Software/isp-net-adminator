--
-- PostgreSQL database dump
--

-- Dumped from database version 12.18
-- Dumped by pg_dump version 12.18

-- Started on 2024-04-22 15:29:13 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 3074 (class 1262 OID 16384)
-- Name: adminator.new; Type: DATABASE; Schema: -; Owner: adminator
--

CREATE DATABASE "adminator.new" WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.utf8' LC_CTYPE = 'en_US.utf8';


ALTER DATABASE "adminator.new" OWNER TO adminator;

\connect "adminator.new"

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 202 (class 1259 OID 16385)
-- Name: fakturacni; Type: TABLE; Schema: public; Owner: adminator
--

CREATE TABLE public.fakturacni (
    id integer NOT NULL,
    ftitle text,
    fulice text,
    fmesto text,
    fpsc text,
    ico text,
    dic text,
    ucet text,
    splatnost text,
    cetnost text
);


ALTER TABLE public.fakturacni OWNER TO adminator;

--
-- TOC entry 208 (class 1259 OID 49155)
-- Name: fakturacni_id_seq; Type: SEQUENCE; Schema: public; Owner: adminator
--

CREATE SEQUENCE public.fakturacni_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.fakturacni_id_seq OWNER TO adminator;

--
-- TOC entry 209 (class 1259 OID 49158)
-- Name: fakturacni_id_seq1; Type: SEQUENCE; Schema: public; Owner: adminator
--

ALTER TABLE public.fakturacni ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.fakturacni_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 203 (class 1259 OID 16391)
-- Name: faktury_neuhrazene; Type: TABLE; Schema: public; Owner: adminator
--

CREATE TABLE public.faktury_neuhrazene (
    id integer NOT NULL,
    cislo "char",
    varsym "char",
    datum date,
    datsplat date,
    kccelkem "char",
    kclikv "char",
    firma "char",
    jmeno "char",
    ico "char",
    dic "char",
    par_id_vlastnika integer,
    par_stav "char",
    datum_vlozeni date,
    overeno integer,
    aut_email_stav "char",
    aut_email_datum date,
    aut_sms_stav "char",
    aut_sms_datum date,
    ignorovat "char",
    po_splatnosti_vlastnik "char"
);


ALTER TABLE public.faktury_neuhrazene OWNER TO adminator;

--
-- TOC entry 204 (class 1259 OID 16394)
-- Name: objekty; Type: TABLE; Schema: public; Owner: adminator
--

CREATE TABLE public.objekty (
    id_komplu integer NOT NULL,
    dov_net "char",
    sikana_status "char",
    sikana_text character(250),
    id_cloveka integer,
    dns_jmeno character(150),
    id_tarifu integer,
    id_nodu integer DEFAULT 0,
    typ integer DEFAULT 0,
    pridano timestamp without time zone,
    ip inet,
    mac macaddr,
    id_tridy integer DEFAULT 0,
    verejna integer DEFAULT 99
);


ALTER TABLE public.objekty OWNER TO adminator;

--
-- TOC entry 205 (class 1259 OID 16400)
-- Name: objekty_id_komplu_seq; Type: SEQUENCE; Schema: public; Owner: adminator
--

ALTER TABLE public.objekty ALTER COLUMN id_komplu ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.objekty_id_komplu_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- TOC entry 206 (class 1259 OID 16402)
-- Name: vlastnici; Type: TABLE; Schema: public; Owner: adminator
--

CREATE TABLE public.vlastnici (
    id_cloveka integer NOT NULL,
    billing_suspend_status integer,
    archiv integer,
    ulice character(100),
    mesto character(100),
    vs character(50),
    icq character(50),
    mail character(50),
    telefon character(50),
    fakturacni_skupina_id integer,
    nick character(150),
    psc character(50),
    firma integer,
    k_platbe integer,
    ucetni_index integer,
    poznamka text,
    fakturacni integer,
    pridano date,
    billing_suspend_start date,
    billing_suspend_stop date,
    billing_freq integer DEFAULT 0,
    jmeno character(100),
    prijmeni character(100),
    splatnost integer,
    trvani_do date,
    sluzba_int integer DEFAULT 0,
    sluzba_iptv integer DEFAULT 0,
    sluzba_voip integer DEFAULT 0,
    datum_podpisu date,
    typ_smlouvy integer DEFAULT 0,
    billing_suspend_reason character(100)
);


ALTER TABLE public.vlastnici OWNER TO adminator;

--
-- TOC entry 207 (class 1259 OID 16408)
-- Name: vlastnici_id_cloveka_seq; Type: SEQUENCE; Schema: public; Owner: adminator
--

CREATE SEQUENCE public.vlastnici_id_cloveka_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vlastnici_id_cloveka_seq OWNER TO adminator;

--
-- TOC entry 3075 (class 0 OID 0)
-- Dependencies: 207
-- Name: vlastnici_id_cloveka_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: adminator
--

ALTER SEQUENCE public.vlastnici_id_cloveka_seq OWNED BY public.vlastnici.id_cloveka;


--
-- TOC entry 2918 (class 2604 OID 16410)
-- Name: vlastnici id_cloveka; Type: DEFAULT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.vlastnici ALTER COLUMN id_cloveka SET DEFAULT nextval('public.vlastnici_id_cloveka_seq'::regclass);


--
-- TOC entry 3061 (class 0 OID 16385)
-- Dependencies: 202
-- Data for Name: fakturacni; Type: TABLE DATA; Schema: public; Owner: adminator
--

INSERT INTO public.fakturacni (id, ftitle, fulice, fmesto, fpsc, ico, dic, ucet, splatnost, cetnost) OVERRIDING SYSTEM VALUE VALUES (2, 'Hradni kancelar', 'Hrad 1', 'Praha', '11111', '', '', '', '15', '1');


--
-- TOC entry 3062 (class 0 OID 16391)
-- Dependencies: 203
-- Data for Name: faktury_neuhrazene; Type: TABLE DATA; Schema: public; Owner: adminator
--

INSERT INTO public.faktury_neuhrazene (id, cislo, varsym, datum, datsplat, kccelkem, kclikv, firma, jmeno, ico, dic, par_id_vlastnika, par_stav, datum_vlozeni, overeno, aut_email_stav, aut_email_datum, aut_sms_stav, aut_sms_datum, ignorovat, po_splatnosti_vlastnik) VALUES (1, '1', '1', '2024-04-09', '2024-04-09', '1', '1', 'H', 'P', '1', '2', 1, '0', '2024-04-09', 0, '0', '2024-04-09', NULL, NULL, '0', '');


--
-- TOC entry 3063 (class 0 OID 16394)
-- Dependencies: 204
-- Data for Name: objekty; Type: TABLE DATA; Schema: public; Owner: adminator
--

INSERT INTO public.objekty (id_komplu, dov_net, sikana_status, sikana_text, id_cloveka, dns_jmeno, id_tarifu, id_nodu, typ, pridano, ip, mac, id_tridy, verejna) OVERRIDING SYSTEM VALUE VALUES (1, 'a', NULL, NULL, 1, 'objekt test 1                                                                                                                                         ', 1, 1, 2, NULL, '1.1.1.1', NULL, 0, 99);


--
-- TOC entry 3065 (class 0 OID 16402)
-- Dependencies: 206
-- Data for Name: vlastnici; Type: TABLE DATA; Schema: public; Owner: adminator
--

INSERT INTO public.vlastnici (id_cloveka, billing_suspend_status, archiv, ulice, mesto, vs, icq, mail, telefon, fakturacni_skupina_id, nick, psc, firma, k_platbe, ucetni_index, poznamka, fakturacni, pridano, billing_suspend_start, billing_suspend_stop, billing_freq, jmeno, prijmeni, splatnost, trvani_do, sluzba_int, sluzba_iptv, sluzba_voip, datum_podpisu, typ_smlouvy, billing_suspend_reason) VALUES (3, NULL, NULL, 'Hrad 1                                                                                              ', 'Praha                                                                                               ', '333                                               ', NULL, 'pavel@hrad.gov.cz                                 ', '800888882                                         ', 1, 'petrp2                                                                                                                                                ', '11000                                             ', 1, 2500, 222, 'prezident 2', NULL, NULL, NULL, NULL, 0, 'Petr                                                                                                ', 'Pavel2                                                                                              ', 15, NULL, 0, 0, 0, NULL, 0, NULL);
INSERT INTO public.vlastnici (id_cloveka, billing_suspend_status, archiv, ulice, mesto, vs, icq, mail, telefon, fakturacni_skupina_id, nick, psc, firma, k_platbe, ucetni_index, poznamka, fakturacni, pridano, billing_suspend_start, billing_suspend_stop, billing_freq, jmeno, prijmeni, splatnost, trvani_do, sluzba_int, sluzba_iptv, sluzba_voip, datum_podpisu, typ_smlouvy, billing_suspend_reason) VALUES (1, 0, 0, 'Nova Ulice 1                                                                                        ', 'Praha                                                                                               ', '1111                                              ', '                                                  ', 'hu@hu.hu                                          ', '123456789                                         ', NULL, 'petrn                                                                                                                                                 ', '11111                                             ', NULL, 0, 111, 'test poznamka', NULL, NULL, NULL, NULL, 0, 'Petr                                                                                                ', 'Novak                                                                                               ', 15, NULL, 0, 0, 0, NULL, 0, NULL);
INSERT INTO public.vlastnici (id_cloveka, billing_suspend_status, archiv, ulice, mesto, vs, icq, mail, telefon, fakturacni_skupina_id, nick, psc, firma, k_platbe, ucetni_index, poznamka, fakturacni, pridano, billing_suspend_start, billing_suspend_stop, billing_freq, jmeno, prijmeni, splatnost, trvani_do, sluzba_int, sluzba_iptv, sluzba_voip, datum_podpisu, typ_smlouvy, billing_suspend_reason) VALUES (8, 0, 1, 'Hrad 1                                                                                              ', 'Praha                                                                                               ', '333                                               ', '                                                  ', 'pavel@hrad.gov.cz                                 ', '800888882                                         ', 1, 'petrp3                                                                                                                                                ', '11000                                             ', 1, 2500, 222, 'prezident 2', NULL, NULL, NULL, NULL, 0, 'Petr                                                                                                ', 'Pavel Archivni                                                                                      ', 15, NULL, 0, 0, 0, NULL, 0, NULL);
INSERT INTO public.vlastnici (id_cloveka, billing_suspend_status, archiv, ulice, mesto, vs, icq, mail, telefon, fakturacni_skupina_id, nick, psc, firma, k_platbe, ucetni_index, poznamka, fakturacni, pridano, billing_suspend_start, billing_suspend_stop, billing_freq, jmeno, prijmeni, splatnost, trvani_do, sluzba_int, sluzba_iptv, sluzba_voip, datum_podpisu, typ_smlouvy, billing_suspend_reason) VALUES (2, 0, 0, 'Hrad 1                                                                                              ', 'Praha                                                                                               ', '222                                               ', '                                                  ', 'pavel@hrad.gov.cz                                 ', '800888888                                         ', 16, 'petrp                                                                                                                                                 ', '11000                                             ', 1, 250, 222, 'prezident', 2, NULL, NULL, NULL, 0, 'Petr                                                                                                ', 'Pavel Fakturacni                                                                                    ', 15, NULL, 0, 0, 0, NULL, 0, NULL);


--
-- TOC entry 3076 (class 0 OID 0)
-- Dependencies: 208
-- Name: fakturacni_id_seq; Type: SEQUENCE SET; Schema: public; Owner: adminator
--

SELECT pg_catalog.setval('public.fakturacni_id_seq', 1, false);


--
-- TOC entry 3077 (class 0 OID 0)
-- Dependencies: 209
-- Name: fakturacni_id_seq1; Type: SEQUENCE SET; Schema: public; Owner: adminator
--

SELECT pg_catalog.setval('public.fakturacni_id_seq1', 2, true);


--
-- TOC entry 3078 (class 0 OID 0)
-- Dependencies: 205
-- Name: objekty_id_komplu_seq; Type: SEQUENCE SET; Schema: public; Owner: adminator
--

SELECT pg_catalog.setval('public.objekty_id_komplu_seq', 1, true);


--
-- TOC entry 3079 (class 0 OID 0)
-- Dependencies: 207
-- Name: vlastnici_id_cloveka_seq; Type: SEQUENCE SET; Schema: public; Owner: adminator
--

SELECT pg_catalog.setval('public.vlastnici_id_cloveka_seq', 8, true);


--
-- TOC entry 2925 (class 2606 OID 16412)
-- Name: fakturacni fakturacni_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.fakturacni
    ADD CONSTRAINT fakturacni_pkey PRIMARY KEY (id);


--
-- TOC entry 2927 (class 2606 OID 16414)
-- Name: faktury_neuhrazene faktury_neuhrazene_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.faktury_neuhrazene
    ADD CONSTRAINT faktury_neuhrazene_pkey PRIMARY KEY (id);


--
-- TOC entry 2932 (class 2606 OID 49154)
-- Name: vlastnici nick_unique; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.vlastnici
    ADD CONSTRAINT nick_unique UNIQUE (nick);


--
-- TOC entry 2930 (class 2606 OID 16416)
-- Name: objekty objekty_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.objekty
    ADD CONSTRAINT objekty_pkey PRIMARY KEY (id_komplu);


--
-- TOC entry 2934 (class 2606 OID 16418)
-- Name: vlastnici vlastnici_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.vlastnici
    ADD CONSTRAINT vlastnici_pkey PRIMARY KEY (id_cloveka);


--
-- TOC entry 2928 (class 1259 OID 16419)
-- Name: id_unique; Type: INDEX; Schema: public; Owner: adminator
--

CREATE UNIQUE INDEX id_unique ON public.faktury_neuhrazene USING btree (id) INCLUDE (id);


-- Completed on 2024-04-22 15:29:13 UTC

--
-- PostgreSQL database dump complete
--

