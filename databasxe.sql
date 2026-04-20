-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: localhost    Database: sample
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--
create schema sample;
use sample;
DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `AdminID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  PRIMARY KEY (`AdminID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Nandhu','nandhinichandhru@gmail.com','$2y$10$xvzk.wPrqdI5H8mVB7CXcOX6TbFgPPgQhVaBOytE0Rn7Z0MkDrGEu');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batchtable`
--

DROP TABLE IF EXISTS `batchtable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batchtable` (
  `BatchID` int NOT NULL,
  `Batch No` varchar(45) NOT NULL,
  `ProductID` int NOT NULL,
  `Buying Rate` decimal(10,0) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `Expiry Date` date NOT NULL,
  PRIMARY KEY (`BatchID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batchtable`
--

LOCK TABLES `batchtable` WRITE;
/*!40000 ALTER TABLE `batchtable` DISABLE KEYS */;
/*!40000 ALTER TABLE `batchtable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bill table`
--

DROP TABLE IF EXISTS `bill table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill table` (
  `BillID` int NOT NULL,
  `Bill Date` date NOT NULL,
  `Total Amount` decimal(10,0) NOT NULL,
  `Final Amount` decimal(10,0) NOT NULL,
  `Payment method` varchar(45) NOT NULL,
  PRIMARY KEY (`BillID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bill table`
--

LOCK TABLES `bill table` WRITE;
/*!40000 ALTER TABLE `bill table` DISABLE KEYS */;
/*!40000 ALTER TABLE `bill table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorytable`
--

DROP TABLE IF EXISTS `categorytable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorytable` (
  `CategoryID` int NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(50) NOT NULL,
  `Description` longtext NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorytable`
--

LOCK TABLES `categorytable` WRITE;
/*!40000 ALTER TABLE `categorytable` DISABLE KEYS */;
INSERT INTO `categorytable` VALUES (4,'haircare','shampoo'),(5,'skin care','hh');
/*!40000 ALTER TABLE `categorytable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collection product table`
--

DROP TABLE IF EXISTS `collection product table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection product table` (
  `CollectionID` int NOT NULL,
  `ProductID` int NOT NULL,
  PRIMARY KEY (`CollectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection product table`
--

LOCK TABLES `collection product table` WRITE;
/*!40000 ALTER TABLE `collection product table` DISABLE KEYS */;
/*!40000 ALTER TABLE `collection product table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collection table`
--

DROP TABLE IF EXISTS `collection table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection table` (
  `CollectionID` int NOT NULL,
  `Collection name` varchar(45) NOT NULL,
  `Quantity` int NOT NULL,
  `Price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`CollectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collection table`
--

LOCK TABLES `collection table` WRITE;
/*!40000 ALTER TABLE `collection table` DISABLE KEYS */;
/*!40000 ALTER TABLE `collection table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `EmployeeID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  PRIMARY KEY (`EmployeeID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,'Dhanalakshmi','dhana532005@gmail.com','$2y$10$WKlREDgkvgYI1s1xVcDVZ.WMomtoQ.Ei5V3bhPh8R3p0txpAU1lem');
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producttable`
--

DROP TABLE IF EXISTS `producttable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producttable` (
  `ProductID` int NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `CategoryID` int NOT NULL,
  `CategoryName` varchar(255) NOT NULL,
  PRIMARY KEY (`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producttable`
--

LOCK TABLES `producttable` WRITE;
/*!40000 ALTER TABLE `producttable` DISABLE KEYS */;
INSERT INTO `producttable` VALUES (1,'shampoo',4,'haircare'),(2,'soup',4,'haircare'),(3,'kkk',4,'haircare'),(4,'gg',4,'haircare'),(5,'hhh',5,'skin care');
/*!40000 ALTER TABLE `producttable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `representative`
--

DROP TABLE IF EXISTS `representative`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `representative` (
  `Email` varchar(100) NOT NULL,
  `Role` enum('Admin','Employee') NOT NULL,
  PRIMARY KEY (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `representative`
--

LOCK TABLES `representative` WRITE;
/*!40000 ALTER TABLE `representative` DISABLE KEYS */;
INSERT INTO `representative` VALUES ('dhana532005@gmail.com','Employee'),('nandhinichandhru@gmail.com','Admin');
/*!40000 ALTER TABLE `representative` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales table`
--

DROP TABLE IF EXISTS `sales table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales table` (
  `SalesID` int NOT NULL,
  `BillID` int NOT NULL,
  `ProductID` int NOT NULL,
  `Quantity` int NOT NULL,
  `Unit Price` decimal(10,0) NOT NULL,
  `Total Price` decimal(10,0) NOT NULL,
  `CollectionID` varchar(45) NOT NULL,
  PRIMARY KEY (`SalesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales table`
--

LOCK TABLES `sales table` WRITE;
/*!40000 ALTER TABLE `sales table` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stockmovement`
--

DROP TABLE IF EXISTS `stockmovement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stockmovement` (
  `Stock Movement ID` int NOT NULL,
  `ProductID` int NOT NULL,
  `Batch No.` varchar(45) NOT NULL,
  `Quantity` int NOT NULL,
  `Type` varchar(45) NOT NULL,
  `Transaction Date` date NOT NULL,
  PRIMARY KEY (`Stock Movement ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stockmovement`
--

LOCK TABLES `stockmovement` WRITE;
/*!40000 ALTER TABLE `stockmovement` DISABLE KEYS */;
/*!40000 ALTER TABLE `stockmovement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocktable`
--

DROP TABLE IF EXISTS `stocktable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocktable` (
  `ProductID` int NOT NULL,
  `Quantity` int NOT NULL,
  `Minimum Stock Level` int NOT NULL,
  PRIMARY KEY (`ProductID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocktable`
--

LOCK TABLES `stocktable` WRITE;
/*!40000 ALTER TABLE `stocktable` DISABLE KEYS */;
/*!40000 ALTER TABLE `stocktable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Nandhu','nandhinichandhru2@gmail.com','$2y$10$k7HuzLwbLCEpgQCma0qoROVvNE9dOrcZ/Lgj/Y451UngxjgDh.i0K','2025-03-02 06:38:43'),(2,'Nandhu','Sreekala523@wec.edu.in','$2y$10$183m1jNdm12jXQGudFiz0OTqhDKfiOhn.uam492xQd2Dq8pRFrPBq','2025-03-03 06:07:09'),(4,'Dhanalakshmi','dhana532005@gmail.com','$2y$10$Fc3EkWHJnKw9lQQSUaKFwOtnAn9rnOq4E1sGKFPeFKKuV97TrLeW6','2025-03-28 05:06:53'),(6,'Chithra','chithra123@gmail.com','$2y$10$UZT1wo0oYtqR0.3PUZKqyuCAo2xe7QGEU.5f0QJlgOpPa/SPswb1e','2025-03-28 05:07:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendortable`
--

DROP TABLE IF EXISTS `vendortable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendortable` (
  `VendorID` int NOT NULL AUTO_INCREMENT,
  `VendorName` varchar(255) NOT NULL,
  `ContactNo` varchar(20) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` text NOT NULL,
  `GSTNo` varchar(20) NOT NULL,
  `ShippingMethod` enum('Courier','In Person') NOT NULL,
  `PaymentMethod` enum('Cash on Delivery','Online') NOT NULL,
  PRIMARY KEY (`VendorID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `GSTNo` (`GSTNo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendortable`
--

LOCK TABLES `vendortable` WRITE;
/*!40000 ALTER TABLE `vendortable` DISABLE KEYS */;
INSERT INTO `vendortable` VALUES (1,'parvez','1234567895','parvezsrange@gmail.com','ddddd','dddddd','In Person','Online'),(2,'hh','1234567895','Sreekala523@wec.edu.in','hrth','ehehe','In Person','Cash on Delivery');
/*!40000 ALTER TABLE `vendortable` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-03 13:23:23
