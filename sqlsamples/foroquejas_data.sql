DROP TABLE users;

CREATE TABLE `users` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `surname` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `password` varchar(150) COLLATE utf8mb4_spanish_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `rol` enum('admin','colaborator') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'colaborator',
  `address` varchar(150) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `telnumber` int(10) DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO users VALUES("89","admin@admin","admin","","21232f297a57a5a743894a0e4a801fc3","public/img/default-user.png","admin","admin","123456789","active");
INSERT INTO users VALUES("90","anonymous@anonymous","anonymous","","294de3557d9d00b3d2d8a1e6aab028cf","public/img/default-user.png","colaborator","anonymous","123456789","active");
INSERT INTO users VALUES("91","pacolozano@email.es","Paco","Lozano","08f9622ef19977d8e71c855f987ae981","public/uploads/profilepics/pacolozano@email.es.jpg","colaborator","Calle Morales, 18","600000045","active");
INSERT INTO users VALUES("92","lisamona@email.es","Lisa","Mona","0e02c60818d77f8ddaa787baf78d31a7","public/uploads/profilepics/lisamona@email.es.jpg","colaborator","Avda Louvre, 22","600000005","active");
INSERT INTO users VALUES("93","faust@foroquejas.es","Faustino","el Culpable","92aee59cba38edbff97e9c027df093ce","public/uploads/profilepics/faust@foroquejas.es.png","admin","Calle Tierra, 99","600000007","active");
INSERT INTO users VALUES("94","goku@email.es","Goku","Gonzalez","bef27466a245ce3ec692bd25409c2549","public/uploads/profilepics/goku@email.es.jpg","colaborator","Planeta Namek, 88","600000048","active");
INSERT INTO users VALUES("95","ratita@email.es","Ratita","Presumida","749d4400efc5fba09fa5d8dc77f52fb3","public/uploads/profilepics/ratita@email.es.jpg","colaborator","Calle Paris, 54","600000096","active");
INSERT INTO users VALUES("96","juanma@email.es","Juan Manuel","Perez","65a368f66ad6b9ee45263577713d8a95","public/uploads/profilepics/juanma@email.es.jpg","colaborator","Plaza España, 782","600123456","active");
INSERT INTO users VALUES("97","mariano@email.es","Mariano","Lucas","0804048efcb1f0b3c2f18a4412b1016c","public/uploads/profilepics/mariano@email.es.jpg","colaborator","Avda Tubería, 32","666444321","active");



DROP TABLE events;

