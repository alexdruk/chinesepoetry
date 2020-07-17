-- MySQL dump 10.13  Distrib 8.0.19, for macos10.15 (x86_64)
--
-- Host: localhost    Database: poetry
-- ------------------------------------------------------
-- Server version	8.0.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `topics_id` int NOT NULL AUTO_INCREMENT,
  `topic_name` varchar(150) NOT NULL,
  `topic_synonym` varchar(150) DEFAULT NULL,
  `present` tinyint NOT NULL,
  PRIMARY KEY (`topics_id`),
  UNIQUE KEY `topic_name` (`topic_name`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
INSERT INTO `topics` VALUES (1,'О темах',NULL,1),(2,'Бренность жизни',NULL,1),(3,'Во храме','В храме',1),(4,'Дао','Глубинное',1),(5,'Друг','Мой друг',1),(6,'Жена',NULL,1),(7,'Жизнеописания',NULL,1),(8,'Изгнание','Немилость',1),(9,'Ирония и сарказм',NULL,1),(10,'Любовь',NULL,1),(11,'Любовь матери',NULL,1),(12,'На чужбине',NULL,1),(13,'Нравоучения',NULL,1),(14,'Одиночество',NULL,1),(15,'Патриотизм',NULL,1),(16,'Поминальная песня',NULL,1),(17,'Поэзия вина',NULL,1),(18,'Поэзия чая',NULL,1),(19,'Природа и поэт',NULL,1),(20,'Прочь от мира!',NULL,1),(21,'Разлука с любимым',NULL,1),(22,'Размышления о вечном',NULL,1),(23,'Скороговорки',NULL,1),(24,'Старость',NULL,1),(25,'Стихи к картинам',NULL,1),(26,'Тоска по древности','Древнее',1),(27,'Тяготы жизни',NULL,1),(28,'Честь и гордость',NULL,1),(29,'Эротическая поэзия','Эротика',1),(30,'Юмор и сарказм',NULL,1);
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-07 12:16:35
