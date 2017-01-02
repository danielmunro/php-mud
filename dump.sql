-- MySQL dump 10.13  Distrib 5.7.16, for osx10.12 (x86_64)
--
-- Host: localhost    Database: phpmud
-- ------------------------------------------------------
-- Server version	5.7.16

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
-- Table structure for table `Ability`
--

DROP TABLE IF EXISTS `Ability`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mob_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FA72D7A016E57E11` (`mob_id`),
  CONSTRAINT `FK_FA72D7A016E57E11` FOREIGN KEY (`mob_id`) REFERENCES `Mob` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ability`
--

LOCK TABLES `Ability` WRITE;
/*!40000 ALTER TABLE `Ability` DISABLE KEYS */;
INSERT INTO `Ability` VALUES (1,1,'berserk',1),(2,1,'bash',1),(3,4,'berserk',1),(4,4,'bash',1),(5,4,'sword',1),(6,6,'sneak',1);
/*!40000 ALTER TABLE `Ability` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Affect`
--

DROP TABLE IF EXISTS `Affect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Affect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attributes_id` int(11) DEFAULT NULL,
  `mob_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timeout` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_2A5DD6D2BAAF4009` (`attributes_id`),
  KEY `IDX_2A5DD6D216E57E11` (`mob_id`),
  KEY `IDX_2A5DD6D2126F525E` (`item_id`),
  CONSTRAINT `FK_2A5DD6D2126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`),
  CONSTRAINT `FK_2A5DD6D216E57E11` FOREIGN KEY (`mob_id`) REFERENCES `Mob` (`id`),
  CONSTRAINT `FK_2A5DD6D2BAAF4009` FOREIGN KEY (`attributes_id`) REFERENCES `Attributes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Affect`
--

LOCK TABLES `Affect` WRITE;
/*!40000 ALTER TABLE `Affect` DISABLE KEYS */;
/*!40000 ALTER TABLE `Affect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Area`
--

DROP TABLE IF EXISTS `Area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Area`
--

LOCK TABLES `Area` WRITE;
/*!40000 ALTER TABLE `Area` DISABLE KEYS */;
INSERT INTO `Area` VALUES (1,'Dark Woods'),(2,'Dark Woods');
/*!40000 ALTER TABLE `Area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Attributes`
--

DROP TABLE IF EXISTS `Attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hp` int(11) NOT NULL,
  `mana` int(11) NOT NULL,
  `mv` int(11) NOT NULL,
  `str` int(11) NOT NULL,
  `int` int(11) NOT NULL,
  `wis` int(11) NOT NULL,
  `dex` int(11) NOT NULL,
  `con` int(11) NOT NULL,
  `cha` int(11) NOT NULL,
  `hit` int(11) NOT NULL,
  `dam` int(11) NOT NULL,
  `acSlash` int(11) NOT NULL,
  `acBash` int(11) NOT NULL,
  `acPierce` int(11) NOT NULL,
  `acMagic` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attributes`
--

LOCK TABLES `Attributes` WRITE;
/*!40000 ALTER TABLE `Attributes` DISABLE KEYS */;
INSERT INTO `Attributes` VALUES (1,20,100,100,18,12,17,11,18,13,1,2,0,10,0,0),(2,20,100,100,15,15,15,15,15,15,1,1,0,0,0,0),(3,20,100,100,15,15,15,15,15,15,1,1,0,0,0,0),(4,20,100,100,18,12,17,11,18,13,1,2,0,10,0,0),(5,20,100,100,15,15,15,15,15,15,1,1,0,0,0,0),(6,20,100,100,15,15,15,15,15,15,1,1,0,0,0,0);
/*!40000 ALTER TABLE `Attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Direction`
--

DROP TABLE IF EXISTS `Direction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Direction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `direction` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sourceRoom_id` int(11) DEFAULT NULL,
  `targetRoom_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BCBB5310D4668B6A` (`sourceRoom_id`),
  KEY `IDX_BCBB53106B93493C` (`targetRoom_id`),
  CONSTRAINT `FK_BCBB53106B93493C` FOREIGN KEY (`targetRoom_id`) REFERENCES `Room` (`id`),
  CONSTRAINT `FK_BCBB5310D4668B6A` FOREIGN KEY (`sourceRoom_id`) REFERENCES `Room` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Direction`
--

LOCK TABLES `Direction` WRITE;
/*!40000 ALTER TABLE `Direction` DISABLE KEYS */;
INSERT INTO `Direction` VALUES (1,'east',1,2),(2,'west',2,1),(3,'east',2,3),(4,'west',3,2),(5,'south',2,4),(6,'north',4,2),(7,'south',4,5),(8,'north',5,4),(9,'south',5,7),(10,'north',6,7),(11,'north',7,5),(12,'south',7,6),(13,'west',5,8),(14,'east',8,5),(15,'east',5,9),(16,'west',9,5),(17,'north',2,10),(18,'south',10,2),(19,'north',10,11),(20,'south',11,10),(21,'north',11,12),(22,'south',12,11),(23,'east',4,13),(24,'west',13,4),(25,'north',13,14),(26,'south',14,13);
/*!40000 ALTER TABLE `Direction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Inventory`
--

DROP TABLE IF EXISTS `Inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gold` int(11) NOT NULL,
  `silver` int(11) NOT NULL,
  `capacityWeight` int(11) NOT NULL,
  `capacityCount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Inventory`
--

LOCK TABLES `Inventory` WRITE;
/*!40000 ALTER TABLE `Inventory` DISABLE KEYS */;
INSERT INTO `Inventory` VALUES (1,0,20,500,250),(2,0,0,500,250),(3,0,0,500,250),(4,0,0,500,250),(5,0,0,500,250),(6,0,0,500,250),(7,0,0,500,250),(8,0,0,500,250),(9,0,0,500,250),(10,0,85,500,250),(11,0,0,500,250),(12,0,0,500,250),(13,0,0,500,250),(14,0,0,500,250),(15,0,0,500,250),(16,0,0,500,250),(17,0,0,500,250),(18,0,0,500,250),(19,0,0,500,250),(20,0,0,500,250),(21,0,0,500,250),(22,0,0,500,250),(23,0,0,500,250),(24,0,0,500,250),(25,100,15,500,250),(26,0,0,500,250);
/*!40000 ALTER TABLE `Inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Item`
--

DROP TABLE IF EXISTS `Item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `look` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `material` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `identifiers` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `weight` decimal(10,0) NOT NULL,
  `value` decimal(10,0) NOT NULL,
  `position` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` int(11) NOT NULL,
  `vNum` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `craftedBy_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF298A209EEA759` (`inventory_id`),
  KEY `IDX_BF298A205CFE4DBC` (`craftedBy_id`),
  KEY `vnum_idx` (`vNum`),
  CONSTRAINT `FK_BF298A205CFE4DBC` FOREIGN KEY (`craftedBy_id`) REFERENCES `Mob` (`id`),
  CONSTRAINT `FK_BF298A209EEA759` FOREIGN KEY (`inventory_id`) REFERENCES `Inventory` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Item`
--

LOCK TABLES `Item` WRITE;
/*!40000 ALTER TABLE `Item` DISABLE KEYS */;
INSERT INTO `Item` VALUES (1,3,'a small brass key',NULL,'brass','a:3:{i:0;s:5:\"small\";i:1;s:5:\"brass\";i:2;s:3:\"key\";}',0,20,'',1,'9c4b540e-d4b4-4b4f-9a5e-a356a4551e26',NULL),(2,5,'a loaf of bread',NULL,'food','a:2:{i:0;s:4:\"loaf\";i:1;s:5:\"bread\";}',0,4,'',1,'e39b41cd-69e5-4ec3-8d0f-3b27677ce22a',NULL),(3,3,'a copper teapot',NULL,'copper','a:2:{i:0;s:6:\"copper\";i:1;s:6:\"teapot\";}',1,0,'',1,'bcc89181-b70b-4370-8883-d11006034b33',NULL),(4,3,'a wooden sword',NULL,'wood','a:2:{i:0;s:6:\"wooden\";i:1;s:5:\"sword\";}',4,0,'wielded',1,'ff5c8d1b-6d76-4ffb-85e2-1ed6fad8d4be',NULL),(5,3,'a wooden mace',NULL,'wood','a:2:{i:0;s:6:\"wooden\";i:1;s:4:\"mace\";}',5,0,'wielded',1,'64befce1-aea0-40ef-a394-e3a866650432',NULL),(6,10,'a wooden torch',NULL,'wood','a:2:{i:1;s:6:\"wooden\";i:2;s:5:\"torch\";}',0,15,'',1,'cc11a12f-8281-4469-949e-964fc4c22a2c',6),(7,25,'a wooden torch',NULL,'wood','a:2:{i:1;s:6:\"wooden\";i:2;s:5:\"torch\";}',0,15,'',1,'cc11a12f-8281-4469-949e-964fc4c22a2c',6);
/*!40000 ALTER TABLE `Item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Mob`
--

DROP TABLE IF EXISTS `Mob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Mob` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) DEFAULT NULL,
  `attributes_id` int(11) DEFAULT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `equipped_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `look` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `disposition` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `identifiers` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `race` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hp` int(11) NOT NULL,
  `mana` int(11) NOT NULL,
  `mv` int(11) NOT NULL,
  `isPlayer` tinyint(1) NOT NULL,
  `gender` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `experience` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `debitLevels` int(11) NOT NULL,
  `ageInSeconds` int(11) NOT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `trains` int(11) NOT NULL,
  `practices` int(11) NOT NULL,
  `skillPoints` int(11) NOT NULL,
  `job` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `alignment` int(11) NOT NULL,
  `creationPoints` int(11) NOT NULL,
  `accessLevel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C6DAB09DBAAF4009` (`attributes_id`),
  UNIQUE KEY `UNIQ_C6DAB09D9EEA759` (`inventory_id`),
  UNIQUE KEY `UNIQ_C6DAB09DBDC3019B` (`equipped_id`),
  KEY `IDX_C6DAB09D54177093` (`room_id`),
  CONSTRAINT `FK_C6DAB09D54177093` FOREIGN KEY (`room_id`) REFERENCES `Room` (`id`),
  CONSTRAINT `FK_C6DAB09D9EEA759` FOREIGN KEY (`inventory_id`) REFERENCES `Inventory` (`id`),
  CONSTRAINT `FK_C6DAB09DBAAF4009` FOREIGN KEY (`attributes_id`) REFERENCES `Attributes` (`id`),
  CONSTRAINT `FK_C6DAB09DBDC3019B` FOREIGN KEY (`equipped_id`) REFERENCES `Inventory` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Mob`
--

LOCK TABLES `Mob` WRITE;
/*!40000 ALTER TABLE `Mob` DISABLE KEYS */;
INSERT INTO `Mob` VALUES (1,1,1,1,2,'A dwarven armorer','A stout dwarf totters around, stinking up the place.','standing','a:3:{i:0;s:1:\"A\";i:1;s:7:\"dwarven\";i:2;s:7:\"armorer\";}','dwarf',20,100,100,0,'neutral',0,1,0,1482544959,'a:1:{i:0;s:10:\"shopkeeper\";}',0,0,0,'uninitiated',0,9,'mob'),(2,12,2,3,4,'a janitor',NULL,'standing','a:2:{i:0;s:1:\"a\";i:1;s:7:\"janitor\";}','human',20,100,100,0,'neutral',0,1,0,1482544959,'a:2:{i:0;s:9:\"scavenger\";i:1;s:6:\"mobile\";}',0,0,0,'uninitiated',0,5,'mob'),(3,3,3,5,6,'a baker','standing behind the counter, %s wipes flour from his forehead.','standing','a:2:{i:0;s:1:\"a\";i:1;s:5:\"baker\";}','human',20,100,100,0,'neutral',0,1,0,1482544959,'a:1:{i:0;s:10:\"shopkeeper\";}',0,0,0,'uninitiated',0,5,'mob'),(4,14,4,10,11,'dan',NULL,'standing','a:1:{i:0;s:3:\"dan\";}','dwarf',20,100,100,1,'neutral',2058,2,1,1482565030,'a:0:{}',0,0,0,'warrior',0,9,'mob'),(6,14,6,25,26,'a grocer',NULL,'standing','a:2:{i:0;s:1:\"a\";i:1;s:6:\"grocer\";}','elf',20,100,100,0,'neutral',0,1,0,1483211321,'a:1:{i:0;s:10:\"shopkeeper\";}',0,0,0,'uninitiated',0,5,'mob');
/*!40000 ALTER TABLE `Mob` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Room`
--

DROP TABLE IF EXISTS `Room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_id` int(11) DEFAULT NULL,
  `area_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `regenRate` double NOT NULL,
  `isOutside` tinyint(1) NOT NULL,
  `visibility` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D2ADFEA59EEA759` (`inventory_id`),
  KEY `IDX_D2ADFEA5BD0F409C` (`area_id`),
  CONSTRAINT `FK_D2ADFEA59EEA759` FOREIGN KEY (`inventory_id`) REFERENCES `Inventory` (`id`),
  CONSTRAINT `FK_D2ADFEA5BD0F409C` FOREIGN KEY (`area_id`) REFERENCES `Area` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Room`
--

LOCK TABLES `Room` WRITE;
/*!40000 ALTER TABLE `Room` DISABLE KEYS */;
INSERT INTO `Room` VALUES (1,9,1,'Arms and Armour','A cramped armory is filled with cheap but sturdy training equipment. A red-hot forge and workshop consume the back half of the already small space. A silhouette of a dwarf can be seen in front of the forge, hammering out new weapons and armor.',0.1,1,0),(2,8,1,'Midgaard Town Center','Before you is the town center.',0.1,1,0),(3,7,1,'A bakery','  A bakery shop is here.',0.1,1,0),(4,12,1,'Midgaard Commons','Standing at the center of a large square, you can see shops and people moving in all directions.',0.1,1,0),(5,13,1,'The South Gate','You are engulfed by a mist.',0.1,1,0),(6,14,1,'A Small Path Leading To Dark Woods','You are engulfed by a mist.',0.1,1,0),(7,15,1,'Outside The South Gate','You are engulfed by a mist.',0.1,1,0),(8,16,1,'Wall Road','An impenetrable wall spans high into the sky. Crafted from enormous stone, strong and resilient, the wall has stood the test of time. A cobblestone path leads around the inside perimeter of the wall.',0.1,1,0),(9,17,1,'Wall Road','You are engulfed by a mist.',0.1,1,0),(10,18,1,'The Temple Square','An immense square with an equally grand temple to the north.',0.1,1,0),(11,19,1,'The Temple of Midgaard','An immense square with an equally grand temple to the north.',0.1,1,0),(12,20,1,'A Sacrifical Pit','An immense square with an equally grand temple to the north.',0.1,1,0),(13,21,1,'Midgaard Commons Road','A narrow and worn road traverses through a chaotic and busy array of shops and merchants.',0.1,1,0),(14,22,1,'A Grocery Store','A grocery store is here.',0.1,1,0);
/*!40000 ALTER TABLE `Room` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-02  0:25:55
