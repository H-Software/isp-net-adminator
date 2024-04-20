--
-- PostgreSQL database cluster dump
--

-- Started on 2024-04-12 17:35:13 UTC

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Drop databases (except postgres and template1)
--

DROP DATABASE "adminator.new";


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
-- TOC entry 3057 (class 1262 OID 16384)
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
-- TOC entry 207 (class 1259 OID 16413)
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
-- TOC entry 202 (class 1259 OID 16385)
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
-- TOC entry 204 (class 1259 OID 16398)
-- Name: objekty; Type: TABLE; Schema: public; Owner: adminator
--

CREATE TABLE public.objekty (
    id_komplu integer NOT NULL,
    ip "char",
    dov_net "char",
    sikana_status "char",
    sikana_text "char",
    id_cloveka integer,
    dns_jmeno text,
    id_tarifu integer
);


ALTER TABLE public.objekty OWNER TO adminator;

--
-- TOC entry 205 (class 1259 OID 16403)
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
-- TOC entry 203 (class 1259 OID 16393)
-- Name: vlastnici; Type: TABLE; Schema: public; Owner: adminator
--

CREATE TABLE public.vlastnici (
    id_cloveka integer NOT NULL,
    jmeno "char",
    prijmeni "char",
    billing_suspend_status integer,
    archiv integer,
    ulice "char",
    mesto "char",
    vs "char",
    icq "char",
    mail "char",
    telefon "char",
    fakturacni_skupina_id integer,
    nick "char",
    psc "char",
    firma integer,
    k_platbe integer,
    ucetni_index integer,
    poznamka text,
    fakturacni integer,
    pridano date,
    billing_suspend_start date,
    billing_suspend_stop date
);


ALTER TABLE public.vlastnici OWNER TO adminator;

--
-- TOC entry 206 (class 1259 OID 16407)
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
-- TOC entry 3058 (class 0 OID 0)
-- Dependencies: 206
-- Name: vlastnici_id_cloveka_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: adminator
--

ALTER SEQUENCE public.vlastnici_id_cloveka_seq OWNED BY public.vlastnici.id_cloveka;


--
-- TOC entry 2910 (class 2604 OID 16409)
-- Name: vlastnici id_cloveka; Type: DEFAULT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.vlastnici ALTER COLUMN id_cloveka SET DEFAULT nextval('public.vlastnici_id_cloveka_seq'::regclass);


--
-- TOC entry 3051 (class 0 OID 16413)
-- Dependencies: 207
-- Data for Name: fakturacni; Type: TABLE DATA; Schema: public; Owner: adminator
--



--
-- TOC entry 3046 (class 0 OID 16385)
-- Dependencies: 202
-- Data for Name: faktury_neuhrazene; Type: TABLE DATA; Schema: public; Owner: adminator
--

INSERT INTO public.faktury_neuhrazene (id, cislo, varsym, datum, datsplat, kccelkem, kclikv, firma, jmeno, ico, dic, par_id_vlastnika, par_stav, datum_vlozeni, overeno, aut_email_stav, aut_email_datum, aut_sms_stav, aut_sms_datum, ignorovat, po_splatnosti_vlastnik) VALUES (1, '1', '1', '2024-04-09', '2024-04-09', '1', '1', 'H', 'P', '1', '2', 1, '0', '2024-04-09', 0, '0', '2024-04-09', NULL, NULL, '0', '');


--
-- TOC entry 3048 (class 0 OID 16398)
-- Dependencies: 204
-- Data for Name: objekty; Type: TABLE DATA; Schema: public; Owner: adminator
--



--
-- TOC entry 3047 (class 0 OID 16393)
-- Dependencies: 203
-- Data for Name: vlastnici; Type: TABLE DATA; Schema: public; Owner: adminator
--

INSERT INTO public.vlastnici (id_cloveka, jmeno, prijmeni, billing_suspend_status, archiv, ulice, mesto, vs, icq, mail, telefon, fakturacni_skupina_id, nick, psc, firma, k_platbe, ucetni_index, poznamka, fakturacni, pridano, billing_suspend_start, billing_suspend_stop) VALUES (1, 'P', 'N', NULL, 0, 'P', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


--
-- TOC entry 3059 (class 0 OID 0)
-- Dependencies: 205
-- Name: objekty_id_komplu_seq; Type: SEQUENCE SET; Schema: public; Owner: adminator
--

SELECT pg_catalog.setval('public.objekty_id_komplu_seq', 1, false);


--
-- TOC entry 3060 (class 0 OID 0)
-- Dependencies: 206
-- Name: vlastnici_id_cloveka_seq; Type: SEQUENCE SET; Schema: public; Owner: adminator
--

SELECT pg_catalog.setval('public.vlastnici_id_cloveka_seq', 1, true);


--
-- TOC entry 2919 (class 2606 OID 16420)
-- Name: fakturacni fakturacni_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.fakturacni
    ADD CONSTRAINT fakturacni_pkey PRIMARY KEY (id);


--
-- TOC entry 2912 (class 2606 OID 16391)
-- Name: faktury_neuhrazene faktury_neuhrazene_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.faktury_neuhrazene
    ADD CONSTRAINT faktury_neuhrazene_pkey PRIMARY KEY (id);


--
-- TOC entry 2917 (class 2606 OID 16402)
-- Name: objekty objekty_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.objekty
    ADD CONSTRAINT objekty_pkey PRIMARY KEY (id_komplu);


--
-- TOC entry 2915 (class 2606 OID 16397)
-- Name: vlastnici vlastnici_pkey; Type: CONSTRAINT; Schema: public; Owner: adminator
--

ALTER TABLE ONLY public.vlastnici
    ADD CONSTRAINT vlastnici_pkey PRIMARY KEY (id_cloveka);


--
-- TOC entry 2913 (class 1259 OID 16392)
-- Name: id_unique; Type: INDEX; Schema: public; Owner: adminator
--

CREATE UNIQUE INDEX id_unique ON public.faktury_neuhrazene USING btree (id) INCLUDE (id);


-- Completed on 2024-04-12 17:35:13 UTC

--
-- PostgreSQL database dump complete
--

--
-- Database "postgres" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 12.18
-- Dumped by pg_dump version 12.18

-- Started on 2024-04-12 17:35:13 UTC

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

DROP DATABASE postgres;
--
-- TOC entry 3018 (class 1262 OID 13524)
-- Name: postgres; Type: DATABASE; Schema: -; Owner: adminator
--

CREATE DATABASE postgres WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.utf8' LC_CTYPE = 'en_US.utf8';


ALTER DATABASE postgres OWNER TO adminator;

\connect postgres

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
-- TOC entry 3019 (class 0 OID 0)
-- Dependencies: 3018
-- Name: DATABASE postgres; Type: COMMENT; Schema: -; Owner: adminator
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


-- Completed on 2024-04-12 17:35:14 UTC

--
-- PostgreSQL database dump complete
--

-- Completed on 2024-04-12 17:35:14 UTC

--
-- PostgreSQL database cluster dump complete
--

