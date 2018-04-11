-- MySQL dump 10.14  Distrib 5.3.11-MariaDB, for portbld-freebsd9.0 (i386)
--
-- Host: localhost    Database: proxygrabber
-- ------------------------------------------------------
-- Server version	5.3.11-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ip_list_never`
--

DROP TABLE IF EXISTS `ip_list_never`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_list_never` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_ip` varchar(21) NOT NULL COMMENT 'ip и порт',
  `moved` int(11) unsigned NOT NULL COMMENT 'перенесено из time',
  `worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда работал',
  `not_worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда не работал',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_list_never`
--

LOCK TABLES `ip_list_never` WRITE;
/*!40000 ALTER TABLE `ip_list_never` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_list_never` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_list_new`
--

DROP TABLE IF EXISTS `ip_list_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_list_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_ip` varchar(21) NOT NULL COMMENT 'ip и порт',
  `checked` int(11) unsigned NOT NULL COMMENT 'Время последней проверки',
  `worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда работал',
  `not_worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда не работал',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_list_new`
--

LOCK TABLES `ip_list_new` WRITE;
/*!40000 ALTER TABLE `ip_list_new` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_list_new` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_list_ok`
--

DROP TABLE IF EXISTS `ip_list_ok`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_list_ok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_ip` varchar(21) NOT NULL COMMENT 'ip и порт',
  `checked` int(11) unsigned NOT NULL COMMENT 'Время последней проверки',
  `worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда работал',
  `not_worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда не работал',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_list_ok`
--

LOCK TABLES `ip_list_ok` WRITE;
/*!40000 ALTER TABLE `ip_list_ok` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_list_ok` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_list_substandard`
--

DROP TABLE IF EXISTS `ip_list_substandard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_list_substandard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_ip` varchar(21) NOT NULL COMMENT 'ip и порт',
  `checked` int(11) unsigned NOT NULL COMMENT 'Время последней проверки',
  `worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда работал',
  `not_worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда не работал',
  `status` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_list_substandard`
--

LOCK TABLES `ip_list_substandard` WRITE;
/*!40000 ALTER TABLE `ip_list_substandard` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_list_substandard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_list_time`
--

DROP TABLE IF EXISTS `ip_list_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_list_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proxy_ip` varchar(21) NOT NULL COMMENT 'ip и порт',
  `checked` int(11) unsigned NOT NULL COMMENT 'Время последней проверки',
  `worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда работал',
  `not_worked` int(11) unsigned NOT NULL COMMENT 'Последнее время, когда не работал',
  `never` tinyint(1) NOT NULL COMMENT 'Никогда не работал',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_list_time`
--

LOCK TABLES `ip_list_time` WRITE;
/*!40000 ALTER TABLE `ip_list_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_list_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proxy_sites_list`
--

DROP TABLE IF EXISTS `proxy_sites_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxy_sites_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proxy_sites_list`
--

LOCK TABLES `proxy_sites_list` WRITE;
/*!40000 ALTER TABLE `proxy_sites_list` DISABLE KEYS */;
INSERT INTO `proxy_sites_list` VALUES (1,'http://www.samair.ru/proxy/time-01.htm'),(2,'http://www.proxynova.com/proxy_list.txt'),(3,'http://fineproxy.org'),(4,'http://www.rmccurdy.com/scripts/proxy/good.txt'),(5,'http://www.slyhold.com/proxy_any_ee.txt'),(6,'http://white55.narod.ru/downloads/proxylist.txt'),(7,'http://ab57.ru/downloads/proxyold.txt'),(8,'http://www.freeproxy.ch/proxy.txt'),(9,'http://wowjp.net/_fr/2037/proxy.txt'),(10,'http://webanet.ucoz.ru/proxy/proxylist_at_05.10.2012.txt'),(11,'http://l2topvote.ucoz.com/_fr/1/proxy.txt'),(12,'http://www.noipmail.com/scripts/L1.txt'),(14,'http://www.noipmail.com/scripts/proxylist-1.txt'),(15,'http://ab57.ru/downloads/proxylist.txt'),(16,'http://www.slyhold.com/proxy_any_usca.txt'),(17,'http://www.slyhold.com/proxy_3_any.txt'),(18,'http://www.slyhold.com/proxy_any_we.txt'),(19,'http://more-proxies.com/xproxy.txt'),(20,'http://www.proxynova.com/proxy_list.txt?country=de,fr'),(21,'http://www.proxynova.com/proxy_list.txt?country=US'),(22,'http://wapland.org/proxy/proxy.txt'),(23,'http://vk.com/club10975869'),(24,'http://awmproxy.com/'),(25,'http://www.cybersyndrome.net/pla5.html'),(26,'http://greenmarketingnetwork.com/fresscrp.txt'),(27,'http://newscatcher.ru/files/exe/unchecked.txt'),(28,'http://www.slyhold.com/proxy_any_aw.txt'),(29,'http://www.slyhold.com/proxy_any_any.txt'),(30,'http://www.slyhold.com/proxy_2_any.txt'),(33,'http://www.slyhold.com/proxy_any_a.txt'),(34,'http://tcdev.ru/kerchfm/proxy.txt'),(35,'http://www.feel-design.com.ua/proxy.txt'),(36,'http://173.45.105.219/US03/PF-D/lists/country/out.txt'),(37,'http://210.4.15.234/KG8/PF-A/tmp/check_report_uniq.txt'),(38,'http://www.greenmarketingnetwork.com/scrp.txt'),(39,'http://www.radiolot.com/prox/xproxy1.txt'),(40,'http://210.4.15.81/KG1/PF-B/tmp/check_report_uniq.txt'),(41,'http://210.4.15.82/KG2/PF-B/tmp/check_report_uniq.txt'),(42,'http://210.4.15.84/KG4/PF-B/tmp/check_report_uniq.txt'),(43,'http://98.126.61.180/baidu/keywords.txt'),(44,'http://210.4.15.235/Dropbox/Public/SMTP/KG5-D.txt'),(45,'http://tips.ath.cx/cgi-bin/all.proxies.tips.ath.cx.txt'),(46,'http://com-pins.info/list.txt'),(47,'http://webkunstgalerie.de/alexa/proxy/zu-klein/jonethgerrr.txt'),(48,'http://www.myiptest.com/staticpages/index.php/Free-Elite-Anonymous-Proxy-lists.html'),(49,'http://anonimseti.blogspot.ru/'),(50,'http://best-proxy.ru/'),(51,'http://good-proxy.ru/'),(52,'http://www.proxylists.net/?HTTP'),(53,'http://weblayout.ru/proxy/index.php?list=updated'),(54,'http://www.proxybox.ru/proxy_list.htm'),(55,'http://elite-proxies.blogspot.ru/'),(56,'http://www.my-proxy.com/free-elite-proxy.html'),(57,'http://globalproxies.blogspot.ru/');
/*!40000 ALTER TABLE `proxy_sites_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `param` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`param`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('site_num','44');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-25 10:18:51
