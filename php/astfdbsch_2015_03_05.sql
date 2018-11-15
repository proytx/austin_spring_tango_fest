-- MySQL dump 10.13  Distrib 5.5.41-37.0, for Linux (x86_64)
--
-- Host: localhost    Database: austioi2_astfdb
-- ------------------------------------------------------
-- Server version	5.5.41-37.0-log

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
-- Table structure for table `tblAddresses`
--

DROP TABLE IF EXISTS `tblAddresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblAddresses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `City` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `State` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Zipcode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=556 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblCheckins`
--

DROP TABLE IF EXISTS `tblCheckins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblCheckins` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RegistrationID` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `CheckinTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `tblComboParticipantDetail`
--

DROP TABLE IF EXISTS `tblComboParticipantDetail`;
/*!50001 DROP VIEW IF EXISTS `tblComboParticipantDetail`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tblComboParticipantDetail` (
  `ID` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `Level` tinyint NOT NULL,
  `LeadFollow` tinyint NOT NULL,
  `SignedUpFor` tinyint NOT NULL,
  `City` tinyint NOT NULL,
  `State` tinyint NOT NULL,
  `Zipcode` tinyint NOT NULL,
  `DiscountCode` tinyint NOT NULL,
  `History` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `tblComboParticipantMoneyActivity`
--

DROP TABLE IF EXISTS `tblComboParticipantMoneyActivity`;
/*!50001 DROP VIEW IF EXISTS `tblComboParticipantMoneyActivity`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tblComboParticipantMoneyActivity` (
  `ID` tinyint NOT NULL,
  `History` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `tblComboParticipantRegistration`
--

DROP TABLE IF EXISTS `tblComboParticipantRegistration`;
/*!50001 DROP VIEW IF EXISTS `tblComboParticipantRegistration`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `tblComboParticipantRegistration` (
  `ID` tinyint NOT NULL,
  `Name` tinyint NOT NULL,
  `Level` tinyint NOT NULL,
  `LeadFollow` tinyint NOT NULL,
  `SignedUpFor` tinyint NOT NULL,
  `DiscountCode` tinyint NOT NULL,
  `TransactID` tinyint NOT NULL,
  `AddressID` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tblDiscountCodes`
--

DROP TABLE IF EXISTS `tblDiscountCodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblDiscountCodes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Year` int(4) DEFAULT NULL,
  `OneHotBitCode` int(11) DEFAULT NULL,
  `Description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Discount` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblEmails`
--

DROP TABLE IF EXISTS `tblEmails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblEmails` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=663 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblEventPackages`
--

DROP TABLE IF EXISTS `tblEventPackages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblEventPackages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Year` int(4) DEFAULT NULL,
  `HotBitsCode` int(11) DEFAULT NULL,
  `PriceTier` int(2) DEFAULT NULL,
  `Price` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblEventPrices`
--

DROP TABLE IF EXISTS `tblEventPrices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblEventPrices` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Year` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `OneHotBit` int(11) DEFAULT NULL,
  `PricingTier` int(2) DEFAULT NULL,
  `AlacartePrice` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblEvents`
--

DROP TABLE IF EXISTS `tblEvents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblEvents` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Venue` int(11) NOT NULL,
  `Level` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `OneHotBit` int(11) DEFAULT NULL,
  `Description` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Teachers` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblFestival`
--

DROP TABLE IF EXISTS `tblFestival`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblFestival` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Year` varchar(6) NOT NULL DEFAULT '',
  `Name` varchar(30) NOT NULL DEFAULT '',
  `StartDay` varchar(10) NOT NULL DEFAULT '',
  `EndDay` varchar(10) NOT NULL DEFAULT '',
  `EarlyBirdDay` varchar(10) NOT NULL DEFAULT '',
  `RegStartDay` varchar(10) NOT NULL DEFAULT '',
  `Teachers` varchar(150) NOT NULL DEFAULT '?',
  `DJs` varchar(100) NOT NULL DEFAULT '?',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblHistory`
--

DROP TABLE IF EXISTS `tblHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblHistory` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TableName` varchar(64) NOT NULL,
  `PrimaryKeyID` int(11) NOT NULL,
  `ParticipantID` int(11) NOT NULL,
  `DateTimeModified` datetime DEFAULT NULL,
  `Description` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=5054 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblIpAddresses`
--

DROP TABLE IF EXISTS `tblIpAddresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblIpAddresses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IPv4` int(11) unsigned NOT NULL,
  `iso2` varchar(10) NOT NULL COMMENT 'countrycode',
  `regioncode` varchar(10) NOT NULL COMMENT 'state',
  `city` varchar(50) NOT NULL,
  `visits` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'no of times ip visited us',
  `knockings` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'no of times close succession visits',
  `timeoflastvisit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `IPv4` (`IPv4`)
) ENGINE=MyISAM AUTO_INCREMENT=3071 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblMapWikiIds`
--

DROP TABLE IF EXISTS `tblMapWikiIds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblMapWikiIds` (
  `ID` int(10) NOT NULL,
  `ParticipantID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblMoneyActivities`
--

DROP TABLE IF EXISTS `tblMoneyActivities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblMoneyActivities` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RegistrationID` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ActivityDate` datetime DEFAULT NULL,
  `ActivityType` int(11) DEFAULT NULL,
  `EventChoices` int(11) DEFAULT NULL,
  `Amount` decimal(14,2) DEFAULT NULL,
  `ExtraFee` decimal(14,2) DEFAULT NULL,
  `CheckOrConfirmationNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2027 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblMoneyActivityCodes`
--

DROP TABLE IF EXISTS `tblMoneyActivityCodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblMoneyActivityCodes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblParticipants`
--

DROP TABLE IF EXISTS `tblParticipants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblParticipants` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LastName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FirstName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MiddleName` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `LeadFollow` int(2) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  FULLTEXT KEY `FirstName` (`FirstName`),
  FULLTEXT KEY `LastName` (`LastName`)
) ENGINE=MyISAM AUTO_INCREMENT=819 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblPricingTiers`
--

DROP TABLE IF EXISTS `tblPricingTiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblPricingTiers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CutoffDate` date DEFAULT NULL,
  `HowmanyTiers` int(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tblRegistrations`
--

DROP TABLE IF EXISTS `tblRegistrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tblRegistrations` (
  `ParticipantID` int(11) DEFAULT NULL,
  `Level` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `EventChoices` int(11) DEFAULT NULL,
  `DiscountCode` int(11) DEFAULT '0',
  `EmailID` int(11) DEFAULT NULL,
  `AddressID` int(11) DEFAULT NULL,
  `ID` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `InviteID` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `TelephoneNo` bigint(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `tblComboParticipantDetail`
--

/*!50001 DROP TABLE IF EXISTS `tblComboParticipantDetail`*/;
/*!50001 DROP VIEW IF EXISTS `tblComboParticipantDetail`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`austioi2_super`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tblComboParticipantDetail` AS select `A`.`TransactID` AS `ID`,`A`.`Name` AS `Name`,`A`.`Level` AS `Level`,`A`.`LeadFollow` AS `LeadFollow`,`A`.`SignedUpFor` AS `SignedUpFor`,`tblAddresses`.`City` AS `City`,`tblAddresses`.`State` AS `State`,`tblAddresses`.`Zipcode` AS `Zipcode`,`A`.`DiscountCode` AS `DiscountCode`,`B`.`History` AS `History` from ((`tblComboParticipantRegistration` `A` join `tblComboParticipantMoneyActivity` `B` on((`B`.`ID` = `A`.`TransactID`))) join `tblAddresses` on((`tblAddresses`.`ID` = `A`.`AddressID`))) order by `A`.`Name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tblComboParticipantMoneyActivity`
--

/*!50001 DROP TABLE IF EXISTS `tblComboParticipantMoneyActivity`*/;
/*!50001 DROP VIEW IF EXISTS `tblComboParticipantMoneyActivity`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`austioi2_super`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tblComboParticipantMoneyActivity` AS select `tblMoneyActivities`.`RegistrationID` AS `ID`,replace(group_concat(concat(date_format(`tblMoneyActivities`.`ActivityDate`,' %Y-%b-%d'),' $',`tblMoneyActivities`.`Amount`,' ',`tblMoneyActivityCodes`.`Description`,' ',(`tblMoneyActivities`.`CheckOrConfirmationNo` collate utf8_unicode_ci)) separator ','),',','<br/>') AS `History` from (`tblMoneyActivities` join `tblMoneyActivityCodes` on((`tblMoneyActivityCodes`.`ID` = `tblMoneyActivities`.`ActivityType`))) group by `tblMoneyActivities`.`RegistrationID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `tblComboParticipantRegistration`
--

/*!50001 DROP TABLE IF EXISTS `tblComboParticipantRegistration`*/;
/*!50001 DROP VIEW IF EXISTS `tblComboParticipantRegistration`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`austioi2_super`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `tblComboParticipantRegistration` AS select `tblParticipants`.`ID` AS `ID`,concat(`tblParticipants`.`LastName`,', ',`tblParticipants`.`FirstName`,' ',`tblParticipants`.`MiddleName`) AS `Name`,`tblRegistrations`.`Level` AS `Level`,elt(`tblParticipants`.`LeadFollow`,'Lead','Follow','Both?') AS `LeadFollow`,export_set(`tblRegistrations`.`EventChoices`,'Y','N',',',10) AS `SignedUpFor`,`tblRegistrations`.`DiscountCode` AS `DiscountCode`,`tblRegistrations`.`ID` AS `TransactID`,`tblRegistrations`.`AddressID` AS `AddressID` from (`tblParticipants` join `tblRegistrations` on((`tblRegistrations`.`ParticipantID` = `tblParticipants`.`ID`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-03-05 20:00:06
