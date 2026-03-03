--
-- PostgreSQL database dump
--

-- Dumped from database version 15.2
-- Dumped by pg_dump version 17.5

-- Started on 2026-02-27 19:12:12

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
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
-- TOC entry 227 (class 1259 OID 16502)
-- Name: horarios_terapeuta; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.horarios_terapeuta (
    id integer NOT NULL,
    terapeuta_id integer NOT NULL,
    dia_semana integer NOT NULL,
    hora_inicio time without time zone NOT NULL,
    hora_fin time without time zone NOT NULL,
    CONSTRAINT chk_horas_horario CHECK ((hora_fin > hora_inicio)),
    CONSTRAINT horarios_terapeuta_dia_semana_check CHECK (((dia_semana >= 0) AND (dia_semana <= 6)))
);


ALTER TABLE public.horarios_terapeuta OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 16501)
-- Name: horarios_terapeuta_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.horarios_terapeuta_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.horarios_terapeuta_id_seq OWNER TO postgres;

--
-- TOC entry 3419 (class 0 OID 0)
-- Dependencies: 226
-- Name: horarios_terapeuta_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.horarios_terapeuta_id_seq OWNED BY public.horarios_terapeuta.id;


--
-- TOC entry 225 (class 1259 OID 16486)
-- Name: pagos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pagos (
    id integer NOT NULL,
    reserva_id integer NOT NULL,
    monto numeric(10,2) NOT NULL,
    metodo_pago character varying(50),
    pagado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pagos_monto_check CHECK ((monto >= (0)::numeric))
);


ALTER TABLE public.pagos OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16485)
-- Name: pagos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pagos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pagos_id_seq OWNER TO postgres;

--
-- TOC entry 3420 (class 0 OID 0)
-- Dependencies: 224
-- Name: pagos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pagos_id_seq OWNED BY public.pagos.id;


--
-- TOC entry 223 (class 1259 OID 16450)
-- Name: reservas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.reservas (
    id integer NOT NULL,
    cliente_id integer NOT NULL,
    terapeuta_id integer NOT NULL,
    servicio_id integer NOT NULL,
    sala_id integer,
    fecha date NOT NULL,
    hora_inicio time without time zone NOT NULL,
    hora_fin time without time zone NOT NULL,
    estado character varying(30) DEFAULT 'PENDIENTE'::character varying,
    creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_estado_valido CHECK (((estado)::text = ANY ((ARRAY['PENDIENTE'::character varying, 'CONFIRMADA'::character varying, 'CANCELADA'::character varying, 'COMPLETADA'::character varying])::text[]))),
    CONSTRAINT chk_horas_validas CHECK ((hora_fin > hora_inicio))
);


ALTER TABLE public.reservas OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16449)
-- Name: reservas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.reservas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.reservas_id_seq OWNER TO postgres;

--
-- TOC entry 3421 (class 0 OID 0)
-- Dependencies: 222
-- Name: reservas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.reservas_id_seq OWNED BY public.reservas.id;


--
-- TOC entry 215 (class 1259 OID 16399)
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    nombre character varying(50) NOT NULL
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- TOC entry 214 (class 1259 OID 16398)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO postgres;

--
-- TOC entry 3422 (class 0 OID 0)
-- Dependencies: 214
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 221 (class 1259 OID 16440)
-- Name: salas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.salas (
    id integer NOT NULL,
    nombre character varying(50) NOT NULL,
    capacidad integer DEFAULT 1,
    activa boolean DEFAULT true,
    CONSTRAINT salas_capacidad_check CHECK ((capacidad > 0))
);


ALTER TABLE public.salas OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16439)
-- Name: salas_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.salas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.salas_id_seq OWNER TO postgres;

--
-- TOC entry 3423 (class 0 OID 0)
-- Dependencies: 220
-- Name: salas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.salas_id_seq OWNED BY public.salas.id;


--
-- TOC entry 219 (class 1259 OID 16428)
-- Name: servicios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.servicios (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    duracion_minutos integer NOT NULL,
    precio numeric(10,2) NOT NULL,
    activo boolean DEFAULT true,
    CONSTRAINT servicios_duracion_minutos_check CHECK ((duracion_minutos > 10)),
    CONSTRAINT servicios_precio_check CHECK ((precio >= (0)::numeric))
);


