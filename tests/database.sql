CREATE DATABASE blog;
USE blog;
SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR (255) NOT NULL DEFAULT 0,
  `slug` VARCHAR(255) NOT NULL,
  `category_id` INT(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;