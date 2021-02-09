-- MySQL dump 10.13  Distrib 5.7.33, for Linux (x86_64)
--
-- Host: localhost    Database: omeka-s3
-- ------------------------------------------------------
-- Server version	5.7.33-0ubuntu0.18.04.1

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
-- Table structure for table `datascribe_dataset`
--

DROP TABLE IF EXISTS `datascribe_dataset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datascribe_dataset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL,
  `item_set_id` int(11) DEFAULT NULL,
  `validated_by_id` int(11) DEFAULT NULL,
  `exported_by_id` int(11) DEFAULT NULL,
  `synced_by_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `modified_by_id` int(11) DEFAULT NULL,
  `guidelines` longtext COLLATE utf8mb4_unicode_ci,
  `export_storage_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validated` datetime DEFAULT NULL,
  `exported` datetime DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `synced` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2C5AD579166D1F9C5E237E06` (`project_id`,`name`),
  KEY `IDX_2C5AD579166D1F9C` (`project_id`),
  KEY `IDX_2C5AD579960278D7` (`item_set_id`),
  KEY `IDX_2C5AD579C69DE5E5` (`validated_by_id`),
  KEY `IDX_2C5AD579F748B80E` (`exported_by_id`),
  KEY `IDX_2C5AD5797141DBAD` (`synced_by_id`),
  KEY `IDX_2C5AD5797E3C61F9` (`owner_id`),
  KEY `IDX_2C5AD579B03A8386` (`created_by_id`),
  KEY `IDX_2C5AD57999049ECE` (`modified_by_id`),
  CONSTRAINT `FK_2C5AD579166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `datascribe_project` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2C5AD5797141DBAD` FOREIGN KEY (`synced_by_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_2C5AD5797E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_2C5AD579960278D7` FOREIGN KEY (`item_set_id`) REFERENCES `item_set` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_2C5AD57999049ECE` FOREIGN KEY (`modified_by_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_2C5AD579B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_2C5AD579C69DE5E5` FOREIGN KEY (`validated_by_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `FK_2C5AD579F748B80E` FOREIGN KEY (`exported_by_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datascribe_dataset`
--

LOCK TABLES `datascribe_dataset` WRITE;
/*!40000 ALTER TABLE `datascribe_dataset` DISABLE KEYS */;
INSERT INTO `datascribe_dataset` VALUES (1,1,7060,NULL,NULL,1,1,1,1,'',NULL,NULL,NULL,'My Dataset',NULL,'2020-11-11 15:59:23','2020-11-11 15:52:42','2021-02-09 15:04:49',0);
/*!40000 ALTER TABLE `datascribe_dataset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datascribe_field`
--

DROP TABLE IF EXISTS `datascribe_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datascribe_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dataset_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `position` int(11) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `data_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`),
  KEY `IDX_6979265FD47C2D1B` (`dataset_id`),
  CONSTRAINT `FK_6979265FD47C2D1B` FOREIGN KEY (`dataset_id`) REFERENCES `datascribe_dataset` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datascribe_field`
--

LOCK TABLES `datascribe_field` WRITE;
/*!40000 ALTER TABLE `datascribe_field` DISABLE KEYS */;
INSERT INTO `datascribe_field` VALUES (10,1,'1',NULL,1,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(11,1,'2',NULL,2,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(12,1,'3',NULL,3,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(13,1,'4',NULL,4,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(14,1,'5',NULL,5,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(15,1,'6',NULL,6,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(16,1,'7',NULL,7,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(17,1,'8',NULL,8,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(18,1,'9',NULL,9,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(19,1,'10',NULL,10,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(20,1,'11',NULL,11,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(21,1,'12',NULL,12,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(22,1,'13',NULL,13,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(23,1,'14',NULL,14,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(24,1,'15',NULL,15,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(25,1,'16',NULL,16,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(26,1,'17',NULL,17,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(27,1,'18',NULL,18,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(28,1,'19',NULL,19,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(29,1,'20',NULL,20,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(30,1,'21',NULL,21,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(31,1,'22',NULL,22,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(32,1,'23',NULL,23,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(33,1,'24',NULL,24,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(34,1,'25',NULL,25,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(35,1,'26',NULL,26,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(36,1,'27',NULL,27,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(37,1,'28',NULL,28,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(38,1,'29',NULL,29,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(39,1,'30',NULL,30,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(40,1,'31',NULL,31,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(41,1,'32',NULL,32,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(42,1,'33',NULL,33,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(43,1,'34',NULL,34,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(44,1,'35',NULL,35,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(45,1,'36',NULL,36,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(46,1,'37',NULL,37,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(47,1,'38',NULL,38,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(48,1,'39',NULL,39,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(49,1,'40',NULL,40,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(50,1,'41',NULL,41,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(51,1,'42',NULL,42,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(52,1,'43',NULL,43,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(53,1,'44',NULL,44,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(54,1,'45',NULL,45,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(55,1,'46',NULL,46,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(56,1,'47',NULL,47,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(57,1,'48',NULL,48,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(58,1,'49',NULL,49,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(59,1,'50',NULL,50,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(60,1,'51',NULL,51,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(61,1,'52',NULL,52,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(62,1,'53',NULL,53,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(63,1,'54',NULL,54,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(64,1,'55',NULL,55,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(65,1,'56',NULL,56,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(66,1,'57',NULL,57,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(67,1,'58',NULL,58,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(68,1,'59',NULL,59,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(69,1,'60',NULL,60,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(70,1,'61',NULL,61,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(71,1,'62',NULL,62,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(72,1,'63',NULL,63,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(73,1,'64',NULL,64,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(74,1,'65',NULL,65,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(75,1,'66',NULL,66,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(76,1,'67',NULL,67,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(77,1,'68',NULL,68,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(78,1,'69',NULL,69,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(79,1,'70',NULL,70,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(80,1,'71',NULL,71,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(81,1,'72',NULL,72,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(82,1,'73',NULL,73,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(83,1,'74',NULL,74,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(84,1,'75',NULL,75,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(85,1,'76',NULL,76,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(86,1,'77',NULL,77,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(87,1,'78',NULL,78,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(88,1,'79',NULL,79,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(89,1,'80',NULL,80,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(90,1,'81',NULL,81,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(91,1,'82',NULL,82,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(92,1,'83',NULL,83,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(93,1,'84',NULL,84,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(94,1,'85',NULL,85,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(95,1,'86',NULL,86,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(96,1,'87',NULL,87,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(97,1,'88',NULL,88,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(98,1,'89',NULL,89,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}'),(99,1,'90',NULL,90,0,0,'text','{\"minlength\":null,\"maxlength\":null,\"placeholder\":null,\"pattern\":\"\",\"default_value\":null,\"label\":null,\"datalist\":[]}');
/*!40000 ALTER TABLE `datascribe_field` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-02-09 10:33:48