ALTER TABLE public.servicios OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16427)
-- Name: servicios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.servicios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.servicios_id_seq OWNER TO postgres;

--
-- TOC entry 3424 (class 0 OID 0)
-- Dependencies: 218
-- Name: servicios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.servicios_id_seq OWNED BY public.servicios.id;


--
-- TOC entry 217 (class 1259 OID 16408)
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nombre character varying(50) NOT NULL,
    apellido character varying(50),
    email character varying(80) NOT NULL,
    telefono character varying(20),
    password text NOT NULL,
    rol_id integer NOT NULL,
    activo boolean DEFAULT true,
    creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.usuarios OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16407)
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO postgres;

--
-- TOC entry 3425 (class 0 OID 0)
-- Dependencies: 216
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- TOC entry 3217 (class 2604 OID 16505)
-- Name: horarios_terapeuta id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_terapeuta ALTER COLUMN id SET DEFAULT nextval('public.horarios_terapeuta_id_seq'::regclass);


--
-- TOC entry 3215 (class 2604 OID 16489)
-- Name: pagos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos ALTER COLUMN id SET DEFAULT nextval('public.pagos_id_seq'::regclass);


--
-- TOC entry 3212 (class 2604 OID 16453)
-- Name: reservas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas ALTER COLUMN id SET DEFAULT nextval('public.reservas_id_seq'::regclass);


--
-- TOC entry 3203 (class 2604 OID 16402)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 3209 (class 2604 OID 16443)
-- Name: salas id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salas ALTER COLUMN id SET DEFAULT nextval('public.salas_id_seq'::regclass);


--
-- TOC entry 3207 (class 2604 OID 16431)
-- Name: servicios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.servicios ALTER COLUMN id SET DEFAULT nextval('public.servicios_id_seq'::regclass);


--
-- TOC entry 3204 (class 2604 OID 16411)
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- TOC entry 3413 (class 0 OID 16502)
-- Dependencies: 227
-- Data for Name: horarios_terapeuta; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.horarios_terapeuta (id, terapeuta_id, dia_semana, hora_inicio, hora_fin) FROM stdin;
\.


--
-- TOC entry 3411 (class 0 OID 16486)
-- Dependencies: 225
-- Data for Name: pagos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pagos (id, reserva_id, monto, metodo_pago, pagado_en) FROM stdin;
\.


--
-- TOC entry 3409 (class 0 OID 16450)
-- Dependencies: 223
-- Data for Name: reservas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.reservas (id, cliente_id, terapeuta_id, servicio_id, sala_id, fecha, hora_inicio, hora_fin, estado, creado_en) FROM stdin;
\.


--
-- TOC entry 3401 (class 0 OID 16399)
-- Dependencies: 215
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, nombre) FROM stdin;
1	ADMIN
2	RECEPCIONISTA
3	TERAPEUTA
4	CLIENTE
\.


--
-- TOC entry 3407 (class 0 OID 16440)
-- Dependencies: 221
-- Data for Name: salas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.salas (id, nombre, capacidad, activa) FROM stdin;
\.


--
-- TOC entry 3405 (class 0 OID 16428)
-- Dependencies: 219
-- Data for Name: servicios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.servicios (id, nombre, descripcion, duracion_minutos, precio, activo) FROM stdin;
\.


--
-- TOC entry 3403 (class 0 OID 16408)
-- Dependencies: 217
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.usuarios (id, nombre, apellido, email, telefono, password, rol_id, activo, creado_en) FROM stdin;
\.


--
-- TOC entry 3426 (class 0 OID 0)
-- Dependencies: 226
-- Name: horarios_terapeuta_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.horarios_terapeuta_id_seq', 1, false);


--
-- TOC entry 3427 (class 0 OID 0)
-- Dependencies: 224
-- Name: pagos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pagos_id_seq', 1, false);


--
-- TOC entry 3428 (class 0 OID 0)
-- Dependencies: 222
-- Name: reservas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.reservas_id_seq', 1, false);


--
-- TOC entry 3429 (class 0 OID 0)
-- Dependencies: 214
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 4, true);


--
-- TOC entry 3430 (class 0 OID 0)
-- Dependencies: 220
-- Name: salas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.salas_id_seq', 1, false);


