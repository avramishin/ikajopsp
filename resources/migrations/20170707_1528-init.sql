/*
SQLyog Ultimate v10.42 
MySQL - 5.7.14 : Database - psp
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`psp` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `psp`;

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` char(16) NOT NULL,
  `channel_id` varchar(16) DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payer_firstname` varchar(25) DEFAULT NULL,
  `payer_lastname` varchar(25) DEFAULT NULL,
  `payer_address` varchar(255) DEFAULT NULL,
  `payer_country` varchar(50) DEFAULT NULL,
  `payer_state` varchar(50) DEFAULT NULL,
  `payer_city` varchar(50) DEFAULT NULL,
  `payer_zip` varchar(25) DEFAULT NULL,
  `payer_email` varchar(100) DEFAULT NULL,
  `payer_phone` varchar(25) DEFAULT NULL,
  `payer_ip` varchar(15) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `async` enum('Y','N') DEFAULT 'N',
  `auth` enum('Y','N') DEFAULT 'N',
  `hash_p1` char(10) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `orders_flow` */

DROP TABLE IF EXISTS `orders_flow`;

CREATE TABLE `orders_flow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` char(16) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `trans_id` varchar(100) DEFAULT NULL,
  `descriptor` varchar(500) DEFAULT NULL,
  `details` text,
  `create_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
