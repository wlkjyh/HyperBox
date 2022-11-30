-- MySQL dump 10.13  Distrib 5.7.26, for Win64 (x86_64)
--
-- Host: localhost    Database: dreamStack
-- ------------------------------------------------------
-- Server version	5.7.26

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
-- Table structure for table `backup`
--

DROP TABLE IF EXISTS `backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('instance','volume') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `backup_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup`
--


--
-- Table structure for table `compute`
--

DROP TABLE IF EXISTS `compute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compute` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hostname` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vcpu` int(11) NOT NULL,
  `ram` int(11) NOT NULL,
  `rule` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `disk` int(11) NOT NULL,
  `console` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compute_hostname_unique` (`hostname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `k` varchar(32) NOT NULL,
  `v` text,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES ('freeoauth','{\"01-00-5e-00-00-16\":\"7225b9bc-5f17-4dc6-ba0e-56d658421a0b\"}');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flavor`
--

DROP TABLE IF EXISTS `flavor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flavor` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vcpu` int(11) NOT NULL,
  `type` int(1) DEFAULT '0',
  `ram` int(11) DEFAULT NULL,
  `min` int(11) DEFAULT NULL,
  `max` int(11) DEFAULT NULL,
  `trx` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `share` int(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `flavor_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `id` varchar(36) NOT NULL,
  `name` text,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES ('3e2e3e48-7d62-4e79-a06b-2bbf638dd8df','default','default group','2022-07-18 14:19:27','2022-07-18 14:32:05');
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compute` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vcpu` int(11) NOT NULL,
  `ram` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rule` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `os` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'Windows',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `image`
--


--
-- Table structure for table `instance`
--

DROP TABLE IF EXISTS `instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instance` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compute` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flavor` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `network` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ipaddr` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` int(11) NOT NULL,
  `error` text COLLATE utf8mb4_unicode_ci,
  `bootstrap` text COLLATE utf8mb4_unicode_ci,
  `portsafe` int(11) DEFAULT '0',
  `vid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_id` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `instance_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instance`
--

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compute` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('physics','vlan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `switchname` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vlan` int(11) NOT NULL,
  `subnet` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `netmask` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dhcp` int(11) NOT NULL,
  `list` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dns` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rule` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ippool` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loginfail` int(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('7225b9bc-5f17-4dc6-ba0e-56d658421a0b','admin','$2y$10$6/s8hh5GCAQKS53j.zWQcu.ND04fV0Ef/F6BZ0ubm48o168qHZoaO','admin@service.com','3e2e3e48-7d62-4e79-a06b-2bbf638dd8df','d692af58-6519-45f8-804b-dab069253fe3',0,'2022-07-07 19:42:18','2022-11-29 17:08:12');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volume`
--

DROP TABLE IF EXISTS `volume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `volume` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `compute` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `instance` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` int(11) NOT NULL,
  `ide` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `volume_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volume`
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-11-30 22:53:17
