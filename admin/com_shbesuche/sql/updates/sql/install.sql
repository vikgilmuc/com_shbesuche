
DROP TABLE IF EXISTS `#__shbesuche`;
CREATE TABLE IF NOT EXISTS `#__shbesuche` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `state` varchar(50) DEFAULT NULL,
  `value` varchar(12) DEFAULT NULL,
  `weight` varchar(12) DEFAULT NULL,
  `lent_by` varchar(50) DEFAULT NULL,
  `lent` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