CREATE TABLE `events` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_spanish_ci,
  `place` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `keywords` varchar(240) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `images` text COLLATE utf8mb4_spanish_ci,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `likes` int(7) NOT NULL DEFAULT '0',
  `dislikes` int(7) NOT NULL DEFAULT '0',
  `status` enum('checking','checked','processed','resolved','irresolvable') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'checking',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO events VALUES("78","Paquetería extremadamente lenta","Por medio de la presente, le hago de conocimiento mi inconformidad con el servicio de entrega y paquetería pues los aparatos que he adquirido en su tienda han llegado dañados, en cinco ocasiones,  aspecto que nunca me sucedió con la empresa que entregaba los paquetes con anterioridad.
\n
\nNo discuto de ninguna manera la reposición de los aparatos, pero alteran mis labores en forma latente, pues retrazan las entregas de los aparatos de computación que se me entregan para su compostura.
\n
\nTambién le hago de su conocimiento, que las memorias DDR3 que me mandaron no fueron de la marca que se habían pedido, habiéndoseme enviado unas de marca más económicas, pero que no me han causado problemas.
\n
\nEl pasado 7 de Julio de 2011, tuve comunicación telefónica con la empresa de transportes, pero no han podido explicarme los daños de las refacciones que se me han entregado, aduciendo la responsabilidad a la empresa emisora.
\n
\nHan existido dos entregas que no llegaron en tiempo, y por las cuales se me realizó un cobro extra de 50 pesos, extra, de los cuales me enteré hasta la realización de las cuentas de corte a fines de mes.
\n
\nLe agradecería que evalúe en forma pronta el problema presentado, pues mis adquisiciones son muy constantes con su empresa, y es solo desde el cambio de empresa de de mensajería que han llegado dañadas las piezas, y que me aclaren las cuentas por el envío de tarjetas de memoria equivocadas. 
\n
\nEsperando su pronta respuesta, agradezco y me despido.","Madrid","","a:2:{i:0;s:23:\"public/uploads/78_0.jpg\";i:1;s:23:\"public/uploads/78_1.jpg\";}","2019-06-02 18:50:24","mariano@email.es","3","0","checked");
INSERT INTO events VALUES("79","Jazztel me la juega","En el mes de febrero inicié un proceso de portabilidad con la empresa ONO. Sin embargo, unos días después recibí una contraoferta de mi actual compañía Jazztel que finalmente acepté. Les remití un fax a ONO para paralizar la portabilidad y hora me reclaman la cantidad de 151,25€ por cancelar el proceso. El único argumento al que se refieren es que no recibieron dicho fax dentro de los 7 días siguientes a la contratación.
\nCuando recibí la propuesta por parte de Jazztel me aseguraron que recibiría asistencia jurídica (la he tenido 5 meses después) y que en caso de penalización se harían cargo de ella.
\nHe solicitado la grabación donde aparece reflejada la conversación telefónica y no me la han remitido aduciendo \"problemas técnicos para ser escuchada\"","Plaza España","","a:1:{i:0;s:23:\"public/uploads/79_0.png\";}","2019-06-02 18:56:09","mariano@email.es","2","0","processed");
INSERT INTO events VALUES("80","Casi me muero chamuscada","Este protector ECRAN LEMONOIL de protección 15, ni protege, ni ilumina. Lo que si hace es propiciar la quemadura y sobre todo pringarte de aceite, engrasarte toda la piel durante todo el tiempo no se absorve en absoluto. EN DEFINITIVA UN DESASTRE NO RECOMIENDO ","Playa de Poniente","","a:1:{i:0;s:23:\"public/uploads/80_0.jpg\";}","2019-06-02 18:59:11","lisamona@email.es","1","1","irresolvable");
INSERT INTO events VALUES("81","Estos alemanes se ríen de mi","No me sirven el pedido de 70 € hecho por su web, de tres cartuchos de tinta. Lo mandaron por UPS, y lleva una semana que UPS esta esperando que le den mi numero de telefono por que son incapaces de encontrar la direccion (que aparece en Google maps) Contestan a mi queja que no pueden hacer nada proque ellos ya lo enviaron, y le pasan la pelota a UPS, pero esta compañia no tiene forma de reclamar si eres en receptor del pedido. Total sin toner, sin respuesta, sin dinero, sin poder quejarme. TONERJET.ES No tiene forma de presentar quejas por internet, solo una direccion de correo que aparece en la factura. (y es alemana, nada de .es, debería ser .deutch)","Asturias","","a:0:{}","2019-06-02 19:02:50","ratita@email.es","4","1","resolved");
INSERT INTO events VALUES("82","Ojito con las mudanzas EBM","Nada serios y no se responsabilizan de absolutamente nada. No cumplen horarios y no puedes hablar con nadie responsable para presentar una reclamación. NADA RECOMENDABLES!!! NO SE PUEDE CONFIAR EN ELLOS!! Y NO CUMPLEN SU PALABRA","Mi casa","","a:1:{i:0;s:23:\"public/uploads/82_0.jpg\";}","2019-06-02 19:08:42","pacolozano@email.es","3","0","processed");



DROP TABLE comments;

CREATE TABLE `comments` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `content` text COLLATE utf8mb4_spanish_ci NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event` int(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `event` (`event`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`event`) REFERENCES `events` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO comments VALUES("49","goku@email.es","No te preocupes Mariano, esto se soluciona, espero...","2019-06-02 18:52:18","78");
INSERT INTO comments VALUES("50","faust@foroquejas.es","Es intolerable, habría que despedirlos a todos.","2019-06-02 18:56:40","79");
INSERT INTO comments VALUES("51","goku@email.es","Huele a pollo asado por aquí......","2019-06-02 18:59:56","80");
INSERT INTO comments VALUES("52","mariano@email.es","Un respeto a nuestra compañera por favor","2019-06-02 19:00:21","80");
INSERT INTO comments VALUES("53","faust@foroquejas.es","Calma, amigos","2019-06-02 19:00:42","80");
INSERT INTO comments VALUES("54","mariano@email.es","Lamentable cuanto menos","2019-06-02 19:04:43","81");
INSERT INTO comments VALUES("55","goku@email.es","Acércate a la oficina del consumidor en Calle Constantinopolitano 77","2019-06-02 19:05:39","81");
INSERT INTO comments VALUES("56","ratita@email.es","Gracias a todos, resuelto","2019-06-02 19:06:08","81");
INSERT INTO comments VALUES("57","faust@foroquejas.es","Una maravilla esta sociedad","2019-06-02 19:06:42","81");
INSERT INTO comments VALUES("58","lisamona@email.es","Tendrían que cerrarles el chiringuito..............","2019-06-02 19:09:25","82");
INSERT INTO comments VALUES("59","goku@email.es","A mi me la jugaron en el pasado, a esa empresa ni agua","2019-06-02 19:09:59","82");
INSERT INTO comments VALUES("60","faust@foroquejas.es","Sinvergüenzas","2019-06-02 19:10:30","82");
INSERT INTO comments VALUES("61","goku@email.es","Vaya robo a mano armada.","2019-06-02 19:11:49","79");



DROP TABLE u_interact_e;

CREATE TABLE `u_interact_e` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL,
  `event` int(7) NOT NULL,
  `interaction` enum('like','dislike') COLLATE utf8mb4_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `event` (`event`),
  CONSTRAINT `u_interact_e_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`),
  CONSTRAINT `u_interact_e_ibfk_2` FOREIGN KEY (`event`) REFERENCES `events` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO u_interact_e VALUES("37","goku@email.es","78","like");
INSERT INTO u_interact_e VALUES("38","mariano@email.es","78","like");
INSERT INTO u_interact_e VALUES("39","faust@foroquejas.es","78","like");
INSERT INTO u_interact_e VALUES("40","faust@foroquejas.es","79","like");
INSERT INTO u_interact_e VALUES("41","goku@email.es","80","like");
INSERT INTO u_interact_e VALUES("42","mariano@email.es","80","dislike");
INSERT INTO u_interact_e VALUES("43","goku@email.es","81","like");
INSERT INTO u_interact_e VALUES("44","faust@foroquejas.es","81","like");
INSERT INTO u_interact_e VALUES("45","mariano@email.es","81","like");
INSERT INTO u_interact_e VALUES("46","ratita@email.es","81","like");
INSERT INTO u_interact_e VALUES("47","mariano@email.es","81","dislike");
INSERT INTO u_interact_e VALUES("48","lisamona@email.es","82","like");
INSERT INTO u_interact_e VALUES("49","goku@email.es","82","like");
INSERT INTO u_interact_e VALUES("50","pacolozano@email.es","82","like");
INSERT INTO u_interact_e VALUES("51","goku@email.es","79","like");



DROP TABLE log;

CREATE TABLE `log` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `message` varchar(240) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=407 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO log VALUES("347","[admin] [admin@admin] -> Salida del sistema","2019-06-02 15:55:11");
INSERT INTO log VALUES("348","[admin] [admin@admin] -> Acceso al sistema","2019-06-02 15:55:15");
INSERT INTO log VALUES("349","[admin] [admin@admin] -> Usuario creado (pacolozano@email.es)","2019-06-02 18:33:49");
INSERT INTO log VALUES("350","[admin] [admin@admin] -> Usuario creado (lisamona@email.es)","2019-06-02 18:34:57");
INSERT INTO log VALUES("351","[admin] [admin@admin] -> Usuario creado (faust@foroquejas.es)","2019-06-02 18:37:08");
INSERT INTO log VALUES("352","[admin] [admin@admin] -> Usuario creado (goku@email.es)","2019-06-02 18:38:57");
INSERT INTO log VALUES("353","[admin] [admin@admin] -> Usuario creado (ratita@email.es)","2019-06-02 18:40:14");
INSERT INTO log VALUES("354","[admin] [admin@admin] -> Usuario creado (juanma@email.es)","2019-06-02 18:41:55");
INSERT INTO log VALUES("355","[admin] [admin@admin] -> Usuario creado (mariano@email.es)","2019-06-02 18:43:19");
INSERT INTO log VALUES("356","[admin] [admin@admin] -> Salida del sistema","2019-06-02 18:46:10");
INSERT INTO log VALUES("357","[colaborator] [mariano@email.es] -> Acceso al sistema","2019-06-02 18:46:24");
INSERT INTO log VALUES("358","[colaborator] [mariano@email.es] -> Queja creada #78","2019-06-02 18:50:24");
INSERT INTO log VALUES("359","[colaborator] [goku@email.es] -> Acceso al sistema","2019-06-02 18:51:36");
INSERT INTO log VALUES("360","[colaborator] [goku@email.es] -> Agrega comentario en evento #78","2019-06-02 18:52:18");
INSERT INTO log VALUES("361","[colaborator] [goku@email.es] -> Like evento #78","2019-06-02 18:52:24");
INSERT INTO log VALUES("362","[colaborator] [mariano@email.es] -> Like evento #78","2019-06-02 18:52:26");
INSERT INTO log VALUES("363","[admin] [faust@foroquejas.es] -> Acceso al sistema","2019-06-02 18:54:23");
INSERT INTO log VALUES("364","[admin] [faust@foroquejas.es] -> Like evento #78","2019-06-02 18:54:38");
INSERT INTO log VALUES("365","[admin] [faust@foroquejas.es] -> Cambio status a (checked) en evento #78","2019-06-02 18:54:49");
INSERT INTO log VALUES("366","[colaborator] [mariano@email.es] -> Queja creada #79","2019-06-02 18:56:09");
INSERT INTO log VALUES("367","[admin] [faust@foroquejas.es] -> Agrega comentario en evento #79","2019-06-02 18:56:40");
INSERT INTO log VALUES("368","[admin] [faust@foroquejas.es] -> Cambio status a (processed) en evento #79","2019-06-02 18:56:45");
INSERT INTO log VALUES("369","[admin] [faust@foroquejas.es] -> Like evento #79","2019-06-02 18:56:46");
INSERT INTO log VALUES("370","[colaborator] [lisamona@email.es] -> Acceso al sistema","2019-06-02 18:57:22");
INSERT INTO log VALUES("371","[colaborator] [lisamona@email.es] -> Queja creada #80","2019-06-02 18:59:11");
INSERT INTO log VALUES("372","[colaborator] [goku@email.es] -> Agrega comentario en evento #80","2019-06-02 18:59:56");
INSERT INTO log VALUES("373","[colaborator] [goku@email.es] -> Like evento #80","2019-06-02 18:59:59");
INSERT INTO log VALUES("374","[colaborator] [mariano@email.es] -> Disliked event #80","2019-06-02 19:00:12");
INSERT INTO log VALUES("375","[colaborator] [mariano@email.es] -> Agrega comentario en evento #80","2019-06-02 19:00:21");
INSERT INTO log VALUES("376","[admin] [faust@foroquejas.es] -> Agrega comentario en evento #80","2019-06-02 19:00:42");
INSERT INTO log VALUES("377","[admin] [faust@foroquejas.es] -> Cambio status a (irresolvable) en evento #80","2019-06-02 19:00:47");
INSERT INTO log VALUES("378","[colaborator] [lisamona@email.es] -> Salida del sistema","2019-06-02 19:01:17");
INSERT INTO log VALUES("379","[colaborator] [ratita@email.es] -> Acceso al sistema","2019-06-02 19:01:23");
INSERT INTO log VALUES("380","[colaborator] [ratita@email.es] -> Queja creada #81","2019-06-02 19:02:50");
INSERT INTO log VALUES("381","[colaborator] [mariano@email.es] -> Agrega comentario en evento #81","2019-06-02 19:04:43");
INSERT INTO log VALUES("382","[colaborator] [goku@email.es] -> Agrega comentario en evento #81","2019-06-02 19:05:39");
INSERT INTO log VALUES("383","[colaborator] [goku@email.es] -> Like evento #81","2019-06-02 19:05:41");
INSERT INTO log VALUES("384","[colaborator] [ratita@email.es] -> Agrega comentario en evento #81","2019-06-02 19:06:08");
INSERT INTO log VALUES("385","[admin] [faust@foroquejas.es] -> Agrega comentario en evento #81","2019-06-02 19:06:42");
INSERT INTO log VALUES("386","[admin] [faust@foroquejas.es] -> Cambio status a (resolved) en evento #81","2019-06-02 19:06:45");
INSERT INTO log VALUES("387","[admin] [faust@foroquejas.es] -> Like evento #81","2019-06-02 19:06:47");
INSERT INTO log VALUES("388","[colaborator] [mariano@email.es] -> Like evento #81","2019-06-02 19:06:50");
INSERT INTO log VALUES("389","[colaborator] [ratita@email.es] -> Like evento #81","2019-06-02 19:06:52");
INSERT INTO log VALUES("390","[colaborator] [mariano@email.es] -> Disliked event #81","2019-06-02 19:07:00");
INSERT INTO log VALUES("391","[colaborator] [ratita@email.es] -> Salida del sistema","2019-06-02 19:07:12");
INSERT INTO log VALUES("392","[colaborator] [pacolozano@email.es] -> Acceso al sistema","2019-06-02 19:07:22");
INSERT INTO log VALUES("393","[colaborator] [pacolozano@email.es] -> Queja creada #82","2019-06-02 19:08:42");
INSERT INTO log VALUES("394","[colaborator] [mariano@email.es] -> Salida del sistema","2019-06-02 19:08:51");
INSERT INTO log VALUES("395","[colaborator] [lisamona@email.es] -> Acceso al sistema","2019-06-02 19:09:10");
INSERT INTO log VALUES("396","[colaborator] [lisamona@email.es] -> Agrega comentario en evento #82","2019-06-02 19:09:25");
INSERT INTO log VALUES("397","[colaborator] [lisamona@email.es] -> Like evento #82","2019-06-02 19:09:31");
INSERT INTO log VALUES("398","[colaborator] [goku@email.es] -> Agrega comentario en evento #82","2019-06-02 19:09:59");
INSERT INTO log VALUES("399","[colaborator] [goku@email.es] -> Like evento #82","2019-06-02 19:10:02");
INSERT INTO log VALUES("400","[admin] [faust@foroquejas.es] -> Cambio status a (processed) en evento #82","2019-06-02 19:10:13");
INSERT INTO log VALUES("401","[admin] [faust@foroquejas.es] -> Agrega comentario en evento #82","2019-06-02 19:10:30");
INSERT INTO log VALUES("402","[colaborator] [pacolozano@email.es] -> Like evento #82","2019-06-02 19:10:36");
INSERT INTO log VALUES("403","[colaborator] [goku@email.es] -> Agrega comentario en evento #79","2019-06-02 19:11:49");
INSERT INTO log VALUES("404","[colaborator] [goku@email.es] -> Like evento #79","2019-06-02 19:11:53");
INSERT INTO log VALUES("405","[colaborator] [lisamona@email.es] -> Salida del sistema","2019-06-02 19:16:44");
INSERT INTO log VALUES("406","[admin] [admin@admin] -> Acceso al sistema","2019-06-02 19:16:46");

