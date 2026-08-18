/*La base de datos propuesta en este script está basada principalemtne en el formulario de ficha de identificación
  utilizado por el Departamento de Trabajo Social del CECyTEZ Río Grande (la mayoría de las tablas), además de información 
  adicional que requiere el susodicho (secundaria de procedencia y grupo de pertenencia). Y finalmente, una tabla para
  los usuarios del sistema (basada en la que por defecto ofrece Laravel pero adaptada).

  Los títulos con número y mayúsculas corresponden a las secciones principales del formulario y sólo se añadieron como
  referencia durante el proceso de codificación del script.*/

create database db_cecytez_prueba character set utf8mb4	collate utf8mb4_unicode_ci;
use db_cecytez_prueba; -- Aún no se integran estas tablas en la BD del proyecto y se creó esta aparte para experimentar.
-- Tablas padre -----------------------------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido1` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido2` varchar(20) COLLATE utf8mb4_unicode_ci,
  `email_personal` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_institucional` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_empleado` int NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pw_recuperacion` bool default false, /*Si el usuario tiene su contraseña de siempre, estará en 0. Pero si la olvida, el
  																				Administrador podrá cambiar el valor a 1, y al hacerlo se ordenará al sistema
  																				crear una contraseña temporal que frozosamente el usuario deberá cambiar después, 
  																				y es entonces cuando regresará a 0.*/
  `tipo` enum('Administrador', 'Directivo', 'Docente', 'Trabajo Social') NOT NULL,
  `privilegios` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL, -- Explicación debajo. <----- MODIFICAR!!!!!!
  `estatus` bool default true,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_personal_unique` (`email_personal`),
  UNIQUE KEY `users_email_institucional_unique` (`email_institucional`),
  UNIQUE KEY `users_num_empleado_unique` (`num_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Los usuarios serán solamente empleados del plantel autorizados a acceder al sistema. Dependiendo del tipo de usuario,
	 sus privilegios en el sistema serán diferentes. Estos se especificarán con una cadena de 3 caracteres usando las
	 siguientes letras: 

   X: Restringido.
   U: Gestión de usuarios (todas las operaciones CRUD, pero sólo en usuarios).
   T: Acceso a la información de todos los grados y grupos.
   P: Acceso a la información de sólo un grupo.
   D: Acceso a la información de sólo un grado.

   La lógica funciona así: la letra especifica el alcance del usuario en el sistema, pero la posición de la letra especifica
   qué operaciones CRUD puede hacer dentro de ese rango. 

   Las posiciones son estas: | Manejo de usuarios | Consultar información | Editar información |

   Sólo el usuario Administrador puede registrar a los usuarios, asignar privilegios, y autorizar el cambio de contraseña.
   Por lo general, una combinación específica de letras va a estar asociada a un tipo específico de usuario, pero por
   razones extraordinarias el Administrador puede cambiar temporalmente los privilegios.

   Por ejemplo, los privilegios del Administrador casi siempre serán "UXX", de un Directivo "XTX", de Trabajo Social "XDD"
   y un Docente "XDX". Pero, pudiera ser que un Directivo temporalmente administre los usuarios, y sería "UTX".*/
-- ------------------------------------------------------------------------------------------------------------------------
create table grupos(
	id int AUTO_INCREMENT PRIMARY KEY,
	semestre enum('1°', '2°', '3°', '4°', '5°', '6°') not null,
	grupo enum( 'A', 'B') not null,
	carrera enum('Soporte y Mantenimiento de Equipo de Cómputo', 
							 'Soporte y Gestión de Tecnologías Informáticas',
							 'Enfermería General',
							 'Ventas',
							 'Diseño Gráfico Digital') not null,
	id_asesor bigint unsigned unique, -- El asesor el es docente asignado al grupo (lo que en el ITSZN es el tutor).
	id_tutor bigint unsigned, /*El tutor en este caso es el empleado de Trabajo Social que tiene asignada la gestión de todo
															un grado o par de semestres (pero abarcando todas las carreras).*/

	foreign key (id_asesor) references users(id) ON DELETE SET NULL,
	foreign key (id_tutor) references users(id) ON DELETE SET NULL
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table lugar_nacimiento(
	id int AUTO_INCREMENT PRIMARY KEY,
	localidad varchar(30) not null,
	municipio varchar(30) not null,
	estado varchar(20) not null,
	pais varchar(15) not null,

	unique key unique_lugar_nacimiento(localidad, municipio, estado, pais)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table domicilio(
	id int AUTO_INCREMENT PRIMARY KEY,
	calle varchar(40) not null,
	num_ext int not null,
	num_int int,
	colonia varchar(40) not null,
	localidad varchar(30) not null,
	municipio varchar(30) not null,
	cp varchar(5) not null,
	estado varchar(20) not null,
	telefono_domicilio varchar(10)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
CREATE TABLE secundaria_procedencia(
  id int AUTO_INCREMENT PRIMARY KEY,
  nombre varchar(100) not null,
  tipo enum('General', 'Técnica', 'Telesecundaria', 'Abierta', 'Privada', 'Otro') not null,
  localidad varchar(30) not null,
  municipio varchar(30) not null,
  estado varchar(20) not null,
  pais varchar(15) not null,

  UNIQUE KEY unique_secundaria(nombre, localidad, municipio, estado)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table alumnos(
	id int AUTO_INCREMENT PRIMARY KEY,
	foto varchar(5), /*Se almacenará el nombre del archivo JPG de la foto (el sistema asignará automáticamente un nombre de 
									   5 caracteres para distinción); el directorio será configurado por defecto por el mismo sistema.*/
	num_control varchar(14) UNIQUE,
	-- 1.- DATOS PERSONALES DEL ALUMNO
	nombre varchar(30) not null,
	apellido1 varchar(20) not null,
	apellido2 varchar(20),
	id_lugar_nacimiento int,
	id_domicilio int,
	telefono_celular varchar(10),
	email_personal varchar(50),
	email_institucional varchar(50),
	curp varchar(18) UNIQUE,
	nss varchar(11),
	id_grupo int,
	estatus bool,
	id_secundaria_procedencia int,

	foreign key (id_lugar_nacimiento) references lugar_nacimiento(id),
	foreign key (id_domicilio) references domicilio(id),
	foreign key (id_grupo) references grupo(id) ON DELETE SET NULL,
	foreign key (id_secundaria_procedencia) references secundaria_procedencia(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table becas(
	id int AUTO_INCREMENT PRIMARY KEY,
	tipo_beca varchar(20) not null,
	descripcion varchar(255),

	unique key unique_beca(tipo_beca)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table alumno_beca(
	id int AUTO_INCREMENT PRIMARY KEY,
  id_alumno int NOT NULL,
  id_beca int NOT NULL,
  activa bool,

  FOREIGN KEY (id_alumno) REFERENCES alumnos(id) ON DELETE CASCADE,
  FOREIGN KEY (id_beca) REFERENCES becas(id) ON DELETE CASCADE,
	UNIQUE KEY unique_alumno_beca (id_alumno, id_beca)
) character set utf8mb4 collate utf8mb4_unicode_ci;

/*Tabla pivote para relacionar al alumno con una beca si la posee. Un alumno puede tener más de una beca y una misma beca
  puede ser otorgada a varios alumnos. Si la beca está inactiva no necesariamente implica que se le cancelará
  definitivamente, además de que el estatus de la beca puede ser distinto para cada alumno.*/
-- ------------------------------------------------------------------------------------------------------------------------
create table trabajo_alumno(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	lugar_trabajo varchar(30),
	horario_laboral varchar(50),
	domicilio_trabajo varchar(100),
	telefono_trabajo varchar(10),
    
  FOREIGN KEY (id_alumno) REFERENCES alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;

/*Se optó por poner esta información a parte debido a que no todos los alumnos tendrán un trabajo, a diferencia de los
	familiares, quienes es más probable que sí tengan.*/
-- Tablas hijas------------------------------------------------------------------------------------------------------------
create table familiares(
	id int AUTO_INCREMENT PRIMARY KEY,
	vive bool, -- El formulario original pregunta sobre si viven los padres.
	nombre varchar(30) not null, 
	apellido1 varchar(20) not null,
	apellido2 varchar(20),
	fecha_nacimiento date not null,
	telefono_celular varchar(10),
	id_domicilio int,
	escolaridad enum('No tiene', 'Primaria', 'Secundaria', 'Bachillerato', 'Educación Superior'),
	ocupacion varchar(30),
	lugar_trabajo varchar(30),
	horario_laboral varchar(50),
	domicilio_trabajo varchar(100),
	telefono_trabajo varchar(10),

	foreign key (id_domicilio) references domicilio(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;

/*El formulario original tiene secciones pidiendo la información de los padres del alumno, de las personas que viven en 
  su misma casa (y su parentesco), de los contactos de emergencia y de los tutores asignados. Como las demás secciones
  básicamente piden los mismos datos que para los padres y, por lo general son las mismas personas en todos los casos,
  se decidó combinar las funciones y datos de esas secciones en esta tabla y en la siguiente para elimnar redundancias.*/
-- ------------------------------------------------------------------------------------------------------------------------
create table alumno_familiar(
	id int AUTO_INCREMENT PRIMARY KEY,
  id_alumno int NOT NULL,
  id_familiar int NOT NULL,
  parentesco varchar(20), /*Se descartó usar "enum" debido a que hay muchas opciones posibles fuera e incluso dentro de
  													la familia nuclear y no podemos las predecir con exactitud todas (padre adoptivo, madrastra,
  													primo, etc...). Para asegurar consistencia de escritura de los tipos, se implementará
  													autocompletado en vivo, usando como referencia registros anteriores.*/
	tutor bool, /* Se especifica si está o no asignado como tutor del alumno; por lo general serán los mismos padres. Este 
								 tutor es personal del alumno, siendo diferente al del grupo que es un trabajador del plantel*/
	contacto_emergencia tinyint not null, /*Se especifica si está o no asignado como contacto de emergencia y su prioridad.
																					Un 0 significa que no está asignado y de 1 en adelante se especifica la
																					prioridad. Puede ser que más de un familiar no esté asignado.*/

  FOREIGN KEY (id_alumno) REFERENCES alumnos(id) ON DELETE CASCADE,
  FOREIGN KEY (id_familiar) REFERENCES familiares(id) ON DELETE CASCADE,
	UNIQUE KEY unique_alumno_familiar (id_alumno, id_familiar)
) character set utf8mb4 collate utf8mb4_unicode_ci;

/*Tabla pivote para relacionar al alumno con los familiares, y describir la naturaleza de la relación y responsabilidades
  del familiar para con el alumno. Un alumno puede tener varios familiares, y un familiar puede estar relacionado con
  varios alumnos pero el parentesco, cualidad de tutor y prioridad como contacto puede o será distinto para cada alumno.*/
-- ------------------------------------------------------------------------------------------------------------------------
create table datos_familiares(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	-- 2. DATOS FAMILIARES
	estado_civil_padres varchar(15), /*Debido a la posibilidad de que existan situaciones muy específicas, se descartó el
																		 uso de "enum" y se dejará abierto. Para asegurar consistencia de los tipos y
																		 facilitar consultas, se implementará también la solución de autocompletado en vivo.
																		 Parecido al dilema con el parentesco.*/
	ingreso_familiar_aprox int,
	gasto_familiar_aprox int,
	casa_propia bool,
	servicios_casa varchar(4), /*Se almacenarán números binarios. El formulario original presenta 4 opciones que puede marcar
															 o dejar en blanco. Para no asignarle una columna tipo bool a cada opción y simplificar, se
															 optó fusionar todo en una misma columna que almacenará sólo una cadena de texto con un
															 binario; así tanto simplificamos la tabla como ahorramos en almacenamiento. 

															 Por ejemplo, si se marcan las opciones 1 y 2, el sistema almacenará un "11". Si se elige 1,
															 3 y 4, se almacena "1011". Además, si hay ceros a la derecha del útimo 1 se omiten para
															 ahorar memoria; si no se marcó ninguna opción, en vez de almacenar puros ceros, se deja
															 vacío para no desperdiciar.

															 El mismo sistema en su backend se encargará de descomponer e interpretar los datos cuando
															 sea necesario.*/
	auto_propio_familia bool,

	foreign key (id_alumno) references alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;

/*Esta tabla almacena información general sobre la situación de la familia y el hogar, a diferencia de familiares, la cual
	almacena información específica de cada individuo.*/
-- ------------------------------------------------------------------------------------------------------------------------
create table info_socioeco(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	-- 3. INFORMACIÓN SOCIOECONÓMICA PERSONAL
	auto_propio_alumno bool,
	transporte JSON, /*Debido a la posibilidad de que el alumno utilice varias opciones de transporte, ya sea en ocasiones
										 diferentes o en un mismo viaje, y de que no podemos predecir con exactitud cuáles serán por la
										 multitud de opciones, se usará JSON para poder almacenar las diferentes opciones como diferentes
										 pero en un mismo espacio. Para facilitar la consistencia de los datos, la cual es necesaria para
										 las consultas que se harán sobre este dato en temas de estadística, se implementará de nuevo el
										 autocompletado en vivo.*/
  traslado_horas int, -- En el fomulario se solicita el tiempo total de traslado ida y vuelta entre el plantel y su casa. 
  traslado_minutos int, -- Se ingresan las horas, minutos o ambos en función de la respuesta que se obtenga. 
	estado_civil_alumno varchar(15), -- Mismos detalles encontrados y solución propuesta que en el estado civil de los padres.
	num_hijos int default 0, -- Sólo de existir (el formulario original solicita estos datos).
	edades_hijos varchar(20), -- Sólo de existir.
	monto_apoyo int,
	gasto_comida_transporte int,
	comidas_diarias int,

	foreign key (id_alumno) references alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table datos_academicos(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	-- 4. DATOS ACADEMICOS
	dispositivos varchar(4), /*Se almacenarán números binarios. El formulario original presenta 4 opciones. Mismos problema y
							solución comentados antes. Se pregunta qué dispositivos electrónicos posee.*/
	otro_dispositio varchar(30), 

	foreign key (id_alumno) references alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table problema_aprendizaje(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	-- 4. DATOS ACADEMICOS (continuación)
	caracteristicas_especificas varchar(12), /*Se almacenarán números binarios. El formulario original presenta 12 opciones.
											Mismos problema y solución. Se preguntan problemas de aprendizaje.*/
	otro_problema varchar(255),

	foreign key (id_alumno) references alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table datos_salud(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	-- 5. DATOS GENERALES DE SALUD
	estatura int not null,
	peso float not null,
	tipo_sangre enum('A+','A-','B+','B-','AB+','AB-','O+','O-') not null,
	frecuencia_dentista tinyint,
	graduacion_anteojos float,
	cuadro_basico_vacunas bool,
	cirugia varchar(255),
	alergia varchar(255),
	limitante_fisico varchar(255),
	problema_auditivo varchar(255),
	adiccion varchar(255),
	padecimiento_emocional varchar(255),
	enfermedad_actual varchar(255),
	sintomas_cuales varchar(5), /*Se almacenarán números binarios. El formulario original presenta 5 opciones. Mismos
								problema y solución. Se preguntan síntomas que afecten la salud mental.*/
	otro_sintoma varchar(255),
	medicamento_controlado varchar(255),
	alergia_medicamento varchar(255),
	diabetes bool not null,
	hipertension bool not null,
	motivo_hospitalizacion varchar(255),
	dolores_cabeza bool not null,
	dolores_estomago bool not null,
	frecuencia_medico tinyint,

	foreign key (id_alumno) references alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------
create table actividades_recreativas(
	id int AUTO_INCREMENT PRIMARY KEY,
	id_alumno int,
	-- 6. ACTIVIDADES RECREATIVAS
	pasatiempo_favorito varchar(100),
	horas_pasatiempo_dedicadas int,
	deporte_practicado JSON, /*Mismo problema y solución que los encontrados en el dato de los transportes. Un alumno puede
							que practique más de un deporte. Se necesita repetir el dato como opción para estadísticas.*/
	horas_deporte_dedicadas int,
	horas_dia_tv int,
	horas_dia_compu int,
	uso_frecuente_compu varchar(50),
	temas_quien_chat varchar(100),

	foreign key (id_alumno) references alumnos(id)
) character set utf8mb4 collate utf8mb4_unicode_ci;
-- ------------------------------------------------------------------------------------------------------------------------