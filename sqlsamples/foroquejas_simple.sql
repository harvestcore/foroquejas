SET FOREIGN_KEY_CHECKS=0;\nDROP TABLE users;

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
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO users VALUES("89","admin@admin","admin","","21232f297a57a5a743894a0e4a801fc3","public/img/default-user.png","admin","admin","123456789","active");
INSERT INTO users VALUES("90","anonymous@anonymous","anonymous","","294de3557d9d00b3d2d8a1e6aab028cf","public/img/default-user.png","colaborator","anonymous","123456789","active");



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
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;




DROP TABLE log;

CREATE TABLE `log` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `message` varchar(240) COLLATE utf8mb4_spanish_ci DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=349 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

INSERT INTO log VALUES("347","[admin] [admin@admin] -> Salida del sistema","2019-06-02 15:55:11");
INSERT INTO log VALUES("348","[admin] [admin@admin] -> Acceso al sistema","2019-06-02 15:55:15");



SET FOREIGN_KEY_CHECKS=1