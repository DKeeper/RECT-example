SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS `rect` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `rect`;

DROP TABLE IF EXISTS `cinema`;
CREATE TABLE IF NOT EXISTS `cinema` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

INSERT INTO `cinema` (`id`, `name`, `description`) VALUES
(1, 'Cinema_1', NULL),
(2, 'Cinema_2', NULL),
(3, 'Cinema_3', NULL);

DROP TABLE IF EXISTS `cinema_hall`;
CREATE TABLE IF NOT EXISTS `cinema_hall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cinema_id` int(11) NOT NULL,
  `number` varchar(3) NOT NULL,
  `seats` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `cinema_id` (`cinema_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `cinema_hall` (`id`, `cinema_id`, `number`, `seats`, `description`) VALUES
(1, 1, '1', 50, NULL),
(2, 1, '2', 75, NULL),
(3, 1, '3', 100, NULL),
(4, 2, '1', 80, NULL),
(5, 2, '2', 100, NULL),
(6, 2, '3', 120, NULL),
(7, 3, '1', 150, NULL),
(8, 3, '2', 175, NULL),
(9, 3, '3', 200, NULL);

DROP TABLE IF EXISTS `film`;
CREATE TABLE IF NOT EXISTS `film` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `film` (`id`, `name`, `description`) VALUES
(1, 'Film_1', NULL),
(2, 'Film_2', NULL),
(3, 'Film_3', NULL),
(4, 'Film_4', NULL),
(5, 'Film_5', NULL),
(6, 'Film_6', NULL);

DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hall_id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `hall_id` (`hall_id`),
  KEY `film_id` (`film_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `session` (`id`, `hall_id`, `film_id`, `date`, `description`) VALUES
(1, 1, 1, '2014-06-01 09:00:00', NULL),
(2, 2, 1, '2014-06-01 09:00:00', NULL),
(3, 3, 2, '2014-06-01 11:00:00', NULL),
(4, 3, 2, '2014-06-01 10:00:00', NULL),
(5, 6, 1, '2014-06-01 14:00:00', NULL),
(6, 8, 1, '2014-06-01 14:00:00', NULL);

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `places` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `tickets` (`id`, `session_id`, `places`) VALUES
(2, 1, '[6,8]'),
(6, 1, '[7,9]');


ALTER TABLE `cinema_hall`
  ADD CONSTRAINT `cinema_hall_ibfk_1` FOREIGN KEY (`cinema_id`) REFERENCES `cinema` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`hall_id`) REFERENCES `cinema_hall` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `session_ibfk_2` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