--
-- TOC entry 3431 (class 0 OID 0)
-- Dependencies: 218
-- Name: servicios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.servicios_id_seq', 1, false);


--
-- TOC entry 3432 (class 0 OID 0)
-- Dependencies: 216
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 1, false);


--
-- TOC entry 3250 (class 2606 OID 16509)
-- Name: horarios_terapeuta horarios_terapeuta_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_terapeuta
    ADD CONSTRAINT horarios_terapeuta_pkey PRIMARY KEY (id);


--
-- TOC entry 3246 (class 2606 OID 16493)
-- Name: pagos pagos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_pkey PRIMARY KEY (id);


--
-- TOC entry 3248 (class 2606 OID 16495)
-- Name: pagos pagos_reserva_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT pagos_reserva_id_key UNIQUE (reserva_id);


--
-- TOC entry 3244 (class 2606 OID 16459)
-- Name: reservas reservas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT reservas_pkey PRIMARY KEY (id);


--
-- TOC entry 3227 (class 2606 OID 16406)
-- Name: roles roles_nombre_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nombre_key UNIQUE (nombre);


--
-- TOC entry 3229 (class 2606 OID 16404)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 3239 (class 2606 OID 16448)
-- Name: salas salas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.salas
    ADD CONSTRAINT salas_pkey PRIMARY KEY (id);


--
-- TOC entry 3237 (class 2606 OID 16438)
-- Name: servicios servicios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.servicios
    ADD CONSTRAINT servicios_pkey PRIMARY KEY (id);


--
-- TOC entry 3233 (class 2606 OID 16419)
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- TOC entry 3235 (class 2606 OID 16417)
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- TOC entry 3240 (class 1259 OID 16484)
-- Name: idx_reservas_cliente; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reservas_cliente ON public.reservas USING btree (cliente_id);


--
-- TOC entry 3241 (class 1259 OID 16482)
-- Name: idx_reservas_fecha; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reservas_fecha ON public.reservas USING btree (fecha);


--
-- TOC entry 3242 (class 1259 OID 16483)
-- Name: idx_reservas_terapeuta_fecha; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reservas_terapeuta_fecha ON public.reservas USING btree (terapeuta_id, fecha);


--
-- TOC entry 3230 (class 1259 OID 16426)
-- Name: idx_usuarios_email; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_usuarios_email ON public.usuarios USING btree (email);


--
-- TOC entry 3231 (class 1259 OID 16425)
-- Name: idx_usuarios_rol; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_usuarios_rol ON public.usuarios USING btree (rol_id);


--
-- TOC entry 3257 (class 2606 OID 16510)
-- Name: horarios_terapeuta fk_horario_terapeuta; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.horarios_terapeuta
    ADD CONSTRAINT fk_horario_terapeuta FOREIGN KEY (terapeuta_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3256 (class 2606 OID 16496)
-- Name: pagos fk_pago_reserva; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pagos
    ADD CONSTRAINT fk_pago_reserva FOREIGN KEY (reserva_id) REFERENCES public.reservas(id) ON DELETE CASCADE;


--
-- TOC entry 3252 (class 2606 OID 16460)
-- Name: reservas fk_reserva_cliente; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT fk_reserva_cliente FOREIGN KEY (cliente_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- TOC entry 3253 (class 2606 OID 16475)
-- Name: reservas fk_reserva_sala; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT fk_reserva_sala FOREIGN KEY (sala_id) REFERENCES public.salas(id) ON DELETE SET NULL;


--
-- TOC entry 3254 (class 2606 OID 16470)
-- Name: reservas fk_reserva_servicio; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT fk_reserva_servicio FOREIGN KEY (servicio_id) REFERENCES public.servicios(id) ON DELETE RESTRICT;


--
-- TOC entry 3255 (class 2606 OID 16465)
-- Name: reservas fk_reserva_terapeuta; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservas
    ADD CONSTRAINT fk_reserva_terapeuta FOREIGN KEY (terapeuta_id) REFERENCES public.usuarios(id) ON DELETE RESTRICT;


--
-- TOC entry 3251 (class 2606 OID 16420)
-- Name: usuarios fk_usuario_rol; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES public.roles(id) ON DELETE RESTRICT;


-- Completed on 2026-02-27 19:12:12

--
-- PostgreSQL database dump complete
--

