

CREATE DATABASE  IF NOT EXISTS `teleteachers` /*!40100 DEFAULT CHARACTER SET utf8 */;
CREATE DATABASE  IF NOT EXISTS `portal` /*!40100 DEFAULT CHARACTER SET utf8 */;
CREATE DATABASE  IF NOT EXISTS `laravel` /*!40100 DEFAULT CHARACTER SET utf8 */;
CREATE DATABASE  IF NOT EXISTS `caseloader` /*!40100 DEFAULT CHARACTER SET utf8 */;

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '123';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;

USE `teleteachers`;
-- MySQL dump 10.13  Distrib 8.0.17, for Win64 (x86_64)
--
-- Host: teleteachers-staging.c9qtqrj9gfbx.us-west-2.rds.amazonaws.com    Database: teleteachers
-- ------------------------------------------------------
-- Server version	5.7.30-log

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `adjustment_reasons`
--

DROP TABLE IF EXISTS `adjustment_reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adjustment_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_adjustment_reasons_on_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adjustment_reasons`
--

LOCK TABLES `adjustment_reasons` WRITE;
/*!40000 ALTER TABLE `adjustment_reasons` DISABLE KEYS */;
INSERT INTO `adjustment_reasons` VALUES (1,'222','Exceeds the contracted maximum number of hours/days/units by this provider for this period. This is not patient specific.'),(2,'115','Procedure postponed, canceled, or delayed.'),(3,'141','Claim spans eligible and ineligible periods of coverage.'),(4,'223','Adjustment code for mandated federal, state or local law/regulation that is not already covered by another code and is mandated before a new code can be created.'),(5,'116','The advance indemnification notice signed by the patient did not comply with requirements.'),(6,'142','Monthly Medicaid patient liability amount.'),(7,'B10','Allowed amount has been reduced because a component of the basic procedure/test was paid. The beneficiary is not liable for more than the charge limit for the basic procedure/test.'),(8,'B1','Non-covered visits.'),(9,'224','Patient identification compromised by identity theft. Identity verification required for processing this and future claims.'),(10,'20','This injury/illness is covered by the liability carrier.'),(11,'117','Transportation is only covered to the closest facility that can provide the necessary care.'),(12,'143','Portion of payment deferred.'),(13,'B11','The claim/service has been transferred to the proper payer/processor for processing. Claim/service not covered by this payer/processor.'),(14,'B2','Covered visits.'),(15,'225','Penalty or Interest Payment by Payer (Only used for plan to plan encounter reporting within the 837)'),(16,'21','This injury/illness is the liability of the no-fault carrier.'),(17,'118','ESRD network support adjustment.'),(18,'144','Incentive adjustment, e.g. preferred product/service.'),(19,'170','Payment is denied when performed/billed by this type of provider.'),(20,'B12','Services not documented in patients\' medical records.'),(21,'B3','Covered charges.'),(22,'226','Information requested from the Billing/Rendering Provider was not provided or was insufficient/incomplete. At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(23,'119','Benefit maximum for this time period or occurrence has been reached.'),(24,'145','Premium payment withholding'),(25,'171','Payment is denied when performed/billed by this type of provider in this type of facility.'),(26,'B13','Previously paid. Payment for this claim/service may have been provided in a previous payment.'),(27,'B4','Late filing penalty.'),(28,'227','Information requested from the patient/insured/responsible party was not provided or was insufficient/incomplete. At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(29,'22','This care may be covered by another payer per coordination of benefits.'),(30,'146','Diagnosis was invalid for the date(s) of service reported.'),(31,'172','Payment is adjusted when performed/billed by a provider of this specialty'),(32,'B14','Only one visit or consultation per physician per day is covered.'),(33,'B5','Coverage/program guidelines were not met or were exceeded.'),(34,'228','Denied for failure of this provider, another provider or the subscriber to supply requested information to a previous payer for their adjudication'),(35,'23','The impact of prior payer(s) adjudication including payments and/or adjustments.'),(36,'50','These are non-covered services because this is not deemed a \'medical necessity\' by the payer.'),(37,'147','Provider contracted/negotiated rate expired or not on file.'),(38,'173','Service was not prescribed by a physician.'),(39,'B15','This service/procedure requires that a qualifying service/procedure be received and covered. The qualifying other service/procedure has not been received/adjudicated.'),(40,'B6','This payment is adjusted when performed/billed by this type of provider, by this type of provider in this type of facility, or by a provider of this specialty.'),(41,'24','Charges are covered under a capitation agreement/managed care plan.'),(42,'51','These are non-covered services because this is a pre-existing condition'),(43,'148','Information from another provider was not provided or was insufficient/incomplete. This change effective 7/1/2009: Information from another provider was not provided or was insufficient/incomplete. At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(44,'174','Service was not prescribed prior to delivery.'),(45,'B16','\'New Patient\' qualifications were not met.'),(46,'B7','This provider was not certified/eligible to be paid for this procedure/service on this date of service.'),(47,'52','The referring/prescribing/rendering provider is not eligible to refer/prescribe/order/perform the service billed.'),(48,'149','Lifetime benefit maximum has been reached for this service/benefit category.'),(49,'175','Prescription is incomplete.'),(50,'25','Payment denied. Your Stop loss deductible has not been met.'),(51,'B17','Payment adjusted because this service was not prescribed by a physician, not prescribed prior to delivery, the prescription is incomplete, or the prescription is not current.'),(52,'B8','Alternative services were available, and should have been utilized.'),(53,'53','Services by an immediate relative or a member of the same household are not covered.'),(54,'176','Prescription is not current.'),(55,'26','Expenses incurred prior to coverage.'),(56,'B18','This procedure code and modifier were invalid on the date of service.'),(57,'B9','Patient is enrolled in a Hospice.'),(58,'80','Outlier days. (Handled in QTY, QTY01=OU)'),(59,'177','Patient has not met the required eligibility requirements.'),(60,'27','Expenses incurred after coverage terminated.'),(61,'54','Multiple physicians/assistants are not covered in this case.'),(62,'B19','Claim/service adjusted because of the finding of a Review Organization.'),(63,'81','Discharges.'),(64,'178','Patient has not met the required spend down requirements.'),(65,'28','Coverage not in effect at the time the service was provided.'),(66,'179','Patient has not met the required waiting requirements.'),(67,'29','The time limit for filing has expired.'),(68,'55','Procedure/treatment is deemed experimental/investigational by the payer.'),(69,'82','PIP days.'),(70,'56','Procedure/treatment has not been deemed \'proven to be effective\' by the payer.'),(71,'83','Total visits.'),(72,'57','Payment denied/reduced because the payer deems the information submitted does not support this level of service, this many services, this length of service, this dosage, or this day\'s supply.'),(73,'84','Capital Adjustment. (Handled in MIA)'),(74,'58','Treatment was deemed by the payer to have been rendered in an inappropriate or invalid place of service.'),(75,'85','Patient Interest Adjustment (Use Only Group code PR)'),(76,'100','Payment made to patient/insured/responsible party/employer.'),(77,'59','Processed based on multiple or concurrent procedure rules. (For example multiple surgery or diagnostic imaging, concurrent anesthesia.)'),(78,'86','Statutory Adjustment.'),(79,'101','Predetermination: anticipated payment upon completion of services or claim adjudication.'),(80,'87','Transfer amount.'),(81,'102','Major Medical Adjustment.'),(82,'103','Provider promotional discount (e.g., Senior citizen discount).'),(83,'210','Payment adjusted because pre-certification/authorization not received in a timely fashion'),(84,'88','Adjustment amount represents collection against receivable created in prior overpayment.'),(85,'104','Managed care withholding.'),(86,'130','Claim submission fee.'),(87,'211','National Drug Codes (NDC) not eligible for rebate, are not covered.'),(88,'89','Professional fees removed from charges.'),(89,'105','Tax withholding.'),(90,'131','Claim specific negotiated discount.'),(91,'212','Administrative surcharges are not covered'),(92,'A0','Patient refund amount.'),(93,'106','Patient payment option/election not in effect.'),(94,'132','Prearranged demonstration project adjustment.'),(95,'213','Non-compliance with the physician self referral prohibition legislation or payer policy.'),(96,'A1','Claim/Service denied. At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(97,'107','The related or qualifying claim/service was not identified on this claim.'),(98,'133','The disposition of this claim/service is pending further review.'),(99,'214','Workers\' Compensation claim adjudicated as non-compensable. This Payer not liable for claim or service/treatment. (Note: To be used for Workers\' Compensation only)'),(100,'10','The diagnosis is inconsistent with the patient\'s gender.'),(101,'A2','Contractual adjustment.'),(102,'108','Rent/purchase guidelines were not met.'),(103,'134','Technical fees removed from charges.'),(104,'160','Injury/illness was the result of an activity that is a benefit exclusion.'),(105,'215','Based on subrogation of a third party settlement'),(106,'A3','Medicare Secondary Payer liability met.'),(107,'109','Claim not covered by this payer/contractor. You must send the claim to the correct payer/contractor.'),(108,'135','Interim bills cannot be processed.'),(109,'161','Provider performance bonus'),(110,'216','Based on the findings of a review organization'),(111,'11','The diagnosis is inconsistent with the procedure.'),(112,'A4','Medicare Claim PPS Capital Day Outlier Amount.'),(113,'136','Failure to follow prior payer\'s coverage rules. (Use Group Code OA).'),(114,'162','State-mandated Requirement for Property and Casualty, see Claim Payment Remarks Code for specific explanation.'),(115,'217','Based on payer reasonable and customary fees. No maximum allowable defined by legislated fee arrangement. (Note: To be used for Workers\' Compensation only)'),(116,'12','The diagnosis is inconsistent with the provider type.'),(117,'D1','Claim/service denied. Level of subluxation is missing or inadequate.'),(118,'A5','Medicare Claim PPS Capital Cost Outlier Amount.'),(119,'137','Regulatory Surcharges, Assessments, Allowances or Health Related Taxes.'),(120,'163','Attachment referenced on the claim was not received.'),(121,'218','Based on entitlement to benefits (Note: To be used for Workers\' Compensation only)'),(122,'13','The date of death precedes the date of service.'),(123,'40','Charges do not meet qualifications for emergent/urgent care.'),(124,'D2','Claim lacks the name, strength, or dosage of the drug furnished.'),(125,'A6','Prior hospitalization or 30 day transfer requirement not met.'),(126,'138','Appeal procedures not followed or time limits not met.'),(127,'164','Attachment referenced on the claim was not received in a timely fashion.'),(128,'190','Payment is included in the allowance for a Skilled Nursing Facility (SNF) qualified stay.'),(129,'219','Based on extent of injury (Note: To be used for Workers\' Compensation only)'),(130,'14','The date of birth follows the date of service.'),(131,'41','Discount agreed to in Preferred Provider contract.'),(132,'W1','Workers Compensation State Fee Schedule Adjustment'),(133,'D20','Claim/Service missing service/product information.'),(134,'D3','Claim/service denied because information to indicate if the patient owns the equipment that requires the part or supply was missing.'),(135,'A7','Presumptive Payment Adjustment'),(136,'139','Contracted funding agreement - Subscriber is employed by the provider of services.'),(137,'165','Referral absent or exceeded.'),(138,'191','Not a work related injury/illness and thus not the liability of the workers\' compensation carrier.'),(139,'15','The authorization number is missing, invalid, or does not apply to the billed services or provider.'),(140,'42','Charges exceed our fee schedule or maximum allowable amount. (Use CARC 45)'),(141,'D21','This (these) diagnosis(es) is (are) missing or are invalid'),(142,'D4','Claim/service does not indicate the period of time for which this will be needed.'),(143,'A8','Ungroupable DRG.'),(144,'166','These services were submitted after this payers responsibility for processing claims under this plan ended.'),(145,'192','Non standard adjustment code from paper remittance. Note: This code is to be used by providers/payers providing Coordination of Benefits information to another payer in the 837 transaction only. This code is only used when the non-standard code cannot be reasonably mapped to an existing Claims Adjustment Reason Code, specifically Deductible, Coinsurance and Co-payment.'),(146,'16','Claim/service lacks information which is needed for adjudication. At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(147,'43','Gramm-Rudman reduction.'),(148,'D5','Claim/service denied. Claim lacks individual lab codes included in the test.'),(149,'167','This (these) diagnosis(es) is (are) not covered.'),(150,'193','Original payment decision is being maintained. Upon review, it was determined that this claim was processed properly.'),(151,'17','Requested information was not provided or was insufficient/incomplete. At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(152,'70','Cost outlier - Adjustment to compensate for additional costs.'),(153,'D6','Claim/service denied. Claim did not include patient\'s medical record for the service.'),(154,'194','Anesthesia performed by the operating physician, the assistant surgeon or the attending physician.'),(155,'18','Duplicate claim/service.'),(156,'44','Prompt-pay discount.'),(157,'71','Primary Payer amount.'),(158,'168','Service(s) have been considered under the patient\'s medical plan. Benefits are not available under this dental plan.'),(159,'D7','Claim/service denied. Claim lacks date of patient\'s most recent physician visit.'),(160,'195','Refund issued to an erroneous priority payer for this claim/service.'),(161,'19','This is a work-related injury/illness and thus the liability of the Worker\'s Compensation Carrier.'),(162,'45','Charge exceeds fee schedule/maximum allowable or contracted/legislated fee arrangement. (Use Group Codes PR or CO depending upon liability).'),(163,'72','Coinsurance day. (Handled in QTY, QTY01=CD)'),(164,'169','Alternate benefit has been provided.'),(165,'D8','Claim/service denied. Claim lacks indicator that \'x-ray is available for review.\''),(166,'46','This (these) service(s) is (are) not covered.'),(167,'73','Administrative days.'),(168,'196','Claim/service denied based on prior payer\'s coverage determination.'),(169,'D9','Claim/service denied. Claim lacks invoice or statement certifying the actual cost of the lens, less discounts or the type of intraocular lens used.'),(170,'47','This (these) diagnosis(es) is (are) not covered, missing, or are invalid.'),(171,'74','Indirect Medical Education Adjustment.'),(172,'197','Precertification/authorization/notification absent.'),(173,'48','This (these) procedure(s) is (are) not covered.'),(174,'75','Direct Medical Education Adjustment.'),(175,'198','Precertification/authorization exceeded.'),(176,'49','These are non-covered services because this is a routine exam or screening procedure done in conjunction with a routine exam.'),(177,'76','Disproportionate Share Adjustment.'),(178,'199','Revenue code and Procedure code do not match.'),(179,'77','Covered days. (Handled in QTY, QTY01=CA)'),(180,'78','Non-Covered days/Room charge adjustment.'),(181,'200','Expenses incurred during lapse in coverage'),(182,'79','Cost Report days. (Handled in MIA15)'),(183,'120','Patient is covered by a managed care plan.'),(184,'201','Workers Compensation case settled. Patient is responsible for amount of this claim/service through WC \'Medicare set aside arrangement\' or other agreement. (Use group code PR).'),(185,'121','Indemnification adjustment - compensation for outstanding member responsibility.'),(186,'202','Non-covered personal comfort or convenience services.'),(187,'122','Psychiatric reduction.'),(188,'203','Discontinued or reduced service.'),(189,'123','Payer refund due to overpayment.'),(190,'204','This service/equipment/drug is not covered under the patient\'s current benefit plan'),(191,'124','Payer refund amount - not our patient.'),(192,'150','Payer deems the information submitted does not support this level of service.'),(193,'205','Pharmacy discount card processing fee'),(194,'125','Submission/billing error(s). At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(195,'151','Payment adjusted because the payer deems the information submitted does not support this many/frequency of services.'),(196,'206','National Provider Identifier - missing.'),(197,'126','Deductible -- Major Medical'),(198,'152','Payer deems the information submitted does not support this length of service.'),(199,'207','National Provider identifier - Invalid format'),(200,'B20','Procedure/service was partially or fully furnished by another provider.'),(201,'127','Coinsurance -- Major Medical'),(202,'153','Payer deems the information submitted does not support this dosage.'),(203,'208','National Provider Identifier - Not matched.'),(204,'30','Payment adjusted because the patient has not met the required eligibility, spend down, waiting, or residency requirements.'),(205,'B21','The charges were reduced because the service/care was partially furnished by another physician.'),(206,'128','Newborn\'s services are covered in the mother\'s Allowance.'),(207,'154','Payer deems the information submitted does not support this day\'s supply.'),(208,'180','Patient has not met the required residency requirements.'),(209,'209','Per regulatory or other agreement. The provider cannot collect this amount from the patient. However, this amount may be billed to subsequent payer. Refund to patient if collected. (Use Group code OA)'),(210,'31','Patient cannot be identified as our insured.'),(211,'D10','Claim/service denied. Completed physician financial relationship form not on file.'),(212,'B22','This payment is adjusted based on the diagnosis.'),(213,'129','Prior processing information appears incorrect.'),(214,'155','Patient refused the service/procedure.'),(215,'181','Procedure code was invalid on the date of service.'),(216,'1','Deductible Amount'),(217,'32','Our records indicate that this dependent is not an eligible dependent as defined.'),(218,'D11','Claim lacks completed pacemaker registration form.'),(219,'B23','Procedure billed is not authorized per your Clinical Laboratory Improvement Amendment (CLIA) proficiency test.'),(220,'156','Flexible spending account payments'),(221,'182','Procedure modifier was invalid on the date of service.'),(222,'2','Coinsurance Amount'),(223,'D12','Claim/service denied. Claim does not identify who performed the purchased diagnostic test or the amount you were charged for the test.'),(224,'157','Service/procedure was provided as a result of an act of war.'),(225,'183','The referring provider is not eligible to refer the service billed.'),(226,'3','Co-payment Amount'),(227,'33','Insured has no dependent coverage.'),(228,'60','Charges for outpatient services with this proximity to inpatient services are not covered. This change to be effective 1/1/2009: Charges for outpatient services are not covered when performed within a period of time prior to or after inpatient services.'),(229,'D13','Claim/service denied. Performed by a facility/supplier in which the ordering/referring physician has a financial interest.'),(230,'158','Service/procedure was provided outside of the United States.'),(231,'184','The prescribing/ordering provider is not eligible to prescribe/order the service billed.'),(232,'4','The procedure code is inconsistent with the modifier used or a required modifier is missing.'),(233,'34','Insured has no coverage for newborns.'),(234,'61','Penalty for failure to obtain second surgical opinion.'),(235,'D14','Claim lacks indication that plan of treatment is on file.'),(236,'159','Service/procedure was provided as a result of terrorism.'),(237,'185','The rendering provider is not eligible to perform the service billed.'),(238,'5','The procedure code/bill type is inconsistent with the place of service.'),(239,'35','Lifetime benefit maximum has been reached.'),(240,'62','Payment denied/reduced for absence of, or exceeded, pre-certification/authorization.'),(241,'D15','Claim lacks indication that service was supervised or evaluated by a physician.'),(242,'186','Level of care change adjustment.'),(243,'6','The procedure/revenue code is inconsistent with the patient\'s age.'),(244,'36','Balance does not exceed co-payment amount.'),(245,'63','Correction to a prior claim.'),(246,'D16','Claim lacks prior payer payment information.'),(247,'187','Health Savings account payments'),(248,'7','The procedure/revenue code is inconsistent with the patient\'s gender.'),(249,'37','Balance does not exceed deductible.'),(250,'64','Denial reversed per Medical Review.'),(251,'90','Ingredient cost adjustment.'),(252,'D17','Claim/Service has invalid non-covered days.'),(253,'188','This product/procedure is only covered when used according to FDA recommendations.'),(254,'8','The procedure code is inconsistent with the provider type/specialty (taxonomy).'),(255,'38','Services not provided or authorized by designated (network/primary care) providers.'),(256,'65','Procedure code was incorrect. This payment reflects the correct code.'),(257,'91','Dispensing fee adjustment.'),(258,'D18','Claim/Service has missing diagnosis information.'),(259,'189','\'Not otherwise classified\' or \'unlisted\' procedure code (CPT/HCPCS) was billed when there is a specific procedure code for this procedure/service'),(260,'9','The diagnosis is inconsistent with the patient\'s age.'),(261,'39','Services denied at the time authorization/pre-certification was requested.'),(262,'92','Claim Paid in full.'),(263,'D19','Claim/Service lacks Physician/Operative or other supporting documentation'),(264,'66','Blood Deductible.'),(265,'93','No Claim level Adjustments.'),(266,'67','Lifetime reserve days. (Handled in QTY, QTY01=LA)'),(267,'94','Processed in Excess of charges.'),(268,'68','DRG weight. (Handled in CLP12)'),(269,'95','Plan procedures not followed.'),(270,'69','Day outlier amount.'),(271,'96','Non-covered charge(s). At least one Remark Code must be provided (may be comprised of either the Remittance Advice Remark Code or NCPDP Reject Reason Code.)'),(272,'110','Billing date predates service date.'),(273,'97','The benefit for this service is included in the payment/allowance for another service/procedure that has already been adjudicated.'),(274,'111','Not covered unless the provider accepts assignment.'),(275,'98','The hospital must file the Medicare claim for this inpatient non-physician service.'),(276,'112','Service not furnished directly to the patient and/or not documented.'),(277,'220','The applicable fee schedule does not contain the billed code. Please resubmit a bill with the appropriate fee schedule code(s) that best describe the service(s) provided and supporting documentation if required. (Note: To be used for Workers\' Compensation only)'),(278,'113','Payment denied because service/procedure was provided outside the United States or as a result of war.'),(279,'221','Workers\' Compensation claim is under investigation. (Note: To be used for Workers\' Compensation only. Claim pending final resolution)'),(280,'99','Medicare Secondary Payer Adjustment Amount.'),(281,'114','Procedure/product not approved by the Food and Drug Administration.'),(282,'140','Patient/Insured health identification number and name do not match.');
/*!40000 ALTER TABLE `adjustment_reasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adjustment_remarks`
--

DROP TABLE IF EXISTS `adjustment_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adjustment_remarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_adjustment_remarks_on_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=796 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adjustment_remarks`
--

LOCK TABLES `adjustment_remarks` WRITE;
/*!40000 ALTER TABLE `adjustment_remarks` DISABLE KEYS */;
INSERT INTO `adjustment_remarks` VALUES (1,'N124','Payment has been denied for the/made only for a less extensive service/item because the information furnished does not substantiate the need for the (more extensive) service/item. The patient is liable for the charges for this service/item as you informed the patient in writing before the service/item was furnished that we would not pay for it, and the patient agreed to pay.'),(2,'N256','Missing/incomplete/invalid billing provider/supplier name.'),(3,'N388','Missing/incomplete/invalid prescription number'),(4,'MA123','Your center was not selected to participate in this study, therefore, we cannot pay for these services.'),(5,'M30','Missing pathology report.'),(6,'N62','Inpatient admission spans multiple rate periods. Resubmit separate claims.'),(7,'N125','Payment has been (denied for the/made only for a less extensive) service/item because the information furnished does not substantiate the need for the (more extensive) service/item. If you have collected any amount from the patient, you must refund that amount to the patient within 30 days of receiving this notice.'),(8,'N257','Missing/incomplete/invalid billing provider/supplier primary identifier.'),(9,'N389','Duplicate prescription number submitted.'),(10,'MA124','Processed for IME only.'),(11,'M31','Missing radiology report.'),(12,'N63','Rebill services on separate claim lines.'),(13,'N126','Social Security Records indicate that this individual has been deported. This payer does not cover items and services furnished to individuals who have been deported.'),(14,'N258','Missing/incomplete/invalid billing provider/supplier address.'),(15,'M32','Alert: This is a conditional payment made pending a decision on this service by the patient\'s primary payer. This payment may be subject to refund upon your receipt of any additional payment for this service from another payer. You must contact this office immediately upon receipt of an additional payment for this service.'),(16,'N64','The \'from\' and \'to\' dates must be different.'),(17,'N390','This service/report cannot be billed separately.'),(18,'N127','This is a misdirected claim/service for a United Mine Workers of America (UMWA) beneficiary. Please submit claims to them.'),(19,'N259','Missing/incomplete/invalid billing provider/supplier secondary identifier.'),(20,'MA125','Per legislation governing this program, payment constitutes payment in full.'),(21,'M33','Missing/incomplete/invalid UPIN for the ordering/referring/performing provider.'),(22,'N65','Procedure code or procedure rate count cannot be determined, or was not on file, for the date of service/provider.'),(23,'N391','Missing emergency department records.'),(24,'N128','This amount represents the prior to coverage portion of the allowance.'),(25,'MA126','Pancreas transplant not covered unless kidney transplant performed.'),(26,'M34','Claim lacks the CLIA certification number.'),(27,'N66','Missing/incomplete/invalid documentation.'),(28,'N392','Incomplete/invalid emergency department records.'),(29,'N129','Not eligible due to the patient\'s age.'),(30,'MA127','Reserved for future use.'),(31,'M35','Missing/incomplete/invalid pre-operative photos or visual field results.'),(32,'N67','Professional provider services not paid separately. Included in facility payment under a demonstration project. Apply to that facility for payment, or resubmit your claim if: the facility notifies you the patient was excluded from this demonstration; or if you furnished these services in another location on the date of the patient\'s admission or discharge from a demonstration hospital. If services were furnished in a facility not involved in the demonstration on the same date the patient was discharged from or admitted to a demonstration facility, you must report the provider ID number for the non-demonstration facility on the new claim.'),(33,'N260','Missing/incomplete/invalid billing provider/supplier contact information.'),(34,'N393','Missing progress notes/report.'),(35,'MA128','Missing/incomplete/invalid FDA approval number.'),(36,'M36','This is the 11th rental month. We cannot pay for this until you indicate that the patient has been given the option of changing the rental to a purchase.'),(37,'N68','Prior payment being cancelled as we were subsequently notified this patient was covered by a demonstration project in this site of service. Professional services were included in the payment made to the facility. You must contact the facility for your payment. Prior payment made to you by the patient or another insurer for this claim must be refunded to the payer within 30 days.'),(38,'N261','Missing/incomplete/invalid operating provider name.'),(39,'MA129','This provider was not certified for this procedure on this date of service.'),(40,'M37','Service not covered when the patient is under age 35.'),(41,'N69','PPS (Prospective Payment System) code changed by claims processing system. Insufficient visits or therapies.'),(42,'N394','Incomplete/invalid progress notes/report.'),(43,'M38','The patient is liable for the charges for this service as you informed the patient in writing before the service was furnished that we would not pay for it, and the patient agreed to pay.'),(44,'N130','Alert: Consult plan benefit documents/guidelines for information about restrictions for this service.'),(45,'N262','Missing/incomplete/invalid operating provider primary identifier.'),(46,'N395','Missing laboratory report.'),(47,'MA130','Your claim contains incomplete and/or invalid information, and no appeal rights are afforded because the claim is unprocessable. Please submit a new claim with the complete/correct information.'),(48,'M39','Alert: The patient is not liable for payment for this service as the advance notice of non-coverage you provided the patient did not comply with program requirements.'),(49,'N131','Total payments under multiple contracts cannot exceed the allowance for this service.'),(50,'N263','Missing/incomplete/invalid operating provider secondary identifier.'),(51,'N396','Incomplete/invalid laboratory report.'),(52,'MA131','Physician already paid for services in conjunction with this demonstration claim. You must have the physician withdraw that claim and refund the payment before we can process your claim.'),(53,'N70','Consolidated billing and payment applies.'),(54,'N132','Alert: Payments will cease for services rendered by this US Government debarred or excluded provider after the 30 day grace period as previously notified.'),(55,'N264','Missing/incomplete/invalid ordering provider name.'),(56,'N397','Benefits are not available for incomplete service(s)/undelivered item(s).'),(57,'MA132','Adjustment to the pre-demonstration rate.'),(58,'N71','Your unassigned claim for a drug or biological, clinical diagnostic laboratory services or ambulance service was processed as an assigned claim. You are required by law to accept assignment for these types of claims.'),(59,'N133','Alert: Services for predetermination and services requesting payment are being processed separately.'),(60,'N265','Missing/incomplete/invalid ordering provider primary identifier.'),(61,'MA133','Claim overlaps inpatient stay. Rebill only those services rendered outside the inpatient stay.'),(62,'M40','Claim must be assigned and must be filed by the practitioner\'s employer.'),(63,'N72','PPS (Prospective Payment System) code changed by medical reviewers. Not supported by clinical records.'),(64,'N134','Alert: This represents your scheduled payment for this service. If treatment has been discontinued, please contact Customer Service.'),(65,'N266','Missing/incomplete/invalid ordering provider address.'),(66,'N398','Missing elective consent form.'),(67,'MA134','Missing/incomplete/invalid provider number of the facility where the patient resides.'),(68,'M41','We do not pay for this as the patient has no legal obligation to pay for this.'),(69,'N73','A Skilled Nursing Facility is responsible for payment of outside providers who furnish these services/supplies under arrangement to its residents.'),(70,'N135','Record fees are the patient\'s responsibility and limited to the specified co-payment.'),(71,'N267','Missing/incomplete/invalid ordering provider secondary identifier.'),(72,'N399','Incomplete/invalid elective consent form.'),(73,'M42','The medical necessity form must be personally signed by the attending physician.'),(74,'N74','Resubmit with multiple claims, each claim covering services provided in only one calendar month.'),(75,'N136','Alert: To obtain information on the process to file an appeal in Arizona, call the Department\'s Consumer Assistance Office at (602) 912-8444 or (800) 325-2548.'),(76,'N268','Missing/incomplete/invalid ordering provider contact information.'),(77,'M43','Payment for this service previously issued to you or another provider by another carrier/intermediary.'),(78,'N75','Missing/incomplete/invalid tooth surface information.'),(79,'N137','Alert: The provider acting on the Member\'s behalf, may file an appeal with the Payer. The provider, acting on the Member\'s behalf, may file a complaint with the State Insurance Regulatory Authority without first filing an appeal, if the coverage decision involves an urgent condition for which care has not been rendered. The address may be obtained from the State Insurance Regulatory Authority.'),(80,'N269','Missing/incomplete/invalid other provider name.'),(81,'M44','Missing/incomplete/invalid condition code.'),(82,'N76','Missing/incomplete/invalid number of riders.'),(83,'N138','Alert: In the event you disagree with the Dental Advisor\'s opinion and have additional information relative to the case, you may submit radiographs to the Dental Advisor Unit at the subscriber\'s dental insurance carrier for a second Independent Dental Advisor Review.'),(84,'M45','Missing/incomplete/invalid occurrence code(s).'),(85,'N77','Missing/incomplete/invalid designated provider number.'),(86,'N270','Missing/incomplete/invalid other provider primary identifier.'),(87,'N139','Alert: Under the Code of Federal Regulations, Chapter 32, Section 199.13 a non-participating provider is not an appropriate appealing party. Therefore, if you disagree with the Dental Advisor\'s opinion, you may appeal the determination if appointed in writing, by the beneficiary, to act as his/her representative. Should you be appointed as a representative, submit a copy of this letter, a signed statement explaining the matter in which you disagree, and any radiographs and relevant information to the subscriber\'s Dental insurance carrier within 90 days from the date of this letter.'),(88,'M46','Missing/incomplete/invalid occurrence span code(s).'),(89,'N78','The necessary components of the child and teen checkup (EPSDT) were not completed.'),(90,'N271','Missing/incomplete/invalid other provider secondary identifier.'),(91,'M47','Missing/incomplete/invalid internal or document control number.'),(92,'N140','Alert: You have not been designated as an authorized OCONUS provider therefore are not considered an appropriate appealing party. If the beneficiary has appointed you, in writing, to act as his/her representative and you disagree with the Dental Advisor\'s opinion, you may appeal by submitting a copy of this letter, a signed statement explaining the matter in which you disagree, and any relevant information to the subscriber\'s Dental insurance carrier within 90 days from the date of this letter.'),(93,'N79','Service billed is not compatible with patient location information.'),(94,'N272','Missing/incomplete/invalid other payer attending provider identifier.'),(95,'M48','Payment for services furnished to hospital inpatients (other than professional services of physicians) can only be made to the hospital. You must request payment from the hospital rather than the patient for this service.'),(96,'M49','Missing/incomplete/invalid value code(s) or amount(s).'),(97,'N141','The patient was not residing in a long-term care facility during all or part of the service dates billed.'),(98,'N273','Missing/incomplete/invalid other payer operating provider identifier.'),(99,'N80','Missing/incomplete/invalid prenatal screening information.'),(100,'N142','The original claim was denied. Resubmit a new claim, not a replacement claim.'),(101,'MA01','Alert: If you do not agree with what we approved for these services, you may appeal our decision. To make sure that we are fair to you, we require another individual that did not process your initial claim to conduct the appeal. However, in order to be eligible for an appeal, you must write to us within 120 days of the date you received this notice, unless you have a good reason for being late.'),(102,'N274','Missing/incomplete/invalid other payer other provider identifier.'),(103,'N81','Procedure billed is not compatible with tooth surface code.'),(104,'N143','The patient was not in a hospice program during all or part of the service dates billed.'),(105,'MA02','Alert: If you do not agree with this determination, you have the right to appeal. You must file a written request for an appeal within 180 days of the date you receive this notice.'),(106,'N275','Missing/incomplete/invalid other payer purchased service provider identifier.'),(107,'M50','Missing/incomplete/invalid revenue code(s).'),(108,'N82','Provider must accept insurance payment as payment in full when a third party payer contract specifies full reimbursement.'),(109,'N144','The rate changed during the dates of service billed.'),(110,'MA03','If you do not agree with the approved amounts and $100 or more is in dispute (less deductible and coinsurance), you may ask for a hearing within six months of the date of this notice. To meet the $100, you may combine amounts on other claims that have been denied, including reopened appeals if you received a revised decision. You must appeal each claim on time.'),(111,'N276','Missing/incomplete/invalid other payer referring provider identifier.'),(112,'M51','Missing/incomplete/invalid procedure code(s).'),(113,'N83','No appeal rights. Adjudicative decision based on the provisions of a demonstration project.'),(114,'N145','Missing/incomplete/invalid provider identifier for this place of service.'),(115,'MA04','Secondary payment cannot be considered without the identity of or payment information from the primary payer. The information was either not reported or was illegible.'),(116,'N277','Missing/incomplete/invalid other payer rendering provider identifier.'),(117,'M52','Missing/incomplete/invalid \'from\' date(s) of service.'),(118,'N84','Alert: Further installment payments are forthcoming.'),(119,'N146','Missing screening document.'),(120,'MA05','Incorrect admission date patient status or type of bill entry on claim.'),(121,'N278','Missing/incomplete/invalid other payer service facility provider identifier.'),(122,'M53','Missing/incomplete/invalid days or units of service.'),(123,'N85','Alert: This is the final installment payment.'),(124,'N147','Long term care case mix or per diem rate cannot be determined because the patient ID number is missing, incomplete, or invalid on the assignment request.'),(125,'MA06','Missing/incomplete/invalid beginning and/or ending date(s).'),(126,'N279','Missing/incomplete/invalid pay-to provider name.'),(127,'M54','Missing/incomplete/invalid total charges.'),(128,'N86','A failed trial of pelvic muscle exercise training is required in order for biofeedback training for the treatment of urinary incontinence to be covered.'),(129,'N148','Missing/incomplete/invalid date of last menstrual period.'),(130,'MA07','Alert: The claim information has also been forwarded to Medicaid for review.'),(131,'M55','We do not pay for self-administered anti-emetic drugs that are not administered with a covered oral anti-cancer drug.'),(132,'N87','Home use of biofeedback therapy is not covered.'),(133,'N280','Missing/incomplete/invalid pay-to provider primary identifier.'),(134,'N149','Rebill all applicable services on a single claim.'),(135,'MA08','Alert: Claim information was not forwarded because the supplemental coverage is not with a Medigap plan, or you do not participate in Medicare.'),(136,'M56','Missing/incomplete/invalid payer identifier.'),(137,'N88','Alert: This payment is being made conditionally. An HHA episode of care notice has been filed for this patient. When a patient is treated under a HHA episode of care, consolidated billing requires that certain therapy services and supplies, such as this, be included in the HHA\'s payment. This payment will need to be recouped from you if we establish that the patient is concurrently receiving treatment under a HHA episode of care.'),(138,'N281','Missing/incomplete/invalid pay-to provider address.'),(139,'MA09','Claim submitted as unassigned but processed as assigned. You agreed to accept assignment for all claims.'),(140,'M57','Missing/incomplete/invalid provider identifier.'),(141,'N150','Missing/incomplete/invalid model number.'),(142,'N89','Alert: Payment information for this claim has been forwarded to more than one other payer, but format limitations permit only one of the secondary payers to be identified in this remittance advice.'),(143,'N282','Missing/incomplete/invalid pay-to provider secondary identifier.'),(144,'M58','Missing/incomplete/invalid claim information. Resubmit claim after corrections.'),(145,'N151','Telephone contact services will not be paid until the face-to-face contact requirement has been met.'),(146,'N283','Missing/incomplete/invalid purchased service provider identifier.'),(147,'M59','Missing/incomplete/invalid \'to\' date(s) of service.'),(148,'MA10','Alert: The patient\'s payment was in excess of the amount owed. You must refund the overpayment to the patient.'),(149,'N152','Missing/incomplete/invalid replacement claim information.'),(150,'MA11','Payment is being issued on a conditional basis. If no-fault insurance, liability insurance, Workers\' Compensation, Department of Veterans Affairs, or a group health plan for employees and dependents also covers this claim, a refund may be due us. Please contact us if the patient is covered by any of these sources.'),(151,'N284','Missing/incomplete/invalid referring provider taxonomy.'),(152,'N90','Covered only when performed by the attending physician.'),(153,'N153','Missing/incomplete/invalid room and board rate.'),(154,'MA12','You have not established that you have the right under the law to bill for services furnished by the person(s) that furnished this (these) service(s).'),(155,'N285','Missing/incomplete/invalid referring provider name.'),(156,'N91','Services not included in the appeal review.'),(157,'N154','Alert: This payment was delayed for correction of provider\'s mailing address.'),(158,'MA13','Alert: You may be subject to penalties if you bill the patient for amounts not reported with the PR (patient responsibility) group code.'),(159,'N286','Missing/incomplete/invalid referring provider primary identifier.'),(160,'M60','Missing Certificate of Medical Necessity.'),(161,'N92','This facility is not certified for digital mammography.'),(162,'N155','Alert: Our records do not indicate that other insurance is on file. Please submit other insurance information for our records.'),(163,'MA14','Alert: The patient is a member of an employer-sponsored prepaid health plan. Services from outside that health plan are not covered. However, as you were not previously notified of this, we are paying this time. In the future, we will not pay you for non-plan services.'),(164,'N287','Missing/incomplete/invalid referring provider secondary identifier.'),(165,'M61','We cannot pay for this as the approval period for the FDA clinical trial has expired.'),(166,'N93','A separate claim must be submitted for each place of service. Services furnished at multiple sites may not be billed in the same claim.'),(167,'N156','Alert: The patient is responsible for the difference between the approved treatment and the elective treatment.'),(168,'MA15','Alert: Your claim has been separated to expedite handling. You will receive a separate notice for the other services reported.'),(169,'N288','Missing/incomplete/invalid rendering provider taxonomy.'),(170,'M62','Missing/incomplete/invalid treatment authorization code.'),(171,'N94','Claim/Service denied because a more specific taxonomy code is required for adjudication.'),(172,'N157','Transportation to/from this destination is not covered.'),(173,'MA16','The patient is covered by the Black Lung Program. Send this claim to the Department of Labor, Federal Black Lung Program, P.O. Box 828, Lanham-Seabrook MD 20703.'),(174,'N289','Missing/incomplete/invalid rendering provider name.'),(175,'M63','We do not pay for more than one of these on the same day.'),(176,'N95','This provider type/provider specialty may not bill this service.'),(177,'N158','Transportation in a vehicle other than an ambulance is not covered.'),(178,'MA17','We are the primary payer and have paid at the primary rate. You must contact the patient\'s other insurer to refund any excess it may have paid due to its erroneous primary payment.'),(179,'M64','Missing/incomplete/invalid other diagnosis.'),(180,'N96','Patient must be refractory to conventional therapy (documented behavioral, pharmacologic and/or surgical corrective therapy) and be an appropriate surgical candidate such that implantation with anesthesia can occur.'),(181,'N159','Payment denied/reduced because mileage is not covered when the patient is not in the ambulance.'),(182,'MA18','Alert: The claim information is also being forwarded to the patient\'s supplemental insurer. Send any questions regarding supplemental benefits to them.'),(183,'M65','One interpreting physician charge can be submitted per claim when a purchased diagnostic test is indicated. Please submit a separate claim for each interpreting physician.'),(184,'N97','Patients with stress incontinence, urinary obstruction, and specific neurologic diseases (e.g., diabetes with peripheral nerve involvement) which are associated with secondary manifestations of the above three indications are excluded.'),(185,'N290','Missing/incomplete/invalid rendering provider primary identifier.'),(186,'MA19','Alert: Information was not sent to the Medigap insurer due to incorrect/invalid information you submitted concerning that insurer. Please verify your information and submit your secondary claim directly to that insurer.'),(187,'M66','Our records indicate that you billed diagnostic tests subject to price limitations and the procedure code submitted includes a professional component. Only the technical component is subject to price limitations. Please submit the technical and professional components of this service as separate line items.'),(188,'N98','Patient must have had a successful test stimulation in order to support subsequent implantation. Before a patient is eligible for permanent implantation, he/she must demonstrate a 50 percent or greater improvement through test stimulation. Improvement is measured through voiding diaries.'),(189,'N291','Missing/incomplete/invalid rending provider secondary identifier.'),(190,'M67','Missing/incomplete/invalid other procedure code(s).'),(191,'N160','The patient must choose an option before a payment can be made for this procedure/ equipment/ supply/ service.'),(192,'N99','Patient must be able to demonstrate adequate ability to record voiding diary data such that clinical results of the implant procedure can be properly evaluated.'),(193,'N292','Missing/incomplete/invalid service facility name'),(194,'M68','Missing/incomplete/invalid attending, ordering, rendering, supervising or referring physician identification.'),(195,'N161','This drug/service/supply is covered only when the associated service is covered.'),(196,'N293','Missing/incomplete/invalid service facility primary identifier.'),(197,'M69','Paid at the regular rate as you did not submit documentation to justify the modified procedure code.'),(198,'N162','Alert: Although your claim was paid, you have billed for a test/specialty not included in your Laboratory Certification. Your failure to correct the laboratory certification information will result in a denial of payment in the near future.'),(199,'MA20','Skilled Nursing Facility (SNF) stay not covered when care is primarily related to the use of an urethral catheter for convenience or the control of incontinence.'),(200,'N294','Missing/incomplete/invalid service facility primary address.'),(201,'MA21','SSA records indicate mismatch with name and sex.'),(202,'N163','Medical record does not support code billed per the code definition.'),(203,'MA22','Payment of less than $1.00 suppressed.'),(204,'N295','Missing/incomplete/invalid service facility secondary identifier.'),(205,'M70','Alert: The NDC code submitted for this service was translated to a HCPCS code for processing, but please continue to submit the NDC on future claims for this item.'),(206,'N164','Transportation to/from this destination is not covered.'),(207,'MA23','Demand bill approved as result of medical review.'),(208,'N296','Missing/incomplete/invalid supervising provider name.'),(209,'M71','Total payment reduced due to overlap of tests billed.'),(210,'N165','Transportation in a vehicle other than an ambulance is not covered.'),(211,'MA24','Christian Science Sanitarium/ Skilled Nursing Facility (SNF) bill in the same benefit period.'),(212,'N297','Missing/incomplete/invalid supervising provider primary identifier.'),(213,'M72','Did not enter full 8-digit date (MM/DD/CCYY).'),(214,'N166','Payment denied/reduced because mileage is not covered when the patient is not in the ambulance.'),(215,'MA25','A patient may not elect to change a hospice provider more than once in a benefit period.'),(216,'N298','Missing/incomplete/invalid supervising provider secondary identifier.'),(217,'M73','The HPSA/Physician Scarcity bonus can only be paid on the professional component of this service. Rebill as separate professional and technical components.'),(218,'N167','Charges exceed the post-transplant coverage limit.'),(219,'MA26','Alert: Our records indicate that you were previously informed of this rule.'),(220,'N299','Missing/incomplete/invalid occurrence date(s).'),(221,'M74','This service does not qualify for a HPSA/Physician Scarcity bonus payment.'),(222,'N168','The patient must choose an option before a payment can be made for this procedure/ equipment/ supply/ service.'),(223,'MA27','Missing/incomplete/invalid entitlement number or name shown on the claim.'),(224,'M75','Multiple automated multichannel tests performed on the same day combined for payment.'),(225,'N169','This drug/service/supply is covered only when the associated service is covered.'),(226,'MA28','Alert: Receipt of this notice by a physician or supplier who did not accept assignment is for information only and does not make the physician or supplier a party to the determination. No additional rights to appeal this decision, above those rights already provided for by regulation/instruction, are conferred by receipt of this notice.'),(227,'M76','Missing/incomplete/invalid diagnosis or condition.'),(228,'MA29','Missing/incomplete/invalid provider name, city, state, or zip code.'),(229,'M77','Missing/incomplete/invalid place of service.'),(230,'N170','A new/revised/renewed certificate of medical necessity is needed.'),(231,'M78','Missing/incomplete/invalid HCPCS modifier.'),(232,'N171','Payment for repair or replacement is not covered or has exceeded the purchase price.'),(233,'M79','Missing/incomplete/invalid charge.'),(234,'N172','The patient is not liable for the denied/adjusted charge(s) for receiving any updated service/item.'),(235,'MA30','Missing/incomplete/invalid type of bill.'),(236,'N173','No qualifying hospital stay dates were provided for this episode of care.'),(237,'MA31','Missing/incomplete/invalid beginning and ending dates of the period billed.'),(238,'MA32','Missing/incomplete/invalid number of covered days during the billing period.'),(239,'N174','This is not a covered service/procedure/ equipment/bed, however patient liability is limited to amounts shown in the adjustments under group \'PR\'.'),(240,'MA33','Missing/incomplete/invalid noncovered days during the billing period.'),(241,'M80','Not covered when performed during the same session/date as a previously processed service for the patient.'),(242,'N175','Missing review organization approval.'),(243,'MA34','Missing/incomplete/invalid number of coinsurance days during the billing period.'),(244,'M81','You are required to code to the highest level of specificity.'),(245,'N176','Services provided aboard a ship are covered only when the ship is of United States registry and is in United States waters. In addition, a doctor licensed to practice in the United States must provide the service.'),(246,'MA35','Missing/incomplete/invalid number of lifetime reserve days.'),(247,'M82','Service is not covered when patient is under age 50.'),(248,'N177','Alert: We did not send this claim to patient\'s other insurer. They have indicated no additional payment can be made'),(249,'MA36','Missing/incomplete/invalid patient name.'),(250,'M100','We do not pay for an oral anti-emetic drug that is not administered for use immediately before, at, or within 48 hours of administration of a covered chemotherapy drug.'),(251,'M83','Service is not covered unless the patient is classified as at high risk.'),(252,'N178','Missing pre-operative photos or visual field results.'),(253,'MA37','Missing/incomplete/invalid patient\'s address.'),(254,'M101','Begin to report a G1-G5 modifier with this HCPCS. We will soon begin to deny payment for this service if billed without a G1-G5 modifier.'),(255,'M84','Medical code sets used must be the codes in effect at the time of service'),(256,'N179','Additional information has been requested from the member. The charges will be reconsidered upon receipt of that information.'),(257,'MA38','Missing/incomplete/invalid birth date.'),(258,'M102','Service not performed on equipment approved by the FDA for this purpose.'),(259,'M85','Subjected to review of physician evaluation and management services.'),(260,'MA39','Missing/incomplete/invalid gender.'),(261,'M103','Information supplied supports a break in therapy. However, the medical information we have for this patient does not support the need for this item as billed. We have approved payment for this item at a reduced level, and a new capped rental period will begin with the delivery of this equipment.'),(262,'M86','Service denied because payment already made for same/similar procedure within set time frame.'),(263,'M104','Information supplied supports a break in therapy. A new capped rental period will begin with delivery of the equipment. This is the maximum approved under the fee schedule for this item or service.'),(264,'M87','Claim/service(s) subjected to CFO-CAP prepayment review.'),(265,'N180','This item or service does not meet the criteria for the category under which it was billed.'),(266,'M105','Information supplied does not support a break in therapy. The medical information we have for this patient does not support the need for this item as billed. We have approved payment for this item at a reduced level, and a new capped rental period will not begin.'),(267,'M88','We cannot pay for laboratory tests unless billed by the laboratory that did the work.'),(268,'N181','Additional information is required from another provider involved in this service.'),(269,'M106','Information supplied does not support a break in therapy. A new capped rental period will not begin. This is the maximum approved under the fee schedule for this item or service.'),(270,'M89','Not covered more than once under age 40.'),(271,'N182','This claim/service must be billed according to the schedule for this plan.'),(272,'MA40','Missing/incomplete/invalid admission date.'),(273,'M107','Payment reduced as 90-day rolling average hematocrit for ESRD patient exceeded 36.5%.'),(274,'N183','Alert: This is a predetermination advisory message, when this service is submitted for payment additional documentation as specified in plan documents will be required to process benefits.'),(275,'MA41','Missing/incomplete/invalid admission type.'),(276,'M108','Missing/incomplete/invalid provider identifier for the provider who interpreted the diagnostic test.'),(277,'N184','Rebill technical and professional components separately.'),(278,'MA42','Missing/incomplete/invalid admission source.'),(279,'M90','Not covered more than once in a 12 month period.'),(280,'M109','We have provided you with a bundled payment for a teleconsultation. You must send 25 percent of the teleconsultation payment to the referring practitioner.'),(281,'MA43','Missing/incomplete/invalid patient status.'),(282,'M91','Lab procedures with different CLIA certification numbers must be billed on separate claims.'),(283,'N185','Alert: Do not resubmit this claim/service.'),(284,'MA44','Alert: No appeal rights. Adjudicative decision based on law.'),(285,'M92','Services subjected to review under the Home Health Medical Review Initiative.'),(286,'N186','Non-Availability Statement (NAS) required for this service. Contact the nearest Military Treatment Facility (MTF) for assistance.'),(287,'MA45','Alert: As previously advised, a portion or all of your payment is being held in a special account.'),(288,'M110','Missing/incomplete/invalid provider identifier for the provider from whom you purchased interpretation services.'),(289,'M93','Information supplied supports a break in therapy. A new capped rental period began with delivery of this equipment.'),(290,'N187','Alert: You may request a review in writing within the required time limits following receipt of this notice by following the instructions included in your contract or plan benefit documents.'),(291,'MA46','The new information was considered, however, additional payment cannot be issued. Please review the information listed for the explanation.'),(292,'M111','We do not pay for chiropractic manipulative treatment when the patient refuses to have an x-ray taken.'),(293,'M94','Information supplied does not support a break in therapy. A new capped rental period will not begin.'),(294,'N188','The approved level of care does not match the procedure code submitted.'),(295,'MA47','Our records show you have opted out of Medicare, agreeing with the patient not to bill Medicare for services/tests/supplies furnished. As result, we cannot pay this claim. The patient is responsible for payment.'),(296,'M112','Reimbursement for this item is based on the single payment amount required under the DMEPOS Competitive Bidding Program for the area where the patient resides.'),(297,'M95','Services subjected to Home Health Initiative medical review/cost report audit.'),(298,'N189','Alert: This service has been paid as a one-time exception to the plan\'s benefit restrictions.'),(299,'MA48','Missing/incomplete/invalid name or address of responsible party or primary payer.'),(300,'M113','Our records indicate that this patient began using this item/service prior to the current contract period for the DMEPOS Competitive Bidding Program.'),(301,'M96','The technical component of a service furnished to an inpatient may only be billed by that inpatient facility. You must contact the inpatient facility for technical component reimbursement. If not already billed, you should bill us for the professional component only.'),(302,'MA49','Missing/incomplete/invalid six-digit provider identifier for home health agency or hospice for physician(s) performing care plan oversight services.'),(303,'M114','This service was processed in accordance with rules and guidelines under the DMEPOS Competitive Bidding Program or a Demonstration Project. For more information regarding these projects, contact your local contractor.'),(304,'M97','Not paid to practitioner when provided to patient in this place of service. Payment included in the reimbursement issued the facility.'),(305,'N190','Missing contract indicator.'),(306,'M115','This item is denied when provided to this patient by a non-contract or non-demonstration supplier.'),(307,'M98','Begin to report the Universal Product Number on claims for items of this type. We will soon begin to deny payment for items of this type if billed without the correct UPN.'),(308,'N191','The provider must update insurance information directly with payer.'),(309,'M116','Paid under the Competitive Bidding Demonstration project. Project is ending, and future services may not be paid under this project.'),(310,'M99','Missing/incomplete/invalid Universal Product Number/Serial Number.'),(311,'N192','Patient is a Medicaid/Qualified Medicare Beneficiary.'),(312,'MA50','Missing/incomplete/invalid Investigational Device Exemption number for FDA-approved clinical trial services.'),(313,'M117','Not covered unless submitted via electronic claim.'),(314,'N193','Specific federal/state/local program may cover this service through another payer.'),(315,'MA51','Missing/incomplete/invalid CLIA certification number for laboratory services billed by physician office laboratory.'),(316,'M118','Alert: Letter to follow containing further information.'),(317,'N194','Technical component not paid if provider does not own the equipment used.'),(318,'MA52','Missing/incomplete/invalid date.'),(319,'M119','Missing/incomplete/invalid/ deactivated/withdrawn National Drug Code (NDC).'),(320,'N195','The technical component must be billed separately.'),(321,'MA53','Missing/incomplete/invalid Competitive Bidding Demonstration Project identification.'),(322,'MA54','Physician certification or election consent for hospice care not received timely.'),(323,'N196','Alert: Patient eligible to apply for other coverage which may be primary.'),(324,'MA55','Not covered as patient received medical health care services, automatically revoking his/her election to receive religious non-medical health care services.'),(325,'N197','The subscriber must update insurance information directly with payer.'),(326,'MA56','Our records show you have opted out of Medicare, agreeing with the patient not to bill Medicare for services/tests/supplies furnished. As result, we cannot pay this claim. The patient is responsible for payment, but under Federal law, you cannot charge the patient more than the limiting charge amount.'),(327,'M120','Missing/incomplete/invalid provider identifier for the substituting physician who furnished the service(s) under a reciprocal billing or locum tenens arrangement.'),(328,'N198','Rendering provider must be affiliated with the pay-to provider.'),(329,'MA57','Patient submitted written request to revoke his/her election for religious non-medical health care services.'),(330,'M121','We pay for this service only when performed with a covered cryosurgical ablation.'),(331,'N199','Additional payment/recoupment approved based on payer-initiated review/audit.'),(332,'MA58','Missing/incomplete/invalid release of information indicator.'),(333,'M122','Missing/incomplete/invalid level of subluxation.'),(334,'MA59','Alert: The patient overpaid you for these services. You must issue the patient a refund within 30 days for the difference between his/her payment and the total amount shown as patient responsibility on this notice.'),(335,'M123','Missing/incomplete/invalid name, strength, or dosage of the drug furnished.'),(336,'M124','Missing indication of whether the patient owns the equipment that requires the part or supply.'),(337,'M125','Missing/incomplete/invalid information on the period of time for which the service/supply/equipment will be needed.'),(338,'MA60','Missing/incomplete/invalid patient relationship to insured.'),(339,'M126','Missing/incomplete/invalid individual lab codes included in the test.'),(340,'MA61','Missing/incomplete/invalid social security number or health insurance claim number.'),(341,'M127','Missing patient medical record for this service.'),(342,'MA62','Alert: This is a telephone review decision.'),(343,'M128','Missing/incomplete/invalid date of the patient\'s last physician visit.'),(344,'MA63','Missing/incomplete/invalid principal diagnosis.'),(345,'M129','Missing/incomplete/invalid indicator of x-ray availability for review.'),(346,'MA64','Our records indicate that we should be the third payer for this claim. We cannot process this claim until we have received payment information from the primary and secondary payers.'),(347,'MA65','Missing/incomplete/invalid admitting diagnosis.'),(348,'MA66','Missing/incomplete/invalid principal procedure code.'),(349,'M130','Missing invoice or statement certifying the actual cost of the lens, less discounts, and/or the type of intraocular lens used.'),(350,'MA67','Correction to a prior claim.'),(351,'M131','Missing physician financial relationship form.'),(352,'MA68','Alert: We did not crossover this claim because the secondary insurance information on the claim was incomplete. Please supply complete information or use the PLANID of the insurer to assure correct and timely routing of the claim.'),(353,'M132','Missing pacemaker registration form.'),(354,'MA69','Missing/incomplete/invalid remarks.'),(355,'M133','Claim did not identify who performed the purchased diagnostic test or the amount you were charged for the test.'),(356,'N500','Incomplete/invalid Medical Legal Report.'),(357,'M134','Performed by a facility/supplier in which the provider has a financial interest.'),(358,'N501','Missing Vocational Report.'),(359,'M135','Missing/incomplete/invalid plan of treatment.'),(360,'N502','Incomplete/invalid Vocational Report.'),(361,'M136','Missing/incomplete/invalid indication that the service was supervised or evaluated by a physician.'),(362,'MA70','Missing/incomplete/invalid provider representative signature.'),(363,'N503','Missing Work Status Report.'),(364,'M137','Part B coinsurance under a demonstration project.'),(365,'MA71','Missing/incomplete/invalid provider representative signature date.'),(366,'N504','Incomplete/invalid Work Status Report.'),(367,'M138','Patient identified as a demonstration participant but the patient was not enrolled in the demonstration at the time services were rendered. Coverage is limited to demonstration participants.'),(368,'MA72','Alert: The patient overpaid you for these assigned services. You must issue the patient a refund within 30 days for the difference between his/her payment to you and the total of the amount shown as patient responsibility and as paid to the patient on this notice.'),(369,'N505','Alert: This response includes only services that could be estimated in real time. No estimate will be provided for the services that could not be estimated in real time.'),(370,'M139','Denied services exceed the coverage limit for the demonstration.'),(371,'MA73','Informational remittance associated with a Medicare demonstration. No payment issued under fee-for-service Medicare as patient has elected managed care.'),(372,'N506','Alert: This is an estimate of the member\'s liability based on the information available at the time the estimate was processed. Actual coverage and member liability amounts will be determined when the claim is processed. This is not a pre-authorization or a guarantee of payment.'),(373,'MA74','This payment replaces an earlier payment for this claim that was either lost, damaged or returned.'),(374,'N507','Plan distance requirements have not been met.'),(375,'MA75','Missing/incomplete/invalid patient or authorized representative signature.'),(376,'M140','Service not covered until after the patient\'s 50th birthday, i.e., no coverage prior to the day after the 50th birthday'),(377,'N508','Alert: This real time claim adjudication response represents the member responsibility to the provider for services reported. The member will receive an Explanation of Benefits electronically or in the mail. Contact the insurer if there are any questions.'),(378,'MA76','Missing/incomplete/invalid provider identifier for home health agency or hospice when physician is performing care plan oversight services.'),(379,'N509','Alert: A current inquiry shows the member\'s Consumer Spending Account contains sufficient funds to cover the member liability for this claim/service. Actual payment from the Consumer Spending Account will depend on the availability of funds and determination of eligible services at the time of payment processing.'),(380,'M141','Missing physician certified plan of care.'),(381,'MA77','Alert: The patient overpaid you. You must issue the patient a refund within 30 days for the difference between the patient\'s payment less the total of our and other payer payments and the amount shown as patient responsibility on this notice.'),(382,'M142','Missing American Diabetes Association Certificate of Recognition.'),(383,'MA78','The patient overpaid you. You must issue the patient a refund within 30 days for the difference between our allowed amount total and the amount paid by the patient.'),(384,'M143','The provider must update license information with the payer.'),(385,'MA79','Billed in excess of interim rate.'),(386,'N510','Alert: A current inquiry shows the member\'s Consumer Spending Account does not contain sufficient funds to cover the member\'s liability for this claim/service. Actual payment from the Consumer Spending Account will depend on the availability of funds and determination of eligible services at the time of payment processing.'),(387,'M144','Pre-/post-operative care payment is included in the allowance for the surgery/procedure.'),(388,'N511','Alert: Information on the availability of Consumer Spending Account funds to cover the member liability on this claim/service is not available at this time.'),(389,'N512','Alert: This is the initial remit of a non-NCPDP claim originally submitted real-time without change to the adjudication.'),(390,'MA80','Informational notice. No payment issued for this claim with this notice. Payment issued to the hospital by its intermediary for all services for this encounter under a demonstration project.'),(391,'N513','Alert: This is the initial remit of a non-NCPDP claim originally submitted real-time with a change to the adjudication.'),(392,'MA81','Missing/incomplete/invalid provider/supplier signature.'),(393,'N514','Consult plan benefit documents/guidelines for information about restrictions for this service.'),(394,'MA82','Missing/incomplete/invalid provider/supplier billing number/identifier or billing name, address, city, state, zip code, or phone number.'),(395,'N515','Alert: Submit this claim to the patient\'s other insurer for potential payment of supplemental benefits. We did not forward the claim information.'),(396,'MA83','Did not indicate whether we are the primary or secondary payer.'),(397,'MA84','Patient identified as participating in the National Emphysema Treatment Trial but our records indicate that this patient is either not a participant, or has not yet been approved for this phase of the study. Contact Johns Hopkins University, the study coordinator, to resolve if there was a discrepancy.'),(398,'MA85','Our records indicate that a primary payer exists (other than ourselves); however, you did not complete or enter accurately the insurance plan/group/program name or identification number. Enter the PlanID when effective.'),(399,'MA86','Missing/incomplete/invalid group or policy number of the insured for the primary coverage.'),(400,'M1','X-ray not taken within the past 12 months or near enough to the start of treatment.'),(401,'MA87','Missing/incomplete/invalid insured\'s name for the primary payer.'),(402,'M2','Not paid separately when the patient is an inpatient.'),(403,'MA88','Missing/incomplete/invalid insured\'s address and/or telephone number for the primary payer.'),(404,'M3','Equipment is the same or similar to equipment already being used.'),(405,'MA89','Missing/incomplete/invalid patient\'s relationship to the insured for the primary payer.'),(406,'M4','Alert: This is the last monthly installment payment for this durable medical equipment.'),(407,'M5','Monthly rental payments can continue until the earlier of the 15th month from the first rental month, or the month when the equipment is no longer needed.'),(408,'M6','Alert: You must furnish and service this item for as long as the patient continues to need it. We can pay for maintenance and/or servicing for every 6 month period after the end of the 15th paid rental month or the end of the warranty period.'),(409,'M7','No rental payments after the item is purchased, or after the total of issued rental payments equals the purchase price.'),(410,'MA90','Missing/incomplete/invalid employment status code for the primary insured.'),(411,'M8','We do not accept blood gas tests results when the test was conducted by a medical supplier or taken while the patient is on oxygen.'),(412,'MA91','This determination is the result of the appeal you filed.'),(413,'M9','Alert: This is the tenth rental month. You must offer the patient the choice of changing the rental to a purchase agreement.'),(414,'MA92','Missing plan information for other insurance.'),(415,'MA93','Non-PIP (Periodic Interim Payment) claim.]'),(416,'MA94','Did not enter the statement \'Attending physician not hospice employee\' on the claim form to certify that the rendering physician is not an employee of the hospice.'),(417,'MA95','A not otherwise classified or unlisted procedure code(s) was billed but a narrative description of the procedure was not entered on the claim. Refer to item 19 on the HCFA-1500.'),(418,'N1','Alert: You may appeal this decision in writing within the required time limits following receipt of this notice by following the instructions included in your contract or plan benefit documents.'),(419,'MA96','Claim rejected. Coded as a Medicare Managed Care Demonstration but patient is not enrolled in a Medicare managed care plan.'),(420,'N2','This allowance has been made in accordance with the most appropriate course of treatment provision of the plan.'),(421,'MA97','Missing/incomplete/invalid Medicare Managed Care Demonstration contract number or clinical trial registry number.'),(422,'N3','Missing consent form.'),(423,'MA98','Claim Rejected. Does not contain the correct Medicare Managed Care Demonstration contract number for this beneficiary.'),(424,'N4','Missing/incomplete/invalid prior insurance carrier EOB.'),(425,'MA99','Missing/incomplete/invalid Medigap information.'),(426,'N5','EOB received from previous payer. Claim not on file.'),(427,'N6','Under FEHB law (U.S.C. 8904(b)), we cannot pay more for covered care than the amount Medicare would have allowed if the patient were enrolled in Medicare Part A and/or Medicare Part B.'),(428,'N7','Processing of this claim/service has included consideration under Major Medical provisions.'),(429,'N400','Alert: Electronically enabled providers should submit claims electronically.'),(430,'N8','Crossover claim denied by previous payer and complete claim data not forwarded. Resubmit this claim to this payer to provide adequate data for adjudication.'),(431,'N401','Missing periodontal charting.'),(432,'N9','Adjustment represents the estimated amount a previous payer may pay.'),(433,'N402','Incomplete/invalid periodontal charting.'),(434,'N403','Missing facility certification.'),(435,'N404','Incomplete/invalid facility certification.'),(436,'N405','This service is only covered when the donor\'s insurer(s) do not provide coverage for the service.'),(437,'N406','This service is only covered when the recipient\'s insurer(s) do not provide coverage for the service.'),(438,'N407','You are not an approved submitter for this transmission format.'),(439,'N408','This payer does not cover deductibles assessed by a previous payer.'),(440,'N409','This service is related to an accidental injury and is not covered unless provided within a specific time frame from the date of the accident.'),(441,'N410','This is not covered unless the prescription changes.'),(442,'N411','This service is allowed one time in a 6-month period. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(443,'N412','This service is allowed 2 times in a 12-month period. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(444,'N413','This service is allowed 2 times in a benefit year. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(445,'N414','This service is allowed 4 times in a 12-month period. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(446,'N415','This service is allowed 1 time in an 18-month period. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(447,'N416','This service is allowed 1 time in a 3-year period. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(448,'N417','This service is allowed 1 time in a 5-year period. (This temporary code will be deactivated on 2/1/09. Must be used with Reason Code 119.)'),(449,'N418','Misrouted claim. See the payer\'s claim submission instructions.'),(450,'N419','Claim payment was the result of a payer\'s retroactive adjustment due to a retroactive rate change.'),(451,'N420','Claim payment was the result of a payer\'s retroactive adjustment due to a Coordination of Benefits or Third Party Liability Recovery.'),(452,'N421','Claim payment was the result of a payer\'s retroactive adjustment due to a review organization decision.'),(453,'N422','Claim payment was the result of a payer\'s retroactive adjustment due to a payer\'s contract incentive program.'),(454,'N423','Claim payment was the result of a payer\'s retroactive adjustment due to a non standard program.'),(455,'N424','Patient does not reside in the geographic area required for this type of payment.'),(456,'N425','Statutorily excluded service(s).'),(457,'N426','No coverage when self-administered.'),(458,'N427','Payment for eyeglasses or contact lenses can be made only after cataract surgery.'),(459,'N428','Service/procedure not covered when performed in this place of service.'),(460,'N429','This is not covered since it is considered routine.'),(461,'N430','Procedure code is inconsistent with the units billed.'),(462,'N431','Service is not covered with this procedure.'),(463,'N300','Missing/incomplete/invalid occurrence span date(s).'),(464,'N432','Adjustment based on a Recovery Audit.'),(465,'N301','Missing/incomplete/invalid procedure date(s).'),(466,'N433','Resubmit this claim using only your National Provider Identifier (NPI)'),(467,'N302','Missing/incomplete/invalid other procedure date(s).'),(468,'N434','Missing/Incomplete/Invalid Present on Admission indicator.'),(469,'N303','Missing/incomplete/invalid principal procedure date.'),(470,'N435','Exceeds number/frequency approved /allowed within time period without support documentation.'),(471,'N304','Missing/incomplete/invalid dispensed date.'),(472,'N436','The injury claim has not been accepted and a mandatory medical reimbursement has been made.'),(473,'N305','Missing/incomplete/invalid accident date.'),(474,'N437','Alert: If the injury claim is accepted, these charges will be reconsidered.'),(475,'N306','Missing/incomplete/invalid acute manifestation date.'),(476,'N438','This jurisdiction only accepts paper claims'),(477,'N307','Missing/incomplete/invalid adjudication or payment date.'),(478,'N439','Missing anesthesia physical status report/indicators.'),(479,'N308','Missing/incomplete/invalid appliance placement date.'),(480,'N309','Missing/incomplete/invalid assessment date.'),(481,'N440','Incomplete/invalid anesthesia physical status report/indicators.'),(482,'N441','This missed appointment is not covered'),(483,'N310','Missing/incomplete/invalid assumed or relinquished care date.'),(484,'N442','Payment based on an alternate fee schedule.'),(485,'N311','Missing/incomplete/invalid authorized to return to work date.'),(486,'N443','Missing/incomplete/invalid total time or begin/end time.'),(487,'N312','Missing/incomplete/invalid begin therapy date.'),(488,'N444','Alert: This facility has not filed the Election for High Cost Outlier form with the Division of Workers\' Compensation.'),(489,'N313','Missing/incomplete/invalid certification revision date.'),(490,'N445','Missing document for actual cost or paid amount.'),(491,'N314','Missing/incomplete/invalid diagnosis date.'),(492,'N446','Incomplete/invalid document for actual cost or paid amount.'),(493,'N315','Missing/incomplete/invalid disability from date.'),(494,'N447','Payment is based on a generic equivalent as required documentation was not provided.'),(495,'N316','Missing/incomplete/invalid disability to date.'),(496,'N448','This drug/service/supply is not included in the fee schedule or contracted/legislated fee arrangement'),(497,'N317','Missing/incomplete/invalid discharge hour.'),(498,'N449','Payment based on a comparable drug/service/supply.'),(499,'N318','Missing/incomplete/invalid discharge or end of care date.'),(500,'N319','Missing/incomplete/invalid hearing or vision prescription date.'),(501,'N450','Covered only when performed by the primary treating physician or the designee.'),(502,'N451','Missing Admission Summary Report.'),(503,'N320','Missing/incomplete/invalid Home Health Certification Period.'),(504,'N452','Incomplete/invalid Admission Summary Report.'),(505,'N321','Missing/incomplete/invalid last admission period.'),(506,'N453','Missing Consultation Report.'),(507,'N322','Missing/incomplete/invalid last certification date.'),(508,'N454','Incomplete/invalid Consultation Report.'),(509,'N323','Missing/incomplete/invalid last contact date.'),(510,'N455','Missing Physician Order.'),(511,'N324','Missing/incomplete/invalid last seen/visit date.'),(512,'N456','Incomplete/invalid Physician Order.'),(513,'N325','Missing/incomplete/invalid last worked date.'),(514,'N457','Missing Diagnostic Report.'),(515,'N326','Missing/incomplete/invalid last x-ray date.'),(516,'N458','Incomplete/invalid Diagnostic Report.'),(517,'N327','Missing/incomplete/invalid other insured birth date.'),(518,'N459','Missing Discharge Summary.'),(519,'N328','Missing/incomplete/invalid Oxygen Saturation Test date.'),(520,'N329','Missing/incomplete/invalid patient birth date.'),(521,'N460','Incomplete/invalid Discharge Summary.'),(522,'N461','Missing Nursing Notes.'),(523,'N330','Missing/incomplete/invalid patient death date.'),(524,'N462','Incomplete/invalid Nursing Notes.'),(525,'N331','Missing/incomplete/invalid physician order date'),(526,'N463','Missing support data for claim.'),(527,'N200','The professional component must be billed separately.'),(528,'N332','Missing/incomplete/invalid prior hospital discharge date.'),(529,'N464','Incomplete/invalid support data for claim.'),(530,'N201','A mental health facility is responsible for payment of outside providers who furnish these services/supplies to residents.'),(531,'N333','Missing/incomplete/invalid prior placement date.'),(532,'N465','Missing Physical Therapy Notes/Report.'),(533,'N202','Alert: Additional information/explanation will be sent separately'),(534,'N334','Missing/incomplete/invalid re-evaluation date'),(535,'N466','Incomplete/invalid Physical Therapy Notes/Report.'),(536,'N203','Missing/incomplete/invalid anesthesia time/units'),(537,'N335','Missing/incomplete/invalid referral date.'),(538,'N467','Missing Report of Tests and Analysis Report.'),(539,'N10','Payment based on the findings of a review organization/professional consult/manual adjudication/medical or dental advisor.'),(540,'N204','Services under review for possible pre-existing condition. Send medical records for prior 12 months'),(541,'N336','Missing/incomplete/invalid replacement date.'),(542,'N468','Incomplete/invalid Report of Tests and Analysis Report.'),(543,'N11','Denial reversed because of medical review.'),(544,'N205','Information provided was illegible'),(545,'N337','Missing/incomplete/invalid secondary diagnosis date.'),(546,'N469','Alert: Claim/Service(s) subject to appeal process, see section 935 of Medicare Prescription Drug, Improvement, and Modernization Act of 2003 (MMA).'),(547,'N206','The supporting documentation does not match the claim'),(548,'N338','Missing/incomplete/invalid shipped date.'),(549,'N12','Policy provides coverage supplemental to Medicare. As the member does not appear to be enrolled in the applicable part of Medicare, the member is responsible for payment of the portion of the charge that would have been covered by Medicare.'),(550,'N470','This payment will complete the mandatory medical reimbursement limit.'),(551,'N207','Missing/incomplete/invalid weight.'),(552,'N339','Missing/incomplete/invalid similar illness or symptom date.'),(553,'N13','Payment based on professional/technical component modifier(s).'),(554,'N208','Missing/incomplete/invalid DRG code'),(555,'N14','Payment based on a contractual amount or agreement, fee schedule, or maximum allowable amount.'),(556,'N471','Missing/incomplete/invalid HIPPS Rate Code.'),(557,'N209','Missing/incomplete/invalid taxpayer identification number (TIN).'),(558,'N15','Services for a newborn must be billed separately.'),(559,'N340','Missing/incomplete/invalid subscriber birth date.'),(560,'N472','Payment for this service has been issued to another provider.'),(561,'N16','Family/member Out-of-Pocket maximum has been met. Payment based on a higher percentage.'),(562,'N341','Missing/incomplete/invalid surgery date.'),(563,'N473','Missing certification.'),(564,'N17','Per admission deductible.'),(565,'N342','Missing/incomplete/invalid test performed date.'),(566,'N474','Incomplete/invalid certification'),(567,'N18','Payment based on the Medicare allowed amount.'),(568,'N210','Alert: You may appeal this decision'),(569,'N343','Missing/incomplete/invalid Transcutaneous Electrical Nerve Stimulator (TENS) trial start date.'),(570,'N475','Missing completed referral form.'),(571,'N19','Procedure code incidental to primary procedure.'),(572,'N211','Alert: You may not appeal this decision'),(573,'N344','Missing/incomplete/invalid Transcutaneous Electrical Nerve Stimulator (TENS) trial end date.'),(574,'N476','Incomplete/invalid completed referral form'),(575,'N212','Charges processed under a Point of Service benefit'),(576,'N345','Date range not valid with units submitted.'),(577,'N477','Missing Dental Models.'),(578,'N213','Missing/incomplete/invalid facility/discrete unit DRG/DRG exempt status information'),(579,'N346','Missing/incomplete/invalid oral cavity designation code.'),(580,'N478','Incomplete/invalid Dental Models'),(581,'N20','Service not payable with other service rendered on the same date.'),(582,'N214','Missing/incomplete/invalid history of the related initial surgical procedure(s)'),(583,'N347','Your claim for a referred or purchased service cannot be paid because payment has already been made for this same service to another provider by a payment contractor representing the payer.'),(584,'N479','Missing Explanation of Benefits (Coordination of Benefits or Medicare Secondary Payer).'),(585,'N21','Alert: Your line item has been separated into multiple lines to expedite handling.'),(586,'N215','Alert: A payer providing supplemental or secondary coverage shall not require a claims determination for this service from a primary payer as a condition of making its own claims determination.'),(587,'N348','You chose that this service/supply/drug would be rendered/supplied and billed by a different practitioner/supplier.'),(588,'N22','This procedure code was added/changed because it more accurately describes the services rendered.'),(589,'N216','Patient is not enrolled in this portion of our benefit package'),(590,'N349','The administration method and drug must be reported to adjudicate this service.'),(591,'N23','Alert: Patient liability may be affected due to coordination of benefits with other carriers and/or maximum benefit provisions.'),(592,'N480','Incomplete/invalid Explanation of Benefits (Coordination of Benefits or Medicare Secondary Payer).'),(593,'N217','We pay only one site of service per provider per claim'),(594,'N24','Missing/incomplete/invalid Electronic Funds Transfer (EFT) banking information.'),(595,'N481','Missing Models.'),(596,'N218','You must furnish and service this item for as long as the patient continues to need it. We can pay for maintenance and/or servicing for the time period specified in the contract or coverage manual.'),(597,'N25','This company has been contracted by your benefit plan to provide administrative claims payment services only. This company does not assume financial risk or obligation with respect to claims processed on behalf of your benefit plan.'),(598,'N219','Payment based on previous payer\'s allowed amount.'),(599,'N26','Missing itemized bill/statement.'),(600,'N350','Missing/incomplete/invalid description of service for a Not Otherwise Classified (NOC) code or for an Unlisted/By Report procedure.'),(601,'N482','Incomplete/invalid Models'),(602,'N27','Missing/incomplete/invalid treatment number.'),(603,'N351','Service date outside of the approved treatment plan service dates.'),(604,'N483','Missing Periodontal Charts.'),(605,'N28','Consent form requirements not fulfilled.'),(606,'N220','Alert: See the payer\'s web site or contact the payer\'s Customer Service department to obtain forms and instructions for filing a provider dispute.'),(607,'N352','Alert: There are no scheduled payments for this service. Submit a claim for each patient visit.'),(608,'N484','Incomplete/invalid Periodontal Charts'),(609,'N29','Missing documentation/orders/notes/summary/report/chart.'),(610,'N221','Missing Admitting History and Physical report.'),(611,'N353','Alert: Benefits have been estimated, when the actual services have been rendered, additional payment will be considered based on the submitted claim.'),(612,'N485','Missing Physical Therapy Certification.'),(613,'N222','Incomplete/invalid Admitting History and Physical report.'),(614,'N354','Incomplete/invalid invoice'),(615,'N486','Incomplete/invalid Physical Therapy Certification.'),(616,'N223','Missing documentation of benefit to the patient during initial treatment period.'),(617,'N355','Alert: The law permits exceptions to the refund requirement in two cases: - If you did not know, and could not have reasonably been expected to know, that we would not pay for this service; or - If you notified the patient in writing before providing the service that you believed that we were likely to deny the service, and the patient signed a statement agreeing to pay for the service.'),(618,'N487','Missing Prosthetics or Orthotics Certification.'),(619,'N30','Patient ineligible for this service.'),(620,'N224','Incomplete/invalid documentation of benefit to the patient during initial treatment period'),(621,'N356','This service is not covered when performed with, or subsequent to, a non-covered service.'),(622,'N488','Incomplete/invalid Prosthetics or Orthotics Certification'),(623,'N31','Missing/incomplete/invalid prescribing provider identifier.'),(624,'N225','Incomplete/invalid documentation/orders/notes/summary/report/chart.'),(625,'N357','Time frame requirements between this service/procedure/supply and a related service/procedure/supply have not been met.'),(626,'N489','Missing referral form.'),(627,'N32','Claim must be submitted by the provider who rendered the service.'),(628,'N226','Incomplete/invalid American Diabetes Association Certificate of Recognition.'),(629,'N358','Alert: This decision may be reviewed if additional documentation as described in the contract or plan benefit documents is submitted.'),(630,'N33','No record of health check prior to initiation of treatment.'),(631,'N490','Incomplete/invalid referral form'),(632,'N227','Incomplete/invalid Certificate of Medical Necessity.'),(633,'N359','Missing/incomplete/invalid height.'),(634,'N34','Incorrect claim form/format for this service.'),(635,'N491','Missing/Incomplete/Invalid Exclusionary Rider Condition.'),(636,'N228','Incomplete/invalid consent form.'),(637,'N35','Program integrity/utilization review decision.'),(638,'N360','Alert: Coordination of benefits has not been calculated when estimating benefits for this pre-determination. Submit payment information from the primary payer with the secondary claim.'),(639,'N492','Alert: A network provider may bill the member for this service if the member requested the service and agreed in writing, prior to receiving the service, to be financially responsible for the billed charge.'),(640,'N229','Incomplete/invalid contract indicator.'),(641,'N36','Claim must meet primary payer\'s processing requirements before we can consider payment.'),(642,'N37','Missing/incomplete/invalid tooth number/letter.'),(643,'N361','Payment adjusted based on multiple diagnostic imaging procedure rules'),(644,'N493','Missing Doctor First Report of Injury.'),(645,'N38','Missing/incomplete/invalid place of service.'),(646,'N230','Incomplete/invalid indication of whether the patient owns the equipment that requires the part or supply.'),(647,'N362','The number of Days or Units of Service exceeds our acceptable maximum.'),(648,'N494','Incomplete/invalid Doctor First Report of Injury.'),(649,'N39','Procedure code is not compatible with tooth number/letter.'),(650,'N231','Incomplete/invalid invoice or statement certifying the actual cost of the lens, less discounts, and/or the type of intraocular lens used.'),(651,'N363','Alert: in the near future we are implementing new policies/procedures that would affect this determination.'),(652,'N495','Missing Supplemental Medical Report.'),(653,'N100','PPS (Prospect Payment System) code corrected during adjudication'),(654,'N232','Incomplete/invalid itemized bill/statement.'),(655,'N364','Alert: According to our agreement, you must waive the deductible and/or coinsurance amounts.'),(656,'N496','Incomplete/invalid Supplemental Medical Report.'),(657,'N101','Additional information is needed in order to process this claim. Please resubmit the claim with the identification number of the provider where this service took place. The Medicare number of the site of service provider should be preceded with the letters \'HSP\' and entered into item #32 on the claim form. You may bill only one site of service provider number per claim.'),(658,'N233','Incomplete/invalid operative note/report.'),(659,'N365','This procedure code is not payable. It is for reporting/information purposes only.'),(660,'N497','Missing Medical Permanent Impairment or Disability Report.'),(661,'MA100','Missing/incomplete/invalid date of current illness or symptoms'),(662,'N102','This claim has been denied without reviewing the medical record because the requested records were not received or were not received timely.'),(663,'N234','Incomplete/invalid oxygen certification/re-certification.'),(664,'N366','Requested information not provided. The claim will be reopened if the information previously requested is submitted within one year after the date of this denial notice.'),(665,'N498','Incomplete/invalid Medical Permanent Impairment or Disability Report.'),(666,'MA101','A Skilled Nursing Facility (SNF) is responsible for payment of outside providers who furnish these services/supplies to residents.'),(667,'N40','Missing radiology film(s)/image(s).'),(668,'N103','Social Security records indicate that this patient was a prisoner when the service was rendered. This payer does not cover items and services furnished to an individual while they are in State or local custody under a penal authority, unless under State or local law, the individual is personally liable for the cost of his or her health care while incarcerated and the State or local government pursues such debt in the same way and with the same vigor as any other debt.'),(669,'N235','Incomplete/invalid pacemaker registration form.'),(670,'N367','Alert: The claim information has been forwarded to a Consumer Spending Account processor for review; for example, flexible spending account or health savings account.'),(671,'N499','Missing Medical Legal Report.'),(672,'MA102','Missing/incomplete/invalid name or provider identifier for the rendering/referring/ ordering/ supervising provider.'),(673,'N41','Authorization request denied.'),(674,'N104','This claim/service is not payable under our claims jurisdiction area. You can identify the correct Medicare contractor to process this claim/service through the CMS website at www.cms.hhs.gov.'),(675,'N236','Incomplete/invalid pathology report.'),(676,'N368','You must appeal the determination of the previously adjudicated claim.'),(677,'M10','Equipment purchases are limited to the first or the tenth month of medical necessity.'),(678,'N42','No record of mental health assessment.'),(679,'N105','This is a misdirected claim/service for an RRB beneficiary. Submit paper claims to the RRB carrier: Palmetto GBA, P.O. Box 10066, Augusta, GA 30999. Call 866-749-4301 for RRB EDI information for electronic claims processing.'),(680,'N237','Incomplete/invalid patient medical record for this service.'),(681,'N369','Alert: Although this claim has been processed, it is deficient according to state legislation/regulation.'),(682,'MA103','Hemophilia Add On.'),(683,'M11','DME, orthotics and prosthetics must be billed to the DME carrier who services the patient\'s zip code.'),(684,'N43','Bed hold or leave days exceeded.'),(685,'N106','Payment for services furnished to Skilled Nursing Facility (SNF) inpatients (except for excluded services) can only be made to the SNF. You must request payment from the SNF rather than the patient for this service.'),(686,'N238','Incomplete/invalid physician certified plan of care'),(687,'MA104','Missing/incomplete/invalid date the patient was last seen or the provider identifier of the attending physician.'),(688,'M12','Diagnostic tests performed by a physician must indicate whether purchased services are included on the claim.'),(689,'N44','Payer\'s share of regulatory surcharges, assessments, allowances or health care-related taxes paid directly to the regulatory authority.'),(690,'N107','Services furnished to Skilled Nursing Facility (SNF) inpatients must be billed on the inpatient claim. They cannot be billed separately as outpatient services.'),(691,'N239','Incomplete/invalid physician financial relationship form.'),(692,'MA105','Missing/incomplete/invalid provider number for this place of service.'),(693,'M13','Only one initial visit is covered per specialty per medical group.'),(694,'N45','Payment based on authorized amount.'),(695,'N370','Billing exceeds the rental months covered/approved by the payer.'),(696,'N108','Missing/incomplete/invalid upgrade information.'),(697,'MA106','PIP (Periodic Interim Payment) claim.'),(698,'M14','No separate payment for an injection administered during an office visit, and no payment for a full office visit if the patient only received an injection.'),(699,'N46','Missing/incomplete/invalid admission hour.'),(700,'N371','Alert: title of this equipment must be transferred to the patient.'),(701,'N109','This claim was chosen for complex review and was denied after reviewing the medical records.'),(702,'MA107','Paper claim contains more than three separate data items in field 19.'),(703,'M15','Separately billed services/tests have been bundled as they are considered components of the same procedure. Separate payment is not allowed.'),(704,'N47','Claim conflicts with another inpatient stay.'),(705,'MA108','Paper claim contains more than one data item in field 23.'),(706,'M16','Alert: Please see our web site, mailings, or bulletins for more details concerning this policy/procedure/decision.'),(707,'N48','Claim information does not agree with information received from other insurance carrier.'),(708,'N240','Incomplete/invalid radiology report.'),(709,'N372','Only reasonable and necessary maintenance/service charges are covered.'),(710,'MA109','Claim processed in accordance with ambulatory surgical guidelines.'),(711,'M17','Alert: Payment approved as you did not know, and could not reasonably have been expected to know, that this would not normally have been covered for this patient. In the future, you will be liable for charges for the same service(s) under the same or similar conditions.'),(712,'N49','Court ordered coverage information needs validation.'),(713,'N241','Incomplete/invalid review organization approval.'),(714,'N373','It has been determined that another payer paid the services as primary when they were not the primary payer. Therefore, we are refunding to the payer that paid as primary on your behalf.'),(715,'M18','Certain services may be approved for home use. Neither a hospital nor a Skilled Nursing Facility (SNF) is considered to be a patient\'s home.'),(716,'N110','This facility is not certified for film mammography.'),(717,'N242','Incomplete/invalid radiology film(s)/image(s).'),(718,'N374','Primary Medicare Part A insurance has been exhausted and a Part B Remittance Advice is required.'),(719,'MA110','Missing/incomplete/invalid information on whether the diagnostic test(s) were performed by an outside entity or if no purchased tests are included on the claim.'),(720,'M19','Missing oxygen certification/re-certification.'),(721,'N111','No appeal right except duplicate claim/service issue. This service was included in a claim that has been previously billed and adjudicated.'),(722,'N243','Incomplete/invalid/not approved screening document.'),(723,'N375','Missing/incomplete/invalid questionnaire/information required to determine dependent eligibility.'),(724,'MA111','Missing/incomplete/invalid purchase price of the test(s) and/or the performing laboratory\'s name and address.'),(725,'N50','Missing/incomplete/invalid discharge information.'),(726,'N112','This claim is excluded from your electronic remittance advice.'),(727,'N244','Incomplete/invalid pre-operative photos/visual field results.'),(728,'N376','Subscriber/patient is assigned to active military duty, therefore primary coverage may be TRICARE.'),(729,'MA112','Missing/incomplete/invalid group practice information.'),(730,'N51','Electronic interchange agreement not on file for provider/submitter.'),(731,'N113','Only one initial visit is covered per physician, group practice or provider.'),(732,'N245','Incomplete/invalid plan information for other insurance'),(733,'N377','Payment based on a processed replacement claim.'),(734,'MA113','Incomplete/invalid taxpayer identification number (TIN) submitted by you per the Internal Revenue Service. Your claims cannot be processed without your correct TIN, and you may not bill the patient pending correction of your TIN. There are no appeal rights for unprocessable claims, but you may resubmit this claim after you have notified this office of your correct TIN.'),(735,'M20','Missing/incomplete/invalid HCPCS.'),(736,'N52','Patient not enrolled in the billing provider\'s managed care plan on the date of service.'),(737,'N114','During the transition to the Ambulance Fee Schedule, payment is based on the lesser of a blended amount calculated using a percentage of the reasonable charge/cost and fee schedule amounts, or the submitted charge for the service. You will be notified yearly what the percentages for the blended payment calculation will be.'),(738,'N246','State regulated patient payment limitations apply to this service.'),(739,'N378','Missing/incomplete/invalid prescription quantity.'),(740,'M21','Missing/incomplete/invalid place of residence for this service/item provided in a home.'),(741,'N53','Missing/incomplete/invalid point of pick-up address.'),(742,'N115','This decision was based on a local medical review policy (LMRP) or Local Coverage Determination (LCD).An LMRP/LCD provides a guide to assist in determining whether a particular item or service is covered. A copy of this policy is available at http://www.cms.hhs.gov/mcd, or if you do not have web access, you may contact the contractor to request a copy of the LMRP/LCD.'),(743,'N247','Missing/incomplete/invalid assistant surgeon taxonomy.'),(744,'N379','Claim level information does not match line level information.'),(745,'MA114','Missing/incomplete/invalid information on where the services were furnished.'),(746,'M22','Missing/incomplete/invalid number of miles traveled.'),(747,'N54','Claim information is inconsistent with pre-certified/authorized services.'),(748,'N116','This payment is being made conditionally because the service was provided in the home, and it is possible that the patient is under a home health episode of care. When a patient is treated under a home health episode of care, consolidated billing requires that certain therapy services and supplies, such as this, be included in the home health agency\'s (HHA\'s) payment. This payment will need to be recouped from you if we establish that the patient is concurrently receiving treatment under an HHA episode of care.'),(749,'N248','Missing/incomplete/invalid assistant surgeon name.'),(750,'MA115','Missing/incomplete/invalid physical location (name and address, or PIN) where the service(s) were rendered in a Health Professional Shortage Area (HPSA).'),(751,'M23','Missing invoice.'),(752,'N55','Procedures for billing with group/referring/performing providers were not followed.'),(753,'N380','The original claim has been processed, submit a corrected claim.'),(754,'N117','This service is paid only once in a patient\'s lifetime.'),(755,'N249','Missing/incomplete/invalid assistant surgeon primary identifier.'),(756,'MA116','Did not complete the statement \'Homebound\' on the claim to validate whether laboratory services were performed at home or in an institution.'),(757,'M24','Missing/incomplete/invalid number of doses per vial.'),(758,'N56','Procedure code billed is not correct/valid for the services billed or the date of service billed.'),(759,'N381','Consult our contractual agreement for restrictions/billing/payment information related to these charges.'),(760,'N118','This service is not paid if billed more than once every 28 days.'),(761,'MA117','This claim has been assessed a $1.00 user fee.'),(762,'M25','The information furnished does not substantiate the need for this level of service. If you believe the service should have been fully covered as billed, or if you did not know and could not reasonably have been expected to know that we would not pay for this level of service, or if you notified the patient in writing in advance that we would not pay for this level of service and he/she agreed in writing to pay, ask us to review your claim within 120 days of the date of this notice. If you do not request a appeal, we will, upon application from the patient, reimburse him/her for the amount you have collected from him/her in excess of any deductible and coinsurance amounts. We will recover the reimbursement from you as an overpayment.'),(763,'N57','Missing/incomplete/invalid prescribing date.'),(764,'N250','Missing/incomplete/invalid assistant surgeon secondary identifier.'),(765,'N382','Missing/incomplete/invalid patient identifier.'),(766,'N119','This service is not paid if billed once every 28 days, and the patient has spent 5 or more consecutive days in any inpatient or Skilled /nursing Facility (SNF) within those 28 days.'),(767,'MA118','Coinsurance and/or deductible amounts apply to a claim for services or supplies furnished to a Medicare-eligible veteran through a facility of the Department of Veterans Affairs. No Medicare payment issued.'),(768,'M26','The information furnished does not substantiate the need for this level of service. If you have collected any amount from the patient for this level of service /any amount that exceeds the limiting charge for the less extensive service, the law requires you to refund that amount to the patient within 30 days of receiving this notice.'),(769,'N58','Missing/incomplete/invalid patient liability amount.'),(770,'MA119','Provider level adjustment for late claim filing applies to this claim.'),(771,'M27','Alert: The patient has been relieved of liability of payment of these items and services under the limitation of liability provision of the law. The provider is ultimately liable for the patient\'s waived charges, including any charges for coinsurance, since the items or services were not reasonable and necessary or constituted custodial care, and you knew or could reasonably have been expected to know, that they were not covered. You may appeal this determination. You may ask for an appeal regarding both the coverage determination and the issue of whether you exercised due care. The appeal request must be filed within 120 days of the date you receive this notice. You must make the request through this office.'),(772,'N59','Alert: Please refer to your provider manual for additional program and provider information.'),(773,'N251','Missing/incomplete/invalid attending provider taxonomy.'),(774,'N383','Services deemed cosmetic are not covered'),(775,'M28','This does not qualify for payment under Part B when Part A coverage is exhausted or not otherwise available.'),(776,'N120','Payment is subject to home health prospective payment system partial episode payment adjustment. Patient was transferred/discharged/readmitted during payment episode.'),(777,'N252','Missing/incomplete/invalid attending provider name.'),(778,'N384','Records indicate that the referenced body part/tooth has been removed in a previous procedure.'),(779,'M29','Missing operative note/report.'),(780,'N121','Medicare Part B does not pay for items or services provided by this type of practitioner for beneficiaries in a Medicare Part A covered Skilled Nursing Facility (SNF) stay.'),(781,'N253','Missing/incomplete/invalid attending provider primary identifier.'),(782,'N385','Notification of admission was not timely according to published plan procedures.'),(783,'MA120','Missing/incomplete/invalid CLIA certification number.'),(784,'N122','Add-on code cannot be billed by itself.'),(785,'N254','Missing/incomplete/invalid attending provider secondary identifier.'),(786,'N386','This decision was based on a National Coverage Determination (NCD). An NCD provides a coverage determination as to whether a particular item or service is covered. A copy of this policy is available at http://www.cms.hhs.gov/mcd/search.asp. If you do not have web access, you may contact the contractor to request a copy of the NCD.'),(787,'MA121','Missing/incomplete/invalid x-ray date.'),(788,'N60','A valid NDC is required for payment of drug claims effective October 02.'),(789,'N123','This is a split service and represents a portion of the units from the originally submitted service.'),(790,'N255','Missing/incomplete/invalid billing provider taxonomy.'),(791,'N387','You should submit this claim to the patient\'s other insurer for potential payment of supplemental benefits. We did not forward the claim information.'),(792,'MA122','Missing/incomplete/invalid initial treatment date.'),(793,'N61','Rebill services on separate claims.'),(794,'N640','Exceeds number/frequency approved/allowed within time period.'),(795,'N643','The services billed are considered Not Covered or Non-Covered (NC) in the applicable state fee schedule.');
/*!40000 ALTER TABLE `adjustment_remarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ailments`
--

DROP TABLE IF EXISTS `ailments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ailments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `diagnosis_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `icd_version` int(11) DEFAULT NULL,
  `plans_mask` int(11) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=540 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ailments`
--

LOCK TABLES `ailments` WRITE;
/*!40000 ALTER TABLE `ailments` DISABLE KEYS */;
INSERT INTO `ailments` VALUES (1,'31539','Other developmental speech or language disorder',NULL,'2018-03-21 06:16:31','ST',9,51,0),(2,'3154','Developmental Coordination Disorder',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(3,'31532','Mixed Receptive-Expressive Language Disorder',NULL,'2018-03-21 06:16:31','ST',9,51,0),(4,'319','Unspecified Mental Retardation',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(5,'31531','Expressive Language Disorder',NULL,'2018-03-21 06:16:31','ST',9,51,0),(6,'33510','Spinal Muscular Atrophy, Unspecified',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(7,'43810','Speech and Language Deficit',NULL,'2018-03-21 06:16:31','ST',9,51,0),(8,'33511','Kugelberg-Welander Disease',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(9,'43811','Aphasia',NULL,'2018-03-21 06:16:31','ST',9,51,0),(10,'33519','Other Spinal Muscular Atrophy',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(11,'43812','Dysphasia',NULL,'2018-03-21 06:16:31','ST',9,51,0),(12,'340','Multiple Sclerosis',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(13,'43819','Other Speech and Language Deficits',NULL,'2018-03-21 06:16:31','ST',9,51,0),(14,'34210','Spastic hemiplegia & hemiparesis affecting unspecified side',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(15,'78451','Other Speech Disturbance, Dysarthria',NULL,'2018-03-21 06:16:31','ST',9,51,0),(16,'34211','Spastic hemiplegia and hemiparesis affecting dominant side',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(17,'V401','Mental and Behavioral Problems with Communication',NULL,'2018-03-21 06:16:31','ST',9,51,0),(18,'34212','Spastic hemiplegia and hemiparesis affecting nondominant side',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(19,'V573','Care Involving Speech Therapy',NULL,'2018-03-21 06:16:31','ST',9,51,0),(20,'3439','Infantile Cerebral Palsy, Unspecified',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(21,'3079','Other Unspecified Special Symptoms or Syndromes',NULL,'2018-03-21 06:16:31','ST',9,51,0),(22,'36221','Retrolental Fibroplasia',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(23,'38030','Disorder of Pinna, Unspecified',NULL,'2018-03-21 06:16:31','ST',9,51,0),(24,'7540','Congenital Musculoskeletal Deformities Of Skull, Face, And Jaw',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(25,'3804','Impacted Cerumen',NULL,'2018-03-21 06:16:31','ST',9,51,0),(26,'7542','Congenital Musculoskeletal Deformities Of Spine',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(27,'38089','Other Disorders of External Ear',NULL,'2018-03-21 06:16:31','ST',9,51,0),(28,'75430','Congenital Dislocation Of Hip, Unilateral',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(29,'3809','Unspecified Disorder of External Ear',NULL,'2018-03-21 06:16:31','ST',9,51,0),(30,'75442','Congenital Bowing Of Femur',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(31,'74900','Cleft Palate, Unspecified',NULL,'2018-03-21 06:16:31','ST',9,51,0),(32,'3181','Severe Mental Retardation',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(33,'38911','Sensory Hearing Loss, Bilateral',NULL,'2018-03-21 06:16:31','ST',9,51,0),(34,'36900','Blindness Of Both Eyes',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(35,'38912','Neural Hearing Loss, Bilateral',NULL,'2018-03-21 06:16:31','ST',9,51,0),(36,'3694','Legal Blindness, As Defined In U.S.A.',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(37,'38914','Central Hearing Loss, Bilateral',NULL,'2018-03-21 06:16:31','ST',9,51,0),(38,'01570','Tuberculosis Of Other Specified Bone',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(39,'38915','Sensorineural Hearing Loss, Unilatera',NULL,'2018-03-21 06:16:31','ST',9,51,0),(40,'04510','Acute Poliomyelitis With Other Paralysis',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(41,'38916','Sensorineural Hearing Loss, Asymmetrical',NULL,'2018-03-21 06:16:31','ST',9,51,0),(42,'31401','Attention Deficit Disorder Of Childhood With Hyperactivity',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(43,'38918','Sensorineural Hearing Loss of Combined Types',NULL,'2018-03-21 06:16:31','ST',9,51,0),(44,'34510','Generalized Convulsive Epilepsy',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(45,'38920','Mixed Conductive and Sensorineural Hearing Loss',NULL,'2018-03-21 06:16:31','ST',9,51,0),(46,'20310','Plasma Cell Leukemia',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(47,'38910','Sensorineural Hearing Loss, unspecified',NULL,'2018-03-21 06:16:31','ST',9,51,0),(48,'75521','Transverse Deficiency Of Upper Limb',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(49,'3158','Other Specified Delays In Development',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(50,'3070','Stuttering',NULL,'2018-03-21 06:16:31','ST',9,51,0),(51,'3159','Unspecified Delay In Development',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(52,'3155','Mixed Development Disorder',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(53,'29900','Autism',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(54,'7580','Down Syndrome',NULL,'2018-03-21 06:16:31','PT/OT',9,51,0),(55,'7812','Abnormal Gait','2009-01-08 21:16:05','2018-03-21 06:16:31','PT',9,51,0),(56,'3591','Hereditary Progressive Muscular Dystrophy ','2009-08-04 19:45:46','2018-03-21 06:16:31','PT/OT',9,51,0),(57,'317','Mild Mental Retardation ','2009-08-04 20:10:22','2018-03-21 06:16:31','PT/OT',9,51,0),(58,'7813','Lack of Coordination','2009-08-07 16:56:20','2018-03-21 06:16:31','OT',9,51,0),(59,'3432','Congenital Quadriplegia','2009-08-25 19:15:26','2018-03-21 06:16:31','PT/OT',9,51,0),(60,'3441','Paraplegia ','2009-08-25 19:27:46','2018-03-21 06:16:31','PT/OT',9,51,0),(61,'7580','Down\'s Syndrome','2009-08-26 17:56:58','2018-03-21 06:16:31','ST',9,51,0),(62,'34281','Hemiparesis other specified affecting the dominant side','2009-10-14 15:01:11','2018-03-21 06:16:31','PT/OT',9,51,0),(63,'78342','Delayed milestones','2011-06-02 21:15:55','2018-03-21 06:16:31','OT',9,51,0),(64,'3590','Congenital hereditary muscular dystrophy','2011-06-09 16:58:27','2018-03-21 06:16:31','NURSE',9,51,0),(65,'75981','Prader–Willi syndrome (PWS)','2011-06-29 19:50:30','2018-03-21 06:16:31','NURSE',9,51,0),(66,'7197','Difficulty in walking','2011-09-07 19:13:29','2018-03-21 06:16:31','PT',9,51,0),(67,'72887','Muscle weakness, general','2011-09-07 19:14:58','2018-03-21 06:16:31','PT',9,51,0),(68,'7813','Lack of coordination','2011-09-07 19:15:43','2018-03-21 06:16:31','PT',9,51,0),(69,'34830','Encephalopathy, unspecified','2011-09-29 19:03:37','2018-03-21 06:16:31','NURSE',9,51,0),(70,'7483','Other anomalies of larynx, trachea, and bronchus','2011-09-29 19:13:48','2018-03-21 06:16:31','NURSE',9,51,0),(71,'V550','Tracheostomy','2011-09-30 20:16:09','2018-03-21 06:16:31','NURSE',9,51,0),(72,'3239','Unspecified cause of encephalitis, myelitis, and encephalomyelitis','2011-10-04 16:59:44','2018-03-21 06:16:31','NURSE',9,51,0),(73,'51919','Other diseases of trachea and bronchus','2011-11-16 22:00:52','2018-03-21 06:16:31','NURSE',9,51,0),(74,'78609','Other dyspnea and respiratory abnormality','2011-11-16 22:08:10','2018-03-21 06:16:31','NURSE',9,51,0),(75,'4168','Other chronic pulmonary heart diseases','2011-12-07 17:41:09','2018-03-21 06:16:31','NURSE',9,51,0),(76,'78039','Other convulsions, seizures, not otherwise specified','2011-12-15 21:52:43','2018-03-21 06:16:31','NURSE',9,51,0),(77,'V4614','Attention to Ventilator','2011-12-15 21:53:54','2018-03-21 06:16:31','NURSE',9,51,0),(78,'V551','Attention to Gastrostomy','2011-12-15 21:57:53','2018-03-21 06:16:31','NURSE',9,51,0),(79,'5793','Other and unspecified postsurgical nonabsorption','2011-12-15 21:58:31','2018-03-21 06:16:31','NURSE',9,51,0),(80,'85400','Brain injury, without mention of open intracranial wound','2011-12-15 22:00:23','2018-03-21 06:16:31','NURSE',9,51,0),(81,'3481','Anoxic brain damage','2011-12-15 22:09:04','2018-03-21 06:16:31','NURSE',9,51,0),(82,'5198','Other diseases of respiratory system, not elsewhere classified','2011-12-15 22:10:04','2018-03-21 06:16:31','NURSE',9,51,0),(83,'3440','Quadriplegia and Quadriparesis','2011-12-15 22:11:22','2018-03-21 06:16:31','NURSE',9,51,0),(84,'3449','Paralysis, Unspecified','2011-12-15 22:12:23','2018-03-21 06:16:31','NURSE',9,51,0),(85,'59654','Neurogenic Bladder, not otherwise specified','2011-12-15 22:13:00','2018-03-21 06:16:31','NURSE',9,51,0),(86,'2594','Dwarfism, not elsewhere classified','2011-12-15 22:13:50','2018-03-21 06:16:31','NURSE',9,51,0),(87,'V462','Supplemental Oxygen','2011-12-15 22:14:28','2018-03-21 06:16:31','NURSE',9,51,0),(88,'78441','Aphonia (Non-Verbal)','2011-12-15 22:15:06','2018-03-21 06:16:31','NURSE',9,51,0),(89,'78830','Urinary incontinence, unspecified','2011-12-15 22:15:37','2018-03-21 06:16:31','NURSE',9,51,0),(90,'7558','Other specified congenital anomalies of unspecified limb','2012-01-09 21:53:13','2018-03-21 06:16:31','NURSE',9,51,0),(91,'74190    ','Spina Bifida without mention of Hydrocephalus','2012-01-10 18:30:46','2018-03-21 06:16:31','PT/OT',9,51,0),(92,'78340','Lack of normal physiological development, unspecified','2012-01-10 18:32:29','2018-03-21 06:16:31','PT/OT',9,51,0),(93,'27787','Disorders of mitochondrial metabolism','2012-01-10 23:48:37','2018-03-21 06:16:31','NURSE',9,51,0),(94,'75989','Congenital malformation syndromes affecting multiple systems','2012-01-10 23:49:51','2018-03-21 06:16:31','NURSE',9,51,0),(95,'75831','Cri-du-chat syndrome','2012-01-10 23:52:19','2018-03-21 06:16:31','NURSE',9,51,0),(96,'79951','Signs and symptoms involving cognition, attention or concentration deficit','2012-01-23 20:12:22','2018-03-21 06:16:31','OT',9,51,0),(97,'2129','Benign neoplasm of respiratory and intrathoracic organs','2012-01-27 01:45:18','2018-03-21 06:16:31','NURSE',9,51,0),(98,'3430','Congenital diplegia','2012-01-30 17:19:21','2018-03-21 06:16:31','OT',9,51,0),(99,'7423','Congenital hydrocephalus','2012-02-07 18:30:16','2018-03-21 06:16:31','PT',9,51,0),(101,'35800','Myasthenia gravis without (acute) exacerbation','2012-02-28 19:19:09','2018-03-21 06:16:31','NURSE',9,51,0),(102,'35801','Myasthenia gravis with (acute) exacerbation','2012-02-28 19:19:43','2018-03-21 06:16:31','NURSE',9,51,0),(103,'3159','Unspecified Delay In Development','2012-03-12 19:37:39','2018-03-21 06:16:31','ST',9,51,0),(104,'F82','Specific developmental disorder of motor function','2012-07-25 19:19:09','2018-03-21 06:16:31','PT/OT',10,63,0),(105,'F79','Unspecified intellectual disabilities','2012-07-25 19:56:42','2018-03-21 06:16:31','PT/OT',10,63,0),(106,'3438','Other specified infantile cerebral palsy','2012-08-31 15:29:47','2018-03-21 06:16:31','PT/OT',9,51,0),(107,'75839','Other Autosomal deletions','2012-08-31 15:30:31','2018-03-21 06:16:31','PT/OT',9,51,0),(108,'34501','Intractable epilepsy','2012-09-20 20:29:49','2018-03-21 06:16:31','PT/OT',9,51,0),(109,'7421','Microcephalus','2012-09-20 20:30:46','2018-03-21 06:16:31','PT/OT',9,51,0),(110,'75555','Acrocephalosyndactyly','2012-12-11 16:56:14','2018-03-21 06:16:31','NURSE',9,51,0),(111,'431','Intracerebral hemorrhage','2012-12-11 16:57:21','2018-03-21 06:16:31','NURSE',9,51,0),(112,'5199','Unspecified disease of respiratory system','2012-12-11 18:15:48','2018-03-21 06:16:31','NURSE',9,51,0),(113,'4329','Unspecified intracranial hemorrhage','2012-12-11 18:47:33','2018-03-21 06:16:31','NURSE',9,51,0),(114,'9518','Injury to other specified cranial nerves','2012-12-11 18:48:08','2018-03-21 06:16:31','NURSE',9,51,0),(115,'31509','Other specific developmental reading disorder','2013-03-15 22:50:03','2018-03-21 06:16:31','OT',9,51,0),(116,'42789','Cardiac dysrhythmias, NEC','2013-04-03 15:42:45','2018-03-21 06:16:31','NURSE',9,51,0),(117,'3091','Prolonged depressive reaction','2013-05-22 20:47:34','2018-03-21 06:16:31','OT',9,51,0),(118,'3139','Unspecified emotional disturbance of childhood or adolescence','2013-06-04 21:09:13','2018-03-21 06:16:31','NURSE',9,51,0),(119,'3155','Mixed Developmental Disorder','2013-10-16 14:07:26','2018-03-21 06:16:31','ST',9,51,0),(120,'3158','Other Specified Delays in Development','2013-10-16 14:08:26','2018-03-21 06:16:31','ST',9,51,0),(121,'3159','Unspecified Delay in Development','2013-10-16 14:09:57','2018-03-21 06:16:31','ST',9,51,0),(122,'317','Mild Mental Retardation','2013-10-16 14:12:37','2018-03-21 06:16:31','ST',9,51,0),(123,'31535','Childhood onset fluency disorder','2013-10-16 18:54:17','2018-03-21 06:16:31','ST',9,51,0),(124,'29901','Autistic Disorder','2013-10-29 16:52:45','2018-03-21 06:16:31','ST',9,51,0),(125,'29981','Other specified pervasive developmental disorders','2013-10-29 16:54:26','2018-03-21 06:16:31','ST',9,51,0),(126,'29991','Unspecified pervasive developmental disorder','2013-10-29 16:55:59','2018-03-21 06:16:31','ST',9,51,0),(127,'3180','Moderate Mental Retardation','2013-10-29 16:57:00','2018-03-21 06:16:31','ST',9,51,0),(128,'A1803','Tuberculosis of other bones','2014-04-15 15:34:07','2018-03-21 06:16:31','PT/OT',10,63,0),(129,'A8039','Other acute paralytic poliomyelitis','2014-04-16 15:10:51','2018-03-21 06:16:31','PT/OT',10,63,0),(130,'C9010','Plasma cell leukemia not having achieved remission','2014-04-16 15:11:41','2018-03-21 06:16:31','PT/OT',10,63,0),(131,'F840','Autistic disorder','2014-04-16 15:13:03','2018-03-21 06:16:31','PT/OT',10,63,0),(132,'F845','Asperger\'s syndrome','2014-04-16 15:13:50','2018-03-21 06:16:31','PT/OT',10,63,0),(133,'F848','Other pervasive developmental disorders','2014-04-16 15:14:34','2018-03-21 06:16:31','PT/OT',10,63,0),(134,'F849','Pervasive developmental disorder, unspecified','2014-04-16 15:15:23','2018-03-21 06:16:31','PT/OT',10,63,0),(135,'F4321','Adjustment disorder with depressed mood','2014-04-16 15:16:05','2018-03-21 06:16:31','OT',10,63,0),(136,'F900','Attention-deficit hyperactivity disorder, predominantly inattentive type','2014-04-16 15:34:54','2018-03-21 06:16:31','PT/OT',10,63,0),(137,'F901','Attention-deficit hyperactivity disorder, predominantly hyperactive type','2014-04-16 15:35:45','2018-03-21 06:16:31','PT/OT',10,63,0),(138,'F902','Attention-deficit hyperactivity disorder, combined type','2014-04-16 15:50:40','2018-03-21 06:16:31','PT/OT',10,63,0),(139,'F909','Attention-deficit hyperactivity disorder, unspecified type','2014-04-16 15:51:33','2018-03-21 06:16:31','PT/OT',10,63,0),(140,'F8181','Disorder of written expression','2014-04-16 15:52:42','2018-03-21 06:16:31','OT',10,63,0),(141,'F89','Unspecified disorder of psychological development','2014-04-16 15:53:34','2018-03-21 06:16:31','PT/OT',10,63,0),(142,'F88','Other disorders of psychological development','2014-04-16 15:54:33','2018-03-21 06:16:31','PT/OT',10,63,0),(143,'F819','Developmental disorder of scholastic skills, unspecified','2014-04-16 15:55:15','2018-03-21 06:16:31','PT/OT',10,63,0),(144,'F70','Mild intellectual disabilities','2014-04-16 15:58:39','2018-03-21 06:16:31','PT/OT',10,63,0),(145,'F72','Severe Intellectual disabilities','2014-04-16 15:59:27','2018-03-21 06:16:31','PT/OT',10,63,0),(146,'F79','Unspecified intellectual disabilities','2014-04-16 16:00:06','2019-01-09 19:10:56','PT/OT',10,63,1),(147,'G129','Spinal muscular atrophy, unspecified','2014-04-16 16:00:51','2018-03-21 06:16:31','PT/OT',10,63,0),(148,'G121','Other inherited spinal muscular atrophy','2014-04-16 16:01:27','2018-03-21 06:16:31','PT/OT',10,63,0),(149,'G128','Other spinal muscular atrophies and related syndromes','2014-04-16 16:02:04','2018-03-21 06:16:31','PT/OT',10,63,0),(150,'G35','Multiple sclerosis','2014-04-16 16:02:40','2018-03-21 06:16:31','PT/OT',10,63,0),(151,'G8110','Spastic hemiplegia affecting unspecified side','2014-04-16 16:03:21','2018-03-21 06:16:31','PT/OT',10,63,0),(152,'G8111','Spastic hemiplegia affecting right dominant side','2014-04-16 16:04:06','2018-03-21 06:16:31','PT/OT',10,63,0),(153,'G8112','Spastic hemiplegia affecting left dominant side','2014-04-16 16:04:48','2018-03-21 06:16:31','PT/OT',10,63,0),(154,'G8113','Spastic hemiplegia affecting right nondominant side','2014-04-16 16:05:30','2018-03-21 06:16:31','PT/OT',10,63,0),(155,'G8114','Spastic hemiplegia affecting left nondominant side','2014-04-16 16:06:10','2018-03-21 06:16:31','PT/OT',10,63,0),(156,'G8191','Hemiplegia, unspecified affecting right dominant side','2014-04-16 16:06:59','2018-03-21 06:16:31','PT/OT',10,63,0),(157,'G8192','Hemiplegia, unspecified affecting left dominant side','2014-04-16 16:07:47','2018-03-21 06:16:31','PT/OT',10,63,0),(158,'G801','Spastic diplegic cerebral palsy','2014-04-16 16:08:26','2018-08-14 19:47:59','PT/OT',10,63,0),(159,'G800','Spastic quadriplegic cerebral palsy','2014-04-16 16:09:16','2018-03-21 06:16:31','PT/OT',10,63,0),(160,'G808','Other cerebral palsy','2014-04-16 16:09:48','2018-03-21 06:16:31','PT/OT',10,63,0),(161,'G809','Cerebral palsy, unspecified','2014-04-16 16:10:31','2018-03-21 06:16:31','PT/OT',10,63,0),(162,'G8220','Paraplegia, unspecified','2014-04-16 16:11:13','2018-03-21 06:16:31','PT/OT',10,63,0),(163,'G40311','Generalized idiopathic epilepsy and epileptic syndromes, intractable with status epilepticus','2014-04-16 16:11:49','2018-03-21 06:16:31','PT/OT',10,63,0),(164,'G40309','Generalized idiopathic epilepsy and epileptic syndromes, not intractable without status epilepticus','2014-04-16 16:12:34','2018-03-21 06:16:31','PT/OT',10,63,0),(165,'G710','Muscular dystrophy','2014-04-16 16:13:25','2018-12-05 19:56:53','PT/OT',10,63,1),(166,'H35179','Retrolental Fibroplasia, Unspecified eye','2014-04-16 16:15:18','2018-03-21 06:16:31','PT/OT',10,63,0),(167,'H540','Blindness, both eyes','2014-04-16 16:15:57','2018-03-21 06:16:31','PT/OT',10,63,1),(168,'H548','Legal blindness, as defined in USA','2014-04-16 16:16:46','2018-03-21 06:16:31','PT/OT',10,63,0),(169,'R262','Difficulty in walking, not elsewhere classified','2014-04-16 16:17:27','2018-03-21 06:16:31','PT',10,63,0),(170,'M6281','Muscle weakness (generalized)','2014-04-16 16:18:07','2018-03-21 06:16:31','PT/OT',10,63,0),(171,'Q058','Sacral spina bifida without hydrocephalus','2014-04-16 16:18:46','2018-03-21 06:16:31','PT/OT',10,63,0),(172,'Q02','Microcephaly','2014-04-16 16:19:26','2018-03-21 06:16:31','PT/OT',10,63,0),(173,'Q030','Malformations of aqueduct of Sylvius','2014-04-16 16:19:58','2018-03-21 06:16:31','PT',10,63,0),(174,'Q031','Atresia of foramina of Magendie and Luschka','2014-04-16 16:20:46','2018-03-21 06:16:31','PT',10,63,0),(175,'Q038','Other congenital hydrocephalus','2014-04-16 16:21:15','2018-03-21 06:16:31','PT',10,63,0),(176,'Q670','Congenital facial asymmetry','2014-04-16 16:22:01','2018-03-21 06:16:31','PT/OT',10,63,0),(177,'Q671','Congenital compression facies','2014-04-16 16:22:32','2018-03-21 06:16:31','PT/OT',10,63,0),(178,'Q672','Dolichocephaly','2014-04-16 16:23:14','2018-03-21 06:16:31','PT/OT',10,63,0),(179,'Q673','Plagiocephaly','2014-04-16 16:23:50','2018-03-21 06:16:31','PT/OT',10,63,0),(180,'Q673','Plagiocephaly','2014-04-16 16:24:45','2018-03-21 06:16:31','PT/OT',10,63,0),(181,'Q673','Plagiocephaly','2014-04-16 16:24:45','2018-03-21 06:16:31','PT/OT',10,63,0),(182,'Q674','Other congenital deformities of skull, face and jaw','2014-04-16 16:25:21','2018-03-21 06:16:31','PT/OT',10,63,0),(183,'Q675','Congenital deformity of spine','2014-04-16 16:25:54','2018-03-21 06:16:31','PT/OT',10,63,0),(184,'Q763','Congenital scoliosis due to congenital bony malformation','2014-04-16 16:26:40','2018-03-21 06:16:31','PT/OT',10,63,0),(185,'Q76425','Congenital lordosis, thoracolumbar region','2014-04-16 16:27:22','2018-03-21 06:16:31','PT/OT',10,63,0),(186,'Q76426','Congenital lordosis, lumbar region','2014-04-16 16:28:10','2018-03-21 06:16:31','PT/OT',10,63,0),(187,'Q76427','Congenital lordosis, lumbosacral region','2014-04-16 16:28:48','2018-03-21 06:16:32','PT/OT',10,63,0),(188,'Q76428','Congenital lordosis, sacral and sacrococcygeal region','2014-04-16 16:29:30','2018-03-21 06:16:32','PT/OT',10,63,0),(189,'Q6500','Congenital dislocation of unspecified hip, unilateral','2014-04-16 16:30:12','2018-03-21 06:16:32','PT/OT',10,63,0),(190,'Q683','Congenital bowing of femur','2014-04-16 16:31:04','2018-03-21 06:16:32','PT/OT',10,63,0),(191,'Q7100','Congenital complete absend of unspecified upper limb','2014-04-16 16:31:55','2018-03-21 06:16:32','PT/OT',10,63,0),(192,'Q909','Down Syndrome, Unspecified','2014-04-16 16:32:35','2018-03-21 06:16:32','PT/OT',10,63,0),(193,'Q933','Deletion of short arm of chromosome 4','2014-04-16 16:33:09','2018-03-21 06:16:32','PT/OT',10,63,0),(194,'Q937','Deletions with other complex rearrangements','2014-04-16 16:33:49','2018-03-21 06:16:32','PT/OT',10,63,0),(195,'Q9389','Other deletions from the autosomes','2014-04-16 16:34:34','2018-03-21 06:16:32','PT/OT',10,63,0),(196,'R260','Ataxic gait','2014-04-16 16:35:15','2018-03-21 06:16:32','PT',10,63,0),(197,'R261','Paralytic gait','2014-04-16 16:35:52','2018-03-21 06:16:32','PT',10,63,0),(198,'R2689','Other abnormalities of gait and mobility','2014-04-16 16:36:37','2018-03-21 06:16:32','PT',10,63,0),(199,'R269','Unspecified abnormalities of gait and mobility','2014-04-16 16:37:32','2018-03-21 06:16:32','PT',10,63,0),(200,'R270','Ataxia, unspecified','2014-04-16 16:38:14','2018-03-21 06:16:32','PT/OT',10,63,0),(201,'R278','Other lack of coordination','2014-04-16 16:38:46','2018-03-21 06:16:32','PT/OT',10,63,0),(202,'R279','Unspecified lack of coordination','2014-04-16 16:39:28','2018-03-21 06:16:32','PT/OT',10,63,0),(203,'R6250','Unspecified lack of expected normal physiological development in childhood','2014-04-16 16:40:04','2018-03-21 06:16:32','PT/OT',10,63,0),(204,'R620','Delayed milestone in childhood','2014-04-16 16:40:45','2018-03-21 06:16:32','PT/OT',10,63,0),(205,'R41840','Attention and concentration deficit','2014-04-16 16:41:26','2018-03-21 06:16:32','OT',10,63,0),(206,'F8081','Childhood onset fluency disorder','2014-04-17 13:24:19','2018-03-21 06:16:32','ST',10,63,0),(207,'F633','Tricholtillomania','2014-04-17 13:28:56','2018-03-21 06:16:32','ST',10,63,0),(208,'R451','Restlessness and agitation','2014-04-17 13:37:52','2018-03-21 06:16:32','ST',10,63,0),(209,'F801','Expressive language disorder (Specific developmental disorders of speech and language)','2014-04-17 14:05:46','2018-03-21 06:16:32','ST',10,63,0),(210,'F802','Mixed receptive-expressive language disorder','2014-04-17 14:10:26','2018-03-21 06:16:32','ST',10,63,0),(211,'H9325','Central auditory processing disorder ','2014-04-17 14:10:59','2018-03-21 06:16:32','ST',10,63,0),(212,'F800','Phonological disorder','2014-04-17 14:11:46','2018-03-21 06:16:32','ST',10,63,0),(213,'F8089','Other developmental disorders of speech and language','2014-04-17 14:15:00','2018-03-21 06:16:32','ST',10,63,0),(214,'F809','Developmental disorder of speech and language, unspecified','2014-04-17 14:21:09','2018-03-21 06:16:32','ST',10,63,0),(215,'F819','Developmental disorder of scholastic skills, unspecified','2014-04-17 14:21:49','2018-03-21 06:16:32','ST',10,63,0),(216,'F89','Unspecified disorder of psychological development','2014-04-17 14:22:22','2018-03-21 06:16:32','ST',10,63,0),(217,'H61101','Unspecified noninfective disorders of pinna, right ear','2014-04-17 14:23:01','2018-03-21 06:16:32','ST',10,63,0),(218,'H61102','Unspecified noninfective disorders of pinna, left ear','2014-04-17 14:23:29','2018-03-21 06:16:32','ST',10,63,0),(219,'H61103','Unspecified noninfective disorders of pinna, bilateral','2014-04-17 14:24:03','2018-03-21 06:16:32','ST',10,63,0),(220,'H61109','Unspecified noninfective disorders of pinna, unspecified ear','2014-04-17 14:24:48','2018-03-21 06:16:32','ST',10,63,0),(221,'H6120','Impacted cerumen, unspecified ear','2014-04-17 14:25:23','2018-03-21 06:16:32','ST',10,63,0),(222,'H6121','Impacted cerumen, right ear','2014-04-17 14:25:54','2018-03-21 06:16:32','ST',10,63,0),(223,'H6122','Impacted cerumen, left ear','2014-04-17 14:26:22','2018-03-21 06:16:32','ST',10,63,0),(224,'H6123','Impacted cerumen, bilateral','2014-04-17 14:26:55','2018-03-21 06:16:32','ST',10,63,0),(225,'H61891','Other specified disorders of right external ear','2014-04-17 14:27:26','2018-03-21 06:16:32','ST',10,63,0),(226,'H61892','Other specified disorders of left external ear','2014-04-17 14:30:40','2018-03-21 06:16:32','ST',10,63,0),(227,'H61893','Other specified disorders of external ear, bilateral','2014-04-17 14:32:03','2018-03-21 06:16:32','ST',10,63,0),(228,'H61899','Other specified disorders of external ear, unspecified ear','2014-04-17 14:32:46','2018-03-21 06:16:32','ST',10,63,0),(229,'H6190','Disorder of external ear, unspecified, unspecified ear','2014-04-17 14:33:40','2018-03-21 06:16:32','ST',10,63,0),(230,'H6191','Disorder of right external ear, unspecified','2014-04-17 14:34:40','2018-03-21 06:16:32','ST',10,63,0),(231,'H6192','Disorder of left external ear, unspecified','2014-04-17 14:35:20','2018-03-21 06:16:32','ST',10,63,0),(232,'H6193','Disorder of external ear, unspecified, bilateral','2014-04-17 14:35:56','2018-03-21 06:16:32','ST',10,63,0),(233,'H905','Unspecified sensorineural hearing loss','2014-04-17 14:41:03','2018-03-21 06:16:32','ST',9,51,0),(234,'H903','Sensorineural hearing loss, bilateral','2014-04-17 14:41:45','2018-03-21 06:16:32','ST',9,51,0),(235,'H9041','Sensorineural hearing loss, unilateral, right ear, with unrestricted hearing on the contralateral side','2014-04-17 14:42:35','2018-03-21 06:16:32','ST',10,63,0),(236,'H9042','Sensorineural hearing loss, unilateral, left ear, with unrestricted hearing on the contralateral side','2014-04-17 15:55:46','2018-03-21 06:16:32','ST',10,63,0),(237,'H905','Unspecified sensorineural hearing loss','2014-04-17 15:56:19','2018-03-21 06:16:32','ST',9,51,0),(238,'H9041','Sensorineural hearing loss, unilateral, right ear, with unrestricted hearing on the contralateral side','2014-04-17 15:56:56','2018-03-21 06:16:32','ST',9,51,0),(239,'H9042','Sensorineural hearing loss, unilateral, left ear, with unrestricted hearing on the contralateral side','2014-04-17 15:57:51','2018-03-21 06:16:32','ST',9,51,0),(240,'H905','Unspecified sensorineural hearing loss','2014-04-17 15:58:34','2018-03-21 06:16:32','ST',10,63,0),(241,'H903','Sensorineural hearing loss, bilateral','2014-04-17 15:59:08','2018-03-21 06:16:32','ST',10,63,0),(242,'H908','Mixed conductive and sensorineural hearing loss, unspecified','2014-04-17 15:59:44','2018-03-21 06:16:32','ST',10,63,0),(243,'I69928','Other speech and language deficits following unspecified cerebrovascular disease','2014-04-17 16:00:18','2018-03-21 06:16:32','ST',10,63,0),(244,'I69020','Aphasia following nontraumatic subarachnoid hemorrhage','2014-04-17 16:00:49','2018-03-21 06:16:32','ST',10,63,0),(245,'I69120','Aphasia following nontraumatic intracerebral hemorrhage','2014-04-17 16:01:27','2018-03-21 06:16:32','ST',10,63,0),(246,'I69220','Aphasia following other nontraumatic intracranial hemorrhage','2014-04-17 16:02:01','2018-03-21 06:16:32','ST',10,63,0),(247,'I69320','Aphasia following cerebral infarction','2014-04-17 16:02:34','2018-03-21 06:16:32','ST',10,63,0),(248,'I69820','Aphasia following other cerebrovascular disease','2014-04-17 16:03:11','2018-03-21 06:16:32','ST',10,63,0),(249,'I69920','Aphasia following unspecified cerebrovascular disease','2014-04-17 16:03:40','2018-03-21 06:16:32','ST',10,63,0),(250,'I69021','Dysphasia following nontraumatic subarachnoid hemorrhage','2014-04-17 16:04:13','2018-03-21 06:16:32','ST',10,63,0),(251,'I69121','Dysphasia following nontraumatic intracerebral hemorrhage','2014-04-17 16:04:47','2018-03-21 06:16:32','ST',10,63,0),(252,'I69221','Dysphasia following other nontraumatic intracranial hemorrhage','2014-04-17 16:05:20','2018-03-21 06:16:32','ST',10,63,0),(253,'I69321','Dysphasia following cerebral infarction','2014-04-17 16:05:52','2018-03-21 06:16:32','ST',10,63,0),(254,'I69821','Dysphasia following other cerebrovascular disease','2014-04-17 16:06:22','2018-03-21 06:16:32','ST',10,63,0),(255,'I69921','Dysphasia following unspecified cerebrovascular disease','2014-04-17 16:06:52','2018-03-21 06:16:32','ST',10,63,0),(256,'I69028','Other speech and language deficits following nontraumatic subarachnoid hemorrhage','2014-04-17 16:07:27','2018-03-21 06:16:32','ST',10,63,0),(257,'I69128','Other speech and language deficits following nontraumatic intracerebral hemorrhage','2014-04-17 16:07:56','2018-03-21 06:16:32','ST',10,63,0),(258,'I69228','Other speech and language deficits following other nontraumatic intracranial hemorrhage','2014-04-17 16:08:26','2018-03-21 06:16:32','ST',10,63,0),(259,'I69328','Other speech and language deficits following cerebral infarction','2014-04-17 16:08:56','2018-03-21 06:16:32','ST',10,63,0),(260,'I69828','Other speech and language deficits following other cerebrovascular disease','2014-04-17 16:09:28','2018-03-21 06:16:32','ST',10,63,0),(261,'I69928','Other speech and language deficits following unspecified cerebrovascular disease','2014-04-17 16:10:05','2018-03-21 06:16:32','ST',9,51,0),(262,'Q351','Cleft hard palate','2014-04-17 16:10:40','2018-03-21 06:16:32','ST',10,63,0),(263,'Q353','Cleft soft palate','2014-04-17 16:11:11','2018-03-21 06:16:32','ST',10,63,0),(264,'Q355','Cleft hard palate with cleft soft palate','2014-04-17 16:11:44','2018-03-21 06:16:32','ST',10,63,0),(265,'Q359','Cleft palate, unspecified','2014-04-17 16:12:22','2018-03-21 06:16:32','ST',10,63,0),(266,'Q900','Trisomy 21, nonmosaicism (meiotic nondisjunction) (Down syndrome)','2014-04-17 16:12:58','2018-03-21 06:16:32','ST',10,63,0),(267,'Q901','Trisomy 21, mosaicism (mitotic nondisjunction) (Down syndrome)','2014-04-17 16:14:14','2018-03-21 06:16:32','ST',10,63,0),(268,'Q902','Trisomy 21, translocation (Down syndrome)','2014-04-17 16:14:54','2018-03-21 06:16:32','ST',10,63,0),(269,'Q909','Down syndrome, unspecified','2014-04-17 16:15:24','2018-03-21 06:16:32','ST',10,63,0),(270,'R471','Dysarthria and anarthria','2014-04-17 16:16:01','2018-03-21 06:16:32','ST',10,63,0),(271,'Z8659','Personal history of other mental and behavioral disorders','2014-04-17 16:16:30','2018-03-21 06:16:32','ST',10,63,0),(272,'Z5189','Encounter for other specified aftercare','2014-04-17 16:16:59','2018-03-21 06:16:32','ST',10,63,0),(273,'R482','Speech articulation impairment due to apraxia','2014-04-17 16:17:29','2018-03-21 06:16:32','ST',10,63,0),(274,'F804','Speech and language developmental delay due to hearing loss ','2014-04-17 16:18:03','2018-03-21 06:16:32','ST',10,63,0),(275,'F8181','Disorder of written expression','2014-04-17 16:19:02','2018-03-21 06:16:32','ST',10,63,0),(276,'D144','Benign neoplasm of respiratory system, unspecified','2014-04-17 19:47:04','2018-03-21 06:16:32','NURSE',10,51,0),(277,'D159','Benign neoplasm of intrathoracic organ, unspecified','2014-04-17 19:47:29','2018-03-21 06:16:32','NURSE',10,51,0),(278,'E343','Short stature due to endocrine disorder','2014-04-17 19:50:31','2018-03-21 06:16:32','NURSE',10,51,0),(279,'E8840','Mitochondrial metabolism disorder, unspecified','2014-04-17 19:50:58','2018-03-21 06:16:32','NURSE',10,51,0),(280,'E8841','MELAS syndrome','2014-04-17 19:54:00','2018-03-21 06:16:32','NURSE',10,51,0),(281,'E8842','MERRF syndrome','2014-04-17 19:54:25','2018-03-21 06:16:32','NURSE',10,51,0),(282,'E8849','Other mitochondrial metabolism disorders','2014-04-17 19:54:51','2018-03-21 06:16:32','NURSE',10,51,0),(283,'H49819','Kearns-Sayre syndrome, unspecified eye','2014-04-17 19:55:20','2018-03-21 06:16:32','NURSE',10,51,0),(284,'F939','Childhood emotional disorder, unspecified','2014-04-17 19:55:49','2018-03-21 06:16:32','NURSE',10,51,0),(285,'F948','Other childhood disorders of social functioning','2014-04-17 20:04:14','2018-03-21 06:16:32','NURSE',10,51,0),(286,'F989','Unspecified behavioral and emotional disorders with onset usually occurring in childhood and adolescence','2014-04-17 20:04:39','2018-03-21 06:16:32','NURSE',10,51,0),(287,'G0490','Encephalitis and encephalomyelitis, unspecified','2014-04-17 20:05:03','2018-03-21 06:16:32','NURSE',10,51,0),(288,'G0491','Myelitis, unspecified','2014-04-17 20:05:34','2018-03-21 06:16:32','NURSE',10,51,0),(289,'G8250','Quadriplegia, unspecified','2014-04-17 20:06:59','2018-03-21 06:16:32','NURSE',10,51,0),(290,'G839','Paralytic syndrome, unspecified','2014-04-17 20:07:25','2018-03-21 06:16:32','NURSE',10,51,0),(291,'G931','Anoxic brain damage, not elsewhere classified','2014-04-17 20:07:52','2018-03-21 06:16:32','NURSE',10,51,0),(292,'G9340','Encephalopathy, unspecified','2014-04-17 20:08:19','2018-03-21 06:16:32','NURSE',10,51,0),(293,'G7000','Myasthenia gravis without (acute) exacerbation','2014-04-17 20:08:46','2018-03-21 06:16:32','NURSE',10,51,0),(294,'G7001','Myasthenia gravis with (acute) exacerbation','2014-04-17 20:09:19','2018-03-21 06:16:32','NURSE',10,51,0),(295,'G712','Congenital myopathies','2014-04-17 20:09:47','2018-03-21 06:16:32','NURSE',10,51,0),(296,'I272','Other secondary pulmonary hypertension','2014-04-17 20:10:14','2018-03-21 06:16:32','NURSE',10,51,0),(297,'I2789','Other specified pulmonary heart diseases','2014-04-17 20:10:40','2018-03-21 06:16:32','NURSE',10,51,0),(298,'I619','Nontraumatic intracerebral hemorrhage, unspecified','2014-04-17 20:11:15','2018-03-21 06:16:32','NURSE',10,51,0),(299,'I629','Nontraumatic intracranial hemorrhage, unspecified','2014-04-17 20:11:38','2018-03-21 06:16:32','NURSE',10,51,0),(300,'J398','Other specified diseases of upper respiratory tract','2014-04-17 20:12:04','2018-03-21 06:16:32','NURSE',10,51,0),(301,'J9809','Other diseases of bronchus, not elsewhere classified','2014-04-17 20:15:25','2018-03-21 06:16:32','NURSE',10,51,0),(302,'J988','Other specified respiratory disorders','2014-04-17 20:15:51','2018-03-21 06:16:32','NURSE',10,51,0),(303,'J989','Respiratory disorder, unspecified','2014-04-17 20:16:17','2018-03-21 06:16:32','NURSE',10,51,0),(304,'K912','Postsurgical malabsorption, not elsewhere classified','2014-04-17 20:16:47','2018-03-21 06:16:32','NURSE',10,51,0),(305,'N319','Neuromuscular dysfunction of bladder, unspecified','2014-04-17 20:17:13','2018-03-21 06:16:32','NURSE',10,51,0),(306,'Q311','Congenital subglottic stenosis','2014-04-17 20:17:36','2018-03-21 06:16:32','NURSE',10,51,0),(307,'Q313','Laryngocele','2014-04-17 20:17:59','2018-03-21 06:16:32','NURSE',10,51,0),(308,'Q318','Other congenital malformations of larynx','2014-04-17 20:18:23','2018-03-21 06:16:32','NURSE',10,51,0),(309,'Q321','Other congenital malformations of trachea','2014-04-17 20:18:47','2018-03-21 06:16:32','NURSE',10,51,0),(310,'Q324','Other congenital malformations of bronchus','2014-04-17 20:19:13','2018-03-21 06:16:32','NURSE',10,51,0),(311,'Q870','Congenital malformation syndromes predominantly affecting facial appearance','2014-04-17 20:19:39','2018-03-21 06:16:32','NURSE',10,51,0),(312,'Q748','Other specified congenital malformations of limb(s)','2014-04-17 20:20:04','2018-03-21 06:16:32','NURSE',10,51,0),(313,'Q934','Deletion of short arm of chromosome 5','2014-04-17 20:20:29','2018-03-21 06:16:32','NURSE',10,51,0),(314,'Q871','Congenital malformation syndromes predominantly associated with short stature','2014-04-17 20:20:52','2019-10-28 19:36:38','NURSE',10,51,1),(315,'E7871','Barth syndrome','2014-04-17 20:21:17','2018-03-21 06:16:32','NURSE',10,51,0),(316,'E7872','Smith-Lemli-Opitz syndrome','2014-04-17 20:22:22','2018-03-21 06:16:32','NURSE',10,51,0),(317,'Q872','Congenital malformation syndromes predominantly involving limbs','2014-04-17 20:22:44','2018-03-21 06:16:32','NURSE',10,51,0),(318,'Q873','Congenital malformation syndromes involving early overgrowth','2014-04-17 20:23:07','2018-03-21 06:16:32','NURSE',10,51,0),(319,'Q875','Other congenital malformation syndromes with other skeletal changes','2014-04-17 20:23:29','2018-03-21 06:16:32','NURSE',10,51,0),(320,'Q8781','Alport syndrome','2014-04-17 20:23:55','2018-03-21 06:16:32','NURSE',10,51,0),(321,'Q8789','Other specified congenital malformation syndromes, not elsewhere classified','2014-04-17 20:24:20','2018-03-21 06:16:32','NURSE',10,51,0),(322,'Q898','Other specified congenital malformations','2014-04-17 20:24:46','2018-03-21 06:16:32','NURSE',10,51,0),(323,'R569','Unspecified convulsions','2014-04-17 20:25:12','2018-03-21 06:16:32','NURSE',10,51,0),(324,'R491','Aphonia','2014-04-17 20:25:37','2018-03-21 06:16:32','NURSE',10,51,0),(325,'R0600','Dyspnea, unspecified','2014-04-17 20:26:04','2018-03-21 06:16:32','NURSE',10,51,0),(326,'R0609','Other forms of dyspnea','2014-04-17 20:26:28','2018-03-21 06:16:32','NURSE',10,51,0),(327,'R063','Periodic breathing','2014-04-17 20:26:51','2018-03-21 06:16:32','NURSE',10,51,0),(328,'R0683','Snoring','2014-04-17 20:27:15','2018-03-21 06:16:32','NURSE',10,51,0),(329,'R0689','Other abnormalities of breathing','2014-04-17 20:27:39','2018-03-21 06:16:32','NURSE',10,51,0),(330,'R32','Unspecified urinary incontinence','2014-04-17 20:28:07','2018-03-21 06:16:32','NURSE',10,51,0),(331,'S06890A','Other specified intracranial injury without loss of consciousness, initial encounter','2014-04-17 20:28:36','2018-03-21 06:16:32','NURSE',10,51,0),(332,'S04819A','Injury of olfactory [1st ] nerve, unspecified side, initial encounter','2014-04-17 20:29:01','2018-03-21 06:16:32','NURSE',10,51,0),(333,'S04899A','Injury of other cranial nerves, unspecified side, initial encounter','2014-04-17 20:29:30','2018-03-21 06:16:32','NURSE',10,51,0),(334,'J95850','Mechanical complication of respirator','2014-04-17 20:29:58','2018-03-21 06:16:32','NURSE',10,51,0),(335,'Z9981','Dependence on supplemental oxygen','2014-04-17 20:30:43','2018-03-21 06:16:32','NURSE',10,51,0),(336,'Z430','Encounter for attention to tracheostomy','2014-04-17 20:31:17','2018-03-21 06:16:32','NURSE',10,51,0),(337,'Z431','Encounter for attention to gastrostomy','2014-04-17 20:31:52','2018-03-21 06:16:32','NURSE',10,51,0),(338,'34402','Quadriplegia, C1-C4, incomplete','2014-05-27 16:10:23','2018-03-21 06:16:32','NURSE',9,51,0),(339,'29900','Autism','2015-01-16 17:22:44','2018-03-21 06:16:32','PT/OT',9,51,0),(340,'31534','Speech and language developmental delay due to hearing loss','2015-08-19 19:36:27','2018-03-21 06:16:32','ST',9,51,0),(341,'3431','Congenital hemiplegia','2015-08-19 19:37:10','2018-03-21 06:16:32','PT/OT',9,51,0),(342,'3152','Other specific developmental learning difficulties','2015-08-21 20:51:22','2018-03-21 06:16:32','ST',9,51,0),(343,'78449','Other Voice and Resonance Disorders','2015-08-26 20:19:55','2018-03-21 06:16:32','ST',9,51,0),(344,'31400 ','Attention deficit disorder without mention of hyperactivity','2015-09-23 19:39:13','2018-03-21 06:16:32','PT',9,51,0),(345,'R6250','Unspecified lack of expected normal physiological development in childhood','2015-10-19 15:28:32','2018-03-21 06:16:32','NURSE',10,51,0),(346,'Q059','Spina bifida, unspecified','2015-11-02 18:58:46','2018-03-21 06:16:32','NURSE',10,51,0),(347,'Q8789','Other specified congenital malformation syndromes, not elsewhere classified','2015-11-06 19:13:50','2018-03-21 06:16:32','PT',10,63,0),(348,'75989','Congenital malformation syndromes affecting multiple systems','2015-11-06 19:15:59','2018-03-21 06:16:32','PT',9,51,0),(349,'27911','Digeorge\'s syndrome','2015-12-01 15:49:46','2018-03-21 06:16:32','OT',9,51,0),(350,'D821','Di George\'s snydrome','2015-12-01 15:50:39','2018-03-21 06:16:32','OT',10,63,0),(351,'G40812','Lennox-Gastaut syndrome not intractable, without status epilepticus','2016-01-21 22:45:20','2018-03-21 06:16:32','NURSE',10,51,0),(352,'F840','Autistic Disorder','2016-03-18 16:04:24','2018-03-21 06:16:32','ST',10,63,0),(353,'R498','Other voice and resonance disorders','2016-03-22 20:59:21','2018-03-21 06:16:32','ST',10,63,0),(354,'7707','Chronic respiratory disease arising in the perinatal period','2016-04-29 15:34:18','2018-03-21 06:16:32','NURSE',9,51,0),(355,'P278','Other chronic respiratory diseases originating in the perinatal period','2016-04-29 15:35:05','2018-03-21 06:16:32','NURSE',10,51,0),(356,'34591','Epilepsy, unspecified, with intractable epilepsy','2016-06-06 15:37:43','2018-03-21 06:16:32','NURSE',9,51,0),(357,'G40911','Epilepsy, unspecified, with intractable epilepsy','2016-06-06 15:38:15','2018-03-21 06:16:32','NURSE',10,51,0),(358,'7560','Anomalies of skull and face bones','2016-06-06 15:39:35','2018-03-21 06:16:32','NURSE',9,51,0),(359,'Q759','Congenital malformation of skull and face bones, unspecified','2016-06-06 15:40:00','2018-03-21 06:16:32','NURSE',10,51,0),(360,'95200','C1-C4 level with unspecified spinal cord injury','2016-06-07 16:34:51','2018-03-21 06:16:32','NURSE',9,51,0),(361,'S14103A','Unspecified injury at C3 level of cervical spinal cord, initial encounter','2016-06-07 16:35:30','2018-03-21 06:16:32','NURSE',10,51,0),(362,'Z8679','Personal history of other diseases of the circulatory system','2016-06-27 17:28:19','2018-03-21 06:16:32','NURSE',10,51,0),(363,'Q039','Congenital hydrocephalus, unspecified','2016-06-28 15:57:00','2018-03-21 06:16:32','NURSE',10,51,0),(364,'00000','Instruction in Academics','2016-08-17 19:54:44','2018-03-21 06:16:32','ST',10,63,0),(365,'F20','Schizophrenia','2016-08-20 21:12:07','2019-09-13 18:57:10','BH',10,127,0),(366,'F200','Paranoid schizophrenia','2016-08-20 21:12:29','2019-09-13 18:57:13','BH',10,127,0),(367,'F203','Undifferentiated schizophrenia','2016-08-20 21:12:50','2019-09-13 18:57:16','BH',10,127,0),(368,'F208','Other schizophrenia','2016-08-20 21:24:30','2019-09-13 18:57:19','BH',10,127,0),(369,'F22','Persistent Delusional Disorder','2016-08-20 21:24:53','2019-09-13 18:57:26','BH',10,127,0),(370,'F220','Delusional Disorder','2016-08-20 21:25:22','2019-09-13 18:57:32','BH',10,127,0),(371,'F25','Schizoaffective Disorder','2016-08-20 21:25:43','2019-09-13 18:57:36','BH',10,127,0),(372,'F250','Schizoaffective Disorder, manic','2016-08-20 21:26:01','2019-09-13 18:57:39','BH',10,127,0),(373,'F251','Schizoaffective Disorder, depressive','2016-08-20 21:26:32','2019-09-13 18:57:42','BH',10,127,0),(374,'F252','Schizoaffective Disorder, mixed type','2016-08-20 21:26:53','2019-09-13 18:57:46','BH',10,127,0),(375,'F258','Other Schizoaffective Disorders','2016-08-20 21:27:43','2019-09-13 18:57:50','BH',10,127,0),(376,'F259','Schizoaffective Disorder, unspecified','2016-08-20 21:28:04','2019-09-13 18:57:53','BH',10,127,0),(377,'F310','Bipolar Affective Disorder, hypomanic','2016-08-21 03:14:29','2019-09-13 18:57:57','BH',10,127,0),(378,'F311','Bipolar Affective Disorder, manic w/o psychotic symptoms','2016-08-21 03:14:45','2019-04-22 18:32:58','BH',10,63,1),(379,'F312','Bipolar Affective Disorder, manic with psychotic ','2016-08-21 03:15:08','2019-09-13 18:45:40','BH',10,127,0),(380,'F313','Bipolar Affective Disorder, mild or moderate depression','2016-08-21 03:15:26','2019-04-22 18:33:08','BH',10,63,1),(381,'F314','Bipolar Affective Disorder, severe depression w/o psychotic','2016-08-21 03:15:59','2019-09-13 18:56:01','BH',10,127,0),(382,'F315','Bipolar Affective Disorder, severe depression with psychotic ','2016-08-21 03:19:03','2019-09-13 18:56:04','BH',10,127,0),(383,'F317','Bipolar Affective Disorder, currently in remission','2016-08-21 03:19:21','2019-04-22 18:39:23','BH',10,63,1),(384,'F318','Other  Bipolar Affective Disorder','2016-08-21 03:19:37','2019-09-13 18:56:11','BH',10,127,0),(385,'F3160','Bipolar Affective Disorder, unspecified','2016-08-21 03:21:10','2019-09-13 18:56:07','BH',10,127,0),(386,'F320','Major depressive disorder, single episode, mild','2016-08-21 03:21:30','2019-09-13 18:56:14','BH',10,127,0),(387,'F322','Major depressive disorder, single episode, severe without psychotic features','2016-08-21 03:21:46','2019-09-13 18:56:21','BH',10,127,0),(388,'F329','Major depressive disorder, single episode, unspecified','2016-08-21 03:22:07','2019-09-13 18:56:37','BH',10,127,0),(389,'F33','Recurrent depression disorder','2016-08-21 03:22:24','2018-12-05 20:25:57','BH',10,63,1),(390,'F340','Cyclothymia','2016-08-21 03:22:41','2019-09-13 18:56:43','BH',10,127,0),(391,'F341','Dysthymia','2016-08-21 03:22:56','2019-09-13 18:56:46','BH',10,127,0),(392,'F348','Other persistent mood (affective) disorders','2016-08-21 03:23:18','2019-09-13 18:56:49','BH',10,127,0),(393,'F349','Persistent mood (affective) disorder, unspecified','2016-08-21 03:23:36','2019-09-13 18:56:59','BH',10,127,0),(394,'F39','Unspecified Mood (affective) disorder','2016-08-21 03:23:51','2019-09-13 18:57:02','BH',10,127,0),(395,'F500','Anorexia Nervosa','2016-08-21 03:24:12','2019-09-13 18:54:40','BH',10,127,0),(396,'F502','Bulimia Nervosa','2016-08-21 03:24:32','2019-09-13 18:54:43','BH',10,127,0),(397,'F509','Eating Disorder, unspecified','2016-08-21 03:24:49','2019-09-13 18:50:19','BH',10,127,0),(398,'F4000','Agoraphobia without panic','2016-08-21 03:27:53','2019-09-13 18:57:06','BH',10,127,0),(399,'F4001','Agoraphobia with panic','2016-08-21 03:28:08','2019-09-13 18:53:27','BH',10,127,0),(400,'F401','Social phobias','2016-08-21 03:28:35','2019-09-13 18:53:31','BH',10,127,0),(401,'F410','Panic disorder','2016-08-21 03:29:03','2019-09-13 18:53:42','BH',10,127,0),(402,'F402','Specified (isolated) phobia','2016-08-21 03:29:33','2019-09-13 18:53:38','BH',10,127,0),(403,'F411','Generalized anxiety disorder','2016-08-21 03:29:54','2019-01-10 17:01:08','BH',10,127,0),(404,'F42','Obsessive Compulsive Disorder','2016-08-21 03:30:09','2019-09-13 18:53:47','BH',10,127,0),(405,'F420','Predominantly obsessional thoughts or ruminations','2016-08-21 03:30:26','2018-03-21 06:16:32','PT',10,63,0),(406,'F421','Predominantly compulsive acts / rituals ','2016-08-21 03:30:43','2019-09-13 18:53:52','BH',10,127,0),(407,'F429','Obsessive – compulsive disorder, unspecified','2016-08-21 03:31:02','2019-09-13 18:53:55','BH',10,127,0),(408,'F430','Acute Stress Reaction','2016-08-21 03:31:54','2019-09-13 18:54:13','BH',10,127,0),(409,'F431','PTSD','2016-08-21 03:32:08','2019-04-22 17:36:23','BH',10,127,1),(410,'F432','Adjustment Disorder','2016-08-21 03:32:30','2018-05-18 14:12:00','BH',10,63,1),(411,'F4320','Adjustment Disorder with brief depressive reaction','2016-08-21 03:33:13','2019-09-13 18:54:17','BH',10,127,0),(412,'F4321','Adjustment Disorder with prolonged depressive reaction','2016-08-21 03:33:31','2019-09-13 18:54:20','BH',10,127,0),(413,'F4322','Adjustment Disorder with mixed anxiety and depressive reactive','2016-08-21 03:33:46','2019-09-13 18:54:23','BH',10,127,0),(414,'F4324','Adjustment Disorder with disturbance of conduct','2016-08-21 03:34:08','2019-09-13 18:54:27','BH',10,127,0),(415,'F4325','Adjustment Disorder with disturbance of emotions and conduct','2016-08-21 03:34:24','2019-09-13 18:54:31','BH',10,127,0),(416,'F438','Other reactions to severe stress','2016-08-21 03:34:41','2019-09-13 18:54:35','BH',10,127,0),(417,'F600','Paranoid Personality ','2016-08-21 13:36:37','2019-09-13 18:50:24','BH',10,127,0),(418,'F601','Schizoid Personality','2016-08-21 13:36:55','2019-09-13 18:50:28','BH',10,127,0),(419,'F602','Dissocial Personality ','2016-08-21 13:37:15','2019-09-13 18:50:32','BH',10,127,0),(420,'F603','Emotionally unstable personality Type ','2016-08-21 13:37:32','2019-09-13 18:50:35','BH',10,127,0),(421,'F6030','Impulsive Type','2016-08-21 13:37:52','2019-09-13 18:50:38','BH',10,127,0),(422,'F6031','Borderline Type','2016-08-21 13:38:15','2019-09-13 18:50:41','BH',10,127,0),(423,'F604','Histrionic personality disorder','2016-08-21 13:38:32','2019-09-13 18:50:44','BH',10,127,0),(424,'F606','Anxious personality disorder','2016-08-21 13:38:53','2019-09-13 18:50:49','BH',10,127,0),(425,'F607','Dependent personality disorder ','2016-08-21 13:39:10','2019-09-13 18:50:53','BH',10,127,0),(426,'F902','ADHD Combined Type','2016-08-21 13:39:27','2019-09-13 18:49:34','BH',10,127,0),(427,'F901','ADHD, hyperactive type','2016-08-21 13:39:46','2019-09-13 19:03:14','BH',10,127,0),(428,'F840','Autistic disorder','2016-08-21 13:40:43','2019-09-13 18:48:55','BH',10,127,0),(429,'F840','Childhood autism','2016-08-21 13:41:04','2019-01-09 19:13:41','BH',10,63,1),(430,'F841','Atypical autism','2016-08-21 13:41:22','2019-09-13 18:49:06','BH',10,127,0),(431,'F842','Rett’s syndrome','2016-08-21 13:41:45','2019-09-13 18:49:11','BH',10,127,0),(432,'F843','Other childhood disintegrative disorder','2016-08-21 13:43:03','2019-09-13 18:49:14','BH',10,127,0),(433,'F845','Asperger’s syndrome','2016-08-21 13:43:18','2019-09-13 18:49:19','BH',10,127,0),(434,'F848','Other pervasive developmental disorders','2016-08-21 13:43:35','2019-09-13 18:49:23','BH',10,127,0),(435,'F849','Pervasive developmental disorder, unspecified ','2016-08-21 13:43:54','2019-09-13 18:49:27','BH',10,127,0),(436,'F910','conduct disorder confined to the family context','2016-08-21 13:44:13','2019-09-13 18:49:39','BH',10,127,0),(437,'F911','Unsocialized conduct disorder','2016-08-21 13:44:28','2019-09-13 18:49:53','BH',10,127,0),(438,'F912','Socialized conduct disorder','2016-08-21 13:44:42','2019-09-13 18:49:56','BH',10,127,0),(439,'F913','Oppositional defiant disorder','2016-08-21 13:45:01','2019-09-13 18:50:00','BH',10,127,0),(440,'F919','Conduct disorder, unspecified','2016-08-21 13:45:20','2019-09-13 18:50:05','BH',10,127,0),(441,'F930','Separation anxiety disorder of childhood','2016-08-21 13:45:37','2019-09-13 18:50:09','BH',10,127,0),(442,'F931','Phobic anxiety disorder of childhood','2016-08-21 13:45:54','2019-09-13 18:50:12','BH',10,127,0),(443,'F932','Social anxiety disorder of childhood','2016-08-21 13:46:15','2018-12-05 20:23:17','BH',10,63,1),(444,'F939','Childhood emotional disorder, unspecified','2016-08-21 13:46:29','2019-09-13 18:50:16','BH',10,127,0),(445,'F940','Elective mutism','2016-08-21 13:46:51','2019-09-13 18:46:24','BH',10,127,0),(446,'F941','Reactive attachment disorder of childhood','2016-08-21 13:47:05','2019-09-13 18:46:28','BH',10,127,0),(447,'F942','Disinhibited attachment disorder of childhood','2016-08-21 13:47:18','2019-09-13 18:46:32','BH',10,127,0),(448,'F948','Other childhood disorders of social functioning','2016-08-21 13:47:35','2019-09-13 18:46:35','BH',10,127,0),(449,'F95','Tic Disorders','2016-08-21 13:47:49','2019-09-13 18:46:40','BH',10,127,0),(450,'F950','Transient tic disorder','2016-08-21 13:48:02','2019-09-13 18:46:43','BH',10,127,0),(451,'F951','Chronic motor or vocal tic disorder','2016-08-21 13:48:20','2019-09-13 18:46:47','BH',10,127,0),(452,'F952','Combined vocal and multiple motor tic disorder','2016-08-21 13:48:38','2019-09-13 18:46:49','BH',10,127,0),(453,'F958','Other tic disorder','2016-08-21 13:48:53','2019-09-13 18:46:53','BH',10,127,0),(454,'F959','Tic disorder, unspecified','2016-08-21 13:49:18','2019-09-13 18:46:56','BH',10,127,0),(455,'F98','Enuresis','2016-08-21 13:50:07','2019-09-13 18:47:01','BH',10,127,0),(456,'F981','Encopresis','2016-08-21 13:50:22','2019-09-13 18:47:04','BH',10,127,0),(457,'F982','Feeding Disorder in infancy','2016-08-21 13:50:37','2019-09-13 18:47:07','BH',10,127,0),(458,'F983','Pica','2016-08-21 13:50:52','2019-09-13 18:47:10','BH',10,127,0),(459,'F984','Stereotyped movement ','2016-08-21 13:51:07','2019-09-13 18:47:13','BH',10,127,0),(460,'99999','N/A','2016-08-22 21:57:51','2018-03-21 06:16:32','BH',9,63,0),(461,'G710','Muscular dystrophy','2016-08-23 22:03:19','2018-12-05 19:56:41','NURSE',10,51,1),(462,'G809','Cerebral palsy, unspecified','2016-08-28 17:54:28','2018-03-21 06:16:32','NURSE',10,49,0),(463,'G40309','Generalized idiopathic epilepsy and epileptic syndromes, not intractable','2016-08-30 17:32:22','2018-03-21 06:16:32','NURSE',10,49,0),(464,'G40311','Generalized idiopathic epilepsy and epileptic syndromes, intractable with status epilepticus','2016-08-31 02:05:08','2018-03-21 06:16:32','NURSE',10,49,0),(465,'G40909','Epilepsy, unspecified without status epilepticus','2016-09-01 18:46:13','2018-03-21 06:16:32','NURSE',10,51,0),(466,'Q909','Down Syndrome, Unspecified','2016-09-20 20:36:43','2019-09-13 18:47:18','BH',10,127,0),(467,'R488','Other Symbolic Dysfunctions','2016-09-21 19:22:25','2018-03-21 06:16:32','ST',10,63,0),(468,'Q909','Down Syndrome, Unspecified','2016-09-22 19:30:37','2018-03-21 06:16:32','NURSE',10,51,0),(469,'G808','Other cerebral palsy','2016-09-27 14:50:24','2018-03-21 06:16:32','NURSE',10,51,0),(470,'Q872','Congenital malformation syndromes predominantly involving limbs','2016-09-29 18:06:31','2018-03-21 06:16:32','PT/OT',10,63,0),(471,'F849','Pervasive developmental disorder, unspecified','2016-10-12 17:15:53','2018-03-21 06:16:32','ST',10,63,0),(472,'R479','Unspecified speech disturbances','2016-10-28 16:08:23','2018-03-21 06:16:32','ST',10,63,0),(473,'S062X9D','Diffuse TBI w loss of consciousness of unsp duration subs','2016-11-02 22:33:24','2018-03-21 06:16:32','PT/OT',10,63,0),(474,'F989','Unspecified behavioral and emotional disorders w/onset usually occurring in childhood and adolescence','2017-01-20 17:09:21','2019-09-13 18:52:22','BH',10,127,0),(475,'F3481','Disruptive mood dysregulation disorder','2017-01-24 19:03:59','2019-09-13 18:56:54','BH',10,127,0),(476,'F419','Anxiety disorder, unspecified','2017-01-24 19:04:36','2019-02-11 20:35:09','BH',10,127,0),(477,'F330','Major depressive disorder, recurrent, mild','2017-02-02 13:25:47','2019-09-13 18:56:40','BH',10,127,0),(478,'F840','Autistic disorder','2017-02-06 18:02:52','2018-03-21 06:16:32','NURSE',10,63,0),(479,'Q059','Spina bifida, unspecified','2017-02-14 20:54:55','2018-03-21 06:16:32','PT',10,63,0),(480,'R569','Unspecified convulsions','2017-02-16 18:31:30','2018-03-21 06:16:32','OT',10,51,0),(481,'F321','Major depressive disorder, single episode, moderate','2017-04-06 20:09:31','2019-09-13 18:56:17','BH',10,127,0),(482,'F323','Major depressive disorder, single episode, severe with psychotic features','2017-04-06 20:11:38','2019-09-13 18:56:34','BH',10,127,0),(483,'G8251','Quadriplegia C1-C4 complete','2017-06-19 16:58:06','2018-03-21 06:16:32','NURSE',10,49,0),(484,'F71','Moderate intellectual disabilities','2017-07-11 15:45:09','2018-03-21 06:16:32','ST',10,49,0),(485,'Q898','Other specified congenital malformations','2017-11-15 18:13:36','2018-03-21 06:16:32','PT',10,51,0),(486,'R633','Feeding difficulties','2017-12-11 15:45:21','2018-03-21 06:16:32','NURSE',10,63,0),(487,'P9160','Hypoxic ischemic encephalopathy (HIE), unspecified','2018-05-10 18:48:26','2018-05-10 18:48:26','NURSE',10,63,0),(488,'E8840','Mitochondrial metabolism disorder, unspecified ','2018-05-17 18:18:03','2019-10-28 19:35:22','PT',10,1,0),(489,'E8840','Mitochondrial metabolism disorder, unspecified ','2018-05-17 18:22:56','2019-10-28 19:37:38','PT',10,1,1),(490,'Q933','Deletion of short arm of chromosome 4','2018-05-29 19:13:33','2018-05-29 19:13:33','NURSE',10,63,0),(491,'R41841','Cognitive communication deficit','2018-05-31 14:35:00','2018-05-31 14:35:00','ST',10,63,0),(492,'Q043','Other reduction deformities of brain','2018-08-21 16:50:03','2018-08-21 16:50:03','NURSE',10,3,0),(493,'H5005','Alternating esotropia','2018-10-22 13:20:57','2018-10-22 13:20:57','OT',10,1,0),(494,'H5111','Convergence insufficiency','2018-10-22 13:21:43','2018-10-22 13:21:43','OT',10,1,0),(495,'G7100','Muscular Dystrophy, Unspecified','2018-12-05 19:57:17','2018-12-05 19:57:17','PT/OT',10,127,0),(496,'G7100','Muscular Dystrophy, Unspecified','2018-12-05 20:03:13','2018-12-05 20:03:13','NURSE',10,127,0),(497,'F339','Major depressive disorder, recurrent, unspecified','2018-12-05 20:28:20','2018-12-05 20:28:20','BH',10,127,0),(498,'E7622','Sanfilippo mucopolysaccharidoses','2019-01-04 20:24:56','2019-01-04 20:24:56','NURSE',10,127,0),(499,'F802','Mixed receptive-expressive language disorder	','2019-01-09 19:12:39','2019-01-09 19:12:39','BH',10,127,0),(500,'F819','Developmental disorder of scholastic skills, unspecified','2019-01-09 19:13:06','2019-01-09 19:13:06','BH',10,127,0),(501,'F810','Specific reading disorder','2019-01-09 19:14:48','2019-01-09 19:14:48','BH',10,127,0),(502,'F8181','Disorder of written expression','2019-01-09 19:15:19','2019-01-09 19:15:19','BH',10,127,0),(503,'F812','Mathematics disorder','2019-01-09 19:15:54','2019-01-09 19:15:54','BH',10,127,0),(504,'F801','Expressive language disorder (Specific developmental disorders of speech and language)','2019-01-09 22:08:55','2019-01-09 22:08:55','BH',10,127,0),(505,'F800','Phonological disorder','2019-01-09 22:09:14','2019-01-09 22:09:14','BH',10,127,0),(506,'F8082','Social pragmatic communication disorder','2019-01-10 14:46:59','2019-12-04 16:27:40','ST',10,1,0),(507,'F4312','PTSD Chronic','2019-02-11 21:55:42','2019-02-11 21:56:36','BH',10,127,0),(508,'F4310','Post-traumatic stress disorder, unspecified','2019-04-22 17:38:26','2019-04-22 17:38:26','BH',10,127,0),(509,'F3110','Bipolar disorder, current episode manic without psychotic features, unspecified','2019-04-22 18:34:04','2019-04-22 18:34:04','BH',10,127,0),(510,'F3111','Bipolar disorder, current episode manic without psychotic features, mild','2019-04-22 18:34:39','2019-04-22 18:34:39','BH',10,127,0),(511,'F3112','Bipolar disorder, current episode manic without psychotic features, moderate','2019-04-22 18:34:58','2019-04-22 18:34:58','BH',10,127,0),(512,'F3113','Bipolar disorder, current episode manic without psychotic features, severe','2019-04-22 18:35:20','2019-04-22 18:35:20','BH',10,127,0),(513,'F3130','Bipolar disorder, current episode depressed, mild or moderate severity, unspecified','2019-04-22 18:36:35','2019-04-22 18:36:35','BH',10,127,0),(514,'F3131','Bipolar disorder, current episode depressed, mild','2019-04-22 18:37:08','2019-04-22 18:37:08','BH',10,127,0),(515,'F3132','Bipolar disorder, current episode depressed, moderate','2019-04-22 18:37:31','2019-04-22 18:37:31','BH',10,127,0),(516,'F3170','Bipolar disorder, currently in remission most recent episode unspecified','2019-04-22 18:40:08','2019-04-22 18:40:08','BH',10,127,0),(517,'F70','Mild intellectual disabilities','2019-05-10 18:30:42','2019-05-10 18:30:42','BH',10,127,0),(518,'F71','Moderate intellectual disabilities','2019-05-10 18:31:14','2019-05-10 18:31:14','BH',10,127,0),(519,'F72','Severe intellectual disabilities','2019-05-10 18:31:41','2019-05-10 18:31:41','BH',10,127,0),(520,'F73','Profound intellectual disabilities','2019-05-10 18:31:56','2019-05-10 18:31:56','BH',10,127,0),(521,'F78','Other intellectual disabilities','2019-05-10 18:32:12','2019-05-10 18:32:12','BH',10,127,0),(522,'F79','Unspecified intellectual disabilities','2019-05-10 18:32:26','2019-05-10 18:32:26','BH',10,127,0),(523,'Q897','Multiple congenital malformations, not elsewhere classified','2019-05-22 19:04:37','2019-05-22 19:04:37','NURSE',10,127,0),(524,'Q998','Other specified chromosome abnormalities','2019-07-26 19:25:44','2019-07-26 19:26:06','NURSE',10,1,0),(525,'R41844','Executive Function Disorder','2019-08-21 20:11:15','2019-08-21 20:11:15','ST',10,127,0),(526,'Z930','Tracheostomy','2019-09-05 13:35:50','2019-09-05 13:36:10','NURSE',10,127,0),(527,'F909','ADHD, unspecified type','2019-09-13 19:00:54','2019-09-13 19:00:54','BH',10,127,0),(528,'F900','ADHD, inattentive type ','2019-09-13 19:04:45','2019-09-13 19:04:45','BH',10,127,0),(529,'F333','Major depressive disorder, recurrent, severe with psychotic symptoms','2019-09-24 14:42:52','2019-09-24 14:42:52','BH',10,127,0),(530,'R633','Feeding difficulties','2019-09-26 20:42:55','2019-09-26 20:42:55','ST',10,127,0),(531,'Q8711','Prader-Willi syndrome','2019-10-28 19:37:21','2019-10-28 19:38:29','NURSE',10,127,0),(532,'Q8719','Other congenital malformation syndromes predominantly associated with short stature','2019-10-28 19:38:56','2019-10-28 19:39:14','NURSE',10,127,0),(533,'Q234','Hypoplastic left heart syndrome','2019-11-15 17:16:02','2019-11-15 17:16:02','NURSE',10,127,0),(534,'I69198','Other sequelae of nontraumatic intracerebral hemorrhage','2019-11-21 20:13:26','2019-11-21 20:13:26','NURSE',10,127,0),(535,'Q315','Congenital laryngomalacia','2019-12-22 19:08:57','2019-12-22 19:08:57','NURSE',10,1,0),(536,'G7111','Myotonic muscular dystrophy','2020-01-22 20:34:29','2020-01-22 20:34:29','NURSE',10,127,0),(537,'N178','Other acute kidney failure','2020-01-24 20:00:34','2020-01-24 20:00:34','NURSE',10,127,0),(538,'F812','Mathematics disorder','2020-02-04 16:51:53','2020-02-04 16:51:53','OT',10,127,0),(539,'Q054','Unspecified spina bifida with hydrocephalus','2020-03-10 22:29:16','2020-03-10 22:29:16','NURSE',10,127,0);
/*!40000 ALTER TABLE `ailments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `applied_provider_pins`
--

DROP TABLE IF EXISTS `applied_provider_pins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `applied_provider_pins` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `document_type` varchar(255) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `provider_pin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_applied_provider_pins_on_document_type_and_document_id` (`document_type`,`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `applied_provider_pins`
--

LOCK TABLES `applied_provider_pins` WRITE;
/*!40000 ALTER TABLE `applied_provider_pins` DISABLE KEYS */;
/*!40000 ALTER TABLE `applied_provider_pins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ar_internal_metadata`
--

DROP TABLE IF EXISTS `ar_internal_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ar_internal_metadata` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ar_internal_metadata`
--

LOCK TABLES `ar_internal_metadata` WRITE;
/*!40000 ALTER TABLE `ar_internal_metadata` DISABLE KEYS */;
INSERT INTO `ar_internal_metadata` VALUES ('environment','development','2020-06-18 10:05:29','2020-06-18 10:05:29');
/*!40000 ALTER TABLE `ar_internal_metadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_claims`
--

DROP TABLE IF EXISTS `archived_sdac_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_claims` (
  `id` int(11) NOT NULL DEFAULT '0',
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `salaries` decimal(12,4) DEFAULT NULL,
  `fringe` decimal(12,4) DEFAULT NULL,
  `other_costs` decimal(12,4) DEFAULT NULL,
  `total_costs` decimal(12,4) DEFAULT NULL,
  `mev_rate` decimal(12,4) DEFAULT NULL,
  `student_count` int(11) DEFAULT NULL,
  `eligibility_count` int(11) DEFAULT NULL,
  `nondiscounted_moment_ratio` decimal(12,4) DEFAULT NULL,
  `mer_moment_ratio` decimal(12,4) DEFAULT NULL,
  `mer_claimable_rate` decimal(12,4) DEFAULT NULL,
  `mer_ppr_moment_ratio` decimal(12,4) DEFAULT NULL,
  `ppr` decimal(12,4) DEFAULT NULL,
  `total_claim_rate` decimal(12,4) DEFAULT NULL,
  `claim_at_half` decimal(12,4) DEFAULT NULL,
  `indirect_costs_rate` decimal(12,4) DEFAULT NULL,
  `indirect_claim` decimal(12,4) DEFAULT NULL,
  `total_claim` decimal(12,4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `sdac_random_moments_survey_id` int(11) DEFAULT NULL,
  `moment_count` int(11) DEFAULT NULL,
  `non_admin_moment_count` int(11) DEFAULT NULL,
  `admin_moment_count` int(11) DEFAULT NULL,
  `non_admin_moment_ratio` float DEFAULT NULL,
  `redistributed_moments_sum` int(11) DEFAULT NULL,
  `total_adjusted_moment_count` int(11) DEFAULT NULL,
  `total_moment_rate_excluding_admin` float DEFAULT NULL,
  `rms_data` mediumtext,
  `nondiscounted_cost_pool_data` mediumtext,
  `mer_cost_pool_data` mediumtext,
  `mer_and_ppr_cost_pool_data` mediumtext,
  `non_discounted_salaries` decimal(12,4) DEFAULT NULL,
  `non_discounted_fringe` decimal(12,4) DEFAULT NULL,
  `non_discounted_other` decimal(12,4) DEFAULT NULL,
  `non_discounted_total_costs` decimal(12,4) DEFAULT NULL,
  `discounted_salaries` decimal(12,4) DEFAULT NULL,
  `discounted_fringe` decimal(12,4) DEFAULT NULL,
  `discounted_other` decimal(12,4) DEFAULT NULL,
  `discounted_total_costs` decimal(12,4) DEFAULT NULL,
  `referral_salaries` decimal(12,4) DEFAULT NULL,
  `referral_fringe` decimal(12,4) DEFAULT NULL,
  `referral_other` decimal(12,4) DEFAULT NULL,
  `referral_total_costs` decimal(12,4) DEFAULT NULL,
  `cost_pool_salaries` decimal(12,4) DEFAULT NULL,
  `cost_pool_fringe` decimal(12,4) DEFAULT NULL,
  `cost_pool_other` decimal(12,4) DEFAULT NULL,
  `cost_pool_total_costs` decimal(12,4) DEFAULT NULL,
  `ppr_claimable_eligibility` decimal(12,4) DEFAULT NULL,
  `referral_claimable_percentage` decimal(12,4) DEFAULT NULL,
  `reconciliation_requested_at` datetime DEFAULT NULL,
  `reconciliation_completed_at` datetime DEFAULT NULL,
  `claim_processed_at` datetime DEFAULT NULL,
  `submitted_to_state_at` datetime DEFAULT NULL,
  `refile_flag` tinyint(1) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `refile_reason` text,
  `adjustment_amount` decimal(12,4) DEFAULT NULL,
  `adjustment_reason` text,
  `last_years_salaries` decimal(12,4) DEFAULT NULL,
  `last_years_fringe` decimal(12,4) DEFAULT NULL,
  `salaries_differential` decimal(8,4) DEFAULT NULL,
  `fringe_differential` decimal(8,4) DEFAULT NULL,
  `salaries_justification` text,
  `fringe_justification` text,
  `not_sent_reason` text,
  `revision` int(11) DEFAULT NULL,
  `paid_on` date DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `paid_amount` decimal(12,4) DEFAULT NULL,
  `payment_error` varchar(255) DEFAULT NULL,
  `total_forms_generated` int(11) DEFAULT NULL,
  `total_non_responses` int(11) DEFAULT NULL,
  `forms_considered_invalid` int(11) DEFAULT NULL,
  `cost_pool_1_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_1_fringe` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_fringe` decimal(12,2) DEFAULT '0.00',
  `cost_pool_1_rms_data` longtext,
  `cost_pool_2_rms_data` longtext,
  KEY `archived_sdac_index` (`district_id`,`year`,`quarter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_claims`
--

LOCK TABLES `archived_sdac_claims` WRITE;
/*!40000 ALTER TABLE `archived_sdac_claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_cost_files`
--

DROP TABLE IF EXISTS `archived_sdac_cost_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_cost_files` (
  `id` int(11) NOT NULL DEFAULT '0',
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `sdac_cost_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_cost_files`
--

LOCK TABLES `archived_sdac_cost_files` WRITE;
/*!40000 ALTER TABLE `archived_sdac_cost_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_cost_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_costs`
--

DROP TABLE IF EXISTS `archived_sdac_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_costs` (
  `id` int(11) NOT NULL DEFAULT '0',
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `salaries` decimal(12,4) DEFAULT NULL,
  `other_costs` decimal(12,4) DEFAULT NULL,
  `fringe` decimal(12,4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ppr_rate` decimal(12,4) DEFAULT NULL,
  `indirect_rate` decimal(12,4) DEFAULT NULL,
  `salaries_justification` text,
  `fringe_justification` text,
  `other_costs_justification` text,
  `data_validated_at` datetime DEFAULT NULL,
  `ready_for_review_at` datetime DEFAULT NULL,
  `redo_reason` text,
  `adjustment_amount` decimal(12,4) DEFAULT NULL,
  `adjustment_reason` text,
  `deleted_at` datetime DEFAULT NULL,
  `certification_validated_at` datetime DEFAULT NULL,
  `certification_ready_for_review_at` datetime DEFAULT NULL,
  `edit_costs_button_active` tinyint(1) DEFAULT '0',
  `cost_pool_1_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_1_fringe` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_fringe` decimal(12,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_costs`
--

LOCK TABLES `archived_sdac_costs` WRITE;
/*!40000 ALTER TABLE `archived_sdac_costs` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_costs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_provider_roster_files`
--

DROP TABLE IF EXISTS `archived_sdac_provider_roster_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_provider_roster_files` (
  `id` int(11) NOT NULL DEFAULT '0',
  `roster_file_size` int(11) DEFAULT NULL,
  `roster_content_type` varchar(255) DEFAULT NULL,
  `roster_file_name` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `redo_reason` text,
  `sent_back_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `validator_id` int(11) DEFAULT NULL,
  `unloaded_at` datetime DEFAULT NULL,
  `roster_updated_at` datetime DEFAULT NULL,
  `template_file_name` varchar(255) DEFAULT NULL,
  `template_content_type` varchar(255) DEFAULT NULL,
  `template_file_size` int(11) DEFAULT NULL,
  `template_updated_at` datetime DEFAULT NULL,
  `in_progress` tinyint(1) DEFAULT '0',
  KEY `archived_sdac_roster_searching_idx` (`district_id`,`year`,`quarter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_provider_roster_files`
--

LOCK TABLES `archived_sdac_provider_roster_files` WRITE;
/*!40000 ALTER TABLE `archived_sdac_provider_roster_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_provider_roster_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_random_moments`
--

DROP TABLE IF EXISTS `archived_sdac_random_moments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_random_moments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_random_moment_id` int(11) DEFAULT NULL,
  `random_moment` datetime DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `rejection_timestamp` datetime DEFAULT NULL,
  `qa_flag` tinyint(1) DEFAULT NULL,
  `qa_timestamp` datetime DEFAULT NULL,
  `qa_checker_id` int(11) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `archiver_id` int(11) DEFAULT NULL,
  `valid_flag` tinyint(1) DEFAULT NULL,
  `verification_flag` tinyint(1) DEFAULT NULL,
  `verification_timestamp` datetime DEFAULT NULL,
  `verifier_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `sdac_position_id` int(11) DEFAULT NULL,
  `sdac_job_title` varchar(255) DEFAULT NULL,
  `sdac_position_description` varchar(255) DEFAULT NULL,
  `esp_id` int(11) DEFAULT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `school_state_id` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `currently_invalid` tinyint(1) DEFAULT NULL,
  `inavlid_reason` varchar(255) DEFAULT NULL,
  `completed_timestamp` datetime DEFAULT NULL,
  `rejected_timestamp` datetime DEFAULT NULL,
  `sent_back_timestamp` datetime DEFAULT NULL,
  `status_cache` varchar(255) DEFAULT NULL,
  `redo_reason` varchar(255) DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `used_in_ratio_calculations` tinyint(1) DEFAULT NULL,
  `original_recipient_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `completed_comments` text,
  `non_response` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_archived_sdac_random_moments_on_non_response` (`non_response`),
  KEY `index_archived_sdac_random_moments_on_provider_id` (`provider_id`),
  KEY `index_archived_sdac_random_moments_on_qa_checker_id` (`qa_checker_id`),
  KEY `index_archived_sdac_random_moments_on_sdac_random_moment_id` (`sdac_random_moment_id`),
  KEY `index_archived_sdac_random_moments_on_survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_random_moments`
--

LOCK TABLES `archived_sdac_random_moments` WRITE;
/*!40000 ALTER TABLE `archived_sdac_random_moments` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_random_moments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_rms_forms`
--

DROP TABLE IF EXISTS `archived_sdac_rms_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_rms_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_rms_form_id` int(11) DEFAULT NULL,
  `communication_timestamp` datetime DEFAULT NULL,
  `collection_timestamp` datetime DEFAULT NULL,
  `sdac_random_moment_id` int(11) DEFAULT NULL,
  `original_activity_description` text,
  `modified_activity_description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `archiver_id` int(11) DEFAULT NULL,
  `signature_timestamp` datetime DEFAULT NULL,
  `submission_timestamp` datetime DEFAULT NULL,
  `sdac_position_id` int(11) DEFAULT NULL,
  `sdac_activity_id` int(11) DEFAULT NULL,
  `submission_method` varchar(255) DEFAULT NULL,
  `other_position_name` varchar(255) DEFAULT NULL,
  `manual_communication_reason` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_archived_sdac_rms_forms_on_sdac_random_moment_id` (`sdac_random_moment_id`),
  KEY `index_archived_sdac_rms_forms_on_sdac_rms_form_id` (`sdac_rms_form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_rms_forms`
--

LOCK TABLES `archived_sdac_rms_forms` WRITE;
/*!40000 ALTER TABLE `archived_sdac_rms_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_rms_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_sdac_student_roster_files`
--

DROP TABLE IF EXISTS `archived_sdac_student_roster_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_sdac_student_roster_files` (
  `id` int(11) NOT NULL DEFAULT '0',
  `roster_file_size` int(11) DEFAULT NULL,
  `roster_content_type` varchar(255) DEFAULT NULL,
  `roster_file_name` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `redo_reason` text,
  `sent_back_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `error_message` text,
  KEY `archived_sdac_student_roster_searching_idx` (`district_id`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_sdac_student_roster_files`
--

LOCK TABLES `archived_sdac_student_roster_files` WRITE;
/*!40000 ALTER TABLE `archived_sdac_student_roster_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_sdac_student_roster_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assistant_students`
--

DROP TABLE IF EXISTS `assistant_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assistant_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_assistant_students_on_provider_id` (`provider_id`),
  KEY `index_assistant_students_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assistant_students`
--

LOCK TABLES `assistant_students` WRITE;
/*!40000 ALTER TABLE `assistant_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `assistant_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bh_prior_authorization_appointments`
--

DROP TABLE IF EXISTS `bh_prior_authorization_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bh_prior_authorization_appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_appointment_id` int(11) DEFAULT NULL,
  `prior_authorization_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `minutes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bh_prior_authorization_appointments`
--

LOCK TABLES `bh_prior_authorization_appointments` WRITE;
/*!40000 ALTER TABLE `bh_prior_authorization_appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `bh_prior_authorization_appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bh_prior_authorization_requests`
--

DROP TABLE IF EXISTS `bh_prior_authorization_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bh_prior_authorization_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `form_data` text,
  `state` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `phaxio_id` int(11) DEFAULT NULL,
  `phaxio_state` varchar(255) DEFAULT NULL,
  `phaxio_error` varchar(255) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bh_prior_authorization_requests`
--

LOCK TABLES `bh_prior_authorization_requests` WRITE;
/*!40000 ALTER TABLE `bh_prior_authorization_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `bh_prior_authorization_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bh_prior_authorization_responses`
--

DROP TABLE IF EXISTS `bh_prior_authorization_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bh_prior_authorization_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_updated_at` datetime DEFAULT NULL,
  `manually` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `phaxio_id` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bh_prior_authorization_responses`
--

LOCK TABLES `bh_prior_authorization_responses` WRITE;
/*!40000 ALTER TABLE `bh_prior_authorization_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `bh_prior_authorization_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bh_prior_authorizations`
--

DROP TABLE IF EXISTS `bh_prior_authorizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bh_prior_authorizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `granted_hours` int(11) DEFAULT NULL,
  `remaining_minutes` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bh_prior_authorizations`
--

LOCK TABLES `bh_prior_authorizations` WRITE;
/*!40000 ALTER TABLE `bh_prior_authorizations` DISABLE KEYS */;
/*!40000 ALTER TABLE `bh_prior_authorizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bill_tracking_records`
--

DROP TABLE IF EXISTS `bill_tracking_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill_tracking_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bill_tracking_records`
--

LOCK TABLES `bill_tracking_records` WRITE;
/*!40000 ALTER TABLE `bill_tracking_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `bill_tracking_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_batch_files`
--

DROP TABLE IF EXISTS `billing_batch_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `billing_batch_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_file_size` int(11) DEFAULT NULL,
  `batch_file_name` varchar(255) DEFAULT NULL,
  `batch_content_type` varchar(255) DEFAULT NULL,
  `district_billing_batch_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_batch_files`
--

LOCK TABLES `billing_batch_files` WRITE;
/*!40000 ALTER TABLE `billing_batch_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `billing_batch_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blackout_dates`
--

DROP TABLE IF EXISTS `blackout_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blackout_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `day_portion` tinyint(1) DEFAULT '0',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `district_fk_idx` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blackout_dates`
--

LOCK TABLES `blackout_dates` WRITE;
/*!40000 ALTER TABLE `blackout_dates` DISABLE KEYS */;
/*!40000 ALTER TABLE `blackout_dates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_activities`
--

DROP TABLE IF EXISTS `calendar_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `with_signature` tinyint(1) DEFAULT '0',
  `direct` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_activities`
--

LOCK TABLES `calendar_activities` WRITE;
/*!40000 ALTER TABLE `calendar_activities` DISABLE KEYS */;
INSERT INTO `calendar_activities` VALUES (1,'Direct Therapy Services','2016-10-27 14:35:10','2016-10-27 14:35:10',0,0),(2,'Facilitator/Speech Aide Training','2016-10-27 14:35:23','2016-10-27 14:35:30',0,0),(3,'Formal Consultation','2016-10-27 14:35:41','2016-10-27 14:35:41',0,0),(4,'IEP Development/Documentation','2016-10-27 14:35:51','2016-10-27 14:35:51',0,0),(5,'IEP Meeting','2016-10-27 14:36:02','2016-10-27 14:36:02',0,0),(6,'Implementer Supervision','2016-10-27 14:36:12','2016-10-27 14:36:12',0,0),(7,'Interpretation & Written Report','2016-10-27 14:36:18','2016-10-27 14:36:18',0,0),(8,'Notice of Evaluation','2016-10-27 14:36:24','2016-10-27 14:36:24',0,0),(9,'Plan Time','2016-10-27 14:36:33','2018-11-14 17:33:29',0,0),(10,'Progress Reports','2016-10-27 14:36:38','2016-10-27 14:36:38',0,0),(11,'RED Meeting','2016-10-27 14:36:46','2016-10-27 14:36:46',1,0),(12,'Written Report Write Up','2016-10-27 14:36:53','2016-10-27 14:36:53',0,0),(13,'Eligibility Meeting','2016-11-02 17:38:46','2016-11-02 17:38:46',1,0),(14,'Notice of Action Development','2016-11-02 17:39:02','2016-11-02 17:39:02',0,0),(15,'Notice of Meeting Development','2016-11-02 17:39:15','2016-11-02 17:39:15',0,0),(16,'SLP-A Supervision ','2016-11-02 17:39:28','2016-11-02 17:39:28',1,0),(17,'Speech/Language/Fluency Evaluations','2016-11-02 17:39:41','2016-11-02 17:39:41',1,0),(18,'Speech/Language/Fluency Screening','2016-11-02 17:39:52','2016-11-02 17:39:52',1,0),(19,'Existing Data Development','2016-11-02 17:40:46','2016-11-02 17:40:46',0,0),(20,'Review of Existing Data Development','2016-11-02 17:44:55','2016-11-02 17:44:55',0,0),(21,'MSBA - Business Development','2016-11-09 16:30:49','2016-11-09 16:30:49',0,0),(22,'MSBA - Special Projects','2016-11-09 16:31:00','2016-11-09 16:31:00',0,0),(23,'MSBA - Training','2016-11-09 16:31:08','2016-11-09 16:31:08',0,0),(24,'Travel to/from district','2016-11-09 16:39:19','2016-11-09 16:40:24',0,0),(25,'Assessment & Evaluation ','2016-11-09 16:44:23','2016-11-09 16:44:23',0,0),(26,'Individual & Group therapy/training','2016-11-09 16:44:41','2016-11-09 16:44:41',0,0),(27,'Sped Consultation','2016-11-09 16:44:51','2016-11-09 16:44:51',0,0),(28,'Supportive Services','2017-03-08 16:21:20','2018-03-12 11:36:33',1,0),(29,'Direct CF supervision of evaluation or treatment','2017-10-12 16:34:54','2017-10-12 16:34:54',0,0),(30,'Indirect CF supervision','2017-10-12 16:35:09','2017-10-12 16:35:09',0,0),(31,'Lunch','2018-11-14 17:26:33','2018-11-14 17:26:33',0,0),(32,'Service Documentation','2018-11-14 17:27:40','2018-11-14 17:27:40',0,0),(33,'SpEd Staff Meeting','2018-11-14 17:28:09','2018-11-14 17:28:09',0,0),(34,'Caregiver Consult','2020-03-31 17:38:48','2020-03-31 17:38:48',1,0);
/*!40000 ALTER TABLE `calendar_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar_transaction_data`
--

DROP TABLE IF EXISTS `calendar_transaction_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_transaction_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_transaction_id` int(11) DEFAULT NULL,
  `calendar_activity_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `notes` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `miles_traveled` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar_transaction_data`
--

LOCK TABLES `calendar_transaction_data` WRITE;
/*!40000 ALTER TABLE `calendar_transaction_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar_transaction_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caseload_records`
--

DROP TABLE IF EXISTS `caseload_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caseload_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `minutes` int(11) DEFAULT NULL,
  `time_period` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_activated` tinyint(1) NOT NULL DEFAULT '1',
  `consult_only` tinyint(1) DEFAULT '0',
  `consult_only_date` date DEFAULT NULL,
  `consult_stop_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_fk_idx` (`provider_id`),
  KEY `index_caseload_records_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caseload_records`
--

LOCK TABLES `caseload_records` WRITE;
/*!40000 ALTER TABLE `caseload_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `caseload_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_confirmations`
--

DROP TABLE IF EXISTS `claim_confirmations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_confirmations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claim_id` int(11) DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `remark_one_code` varchar(255) DEFAULT NULL,
  `remark_two_code` varchar(255) DEFAULT NULL,
  `taxonomy_code` varchar(255) DEFAULT NULL,
  `adjustment_reason_one_code` varchar(255) DEFAULT NULL,
  `adjustment_reason_two_code` varchar(255) DEFAULT NULL,
  `service_start_date` date DEFAULT NULL,
  `service_end_date` date DEFAULT NULL,
  `billed_amount` decimal(12,2) DEFAULT NULL,
  `paid_amount` decimal(12,2) DEFAULT NULL,
  `claim_status_code` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `confirmation_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_confirmations`
--

LOCK TABLES `claim_confirmations` WRITE;
/*!40000 ALTER TABLE `claim_confirmations` DISABLE KEYS */;
/*!40000 ALTER TABLE `claim_confirmations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_payments`
--

DROP TABLE IF EXISTS `claim_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remittance_id` int(11) DEFAULT NULL,
  `claim_id` int(11) DEFAULT NULL,
  `payment_amount` decimal(12,2) DEFAULT NULL,
  `charged_amount` decimal(12,2) DEFAULT NULL,
  `patient_responsibility` decimal(12,2) DEFAULT '0.00',
  `status_code` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `data_cache` varchar(1024) DEFAULT NULL,
  `icn` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_claim_payments_on_claim_id` (`claim_id`),
  KEY `index_claim_payments_on_icn` (`icn`),
  KEY `index_claim_payments_on_remittance_id` (`remittance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_payments`
--

LOCK TABLES `claim_payments` WRITE;
/*!40000 ALTER TABLE `claim_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `claim_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claim_submissions`
--

DROP TABLE IF EXISTS `claim_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claim_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claim_id` int(11) DEFAULT NULL,
  `district_billing_batch_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `icn` varchar(255) DEFAULT NULL,
  `tpl_denial` text,
  PRIMARY KEY (`id`),
  KEY `claim_batch_idx_on_claim_submissions` (`claim_id`,`district_billing_batch_id`),
  KEY `index_claim_submissions_on_claim_id` (`claim_id`),
  KEY `index_claim_submissions_on_district_billing_batch_id` (`district_billing_batch_id`),
  KEY `index_claim_submissions_on_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claim_submissions`
--

LOCK TABLES `claim_submissions` WRITE;
/*!40000 ALTER TABLE `claim_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `claim_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `claims`
--

DROP TABLE IF EXISTS `claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claims` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status_text` varchar(255) DEFAULT NULL,
  `amount_billed` float DEFAULT NULL,
  `amount_paid` float DEFAULT NULL,
  `icn` varchar(255) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_claims_on_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `claims`
--

LOCK TABLES `claims` WRITE;
/*!40000 ALTER TABLE `claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `components`
--

DROP TABLE IF EXISTS `components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `plans_mask` int(11) NOT NULL DEFAULT '0',
  `categories_mask` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `components`
--

LOCK TABLES `components` WRITE;
/*!40000 ALTER TABLE `components` DISABLE KEYS */;
INSERT INTO `components` VALUES (1,'Semantics','Morphological Analysis Strategies',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:58'),(2,'Semantics','SIM™-Visual Imagery',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:29'),(3,'Semantics','Verbalizing and Visualizing',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:33'),(4,'Semantics','SIM™- LINCS',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:15'),(5,'Semantics','SIM™- Word Mapping',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:25'),(6,'Semantics','SIM™- Fundamentals of Paraphrasing and Summarizing',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:07'),(7,'Semantics','Language!',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:28'),(8,'Semantics','Language for Learning',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:21'),(9,'Semantics','Word Wisdom',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:45'),(10,'Semantics','Word Feast',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:42'),(11,'Semantics','Frayer Model (example/non-example)',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:14'),(12,'Semantics','Semantic Feature analysis',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:03'),(13,'Semantics','Marzano- Similarities and Differences',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:51'),(14,'Semantics','Marzano- Non-linguistic representation',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:41'),(15,'Semantics','SIM™- Self Questioning',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:19'),(16,'Semantics','SIM™- Inferencing',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:10'),(17,'Semantics','Marzano- Summarizing and Note-Taking',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:54'),(18,'Semantics','Marzano- Cooperative Learning',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:25'),(19,'Semantics','Marzano- Generating and testing hypothesis',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:38'),(20,'Semantics','Marzano- Questions, Cues and Advance Organizers',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:45'),(21,'Syntax','SIM™- Sentence Writing',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:56'),(22,'Syntax','SIM™- Paragraph Writing',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:01'),(23,'Syntax','SIM™- Theme Writing',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:34'),(24,'Syntax','Sentence Combining',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:08'),(25,'Pragmatics','Social Thinking',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:11'),(26,'Pragmatics','Role Playing',1,8,'2016-08-19 22:16:40','2016-08-21 20:35:46'),(27,'Pragmatics','Social Stories',1,8,'2016-08-19 22:16:40','2016-08-21 20:36:07'),(28,'Speech','5 Minute Speech',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:50'),(29,'Speech','Complexity Model',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:56'),(30,'Speech','Cycles Approach',1,8,'2016-08-19 22:16:40','2016-08-21 20:38:01'),(31,'Speech','Van Riper (traditional)',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:14'),(32,'Speech','Minimal Pairs',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:42'),(33,'Speech','Auditory Bombardment',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:53'),(34,'Speech','Source for Apraxia',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:36'),(35,'Speech','Kaufman Speech Praxis Kit',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:53'),(36,'Speech','Metaphon Therapy',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:48'),(37,'Speech','Stuttering Modification',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:19'),(38,'Speech','Fluency Shaping',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:59'),(39,'Speech','Source for Fluency',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:31'),(40,'Speech','Source for Voice',1,8,'2016-08-19 22:16:40','2016-08-21 20:37:25'),(41,'','1. Psychoeducation',15,1920,'2016-08-20 21:35:09','2016-10-13 13:26:02'),(42,'','2. Emotion Awareness (Somatic Management)',15,1920,'2016-08-20 21:35:40','2016-10-13 13:28:40'),(43,'','3. Cognitive Restructuring (Learning flexible cognitive interpretations)',15,1920,'2016-08-20 21:40:13','2016-10-13 13:28:52'),(44,'','4. Problem Solving/Strategy Selection (Emotional Management)',15,1920,'2016-08-20 21:40:51','2016-10-13 13:29:13'),(45,'','5. Exposure/Generalization (Relapse Prevention)',15,1920,'2016-08-20 21:41:09','2016-10-13 13:29:34'),(46,'','6. Maintenance/Fading',15,1920,'2016-08-20 21:41:19','2016-10-13 13:29:58'),(47,'','7. Crisis Management',15,1920,'2016-08-20 21:41:40','2016-10-13 13:30:07'),(48,'','8. Parent Training',15,1920,'2016-08-20 21:41:51','2016-10-13 13:30:17');
/*!40000 ALTER TABLE `components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confidential_notes`
--

DROP TABLE IF EXISTS `confidential_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `confidential_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) DEFAULT NULL,
  `provider_id` bigint(20) DEFAULT NULL,
  `note` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_confidential_notes_on_provider_id` (`provider_id`),
  KEY `index_confidential_notes_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confidential_notes`
--

LOCK TABLES `confidential_notes` WRITE;
/*!40000 ALTER TABLE `confidential_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `confidential_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `copier_logs`
--

DROP TABLE IF EXISTS `copier_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `copier_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_type` varchar(255) DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `into_type` varchar(255) DEFAULT NULL,
  `into_id` int(11) DEFAULT NULL,
  `status` varchar(1000) DEFAULT NULL,
  `result` mediumtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `copier_logs`
--

LOCK TABLES `copier_logs` WRITE;
/*!40000 ALTER TABLE `copier_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `copier_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delayed_jobs`
--

DROP TABLE IF EXISTS `delayed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delayed_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` int(11) DEFAULT '0',
  `attempts` int(11) DEFAULT '0',
  `handler` mediumtext,
  `last_error` mediumtext,
  `run_at` datetime DEFAULT NULL,
  `locked_at` datetime DEFAULT NULL,
  `failed_at` datetime DEFAULT NULL,
  `locked_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `queue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_delayed_jobs_on_locked_by` (`locked_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delayed_jobs`
--

LOCK TABLES `delayed_jobs` WRITE;
/*!40000 ALTER TABLE `delayed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `delayed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `device_type` int(11) DEFAULT NULL,
  `device_token` varchar(255) DEFAULT NULL,
  `hwid` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `district_bank_details`
--

DROP TABLE IF EXISTS `district_bank_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `district_bank_details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `district_id` bigint(20) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `routing_number` varchar(255) DEFAULT NULL,
  `superintendent_name` varchar(255) DEFAULT NULL,
  `superintendent_ssn` varchar(255) DEFAULT NULL,
  `superintendent_birth_date` date DEFAULT NULL,
  `board_members` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_district_bank_details_on_district_id` (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `district_bank_details`
--

LOCK TABLES `district_bank_details` WRITE;
/*!40000 ALTER TABLE `district_bank_details` DISABLE KEYS */;
INSERT INTO `district_bank_details` VALUES (1,2,'','','','','',NULL,NULL,NULL,'2020-10-29 12:05:08','2020-10-29 12:05:08');
/*!40000 ALTER TABLE `district_bank_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `district_billing_batch_messages`
--

DROP TABLE IF EXISTS `district_billing_batch_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `district_billing_batch_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_billing_batch_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `text` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `district_billing_batch_messages`
--

LOCK TABLES `district_billing_batch_messages` WRITE;
/*!40000 ALTER TABLE `district_billing_batch_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `district_billing_batch_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `district_billing_batches`
--

DROP TABLE IF EXISTS `district_billing_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `district_billing_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accepted` tinyint(1) DEFAULT NULL,
  `data_cache_file_name` varchar(255) DEFAULT NULL,
  `data_cache_file_size` int(11) DEFAULT NULL,
  `data_cache_content_type` varchar(255) DEFAULT NULL,
  `ack_messages` varchar(1024) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `district_billing_batches_date_index` (`date`),
  KEY `index_district_billing_batches_on_state_id` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `district_billing_batches`
--

LOCK TABLES `district_billing_batches` WRITE;
/*!40000 ALTER TABLE `district_billing_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `district_billing_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `plan_type` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `npi` varchar(255) DEFAULT NULL,
  `ein` varchar(255) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `mo_dese_id` varchar(255) DEFAULT NULL,
  `billing_provider_number` varchar(255) DEFAULT NULL,
  `dist_type` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `billing_info_confirmed` tinyint(1) DEFAULT NULL,
  `simple_billing_enabled` tinyint(1) DEFAULT NULL,
  `taxonomy_code` varchar(255) DEFAULT NULL,
  `paper_logging_enabled` tinyint(1) DEFAULT NULL,
  `bills_transportation` tinyint(1) DEFAULT NULL,
  `mhd_code` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `mo_ftp_username` varchar(255) DEFAULT NULL,
  `mo_ftp_password` varchar(255) DEFAULT NULL,
  `using_online_sdac_training` tinyint(1) DEFAULT NULL,
  `disable_consent_tracking` tinyint(1) DEFAULT NULL,
  `sdac_participant` tinyint(1) DEFAULT '0',
  `sdac_vendor_id` varchar(255) DEFAULT NULL,
  `basecamp_project_id` int(11) DEFAULT NULL,
  `minutes_in_school_day` int(11) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT NULL,
  `electronic_sdac_enabled_on` date DEFAULT NULL,
  `enable_billing` tinyint(1) DEFAULT '0',
  `state_id` int(11) DEFAULT NULL,
  `offer_billing_question` tinyint(1) DEFAULT '1',
  `sftp_login` varchar(255) DEFAULT NULL,
  `sftp_password` varchar(255) DEFAULT NULL,
  `allow_conferencing` tinyint(1) DEFAULT '0',
  `signature_required` varchar(255) DEFAULT NULL,
  `sftp_loader_klass` varchar(255) DEFAULT NULL,
  `sftp_loader_filename` varchar(255) DEFAULT NULL,
  `simple_management_enabled` tinyint(1) DEFAULT '0',
  `overwrite_tm_calendar` tinyint(1) DEFAULT '0',
  `sftp_loader_emails` text,
  `turn_off_medicaid_indicator` tinyint(1) DEFAULT '0',
  `script_requestor_id` int(11) DEFAULT NULL,
  `plans_mask` int(11) DEFAULT NULL,
  `therapy_component_enabled` tinyint(1) DEFAULT '0',
  `student_roster_upload_from_date` date DEFAULT NULL,
  `student_roster_upload_to_date` date DEFAULT NULL,
  `bill_tracking_records_enabled` tinyint(1) DEFAULT '0',
  `simple_attendance_enabled` tinyint(1) DEFAULT '0',
  `script_request_start_date` date DEFAULT NULL,
  `student_school_enabled` tinyint(1) DEFAULT '0',
  `prior_auths_enabled` tinyint(1) DEFAULT '0',
  `medicaid_district_start_date` date DEFAULT NULL,
  `medicaid_district_end_date` date DEFAULT NULL,
  `medicaid_id` varchar(255) DEFAULT NULL,
  `remittance_suspension_date` date DEFAULT NULL,
  `confidential_notes_enabled` tinyint(1) DEFAULT '0',
  `w9_form_file_name` varchar(255) DEFAULT NULL,
  `w9_form_content_type` varchar(255) DEFAULT NULL,
  `w9_form_file_size` int(11) DEFAULT NULL,
  `w9_form_updated_at` datetime DEFAULT NULL,
  `provider_pins_enabled` tinyint(1) DEFAULT '0',
  `iep_ratio_upload_from_date` date DEFAULT NULL,
  `iep_ratio_upload_to_date` date DEFAULT NULL,
  `iep_ratio_ftp_folder` varchar(255) DEFAULT NULL,
  `fairbanks_district_name` varchar(255) DEFAULT NULL,
  `bh_participant` tinyint(1) DEFAULT NULL,
  `bh_start_date` date DEFAULT NULL,
  `nurse_supervision_participant` tinyint(1) DEFAULT NULL,
  `nurse_supervision_start_date` date DEFAULT NULL,
  `speech_services_participant` tinyint(1) DEFAULT NULL,
  `speech_services_start_date` date DEFAULT NULL,
  `cost_settlement_participant` tinyint(1) DEFAULT NULL,
  `lte_participant` tinyint(1) DEFAULT NULL,
  `ds_participant` tinyint(1) DEFAULT '0',
  `level_of_service` varchar(255) NOT NULL DEFAULT 'PLATFORM',
  PRIMARY KEY (`id`),
  KEY `index_districts_on_district_id` (`district_id`),
  KEY `dese_idx` (`mo_dese_id`),
  KEY `index_districts_on_name` (`name`),
  KEY `index_districts_on_state_id` (`state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,'Any School District','ACTIVE','MEMBER','123 CPS Lane','Columbia','65201','2008-07-30 00:47:15','2019-12-05 20:54:59','','12345','','','','STANDARD',NULL,0,1,'',1,0,'',NULL,NULL,'','',0,0,0,'',NULL,NULL,0,'2010-01-01',0,1,0,'','',1,'Always','','',1,1,'',0,NULL,119,0,NULL,NULL,0,0,NULL,0,1,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,1,'2020-01-01','2020-01-31',NULL,NULL,0,NULL,0,NULL,0,NULL,0,0,0,'PLATFORM'),(2,'Test District','ACTIVE','MEMBER','123 abc lane','st louis','63110',NULL,'2020-10-29 12:05:08','','','','','','STANDARD',NULL,0,0,'',0,0,'',NULL,NULL,'','',0,0,0,'',NULL,NULL,NULL,NULL,0,1,0,'','',1,'Always','','',0,0,'',0,NULL,1,0,NULL,NULL,0,0,NULL,0,0,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'','',0,NULL,0,NULL,0,NULL,0,0,0,'PROVIDERS');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts_parental_consent_templates`
--

DROP TABLE IF EXISTS `districts_parental_consent_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts_parental_consent_templates` (
  `district_id` bigint(20) NOT NULL,
  `parental_consent_template_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts_parental_consent_templates`
--

LOCK TABLES `districts_parental_consent_templates` WRITE;
/*!40000 ALTER TABLE `districts_parental_consent_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `districts_parental_consent_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_evaluations_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluations`
--

LOCK TABLES `evaluations` WRITE;
/*!40000 ALTER TABLE `evaluations` DISABLE KEYS */;
/*!40000 ALTER TABLE `evaluations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fairbanks_accounts`
--

DROP TABLE IF EXISTS `fairbanks_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fairbanks_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT NULL,
  `editor_id_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_fairbanks_accounts_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fairbanks_accounts`
--

LOCK TABLES `fairbanks_accounts` WRITE;
/*!40000 ALTER TABLE `fairbanks_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `fairbanks_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fairbanks_logs`
--

DROP TABLE IF EXISTS `fairbanks_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fairbanks_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fairbanks_account_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_fairbanks_logs_on_district_id_and_year_and_quarter` (`district_id`,`year`,`quarter`),
  KEY `index_fairbanks_logs_on_fairbanks_account_id` (`fairbanks_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fairbanks_logs`
--

LOCK TABLES `fairbanks_logs` WRITE;
/*!40000 ALTER TABLE `fairbanks_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `fairbanks_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faqs`
--

LOCK TABLES `faqs` WRITE;
/*!40000 ALTER TABLE `faqs` DISABLE KEYS */;
INSERT INTO `faqs` VALUES (1,'How do I get Started?','Getting started you will need to first make sure your caseload is up to date by selecting \'Case Load\' under the Therapist tools heading on this page.  Once you have made sure that every student that you for direct therapy is listed on your caseload, you can proceed to recording your data.  Therapylog.com is designed to allow you to do all your therapy related planning on this site and using the information about each of your therapy sessions sends the billing information to Medicaid.  This means that instead of keeping your therapy notes (which contains much of the same information that your billing logs would) and a billing log, you can keep your therapy information on Therapylog.com and the billing is done automatically for you.  By selecting \'My Calendar\' you can access the area where you can enter your session planning.  If you have questions about the calendar you can look at the FAQ on the calendar page.','2010-08-02 21:24:37','2010-08-02 21:24:37'),(2,'How do I change my personal information?','If your name has changed, your email has changed, or you wish to change your password you can do so on this home page by selecting \'Edit My Account\' under the therapist tools heading.  Once there you will see fields that are changeable and a link to change your password.  Remember to select \'submit\' once you have successfully made your changes.','2010-08-02 21:24:37','2010-08-02 21:24:37'),(3,'What are Alerts?','The blue links listed under the alerts show appointments that have yet to be documented.  There will be one link per session.  When you select the link you can enter your notes, your progress records, attendance, there by completing your documentation for the session.  The number of links listed under this heading is shortened to a list that shows only the most recent sessions that have fallen into the past.  As you document they will no longer appear on this list.  When there are no links under the heading, you have completed all your documentation for every session that has already happened.  It may be wise to do your documentation at regular intervals as to not have too many sessions showing in this list.','2010-08-02 21:24:37','2010-08-02 21:24:37'),(4,'What are Generated Report Alerts?','Below this heading you will see that there are links listed below.  Whenever you generate a report from the report menu (which you can get to by accessing the \'My Reports\' under the therapist tools heading on the home page), that report will be sent to your email address and a link will be listed under this heading.  These links are only good for one week and will disappear every Sunday.  To prevent loosing any data be sure and save a copy of the report to your computer.','2010-08-02 21:24:37','2010-08-02 21:24:37'),(6,'Who should be on my Caseload?','Your caseload should include all students that you see for direct therapy or consultation.  You will want to keep students on your caseload until you are certain that all documentation has been completed for any appointment that this student was scheduled for.  Do not worry about adding a student to your caseload that used to be on another therapist\'s list before they remove the student from their list.  A student can be on the caseload of multiple therapists.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(7,'What information should I include for each student?','For the most part you will be responsible for keeping IEP/Re-Eval dates, IEP minutes, and goals up to date in the system although the IEP minutes are not required.  If you choose to add the minutes you will have access to a report that allows you to compare the amount of therapy you scheduled to the amount of therapy is recommended in the IEP.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(8,'How do I add a student?','At the top of your caseload page there is a button that says \'Add Student\' which will redirect you to the add student page.  ','2010-08-02 21:24:38','2010-08-02 21:24:38'),(9,'How do I add/change IEP dates','You will want to keep the IEP dates and Re-Eval dates current so it is our recommendation that after an IEP meeting is conducted you go in and update these dates to the current IEP.  You can do this by clicking on the students name in the caseload list.  This will take you to the student\'s profile page.  On this page you can use the calendars to set the current IEP date (the start date of the current IEP) and then the ReEvaluation date (the scheduled date of the upcoming 3 year ReEvaluation).  Therapylog will not bill a session if it appears that the therapy occurred based on an outdated IEP so it is important to keep these dates up to date.  In some cases your administrators will send update files to Therapylog, but it is the therapist\'s responsibility to maintain this information.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(10,'How do I adjust minutes?','If after an IEP meeting it is determined that the number of minutes per week the child is seen should be extended or reduced, you can update this information by selecting \'Edit\' to the right of the student\'s name then adjusting the minutes in the box.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(11,'How do I delete a student?','If you are no longer seeing a student for therapy and all appointments that student attended have been documented you are free to remove that student from your caseload.  You will not loose goal or IEP data entered for that student if you delete them, and the next therapist who adds that student will be able to pull that student information when they add the student.  Your session notes will still be housed in your records that can be pulled via the reporting section of the site.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(12,'Where are my Goals?','If you are using the therapy management version of the site you will be keeping your notes and documentation in the system.  If you load all your goals in the goals section then you will not have to re-write the goals every time you address them in your documentation.  To add a goal select \'Goals\' to the left of the student\'s name.  It is very important that you have all the goals you plan on addressing for the course of that IEP listed in the goals section.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(13,'What if I can\'t find my student','After typing 3 or more letters into the search box student names will begin to appear in the bottom as options of students to add to your caseload.  If you do not see the student that you wish to add listed among them, continue to type the rest of the student\'s full name.  This will narrow down the search and give you a better chance at finding the student.  If you still cannot find the student you are looking for, contact your administrator with the student\'s name, and student ID so they can add that student to the pool of students you are building your caseload from.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(14,'When do I check \'Consult Only\'','You will check this box only if you are adding a student that does not receive Direct Therapy services but you would still like to record data on this student you work with in a consultative situation.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(15,'How do I add a goal?','At the end of the list of goals there is a link that says \'Add New Goal\'.  Select the link and you will be taken to a Goal entry page.  Remember you only have to add the goals that you address in the IEP, even though you see goals listed for other therapy disciplines.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(16,'Why do I see other therapists\' goals?','Goals are associated with a student record.  If two therapists work with the same student they want to be recording their data for that student so they must work on the same record.  As a result all goals will show up across all therapy disciplines.  Each therapist is only responsible for the goals that they address from the IEP.  Any edits you make to any of the goals will be saved so be sure and only make changes to goals that you address.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(17,'How do I change a goal?','Your goal should read as it does in the IEP so that your documentation reflects that you addressed a goal from the IEP.  If there are spelling errors or other mis-typings, these can be edited by choosing \'Edit\' to the left of the goal.  If you are updating the goals because the IEP has been re-written, it is better to leave the old goal and Inactivate it, and add the newer goals from the most current IEP.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(18,'How do I add a Benchmark?','If a student has benchmarks under a particular goal, you can add these by selecting \'Benchmarks\' to the right of the goal and attach as many benchmarks as you need.   Simply type the Benchmarks into the field on the Benchmark page and submit the form.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(19,'What is an Active goal vs. an Inactive goal?','An Active goal means that this is a goal that you intend to work on with a student for the current IEP.  This means that it is a goal that you will have access to when it comes time to lesson plan for that student.  An Inactive goal will not be accessible to you during the lesson planning stage.  Inactive goals are usually goals that you no longer work with either because they are from a past IEP or they have been met already.  Goals do not become Active or Inactive automatically.  You will have to select \'Inactivate\' in order to Inactivate a goal.  The status of the goal will be shown to the right of the goal.  It\'s important to remember that if you inactivate a goal that does not belong to you the therapist that goal does belong to will not be able to access that goal during the lesson planning stage.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(20,'Why can I delete some goals but not others?','If a goal has already been used in lesson planning it can no longer be deleted because it is needed for the archived documentation for the session in which it was addressed.   Instead if you have used a goal but no longer have need of it, you are welcome to Inactivate this goal.  If there is a link to the right of the goal that says \'Delete\' this goal has not been addressed in a therapy session.  If the goal belongs to you and it will never be addressed in future sessions you are welcome to select the delete link and delete that goal.  This action cannot be undone.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(21,'What should the goal name be?','The goal name should read as it does in the IEP.  You are free to copy the goal out of your IEP system and past it into the Goal Name field to expedite the goal adding process.  If you choose to put a cue at the beginning of a goal for quicker recognition during lesson planning, put that cue at the front of the goal.  If you choose to abbreviate the goal rather then enter it as written in the IEP just keep in mind that the goal when combined with your progress notes comes together to form your complete documentation for a session.  Please direct questions about goal writing to your district administrator.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(22,'What is a GLE?','GLE stands for Grade Level Equivalency.  If you choose to attach a GLE to your goal everywhere your goal is documented the GLE will appear in parenthesis next to the goal.  If your district requires GLEs having the GLE attached to a goal should save you time as you will only ever have to enter it once per IEP cycle.  If your district does not require GLEs you do not have to enter one at all to submit services and your documentation will still show your goals accurately.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(23,'What do I do about diagnosis codes?','Diagnosis codes are the most important part of the goal entering process from a Medicaid perspective.  A session cannot be billed if that session is addressing a goal that does not have an associated diagnosis.  Your district administrators may provide you with a list of diagnosis codes they want you to connect to each goal or may let you choose based on the list of codes in the diagnosis drop down.  Remember when choosing a diagnosis code you are not diagnosing the child but rather categorizing what type of treatment this goal is addressing.  For example if you are an SLP working with an autistic student on articulation a common choice would be 315.39.  Diagnosis codes are left to your professional discretion.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(24,'How do I schedule an Appointment?','Scheduling appointments should be done in advance of the service that you are recording and this task should take the place of your regular lesson planning.  The first step is to select a day you wish to plan for.  The day planner will appear and you can select the hour that you wish to schedule the service.  From there you will be brought to the schedule service page where you can build your therapy groups and do your lesson planning.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(25,'How do I make scheduling more efficient?','There are a couple of features built into the site that will make scheduling and lesson planning faster and easier.  Copy Entire Day:  This feature lets you build your whole day, and then move all the groups and lesson plans for that day into a new day of your choice.  So if you Monday\'s, Wednesday\'s and Friday\'s you have the same schedule, you only have to build that day once.  The button that allows you to copy an entire day is located at the top of the Day Planner.  Copy Appointment: This feature allows you to take a single appointment and copy that over into new days, there by building your day out of appointments that you have already made.  This is particularly effective if none of your days are exactly the same but your groups manage to stay the same.  Warning:  These actions will bring over the lesson plan from the original appointment.  If you wish you can change the lesson plan by editing the session before the session occurs or you can make activity, and goal changes while you are entering your progress notes and documentation.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(26,'What are the drop downs for?','The drop-downs at the top of the month calendar allow you to jump between months and years quickly if you need to move more than one in either direction.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(27,'Why is one day gray?','The day highlighted gray is the current date','2010-08-02 21:24:38','2010-08-02 21:24:38'),(28,'What does a Green or Purple day mean?','A green day indicates a day that has one or more appointment that has not had its documentation and progress entered.  A purple day indicates that all appointments on that day are completed and can be accessed by your reports.  Its good practice to not go too long with green days but instead to turn them purple as soon as possible.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(29,'How do I print my lesson plan?','To print your whole day\'s lesson plan select the day on the calendar then in the top left hand corner you will see a link that says \'Print Lesson Plans\'.  When you are brought to this link you will be brought to a page that has all of the planning information you entered during your scheduling.  If you are a pen and paper person you may like to keep short hand session notes on these pages before transferring them into Therapylog.com','2010-08-02 21:24:38','2010-08-02 21:24:38'),(30,'What If I Made a Mistake?','Mistakes happen, and it\'s good if you catch them early on.  After an appointment has been submitted for billing you only have three days before the transactions can no longer be pulled back from Medicaid (its already been billed).  So it\'s important to catch and fix your mistakes early.  If you made a scheduling or Lesson plan error that you would like to correct before the session starts you can click on the green part of the day, click on the appointment then you will be brought to the scheduling page.  You will be brought to the Edit Scheduled Service page where you can change any drop down, add Students, and change lesson plans.If you made a documentation error, as in you have submitted the service for billing and archival, you will need to first unlock the submitted appointment.  You can do this by finding the day of the appointment, then the purple appointment that is incorrect. Inline with the appointment there will be a padlock icon.  If you select this icon you will unlock the appointment.  Unlocking an appointment will pull it back from the group of transactions going to Medicaid, and will turn the appointment from purple to green.  At this point you can either go into the appointment and delete it, or make the necessary changes by editing the appointment.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(31,'What fields are required?','In order for you to create a valid transaction you must choose the school that you are working in, the correct start and stop time, choose a service, and select a student or students.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(32,'How do I build a group?','Adding students to a session to build a group is easy.  Just begin typing a student from your caseload\'s name in the student name field.  Below the box a list of potential students will appear so that you can choose these students.  When you choose a student you will be prompted to lesson plan for this session.  You will select a goal or goals, enter your scheduled activity or activities, and then you can enter the next student in the group.  To add a new student, move your curser to the student field and begin typing the next student\'s name.  Do not hit submit between students.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(33,'Can I have multiple services in a single appointment?','Yes, to choose an additional service you should select from the \'Portion of Appointment\' drop down \'Some\' instead of \'all\' then click on the tab that reads \'Add Another Service\'.   A screen very similar to your first lesson planning screen will show up where you can add student to this portion of appointments.  Remember to enter the amount of time you wish to spend on this part of the appointment.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(34,'How should I word my activities?','If your district has not already established how they would like your activities to be worded, just keep in mind that the activity, along with the IEP goal and your session progress notes together form your official documentation record.  The more detailed you are with your Activity description, the less detailed you will need to be in your progress notes.  If you are still unsure contact your administrator and ask their opinion.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(35,'How do I edit a service?','To edit a service that you have already scheduled make sure that you have unlocked the service appointment that you wish to change (It Should Be Green) then select the day from your calendar, and then the service from your Day Planner.  At this point you will be brought to the Edit Schedule Service page.  It should look a lot like the Scheduling page.  Here you can change any of the drop downs remove or add goals, change times, dates, and add students and services.  Just remember to submit your changes when they are done.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(36,'I can\'t see the goal I am supposed to document on','Instead of listing the goals you worked on for each student on your documentation page, the documentation form provides links to the goals, right next to where you would rate the students\' progress, and enter any data taken.  The blue link that says goals will expand to a pop up that writes out the whole goal.  When you are done reading the goal click the small X in the right corner of the pop up.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(37,'What if I didn\'t work on what I planned to work on?','Sometimes it happens that even the best laid plans don\'t work out.  If this is the case for a session, don\'t go back to the calendar to edit your plan, instead select the \'Edit Service\' button on the top of the documentation screen.  It will take you back to the Edit Scheduled Service page and you can change the service, goals, students and time right there.','2010-08-02 21:24:38','2015-11-03 09:52:17'),(38,'What if the student wasn\'t present?','You can record attendance by selecting the present or absent buttons at the top of the screen.  If you select Absent, then you will not be required to enter data or progress and if you choose you can still may enter comments.  Check with your administrator if you are unsure comments are required if a student is absent in your district.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(39,'What should my comments say?','It\'s recommended that you enter a note about the session ever time you are in an appointment with the student.  These notes will appear in your reports so that they can be used in parent teacher conferences, IEP meetings, and can be used by another therapist should the student transfer off of your caseload.  With this in mind your session comments should also describe what you did with the student if it\'s not clear from your activity.  Also, it gives you the opportunity to provide a little more detail then your quantitative data and progress rating you gave.  As always when in doubt, contact your administrator to see if they have any tips or requirements for your notes.','2010-08-02 21:24:38','2010-08-02 21:24:38'),(40,'What happens when I am done?','When you select \'Document\' the session is then locked.  If the service and students were billable this data is off to Medicaid to be reimbursed for your district.  If the service is unbillable then it is archived for your records so that you can go back and look at your information at any time.  In the case of Billable services you can make corrections up to 3 days after you complete your documentation.  After that these services are sent to Medicaid and have to be permanently locked.  If you make a mistake and it is discovered after the 3 day timeline please contact your administrator.  We are able to void claims that are sent to Medicaid with errors.','2010-08-02 21:24:38','2010-08-02 21:24:38');
/*!40000 ALTER TABLE `faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favourite_reports`
--

DROP TABLE IF EXISTS `favourite_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favourite_reports` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `provider_id` bigint(20) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_favourite_reports_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favourite_reports`
--

LOCK TABLES `favourite_reports` WRITE;
/*!40000 ALTER TABLE `favourite_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `favourite_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goal_benchmarks`
--

DROP TABLE IF EXISTS `goal_benchmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goal_benchmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10000) DEFAULT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `gle` varchar(255) DEFAULT NULL,
  `goal_benchmark_type` varchar(255) DEFAULT NULL,
  `target_percentage` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_goal_benchmarks_on_goal_id_and_number` (`goal_id`,`number`),
  KEY `goal_fk_idx` (`goal_id`),
  KEY `index_goal_benchmarks_on_number` (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goal_benchmarks`
--

LOCK TABLES `goal_benchmarks` WRITE;
/*!40000 ALTER TABLE `goal_benchmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `goal_benchmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goals`
--

DROP TABLE IF EXISTS `goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `name` varchar(10000) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ailment_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `gle` varchar(255) DEFAULT NULL,
  `target_percentage` int(11) DEFAULT NULL,
  `goal_benchmarks_count` int(11) DEFAULT '0',
  `goal_type` varchar(255) DEFAULT NULL,
  `icd10_ailment_id` int(11) DEFAULT NULL,
  `iep_id` int(11) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `for_simple_billing` tinyint(1) NOT NULL DEFAULT '0',
  `baseline` int(11) DEFAULT NULL,
  `problem` varchar(2000) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_goals_on_iep_id_and_number_and_type` (`iep_id`,`number`,`type`),
  KEY `index_objectives_on_ailment_id` (`ailment_id`),
  KEY `index_goals_on_iep_id` (`iep_id`),
  KEY `index_goals_on_number` (`number`),
  KEY `index_goals_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goals`
--

LOCK TABLES `goals` WRITE;
/*!40000 ALTER TABLE `goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iep_ratio_files`
--

DROP TABLE IF EXISTS `iep_ratio_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iep_ratio_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `district_id` bigint(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `sent_back_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `error_message` text,
  `redo_reason` text,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `restored_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_iep_ratio_files_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iep_ratio_files`
--

LOCK TABLES `iep_ratio_files` WRITE;
/*!40000 ALTER TABLE `iep_ratio_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `iep_ratio_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iep_ratio_letters`
--

DROP TABLE IF EXISTS `iep_ratio_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iep_ratio_letters` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `district_id` bigint(20) DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `medicaid_eligible_students` int(11) DEFAULT NULL,
  `medicaid_eligible_percentage` float DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `stamp` varchar(255) DEFAULT NULL,
  `full_text` text,
  `district_name` varchar(255) DEFAULT NULL,
  `school_year` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `initial_student_count` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `not_valid` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_iep_ratio_letters_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iep_ratio_letters`
--

LOCK TABLES `iep_ratio_letters` WRITE;
/*!40000 ALTER TABLE `iep_ratio_letters` DISABLE KEYS */;
/*!40000 ALTER TABLE `iep_ratio_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iep_ratio_mhd_files`
--

DROP TABLE IF EXISTS `iep_ratio_mhd_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iep_ratio_mhd_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `district_id` bigint(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_iep_ratio_mhd_files_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iep_ratio_mhd_files`
--

LOCK TABLES `iep_ratio_mhd_files` WRITE;
/*!40000 ALTER TABLE `iep_ratio_mhd_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `iep_ratio_mhd_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ieps`
--

DROP TABLE IF EXISTS `ieps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ieps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `iep_content_type` varchar(255) DEFAULT NULL,
  `iep_file_name` varchar(255) DEFAULT NULL,
  `iep_file_size` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `effective_on` date DEFAULT NULL,
  `includes_transportation` tinyint(1) NOT NULL DEFAULT '0',
  `transportation_trips` int(11) NOT NULL DEFAULT '0',
  `transportation_purpose` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_ieps_on_student_id_and_effective_on` (`student_id`,`effective_on`),
  KEY `index_ieps_on_effective_on` (`effective_on`),
  KEY `index_ieps_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ieps`
--

LOCK TABLES `ieps` WRITE;
/*!40000 ALTER TABLE `ieps` DISABLE KEYS */;
/*!40000 ALTER TABLE `ieps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `insurers`
--

DROP TABLE IF EXISTS `insurers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `insurers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(9) DEFAULT NULL,
  `contact_info` text,
  `filing_code` varchar(2) DEFAULT '11',
  `primary_identifier` varchar(255) DEFAULT NULL,
  `npi` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `insurers`
--

LOCK TABLES `insurers` WRITE;
/*!40000 ALTER TABLE `insurers` DISABLE KEYS */;
/*!40000 ALTER TABLE `insurers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `knex_migrations`
--

DROP TABLE IF EXISTS `knex_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `knex_migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  `migration_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `knex_migrations`
--

LOCK TABLES `knex_migrations` WRITE;
/*!40000 ALTER TABLE `knex_migrations` DISABLE KEYS */;
INSERT INTO `knex_migrations` VALUES (1,'20200722115725_parent-role.js',1,'2020-09-18 07:43:02'),(2,'20200722180848_parent-student-relationship.js',1,'2020-09-18 07:43:02'),(3,'20200722191152_tt-therapist-role.js',1,'2020-09-18 07:43:02'),(4,'20200723173546_service-request.js',1,'2020-09-18 07:43:02'),(5,'20200725114522_add-sendbird-to-providers.js',1,'2020-09-18 07:43:02'),(6,'20200727105011_create_provider_assignments_table.js',1,'2020-09-18 07:43:03'),(7,'20200729160920_add-states.js',1,'2020-09-18 07:43:03'),(8,'20200731100731_district-level-of-service.js',1,'2020-09-18 07:43:03'),(9,'20200806151143_my-attachments.js',1,'2020-09-18 07:43:03'),(10,'20200807142559_calendar-faq.js',1,'2020-09-18 07:43:03');
/*!40000 ALTER TABLE `knex_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `knex_migrations_lock`
--

DROP TABLE IF EXISTS `knex_migrations_lock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `knex_migrations_lock` (
  `index` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_locked` int(11) DEFAULT NULL,
  PRIMARY KEY (`index`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `knex_migrations_lock`
--

LOCK TABLES `knex_migrations_lock` WRITE;
/*!40000 ALTER TABLE `knex_migrations_lock` DISABLE KEYS */;
INSERT INTO `knex_migrations_lock` VALUES (1,0);
/*!40000 ALTER TABLE `knex_migrations_lock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_attendance_record_statuses`
--

DROP TABLE IF EXISTS `lte_attendance_record_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_attendance_record_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lte_attendance_record_id` int(11) DEFAULT NULL,
  `lte_attendance_status_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_attendance_record_status_and_record` (`lte_attendance_record_id`,`lte_attendance_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_attendance_record_statuses`
--

LOCK TABLES `lte_attendance_record_statuses` WRITE;
/*!40000 ALTER TABLE `lte_attendance_record_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_attendance_record_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_attendance_records`
--

DROP TABLE IF EXISTS `lte_attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_attendance_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  `days_attended` decimal(8,4) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT '0.00',
  `paid` tinyint(1) DEFAULT '0',
  `lte_invoice_id` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `form_data` varchar(4000) DEFAULT NULL,
  `form_generated_at` datetime DEFAULT NULL,
  `form_submitted_at` datetime DEFAULT NULL,
  `form_notified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `school_year` int(11) DEFAULT NULL,
  `not_billable` tinyint(1) DEFAULT '0',
  `time_attended` decimal(8,2) DEFAULT NULL,
  `time_attended_unit` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `not_billable_reason` varchar(255) DEFAULT NULL,
  `form_requester_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_attendance_records_on_district_id` (`district_id`),
  KEY `index_lte_attendance_records_on_lte_invoice_id` (`lte_invoice_id`),
  KEY `index_lte_attendance_records_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_attendance_records`
--

LOCK TABLES `lte_attendance_records` WRITE;
/*!40000 ALTER TABLE `lte_attendance_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_attendance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_attendance_statuses`
--

DROP TABLE IF EXISTS `lte_attendance_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_attendance_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `position` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_attendance_statuses`
--

LOCK TABLES `lte_attendance_statuses` WRITE;
/*!40000 ALTER TABLE `lte_attendance_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_attendance_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_district_additions`
--

DROP TABLE IF EXISTS `lte_district_additions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_district_additions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `school_year` int(11) DEFAULT NULL,
  `rate` decimal(8,2) DEFAULT '0.00',
  `days_in_session` int(11) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `day_length` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_district_additions`
--

LOCK TABLES `lte_district_additions` WRITE;
/*!40000 ALTER TABLE `lte_district_additions` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_district_additions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_district_cover_letters`
--

DROP TABLE IF EXISTS `lte_district_cover_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_district_cover_letters` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `district_id` bigint(20) DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_district_cover_letters_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_district_cover_letters`
--

LOCK TABLES `lte_district_cover_letters` WRITE;
/*!40000 ALTER TABLE `lte_district_cover_letters` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_district_cover_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_domiciles`
--

DROP TABLE IF EXISTS `lte_domiciles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_domiciles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lte_attendance_record_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `parent` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  `billed_days` decimal(8,4) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `display_on_summary` tinyint(1) DEFAULT '0',
  `billed_time` decimal(8,2) DEFAULT NULL,
  `billed_time_unit` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `paid` tinyint(1) DEFAULT '0',
  `paid_on` date DEFAULT NULL,
  `lte_invoice_id` bigint(20) DEFAULT NULL,
  `other_parent_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_domiciles_on_district_id` (`district_id`),
  KEY `index_lte_domiciles_on_lte_attendance_record_id` (`lte_attendance_record_id`),
  KEY `index_lte_domiciles_on_lte_invoice_id` (`lte_invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_domiciles`
--

LOCK TABLES `lte_domiciles` WRITE;
/*!40000 ALTER TABLE `lte_domiciles` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_domiciles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_external_invoices`
--

DROP TABLE IF EXISTS `lte_external_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_external_invoices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `domicile_district_id` bigint(20) DEFAULT NULL,
  `district_id` bigint(20) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `msba_date` date DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `invoice_amount` decimal(8,2) DEFAULT '0.00',
  `agency` varchar(255) DEFAULT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  `legal_status` varchar(255) DEFAULT NULL,
  `changes_in_legal_status` varchar(255) DEFAULT NULL,
  `responsible_person_1` varchar(255) DEFAULT NULL,
  `responsible_person_2` varchar(255) DEFAULT NULL,
  `responsible_person_3` varchar(255) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `agency_contacted` varchar(255) DEFAULT NULL,
  `individual_contacted` varchar(255) DEFAULT NULL,
  `date_contacted` date DEFAULT NULL,
  `contact_comments` varchar(5000) DEFAULT NULL,
  `confirm_domicile` tinyint(4) DEFAULT NULL,
  `confirm_domicile_district_id` bigint(20) DEFAULT NULL,
  `confirm_invoice` tinyint(4) DEFAULT NULL,
  `confirm_invoice_amount` decimal(8,2) DEFAULT '0.00',
  `amount_saved` decimal(8,2) DEFAULT '0.00',
  `comments` varchar(5000) DEFAULT NULL,
  `domicile_notified` date DEFAULT NULL,
  `educating_notified` date DEFAULT NULL,
  `domicile_notified_person` varchar(255) DEFAULT NULL,
  `educating_notified_person` varchar(255) DEFAULT NULL,
  `recommendation` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `completed_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_external_invoices_on_confirm_domicile_district_id` (`confirm_domicile_district_id`),
  KEY `index_lte_external_invoices_on_district_id` (`district_id`),
  KEY `index_lte_external_invoices_on_domicile_district_id` (`domicile_district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_external_invoices`
--

LOCK TABLES `lte_external_invoices` WRITE;
/*!40000 ALTER TABLE `lte_external_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_external_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_invoices`
--

DROP TABLE IF EXISTS `lte_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT '0.00',
  `paid_amount` decimal(8,2) DEFAULT '0.00',
  `notes` varchar(2000) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `domicile_district_id` int(11) DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_invoices_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_invoices`
--

LOCK TABLES `lte_invoices` WRITE;
/*!40000 ALTER TABLE `lte_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_placement_letters`
--

DROP TABLE IF EXISTS `lte_placement_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_placement_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `letter_file_name` varchar(255) DEFAULT NULL,
  `letter_content_type` varchar(255) DEFAULT NULL,
  `letter_file_size` int(11) DEFAULT NULL,
  `letter_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_placement_letters_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_placement_letters`
--

LOCK TABLES `lte_placement_letters` WRITE;
/*!40000 ALTER TABLE `lte_placement_letters` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_placement_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lte_student_data`
--

DROP TABLE IF EXISTS `lte_student_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lte_student_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `county_of_jurisdiction` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `case_manager` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `placing_agency` varchar(255) DEFAULT NULL,
  `iep` tinyint(4) DEFAULT NULL,
  `building` varchar(255) DEFAULT NULL,
  `grade_level` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_lte_student_data_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lte_student_data`
--

LOCK TABLES `lte_student_data` WRITE;
/*!40000 ALTER TABLE `lte_student_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `lte_student_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailing_clicks`
--

DROP TABLE IF EXISTS `mailing_clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_clicks` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mailing_recipient_id` bigint(20) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_mailing_clicks_on_mailing_recipient_id` (`mailing_recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailing_clicks`
--

LOCK TABLES `mailing_clicks` WRITE;
/*!40000 ALTER TABLE `mailing_clicks` DISABLE KEYS */;
/*!40000 ALTER TABLE `mailing_clicks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailing_files`
--

DROP TABLE IF EXISTS `mailing_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mailing_id` bigint(20) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_size` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_mailing_files_on_mailing_id` (`mailing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailing_files`
--

LOCK TABLES `mailing_files` WRITE;
/*!40000 ALTER TABLE `mailing_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `mailing_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailing_groups`
--

DROP TABLE IF EXISTS `mailing_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_groups` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `district_ids` text,
  `role_ids` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailing_groups`
--

LOCK TABLES `mailing_groups` WRITE;
/*!40000 ALTER TABLE `mailing_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `mailing_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailing_groups_providers`
--

DROP TABLE IF EXISTS `mailing_groups_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_groups_providers` (
  `mailing_group_id` bigint(20) NOT NULL,
  `provider_id` bigint(20) NOT NULL,
  KEY `provider_mailing_group` (`mailing_group_id`,`provider_id`),
  KEY `mailing_group_provider` (`provider_id`,`mailing_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailing_groups_providers`
--

LOCK TABLES `mailing_groups_providers` WRITE;
/*!40000 ALTER TABLE `mailing_groups_providers` DISABLE KEYS */;
/*!40000 ALTER TABLE `mailing_groups_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailing_recipients`
--

DROP TABLE IF EXISTS `mailing_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailing_recipients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mailing_id` bigint(20) DEFAULT NULL,
  `provider_id` bigint(20) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_mailing_recipients_on_mailing_id` (`mailing_id`),
  KEY `index_mailing_recipients_on_provider_id` (`provider_id`),
  KEY `index_mailing_recipients_on_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailing_recipients`
--

LOCK TABLES `mailing_recipients` WRITE;
/*!40000 ALTER TABLE `mailing_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `mailing_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mailings`
--

DROP TABLE IF EXISTS `mailings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailings` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) DEFAULT NULL,
  `body` longtext,
  `send_at` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `district_ids` text,
  `role_ids` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `from` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mailings`
--

LOCK TABLES `mailings` WRITE;
/*!40000 ALTER TABLE `mailings` DISABLE KEYS */;
/*!40000 ALTER TABLE `mailings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_participants`
--

DROP TABLE IF EXISTS `meeting_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `meeting_id` int(11) DEFAULT NULL,
  `owner` tinyint(1) DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_meeting_participants_on_meeting_id` (`meeting_id`),
  KEY `index_meeting_participants_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_participants`
--

LOCK TABLES `meeting_participants` WRITE;
/*!40000 ALTER TABLE `meeting_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meetings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zoomus_id` bigint(20) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `reason` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `error` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `zoomus_user_id` varchar(255) DEFAULT NULL,
  `minutes_saved` int(11) DEFAULT NULL,
  `miles_travel_saved` int(11) DEFAULT NULL,
  `option_use_pmi` tinyint(1) DEFAULT '0',
  `repeat` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mev_files`
--

DROP TABLE IF EXISTS `mev_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mev_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mev_files`
--

LOCK TABLES `mev_files` WRITE;
/*!40000 ALTER TABLE `mev_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `mev_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mev_letters`
--

DROP TABLE IF EXISTS `mev_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mev_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `medicaid_eligible_students` int(11) DEFAULT NULL,
  `medicaid_eligible_percentage` float DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `stamp` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `full_text` text,
  `district_name` varchar(255) DEFAULT NULL,
  `school_year` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `initial_student_count` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `not_valid` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mev_letters`
--

LOCK TABLES `mev_letters` WRITE;
/*!40000 ALTER TABLE `mev_letters` DISABLE KEYS */;
/*!40000 ALTER TABLE `mev_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missing_son_appointments`
--

DROP TABLE IF EXISTS `missing_son_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `missing_son_appointments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `student_appointment_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_missing_son_appointments_on_provider_id` (`provider_id`),
  KEY `index_missing_son_appointments_on_student_appointment_id` (`student_appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missing_son_appointments`
--

LOCK TABLES `missing_son_appointments` WRITE;
/*!40000 ALTER TABLE `missing_son_appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `missing_son_appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_items`
--

DROP TABLE IF EXISTS `news_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` text,
  `title` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `show_at` datetime DEFAULT NULL,
  `hide_at` datetime DEFAULT NULL,
  `category` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_news_items_on_category` (`category`),
  KEY `index_news_items_on_created_at` (`created_at`),
  KEY `index_news_items_on_show_at_and_hide_at` (`show_at`,`hide_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_items`
--

LOCK TABLES `news_items` WRITE;
/*!40000 ALTER TABLE `news_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `news_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `notifiable_id` int(11) DEFAULT NULL,
  `notifiable_type` varchar(255) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `notification_type` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `read` tinyint(1) DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `deliver_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_faqs`
--

DROP TABLE IF EXISTS `page_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) DEFAULT NULL,
  `faq_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_page_faqs_on_faq_id` (`faq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_faqs`
--

LOCK TABLES `page_faqs` WRITE;
/*!40000 ALTER TABLE `page_faqs` DISABLE KEYS */;
INSERT INTO `page_faqs` VALUES (1,'HOME',1,'2010-08-02 21:24:37','2010-08-02 21:24:37'),(2,'HOME',2,'2010-08-02 21:24:37','2010-08-02 21:24:37'),(3,'HOME',3,'2010-08-02 21:24:37','2010-08-02 21:24:37'),(4,'HOME',4,'2010-08-02 21:24:37','2010-08-02 21:24:37'),(6,'CASELOAD',6,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(7,'CASELOAD',7,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(8,'CASELOAD',8,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(9,'CASELOAD',9,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(10,'CASELOAD',10,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(11,'CASELOAD',11,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(12,'CASELOAD',12,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(13,'ADD_STUDENT',13,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(14,'ADD_STUDENT',14,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(15,'GOALS',15,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(16,'GOALS',16,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(17,'GOALS',17,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(18,'GOALS',18,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(19,'GOALS',19,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(20,'GOALS',20,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(21,'ADD_GOAL',21,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(22,'ADD_GOAL',22,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(23,'ADD_GOAL',23,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(24,'CALENDAR',24,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(25,'CALENDAR',25,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(26,'CALENDAR',26,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(27,'CALENDAR',27,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(28,'CALENDAR',28,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(29,'CALENDAR',29,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(30,'CALENDAR',30,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(31,'SCHEDULE_SERVICE',31,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(32,'SCHEDULE_SERVICE',32,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(33,'SCHEDULE_SERVICE',33,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(34,'SCHEDULE_SERVICE',34,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(35,'EDIT_SERVICE',35,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(36,'DOCUMENT_SERVICE',36,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(37,'DOCUMENT_SERVICE',37,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(38,'DOCUMENT_SERVICE',38,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(39,'DOCUMENT_SERVICE',39,'2010-08-02 21:24:38','2010-08-02 21:24:38'),(40,'DOCUMENT_SERVICE',40,'2010-08-02 21:24:38','2010-08-02 21:24:38');
/*!40000 ALTER TABLE `page_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parent_student_relationship`
--

DROP TABLE IF EXISTS `parent_student_relationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parent_student_relationship` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_student_relationship_parent_id_foreign` (`parent_id`),
  KEY `parent_student_relationship_student_id_foreign` (`student_id`),
  CONSTRAINT `parent_student_relationship_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `providers` (`id`),
  CONSTRAINT `parent_student_relationship_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parent_student_relationship`
--

LOCK TABLES `parent_student_relationship` WRITE;
/*!40000 ALTER TABLE `parent_student_relationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `parent_student_relationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parental_consent_forms`
--

DROP TABLE IF EXISTS `parental_consent_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parental_consent_forms` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parental_consent_id` bigint(20) DEFAULT NULL,
  `parental_consent_template_id` bigint(20) DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `start_on` date DEFAULT NULL,
  `signed_on` date DEFAULT NULL,
  `parent_name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_parental_consent_forms_on_parental_consent_id` (`parental_consent_id`),
  KEY `index_parental_consent_forms_on_parental_consent_template_id` (`parental_consent_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parental_consent_forms`
--

LOCK TABLES `parental_consent_forms` WRITE;
/*!40000 ALTER TABLE `parental_consent_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `parental_consent_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parental_consent_templates`
--

DROP TABLE IF EXISTS `parental_consent_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parental_consent_templates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parental_consent_templates`
--

LOCK TABLES `parental_consent_templates` WRITE;
/*!40000 ALTER TABLE `parental_consent_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `parental_consent_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parental_consents`
--

DROP TABLE IF EXISTS `parental_consents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parental_consents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `granted` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `type` varchar(15) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `denied_on` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_parental_consents_on_student_id` (`student_id`),
  KEY `index_parental_consents_on_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parental_consents`
--

LOCK TABLES `parental_consents` WRITE;
/*!40000 ALTER TABLE `parental_consents` DISABLE KEYS */;
/*!40000 ALTER TABLE `parental_consents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_adjustment_reasons`
--

DROP TABLE IF EXISTS `payment_adjustment_reasons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_adjustment_reasons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monetary_amount` decimal(10,2) DEFAULT NULL,
  `reasonable_type` varchar(255) DEFAULT NULL,
  `reasonable_id` int(11) DEFAULT NULL,
  `adjustment_reason_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_payment_adjustment_reasons_on_adjustment_reason_id` (`adjustment_reason_id`),
  KEY `payment_reason_idx` (`reasonable_type`,`reasonable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_adjustment_reasons`
--

LOCK TABLES `payment_adjustment_reasons` WRITE;
/*!40000 ALTER TABLE `payment_adjustment_reasons` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_adjustment_reasons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_adjustment_remarks`
--

DROP TABLE IF EXISTS `payment_adjustment_remarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_adjustment_remarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remarkable_type` varchar(255) DEFAULT NULL,
  `remarkable_id` int(11) DEFAULT NULL,
  `adjustment_remark_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_payment_adjustment_remarks_on_adjustment_remark_id` (`adjustment_remark_id`),
  KEY `payment_remark_idx` (`remarkable_type`,`remarkable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_adjustment_remarks`
--

LOCK TABLES `payment_adjustment_remarks` WRITE;
/*!40000 ALTER TABLE `payment_adjustment_remarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_adjustment_remarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_updated_at` datetime DEFAULT NULL,
  `effective_on` date DEFAULT NULL,
  `reeval_date` date DEFAULT NULL,
  `includes_transportation` tinyint(1) DEFAULT '0',
  `transportation_trips` int(11) DEFAULT '0',
  `transportation_purpose` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `completion_on` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_plans_on_effective_on_and_completion_on_and_type` (`effective_on`,`completion_on`,`type`),
  KEY `index_plans_on_effective_on_and_completion_on` (`effective_on`,`completion_on`),
  KEY `index_plans_on_student_id_and_type_and_effective_on` (`student_id`,`type`,`effective_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppr_verification_files`
--

DROP TABLE IF EXISTS `ppr_verification_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppr_verification_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_cost_id` int(11) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_ppr_verification_files_on_sdac_cost_id` (`sdac_cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppr_verification_files`
--

LOCK TABLES `ppr_verification_files` WRITE;
/*!40000 ALTER TABLE `ppr_verification_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `ppr_verification_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescription_invoices`
--

DROP TABLE IF EXISTS `prescription_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prescription_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `script_count` int(11) DEFAULT NULL,
  `script_price` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_prescription_invoices_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescription_invoices`
--

LOCK TABLES `prescription_invoices` WRITE;
/*!40000 ALTER TABLE `prescription_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescription_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prescriptions`
--

DROP TABLE IF EXISTS `prescriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `granted` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `script_request_id` int(11) DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `plan_id` bigint(20) DEFAULT NULL,
  `provider_id` bigint(20) DEFAULT NULL,
  `prescription_id` bigint(20) DEFAULT NULL,
  `therapy_type` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `therapy_types` varchar(255) DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `ot_valid_until` date DEFAULT NULL,
  `st_valid_until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_prescriptions_on_ot_valid_until` (`ot_valid_until`),
  KEY `index_prescriptions_on_plan_id` (`plan_id`),
  KEY `index_prescriptions_on_prescription_id` (`prescription_id`),
  KEY `index_prescriptions_on_provider_id` (`provider_id`),
  KEY `index_prescriptions_on_st_valid_until` (`st_valid_until`),
  KEY `index_prescriptions_on_student_id` (`student_id`),
  KEY `index_prescriptions_on_valid_until` (`valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prescriptions`
--

LOCK TABLES `prescriptions` WRITE;
/*!40000 ALTER TABLE `prescriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `prescriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `processed_edi_files`
--

DROP TABLE IF EXISTS `processed_edi_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `processed_edi_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `processing_start` datetime DEFAULT NULL,
  `processing_stop` datetime DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `remote_modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_processed_edi_files_on_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processed_edi_files`
--

LOCK TABLES `processed_edi_files` WRITE;
/*!40000 ALTER TABLE `processed_edi_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `processed_edi_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_assignments`
--

DROP TABLE IF EXISTS `provider_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_assignments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assignment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `provider_assignments_provider_id_foreign` (`provider_id`),
  KEY `provider_assignments_student_id_foreign` (`student_id`),
  CONSTRAINT `provider_assignments_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`),
  CONSTRAINT `provider_assignments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_assignments`
--

LOCK TABLES `provider_assignments` WRITE;
/*!40000 ALTER TABLE `provider_assignments` DISABLE KEYS */;
INSERT INTO `provider_assignments` VALUES (1,3,245,'EVALUATION','2020-10-06 15:14:23','2020-10-06 15:14:23');
/*!40000 ALTER TABLE `provider_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_district_memberships`
--

DROP TABLE IF EXISTS `provider_district_memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_district_memberships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `alt_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `hourly_rate` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_provider_district_memberships_on_district_id` (`district_id`),
  KEY `district_membership_join_idx` (`provider_id`,`district_id`),
  KEY `index_provider_district_memberships_on_provider_id` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_district_memberships`
--

LOCK TABLES `provider_district_memberships` WRITE;
/*!40000 ALTER TABLE `provider_district_memberships` DISABLE KEYS */;
INSERT INTO `provider_district_memberships` VALUES (1,1,1,1,'','2020-06-18 10:43:21','2020-10-29 12:16:32',NULL),(2,2,1,1,'','0000-00-00 00:00:00','2020-10-15 19:30:54',NULL),(3,3,1,1,'','2020-10-09 17:39:37','2020-10-09 17:39:37',NULL),(4,4,1,1,'','2020-10-15 19:22:59','2020-10-15 19:22:59',NULL);
/*!40000 ALTER TABLE `provider_district_memberships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_files`
--

DROP TABLE IF EXISTS `provider_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_file_name` varchar(255) DEFAULT NULL,
  `folder_name` varchar(255) DEFAULT NULL,
  `ancestry` varchar(255) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` varchar(255) DEFAULT NULL,
  `imageable_type` varchar(255) DEFAULT NULL,
  `imageable_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `protected` tinyint(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tmp` tinyint(1) DEFAULT '0',
  `shared` tinyint(1) DEFAULT '0',
  `info` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_provider_files_on_ancestry` (`ancestry`),
  KEY `index_provider_files_on_provider_id` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_files`
--

LOCK TABLES `provider_files` WRITE;
/*!40000 ALTER TABLE `provider_files` DISABLE KEYS */;
INSERT INTO `provider_files` VALUES (1,NULL,'/',NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03',1,NULL,0,0,NULL),(2,NULL,'Student Appointments','1',NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03',1,NULL,0,0,NULL),(3,NULL,'Calendar Activity','1',NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03',1,NULL,0,0,NULL),(4,NULL,'Other','1',NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03',1,NULL,0,0,NULL);
/*!40000 ALTER TABLE `provider_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_merge_records`
--

DROP TABLE IF EXISTS `provider_merge_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_merge_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mergeable_id` int(11) DEFAULT NULL,
  `mergeable_type` varchar(255) DEFAULT NULL,
  `provider_merger_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_merge_records`
--

LOCK TABLES `provider_merge_records` WRITE;
/*!40000 ALTER TABLE `provider_merge_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider_merge_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_mergers`
--

DROP TABLE IF EXISTS `provider_mergers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_mergers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `into_provider_id` int(11) DEFAULT NULL,
  `from_provider_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_mergers`
--

LOCK TABLES `provider_mergers` WRITE;
/*!40000 ALTER TABLE `provider_mergers` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider_mergers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_off_days`
--

DROP TABLE IF EXISTS `provider_off_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_off_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `valid_flag` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `off_date` date DEFAULT NULL,
  `off_day_able_type` varchar(255) DEFAULT NULL,
  `off_day_able_id` int(11) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `full_day` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_provider_off_days_on_off_date` (`off_date`),
  KEY `index_provider_off_days_on_off_day_able_id` (`off_day_able_id`),
  KEY `index_provider_off_days_on_off_day_able_type_and_off_day_able_id` (`off_day_able_type`,`off_day_able_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_off_days`
--

LOCK TABLES `provider_off_days` WRITE;
/*!40000 ALTER TABLE `provider_off_days` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider_off_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_pins`
--

DROP TABLE IF EXISTS `provider_pins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_pins` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `provider_id` bigint(20) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_provider_pins_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_pins`
--

LOCK TABLES `provider_pins` WRITE;
/*!40000 ALTER TABLE `provider_pins` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider_pins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_schedules`
--

DROP TABLE IF EXISTS `provider_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provider_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time_legacy` time DEFAULT NULL,
  `end_time_legacy` time DEFAULT NULL,
  `valid_flag` tinyint(1) DEFAULT NULL,
  `monday_included_flag` tinyint(1) DEFAULT NULL,
  `tuesday_included_flag` tinyint(1) DEFAULT NULL,
  `wednesday_included_flag` tinyint(1) DEFAULT NULL,
  `thursday_included_flag` tinyint(1) DEFAULT NULL,
  `friday_included_flag` tinyint(1) DEFAULT NULL,
  `saturday_included_flag` tinyint(1) DEFAULT NULL,
  `sunday_included_flag` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `schedulable_id` int(11) DEFAULT NULL,
  `schedulable_type` varchar(255) DEFAULT NULL,
  `sdac_provider_roster_file_id` int(11) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_provider_schedules_on_schedulable_id` (`schedulable_id`),
  KEY `index_provider_schedules_on_schedulable_type_and_schedulable_id` (`schedulable_type`,`schedulable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_schedules`
--

LOCK TABLES `provider_schedules` WRITE;
/*!40000 ALTER TABLE `provider_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `providers`
--

DROP TABLE IF EXISTS `providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `home_phone` varchar(255) DEFAULT NULL,
  `mobile_phone` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `service_phone` varchar(255) DEFAULT NULL,
  `alt_id` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `time_zone` varchar(255) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT NULL,
  `npi` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `iphone_option` tinyint(1) DEFAULT NULL,
  `is_medicaid_provider` tinyint(1) DEFAULT NULL,
  `cert_date` date DEFAULT NULL,
  `cert_expire_date` date DEFAULT NULL,
  `ssn` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `state_license_number` varchar(255) DEFAULT NULL,
  `middle_initial` varchar(255) DEFAULT NULL,
  `mo_medicaid_number` varchar(255) DEFAULT NULL,
  `disable_auto_logout` tinyint(1) DEFAULT NULL,
  `therapist_group_id` int(11) DEFAULT NULL,
  `eligible_students_at_last_login` varchar(5120) NOT NULL DEFAULT '',
  `is_sdac_coordinator` tinyint(1) NOT NULL DEFAULT '0',
  `termination_date` date DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `sdac_rms_email_notice` tinyint(1) DEFAULT NULL,
  `sdac_rms_sms_notice` tinyint(1) DEFAULT NULL,
  `sdac_position_id` int(11) DEFAULT NULL,
  `sdac_job_title` varchar(255) DEFAULT NULL,
  `sdac_trained_timestamp` datetime DEFAULT NULL,
  `sdac_pin` int(11) DEFAULT NULL,
  `sdac_position_description` varchar(255) DEFAULT NULL,
  `sdac_participant` tinyint(1) DEFAULT '0',
  `medicaid_provider_start_date` date DEFAULT NULL,
  `hourly_rate` decimal(12,2) DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `_config` text,
  `school_name` varchar(255) DEFAULT NULL,
  `telemedicine` tinyint(1) DEFAULT '0',
  `telemedicine_trained_at` datetime DEFAULT NULL,
  `reset_password_token` varchar(255) DEFAULT NULL,
  `reset_password_sent_at` datetime DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `taxonomy_code` varchar(255) DEFAULT NULL,
  `reply_to` varchar(255) DEFAULT NULL,
  `provider_group` varchar(255) DEFAULT NULL,
  `medicaid_provider_expiration_date` date DEFAULT NULL,
  `receive_lte_notifications` tinyint(1) DEFAULT '0',
  `maiden_name` varchar(255) DEFAULT NULL,
  `managed_care_plans` varchar(255) DEFAULT NULL,
  `rtp_staff` tinyint(1) DEFAULT '0',
  `zoomus_id` varchar(255) DEFAULT NULL,
  `zoomus_pmi` bigint(20) DEFAULT NULL,
  `zoomus_password` varchar(255) DEFAULT NULL,
  `zoomus_pmi_link` varchar(255) DEFAULT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `refresh_token` varchar(2500) DEFAULT NULL,
  `sendbird` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_index` (`email`,`password`),
  KEY `index_providers_on_alt_id` (`alt_id`),
  KEY `index_providers_on_school_id` (`school_id`),
  KEY `index_providers_on_sdac_position_id` (`sdac_position_id`),
  KEY `index_providers_on_uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `providers`
--

LOCK TABLES `providers` WRITE;
/*!40000 ALTER TABLE `providers` DISABLE KEYS */;
INSERT INTO `providers` VALUES (1,'Test','User','ACTIVE','test@example.com','bf4c6e990000878858ea2394b2c164d96c2f6220','','','','Columbia','MO','65201','','','2008-07-30 00:48:14','2020-11-04 12:30:02','Central Time (US & Canada)',1,'','ADMIN',1,1,NULL,NULL,NULL,NULL,NULL,'','C','',0,NULL,'',1,NULL,NULL,0,0,8,'','2011-02-01 08:00:00',1316072,NULL,1,NULL,NULL,'2020-11-04 19:35:27','2020-11-04 12:30:02','{\"shown_announcements\":[\"blog_post\",\"eligibility\",\"remittance\"]}',NULL,0,NULL,NULL,NULL,NULL,'','','',NULL,0,'',NULL,1,NULL,NULL,NULL,NULL,'6268b6ce-642a-49e5-ab11-0a0af7426333','eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.LsoIuL4pCozxoDORGxzp29UvaIIDvY_kSFR5xaNFpkUwcUHELD4yrIqcJOxkKbI0e8iLqhD2jsCyJR_B49fltjPhsJJV6aCqDLaVF8EmDsjiTF-lXOF-_kBbLy7QABf_3YXrw7b10ed1rFkLB9GNgPl7ezLsHF55SMyssSTSY06USK4N6j7UTKoP5i4bPgFetokgvZsxkfJSJAUvtksroLQbn-kjpXolSWQJK90xZm4ikHZqpUm9ddLpBa5KdRbINEG6WVuA56nuIsUhQFVjEKBvTwfMdRPjvDO3odsLMl22QekuScElUp33WdecBgYmU4au0zt_tIvjDmKbtqzjZw.Kd51gINZfzRPsGDp.Wj8-cDCdWy2lqhwusLWjuppB9_i27Xso4ZdHJj2O70O24AKdRUabae-tukvn3ft8b26z-fif5-RF-LzGBvgL8MdpJzayEA4CANqaxJqCYypGsFjHfh9yLlBoyDm5lSSJWm5Vj1bjp2mzaI0Ed4Km8cIrpUALfyZtYE-MRZ1yfaEQcjVTJ9LMu27pbwDxEj62U6nODW--VY4Cxf1gQNIvtQ0F4te98Pc8X283w_DhQWvHbXuA7JJCjLyNtYUE32Tq3fmdIGG8Sc5kqW6vs3fE5lPvvJD_pM5r3GeX_Jo0AfRFQHUghNBAxbLgF-bHZQDLRzoPo-ozRpnLNncGP5K_2Qj1hCgimgntwX7N2LfN5_TIYMIu4Vlu1cKkUpd_vgoNnQ9vAeMPerI--styETaQdUpH0AeHFJPlluXi1fyYBN8zc_adKnMVTk1nWd-jaAGRjmwfLxIEP2VLoQYHNfi6SLqH16n6lGAonlNT6eZg3jjn7bq2KGFV6uON038a6f5KPrXx3hMSav0sUn09eAC1CmEQSylH8qxKIDT_nRdOICjFCLFgB0IiKagYeR8DNYnsQ_WgAwKqTS-TKanT3nh56J8Ws7Qq4rX6KtimLzhNXT_wRMHrZxFwS-df5iAvJgnPTKIkzfn-cd9DC6-2UdqNoCP_lwA4WyhqbNArjKKJtoTxFAzDaj_4RCn4WpQV8EXWc8dA3UOao-mJkaxBnenSgcIXFKIhSW16CtK5YN9vJV_dL4Qi3NpBP4mQka_gzvFyxJLKP-bM_Bvgj7c3VHuU4nKmwbQOGTikj91-cHD41wSlH-Km5PLmlmfFBtSiztkfAtunZBOTbDUw_jkxbzuLVSNYAULDotrtJ-_Lh5HnUVqXMEoqIbX-9KNmOV5VRh66w2WNBQTx3ENhZKmPy_KXuCU1qOtUzq8UwbUVvUH9f2iQV-uy4GlP5lrl6qqeLwsF-jYqRPTWUFL7ek2kb_XDX_vhmzz0MY3HeqcRWfP8Tkik9ArMIC-lis7mE7Djxqw1PtTgV3dN1-f4kot5_UBLtLzFXYRU8_PSSjBxR99pzCaXEOtCDhwJyMaHjHPWdoscNy9U4tiZCc6lGJTGFllbbDyHWYyKP2C3g38ntBLW6FmzzFmGqt_ay82yBjOvD9T4EgGIVz4jgnbuGyZxXQLZXCv9b0pZOF8MNhdrrjWqyHyBCLASZk_Dm9J4jUG3UBb_ejBUeL4c-PJy3SB4JNigVi3BF0mQMo6SWKOi_aEERxUiB5RHQISGlnQ4sP05boIHYioeHvdTOub4abG2ilYYLCy7av42Cq-Ctb9ZlA7lwsNIRaAtbR6_vTwNwg.y_pKtNshyA8D2tI8QydUPg',NULL),(2,'School','Admin','ACTIVE','school.admin@teleteachers.com','bf4c6e990000878858ea2394b2c164d96c2f6220','','','','Columbia','MO','65201','','','2008-07-30 00:48:14','2020-10-21 12:27:28','Central Time (US & Canada)',1,'','ADMIN',1,1,NULL,NULL,NULL,NULL,NULL,'','C','',0,NULL,'',1,NULL,NULL,0,0,8,'','2011-02-01 08:00:00',1316072,NULL,1,NULL,NULL,'2020-10-21 12:27:29','2020-10-21 12:27:28','{\"shown_announcements\":[\"blog_post\",\"eligibility\",\"remittance\"]}',NULL,0,NULL,NULL,NULL,NULL,'','','',NULL,0,'',NULL,0,NULL,NULL,NULL,NULL,'62911637-3245-497b-85af-7825ca48d701','eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.0in612BANzKAw2wc3v_K8u4qhJswZHJnj7ew8qIrQrZsAtGZt_aEvFMzWBfXaAOagbtJqUibHdwqiV50caSza84HKTa3IaipWvnZhRWs3GlpTUIEXdiVe1Q3TAw1vybBC6TOWHDuM2HKDlaUCaN9ZLT28IacpqkjI66KULb4IJXzl1B1ALPOiP3VRFJu-0x9B_bjUIQEM60P8FVv_2DYq0_WNyPEfFFj4rIVXirkLVvJtYOHxIDJ0KLTqPy_I1UY0Xl8zsf9A0O-6IiwXTaC1ERtILw3bW9vcX-yJe6a_xhmrtVufmxmeuA3ieeihycFr0ADNc_9RThxynn96uOxQQ.jyYGR7EStNvvrzHJ.O4xg2ry_p6qyCeOWsjWRCKELxuV7RQVOdBqttqBQy5ppKciW5PRlhU2ZAh9Gilk1GDPdVtzfmWsB4XRg8fN0HGMSAN9Om1pjnwCCu21Yp2BybOjUHiu7c5n3_kJzILEhDdI_5CBV8ytYmLAASIXC8Y9g2ugZ77ZA50FeitEEZRYhJ5OXnKnqsa0KB4yieXXZvqylEisK7sSugmyhMmcydS-Tf1NAkRLviUOPhO8RjasBL2-hr0rC9Yx1xzBr_tluFMpMGrVKCFUk4s2Ee9HnqTmW4z-fG7kCv-8WmxA_y7qg19S5DFd52vQ6cxDEJcCVJWGGzhuN-cUbgLodqbILpa0JDlSFbNZyV3ZTEthuWAjk27EBI-sMi54yOhQni8bYp52Q6QxjnScIia3TgsfFSuA9mc-tKYYuuRHWJ5wsCyGcWO5-jCuEa6J8qfbDc5XWyKWRhHapp7BuQxIvNaxeWAk2RGPVFgt8ST2laQwZsiGDBHfijhX32VFbb0jglsHtHY9ynZ7nF4QwUAbFEB6ijTXUt3tXGVaXrwVyCbdGdaNEfF4agz1hd-r9_g-m4lG9Z5mrxvFKT0a54-8yQ6f-2pmMiHKt6pUvNjX3dSu4Awkhw-ds52P2rBl2ALS_VbsARHgTfjZhvszJ5v-pZSYgIwAIo2raTI-zG_hjxFDOJjeynC8ndWzGPlcIgqjrf5q7a4Rs-7JwM74fb5v-CkgN7O9EQr0AHm4tRYbCWguvR98CXnJSpPW73I0W1rVYLCdk9OIkfG5Vf5mBs37-37t3uSywCWGbROofefOR9yUX6M12ToERjytDoi1T5GrP8DO0wkFKwwO7I61dqVoYECXn2r8_flIuP7nnrknaCUwKk7xgzZnKXiu6PkCuKnDoJzvPYwO87N3WkLnzcWmeo2ROgi8chVelGK7D9fai6_QRK6KRthN3TNm1p3miVmusH9BqKxh4MhVYc816wuKvzizypfb26RuobqSJxgqoFVFDfwJpMSr8tXZuVzpTEOHl_fqlp4OgE5hmU7UWLno0PB-CXDSMgtREK3hL8R_u42e6sb-wuWsaeQimwZsWbcj4UmTlsJYHxJqS3myxGaQb3Zw7JxlkqN1IhI2BUkfFE8APsAigJXnIHnkq5EfheCEcfC2g2KSXOvEh1hAz4Tt3hxLepkb5_N1syXlPIYv5mbiTFDHfQhYm40EuWUv40OaK4hfUAPmzvTpyaBxEcoX77ZSZM3DdgTxQriJHepCD9P6-CQFNGBNQH5j5UPmTbumEn_UiLcGL5nuOa_ulz0l9oTl04nvJOu_mgQowsb_1ZHRQ2TFjsluA4TWbc45_Hg.Xz3olqVZU37tNxOIwcpb_Q',NULL),(3,'Justin','Therapist','ACTIVE','therapist@teleteachers.com','bf4c6e990000878858ea2394b2c164d96c2f6220','','','','Columbia','MO','65201','','','2008-07-30 00:48:14','2020-10-09 17:39:37','Central Time (US & Canada)',1,'','OTHER',1,1,NULL,NULL,NULL,NULL,NULL,'','C','',0,NULL,'',1,NULL,NULL,0,0,8,'','2011-02-01 08:00:00',1316072,NULL,1,NULL,NULL,'2020-09-24 17:16:49','2020-09-24 17:16:48','{\"shown_announcements\":[\"blog_post\",\"eligibility\",\"remittance\"]}',NULL,0,NULL,NULL,NULL,NULL,'','','',NULL,0,'',NULL,0,NULL,NULL,NULL,NULL,'62911637-3245-497b-85af-7825ca48d701','eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.AXsRYw_UF-KPKBFaCr9kBnv2zOR3jg0GP4O3Rf84qvr6gpk0T7xhpYiAyA_HzXA62D8opgd25gEtx_hH9wT1zggnhkapji66Y0zveVdiYeuy9Zj7r8xOzREUjKt1-Funpb5jQlo4QC-19aQqXlDaRM4--5KQjx2VgWDA1r3lJCgSopTfLDVUnl2PKB9T5lnpWpkPVeRrVi7lyisc3WYJ7oHvmmZBXE90YZfn_B20NPax5ULaEALuyowgCz-O5JR5IP2QVLWTIjMfMsdFUGj6A-NRK3uGTncwAhNTW-x-7KfA7epIQm9p8NfX9z4WdtTOzA1ulvJoMsE5WyMdpYa0oQ.EbSomKqjwuiB8FWm.JaAnybw8MCMhIKoFFCxqAyKtmBh-IgLTgCXbg-Sqmtm7uGh-VJMgB-kwC8-lmvwgSFNFbZtZXMTT3PBuk5uzoG-6a1bJJVfWSL9SgQOpEUoI3UYZSNOimN9phWlKt9OtirbRAB9r1iZzvIg8Y-ZWVbjVuCwNbcUM_SkFJ9um0mrlYO9HJjcH7nnsfMfLH1MrsNbLBuCTUJQULyMEff5ZWMic2jxCKW1SHKGsFHaVnRZPT--yCJ7homRpxxTIsjPI8biW4gZdOYEwizPcyszy3wMrskCnufGgjb2Q1kv0OBH9YnoRJBx1H39Qq-uL8TVRwFcGHbIGIrmKsHyTQeuco4FreA6GV_Fe5RbjKIYvK38kyOQdU5S5ZbBrfLDU0QArBw3QOaILnDqK3gELKc3c4ibPjFUCPAf9G95Wu0Heh91m-3MHdWmezD2-tudCW3AfHA3k8C21zmhGy86DPWCatFM6GPEp8WK6-sDoBItYRK3PKe1aBH6dDTR_vkyt4lz6A4o74G2Ad1dUX5rNNXItucd1wXv5t_9JyS9RsRT0iIf8ipmgq4silzABBnLJC_FT3XXcicFuk6icdV9A0ZHPdq0iH8MH-SDzX5OX_ITsqHo38ZFE4f0SmCE1qFqQyQog_O4Kbo-HT0yZhpz7GrAKg-XMb9x7EHlVvbi9kwaFu3WcfITPd8ta5MhMHpFVNQ_o6p1bzTm7V66RuXsOxzGfH0NvoFyaf9lBwD1oqALtXYmExxpDk1ApFTyo_ryAmdzf8YdoRF0zxdszuBWDilhXu32oUxHl8BGtS9X3wXw_FnjBnsyrstAH5QgMj9_JwW7ILC0yiNjWm8-nOXKqqm_CR5h0m4pMfDBoER-dnbo5N0nj7G7aPB4zLJ_26W7AjHpAxilqnLN3R3ymwn8HYeSsV7FPgz-2nduEvA3sCOISJ9PeHBCCMBxQeY3QHcgKb7cA2mzTnKT_kxlM8H7uB-AeH0GN0bO5c8Ij_Ft0FrNbu3wEoegC3Rz4Hx5nYWjI8vZ9wI9oGaJZFChu7y27f-wbNqgXwNGHPLXEgoyf6JJMTZ0QGFiJQA82uJDr9I956va4bT5apIAxCBfWdKwjPPmszuYQMKHt8KnBk3VThZDOSxpUxzOCG5M4YifeTp9cd5qcz5HKUfKZXkgWbg0MGA8TDPrNHJ1oSF9nsdE4lfEdciqZJG2uUPm_wBymvsyoikaW4_XoixtzBLIOZt4nkzNK6MzVfuNjw7XK3Re_ATXc5gRtaXBw91FP3S7Zbkm8wGrCgrBoYW__nNGL0H1iux7Mc9usUvtU9OiDdJRDWIWVeIfpqVasAg1hLlkmFA.PFLCtwrGY-uqflu8mIzXtg',NULL),(4,'Demo','TT Admin','ACTIVE','platform@teleteachers.com','5f4a0a8ce5521f33c46fcec6183dde2265d0f1dc','','','','','','',NULL,NULL,'2020-10-15 19:22:59','2020-10-15 19:29:00','Central Time (US & Canada)',1,'','OTHER',NULL,0,NULL,NULL,NULL,NULL,NULL,'','','',0,NULL,'',0,NULL,NULL,NULL,NULL,NULL,'',NULL,8882652,NULL,0,NULL,NULL,'2020-10-15 19:31:00','2020-10-15 19:29:00','{\"shown_announcements\":[\"blog_post\",\"eligibility\",\"remittance\"]}',NULL,1,'2020-03-01 06:00:00',NULL,NULL,'S-O89_GqtEXj_iJKA06y3t6quB0LfAv1FeUeq46c8klc6gS86VBMvhxKNkDHIfP2rIZ20t_dVarOyyww80YjGQ','','','',NULL,0,'',NULL,1,NULL,NULL,NULL,NULL,'e302ba68-be5d-4b42-b0fd-0192f4636087','eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.UCapcJa9ZzlfgVL9uYwEW1QjDn1blrkTjRFhftroYnsFdfYje1Xrm9uu5kXrkQkafBdo5QhHbWI1cbXbgDwBuOwsKLK4iFB4UcTuHE7kHBinlluN8ZdnkUq5MTiZtQQsXwYQ7sPm--k25kfvFzSPXN_llFapOn8MaGRwkRSP3tlZXeKRllWHiHZEU77VOFjvj30fEjO2H6KGXCUykPkZgbnH4tgeDDodTbSnxr89xN28o-parVyXKp9wpfZxziIJ86-rqq7Ebch2e68Bh6q6IMINjDEPQ2EKy1vd9aLOvI5fdQgryX36m-V2VZ9Ww7OZlSuIfbL2l13MRgb3dWq2Yw.2k2bpyBmoZKIGZN2.x44dsMl4ctUNMzfFNecLy7tGKZjyAKZzatHi8daxIqvXr0JRYHIeweZngM8NcVQbCwpgQW3M7_z2Eox2NeHsr637I2UGx6zFWGgCggYTmuJ7JXaYklqHt3Ax5BFbjLhQXvHDDjJatZjTeLApVKLqEhwKhCHoUXvntrVI069jTTHA5dEX-lxBmpSASLw4VKe6fo9Gr5j4ndGeBiQ5O9iSeie4Qfddxfq__7sFCJth-vjc2VkzXlv2E4aYdGTRjbNSQ0LAVw-gqG3H-e-7ltRVTmmxEfe6ZY9WDuXgnLsttv3pZZrL9N0ywKy56XcVHp_ZuppA4MED5ePGtdnw36zlLezk2yeDQI5yuXh7mVIKR2eSjfgo3S-rSOLfGvD_elWWhHB0rbY1DcqvHIItTynZaNGZdqkc5u3hxUHZ1LhGhbhw3yUkrFfIohnba3CUVhYEALXZb01DTSI1U2aa6iEPxuSwhHbKmOrQTBSZGiI792OsMSmVXYboMGzLIwiAZXUO7_FBrk71JWEZPLltGWb_qs99VU68cE7pRHYEXaoV9GxvWS1W7imFV9Jn0X9GhObJk3pWJgqiMkk9XOxuegFNOEJdwMtusejJT1LEEwCh6fN6-ZG00DVtgAdXGJ5IqBdXJug8Y7ECsGBnyOvT67z-ZmBgTulXOKtnYyRjbt9VJM57fOkUd1CH-jHbyb8xZm0yNvIGM82MWbTTmsjeBZ_OukTFpt1p9Xad11diwN95LlsXWfw0qMcbP6Hm2zeZt-EeUWVAT6UcKP560w5ieEZS6C1NfwE0kP_gP7pSeHulOREvshIzYHdM1w9UVJh67I-BGQdWtmwvwodlq0chzKQiCETeugy3qOFkUwrWEwKkuFiNpL4dJngOjmPzDE6dRAdCm3saGffIeJZv5Aswu8amzqh3plYj7Py6er2xJZHQ1SVXVHNzb128hSI51UKGypfQxssOSL0PoZZItpkMO6pKjnUNNnDvSrFgkRT4J5TM1Rzq2oDcp2Sttz2oa2y2NhaKwmr1OmNOPvhZsh53OWNU38oJbApW2tn3PM2ij6rj1ejSfZTU_VCOs-JrAc9lBzdWNnM4bDPBsNNea5G3mqMLAYHF5UuSF6oE2luGQjOwrODsm7GQLJUZPscHMiKJ_W70E8kOi69bMf3ipfk0IUUVbie_Ir1vQVZSvta5uUtZw90PBNd89Kcy7IvTU4Tb8y-CVWgV69rnvwYPGol6EtmQkjhccLEcFl1D62FttpGoe-E0f94SVrGireD6AMXcNxrot9BFYcN9_waihsEB_mYb8eKi5Jc8OVr0rZ_LYHYe3whMrj75Zy0TukP7WA.E-0SvIDRdICM-ylJxjvdEA',NULL);
/*!40000 ALTER TABLE `providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `providers_schools`
--

DROP TABLE IF EXISTS `providers_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `providers_schools` (
  `provider_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `providers_schools`
--

LOCK TABLES `providers_schools` WRITE;
/*!40000 ALTER TABLE `providers_schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `providers_schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pushwoosh_notifications`
--

DROP TABLE IF EXISTS `pushwoosh_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pushwoosh_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notifiable_id` int(11) DEFAULT NULL,
  `notifiable_type` varchar(255) DEFAULT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pushwoosh_notifications`
--

LOCK TABLES `pushwoosh_notifications` WRITE;
/*!40000 ALTER TABLE `pushwoosh_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `pushwoosh_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remittances`
--

DROP TABLE IF EXISTS `remittances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `remittances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_issue_date` date DEFAULT NULL,
  `check_amount` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `trace_number` varchar(50) DEFAULT NULL,
  `production_date` date DEFAULT NULL,
  `payment_method` varchar(3) DEFAULT NULL,
  `payment_details` varchar(500) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_remittances_on_check_issue_date` (`check_issue_date`),
  KEY `index_remittances_on_district_id` (`district_id`),
  KEY `index_remittances_on_state_id` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remittances`
--

LOCK TABLES `remittances` WRITE;
/*!40000 ALTER TABLE `remittances` DISABLE KEYS */;
/*!40000 ALTER TABLE `remittances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `report_content_type` varchar(255) DEFAULT NULL,
  `report_file_name` varchar(255) DEFAULT NULL,
  `report_file_size` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `modifier` varchar(255) DEFAULT NULL,
  `billable` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'psychologist','2020-06-18 10:18:26','2020-06-18 10:18:26',NULL,0),(2,'provisionally_licensed_psychologist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(3,'school_psychologist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(4,'licensed_clinical_social_worker','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(5,'licensed_master_social_worker','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(6,'master_social_worker','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(7,'counselor','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(8,'provisionally_licensed_counselor','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(9,'psychiatrist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(10,'psychiatric_clinical_nurse_specialist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(11,'psychiatric_mental_health_nurse_practitioner','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(12,'ot','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(13,'pt','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(14,'st','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(15,'nurse','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(16,'personal_care','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(17,'audiologist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(18,'cota','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(19,'pta','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(20,'slp_a','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(21,'sl_implementer','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(22,'group_supervisor','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(23,'user','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(24,'teleconference_user','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(25,'teletherapy_assistant','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(26,'therapist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(27,'ds_admin','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(28,'sdac_participant','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(29,'sdac_coordinator','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(30,'physician','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(31,'insurer','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(32,'wizard','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(33,'ds_specialist','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(34,'root','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(35,'lte_coordinator','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(36,'transportation_user','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(37,'esce_teacher','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(38,'ape_teacher','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(39,'tvi_teacher','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(40,'sped_teacher','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(41,'superintendent_ceo','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(42,'cost_settlement_coord','2020-06-18 10:18:27','2020-06-18 10:18:27',NULL,0),(43,'parent','2020-09-18 07:43:02','2020-09-18 07:43:02',NULL,0),(44,'teleteachers_therapist','2020-09-18 07:43:02','2020-09-18 07:43:02',NULL,0);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rolifications`
--

DROP TABLE IF EXISTS `rolifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rolifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pro_id_on_rolifications` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rolifications`
--

LOCK TABLES `rolifications` WRITE;
/*!40000 ALTER TABLE `rolifications` DISABLE KEYS */;
INSERT INTO `rolifications` VALUES (1,34,1,'2020-06-18 10:21:18','2020-06-18 10:21:18'),(2,27,2,NULL,NULL),(4,32,1,NULL,NULL),(5,39,3,NULL,NULL),(7,32,4,'2020-10-15 19:22:59','2020-10-15 19:22:59'),(8,33,1,'2020-10-29 12:16:31','2020-10-29 12:16:31');
/*!40000 ALTER TABLE `rolifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schema_migrations`
--

DROP TABLE IF EXISTS `schema_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schema_migrations` (
  `version` varchar(255) NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schema_migrations`
--

LOCK TABLES `schema_migrations` WRITE;
/*!40000 ALTER TABLE `schema_migrations` DISABLE KEYS */;
INSERT INTO `schema_migrations` VALUES ('20200603134323'),('20200911120425'),('20200916182528');
/*!40000 ALTER TABLE `schema_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `state_id` varchar(255) DEFAULT NULL,
  `location_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_schools_on_district_id` (`district_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7323 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `script_requests`
--

DROP TABLE IF EXISTS `script_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `script_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `type` varchar(15) NOT NULL,
  `additional_info` text,
  `auto_generated` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_script_requests_on_district_id` (`district_id`),
  KEY `index_script_requests_on_provider_id` (`provider_id`),
  KEY `index_script_requests_on_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `script_requests`
--

LOCK TABLES `script_requests` WRITE;
/*!40000 ALTER TABLE `script_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `script_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_activities`
--

DROP TABLE IF EXISTS `sdac_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `billable` tinyint(1) DEFAULT NULL,
  `defenition` text,
  `cost_pool` varchar(255) DEFAULT NULL,
  `reduce_by_mer` tinyint(1) DEFAULT NULL,
  `reduce_by_ppr` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_activities`
--

LOCK TABLES `sdac_activities` WRITE;
/*!40000 ALTER TABLE `sdac_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_allowed_activities`
--

DROP TABLE IF EXISTS `sdac_allowed_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_allowed_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `sdac_activity_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_sdac_allowed_activities_on_provider_id` (`provider_id`),
  KEY `index_sdac_allowed_activities_on_sdac_activity_id` (`sdac_activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_allowed_activities`
--

LOCK TABLES `sdac_allowed_activities` WRITE;
/*!40000 ALTER TABLE `sdac_allowed_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_allowed_activities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_calendar_validations`
--

DROP TABLE IF EXISTS `sdac_calendar_validations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_calendar_validations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `sent_back_at` datetime DEFAULT NULL,
  `redo_reason` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `district_calendar_index` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_calendar_validations`
--

LOCK TABLES `sdac_calendar_validations` WRITE;
/*!40000 ALTER TABLE `sdac_calendar_validations` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_calendar_validations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_claims`
--

DROP TABLE IF EXISTS `sdac_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_claims` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `salaries` decimal(12,4) DEFAULT NULL,
  `fringe` decimal(12,4) DEFAULT NULL,
  `other_costs` decimal(12,4) DEFAULT NULL,
  `total_costs` decimal(12,4) DEFAULT NULL,
  `mev_rate` decimal(12,4) DEFAULT NULL,
  `student_count` int(11) DEFAULT NULL,
  `eligibility_count` int(11) DEFAULT NULL,
  `nondiscounted_moment_ratio` decimal(12,4) DEFAULT NULL,
  `mer_moment_ratio` decimal(12,4) DEFAULT NULL,
  `mer_claimable_rate` decimal(12,4) DEFAULT NULL,
  `mer_ppr_moment_ratio` decimal(12,4) DEFAULT NULL,
  `ppr` decimal(12,4) DEFAULT NULL,
  `total_claim_rate` decimal(12,4) DEFAULT NULL,
  `claim_at_half` decimal(12,4) DEFAULT NULL,
  `indirect_costs_rate` decimal(12,4) DEFAULT NULL,
  `indirect_claim` decimal(12,4) DEFAULT NULL,
  `total_claim` decimal(12,4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `sdac_random_moments_survey_id` int(11) DEFAULT NULL,
  `moment_count` int(11) DEFAULT NULL,
  `non_admin_moment_count` int(11) DEFAULT NULL,
  `admin_moment_count` int(11) DEFAULT NULL,
  `non_admin_moment_ratio` float DEFAULT NULL,
  `redistributed_moments_sum` int(11) DEFAULT NULL,
  `total_adjusted_moment_count` int(11) DEFAULT NULL,
  `total_moment_rate_excluding_admin` float DEFAULT NULL,
  `rms_data` mediumtext,
  `nondiscounted_cost_pool_data` mediumtext,
  `mer_cost_pool_data` mediumtext,
  `mer_and_ppr_cost_pool_data` mediumtext,
  `non_discounted_salaries` decimal(12,4) DEFAULT NULL,
  `non_discounted_fringe` decimal(12,4) DEFAULT NULL,
  `non_discounted_other` decimal(12,4) DEFAULT NULL,
  `non_discounted_total_costs` decimal(12,4) DEFAULT NULL,
  `discounted_salaries` decimal(12,4) DEFAULT NULL,
  `discounted_fringe` decimal(12,4) DEFAULT NULL,
  `discounted_other` decimal(12,4) DEFAULT NULL,
  `discounted_total_costs` decimal(12,4) DEFAULT NULL,
  `referral_salaries` decimal(12,4) DEFAULT NULL,
  `referral_fringe` decimal(12,4) DEFAULT NULL,
  `referral_other` decimal(12,4) DEFAULT NULL,
  `referral_total_costs` decimal(12,4) DEFAULT NULL,
  `cost_pool_salaries` decimal(12,4) DEFAULT NULL,
  `cost_pool_fringe` decimal(12,4) DEFAULT NULL,
  `cost_pool_other` decimal(12,4) DEFAULT NULL,
  `cost_pool_total_costs` decimal(12,4) DEFAULT NULL,
  `ppr_claimable_eligibility` decimal(12,4) DEFAULT NULL,
  `referral_claimable_percentage` decimal(12,4) DEFAULT NULL,
  `reconciliation_requested_at` datetime DEFAULT NULL,
  `reconciliation_completed_at` datetime DEFAULT NULL,
  `claim_processed_at` datetime DEFAULT NULL,
  `submitted_to_state_at` datetime DEFAULT NULL,
  `refile_flag` tinyint(1) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `refile_reason` text,
  `adjustment_amount` decimal(12,4) DEFAULT NULL,
  `adjustment_reason` text,
  `last_years_salaries` decimal(12,4) DEFAULT NULL,
  `last_years_fringe` decimal(12,4) DEFAULT NULL,
  `salaries_differential` decimal(8,4) DEFAULT NULL,
  `fringe_differential` decimal(8,4) DEFAULT NULL,
  `salaries_justification` text,
  `fringe_justification` text,
  `not_sent_reason` text,
  `revision` int(11) DEFAULT NULL,
  `paid_on` date DEFAULT NULL,
  `paid_amount` decimal(12,4) DEFAULT NULL,
  `payment_error` varchar(255) DEFAULT NULL,
  `total_forms_generated` int(11) DEFAULT NULL,
  `total_non_responses` int(11) DEFAULT NULL,
  `forms_considered_invalid` int(11) DEFAULT NULL,
  `cost_pool_1_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_1_fringe` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_fringe` decimal(12,2) DEFAULT '0.00',
  `cost_pool_1_rms_data` longtext,
  `cost_pool_2_rms_data` longtext,
  PRIMARY KEY (`id`),
  KEY `index_sdac_claims_on_district_id` (`district_id`),
  KEY `claim_survey_idx` (`sdac_random_moments_survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_claims`
--

LOCK TABLES `sdac_claims` WRITE;
/*!40000 ALTER TABLE `sdac_claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_cost_certifications`
--

DROP TABLE IF EXISTS `sdac_cost_certifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_cost_certifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `sdac_cost_id` int(11) DEFAULT NULL,
  `faxed` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `attachment_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_cost_certifications`
--

LOCK TABLES `sdac_cost_certifications` WRITE;
/*!40000 ALTER TABLE `sdac_cost_certifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_cost_certifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_cost_files`
--

DROP TABLE IF EXISTS `sdac_cost_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_cost_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `sdac_cost_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_sdac_cost_files_on_sdac_cost_id` (`sdac_cost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_cost_files`
--

LOCK TABLES `sdac_cost_files` WRITE;
/*!40000 ALTER TABLE `sdac_cost_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_cost_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_cost_forms`
--

DROP TABLE IF EXISTS `sdac_cost_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_cost_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_cost_id` int(11) DEFAULT NULL,
  `total_claimable_salary` decimal(12,4) DEFAULT NULL,
  `total_claimable_fringe` decimal(12,4) DEFAULT NULL,
  `run_date` datetime DEFAULT NULL,
  `person_completing_report` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `date_completed` date DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `state` varchar(255) DEFAULT NULL,
  `total_claimable_salary_cost_pool_1` decimal(12,4) DEFAULT '0.0000',
  `total_claimable_salary_cost_pool_2` decimal(12,4) DEFAULT '0.0000',
  `total_claimable_fringe_cost_pool_1` decimal(12,4) DEFAULT '0.0000',
  `total_claimable_fringe_cost_pool_2` decimal(12,4) DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_cost_forms`
--

LOCK TABLES `sdac_cost_forms` WRITE;
/*!40000 ALTER TABLE `sdac_cost_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_cost_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_cost_ppr_records`
--

DROP TABLE IF EXISTS `sdac_cost_ppr_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_cost_ppr_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_cost_form_id` int(11) DEFAULT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `provider_type` varchar(255) DEFAULT NULL,
  `npi` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_cost_ppr_records`
--

LOCK TABLES `sdac_cost_ppr_records` WRITE;
/*!40000 ALTER TABLE `sdac_cost_ppr_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_cost_ppr_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_cost_records`
--

DROP TABLE IF EXISTS `sdac_cost_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_cost_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_cost_form_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `sdac_loaded_provider_id` int(11) DEFAULT NULL,
  `fund` varchar(255) DEFAULT NULL,
  `func` varchar(255) DEFAULT NULL,
  `job_title` varchar(500) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `alt_id` varchar(255) DEFAULT NULL,
  `gross_salary` decimal(12,4) DEFAULT NULL,
  `state_funds` decimal(5,2) DEFAULT NULL,
  `gross_fringe` decimal(12,4) DEFAULT NULL,
  `fringe_state_funds` decimal(5,2) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `cost_pool` tinyint(4) DEFAULT NULL,
  `district_support_personnel` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_cost_records`
--

LOCK TABLES `sdac_cost_records` WRITE;
/*!40000 ALTER TABLE `sdac_cost_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_cost_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_costs`
--

DROP TABLE IF EXISTS `sdac_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_costs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `salaries` decimal(12,4) DEFAULT NULL,
  `other_costs` decimal(12,4) DEFAULT NULL,
  `fringe` decimal(12,4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ppr_rate` decimal(12,4) DEFAULT NULL,
  `indirect_rate` decimal(12,4) DEFAULT NULL,
  `salaries_justification` text,
  `fringe_justification` text,
  `other_costs_justification` text,
  `data_validated_at` datetime DEFAULT NULL,
  `ready_for_review_at` datetime DEFAULT NULL,
  `redo_reason` text,
  `adjustment_amount` decimal(12,4) DEFAULT NULL,
  `adjustment_reason` text,
  `certification_validated_at` datetime DEFAULT NULL,
  `certification_ready_for_review_at` datetime DEFAULT NULL,
  `edit_costs_button_active` tinyint(1) DEFAULT '0',
  `cost_pool_1_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_salaries` decimal(12,2) DEFAULT '0.00',
  `cost_pool_1_fringe` decimal(12,2) DEFAULT '0.00',
  `cost_pool_2_fringe` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `index_sdac_costs_on_district_id` (`district_id`),
  KEY `review_costs_idx` (`ready_for_review_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_costs`
--

LOCK TABLES `sdac_costs` WRITE;
/*!40000 ALTER TABLE `sdac_costs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_costs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_district_statuses`
--

DROP TABLE IF EXISTS `sdac_district_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_district_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `roster_state` varchar(255) DEFAULT NULL,
  `calendar_state` varchar(255) DEFAULT NULL,
  `audit_record_file_name` varchar(255) DEFAULT NULL,
  `audit_record_content_type` varchar(255) DEFAULT NULL,
  `audit_record_file_size` int(11) DEFAULT NULL,
  `audit_record_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_limiting_idx` (`district_id`,`year`,`quarter`),
  KEY `index_sdac_district_statuses_on_calendar_state` (`calendar_state`),
  KEY `index_sdac_district_statuses_on_district_id` (`district_id`),
  KEY `index_sdac_district_statuses_on_roster_state` (`roster_state`),
  KEY `index_sdac_district_statuses_on_state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_district_statuses`
--

LOCK TABLES `sdac_district_statuses` WRITE;
/*!40000 ALTER TABLE `sdac_district_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_district_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_ledger_transactions`
--

DROP TABLE IF EXISTS `sdac_ledger_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_ledger_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `sdac_ledger_id` int(11) DEFAULT NULL,
  `amount` decimal(12,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `transaction_type` varchar(255) DEFAULT NULL,
  `voided` tinyint(1) DEFAULT NULL,
  `voided_at` datetime DEFAULT NULL,
  `voided_by` int(11) DEFAULT NULL,
  `sdac_claim_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `district_sdac_ledger_index` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_ledger_transactions`
--

LOCK TABLES `sdac_ledger_transactions` WRITE;
/*!40000 ALTER TABLE `sdac_ledger_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_ledger_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_ledgers`
--

DROP TABLE IF EXISTS `sdac_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_ledgers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_id` int(11) DEFAULT NULL,
  `balance` decimal(12,4) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_ledgers`
--

LOCK TABLES `sdac_ledgers` WRITE;
/*!40000 ALTER TABLE `sdac_ledgers` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_ledgers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_loaded_providers`
--

DROP TABLE IF EXISTS `sdac_loaded_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_loaded_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roster_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `sdac_job_title` varchar(255) DEFAULT NULL,
  `alt_id` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `cost_pool` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_sdac_loaded_providers_on_district_id` (`district_id`),
  KEY `index_sdac_loaded_providers_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_loaded_providers`
--

LOCK TABLES `sdac_loaded_providers` WRITE;
/*!40000 ALTER TABLE `sdac_loaded_providers` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_loaded_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_logs`
--

DROP TABLE IF EXISTS `sdac_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `loggable_type` varchar(255) DEFAULT NULL,
  `loggable_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_sdac_logs_on_loggable_id` (`loggable_id`),
  KEY `index_sdac_logs_on_loggable_type_and_loggable_id` (`loggable_type`,`loggable_id`),
  KEY `index_sdac_logs_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_logs`
--

LOCK TABLES `sdac_logs` WRITE;
/*!40000 ALTER TABLE `sdac_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_positions`
--

DROP TABLE IF EXISTS `sdac_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_positions`
--

LOCK TABLES `sdac_positions` WRITE;
/*!40000 ALTER TABLE `sdac_positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_provider_pay_infos`
--

DROP TABLE IF EXISTS `sdac_provider_pay_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_provider_pay_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `salary` decimal(10,0) DEFAULT NULL,
  `benefits` decimal(10,0) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `audit_comment` text,
  PRIMARY KEY (`id`),
  KEY `index_sdac_provider_pay_infos_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_provider_pay_infos`
--

LOCK TABLES `sdac_provider_pay_infos` WRITE;
/*!40000 ALTER TABLE `sdac_provider_pay_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_provider_pay_infos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_provider_roster_files`
--

DROP TABLE IF EXISTS `sdac_provider_roster_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_provider_roster_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roster_file_size` int(11) DEFAULT NULL,
  `roster_content_type` varchar(255) DEFAULT NULL,
  `roster_file_name` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `redo_reason` text,
  `sent_back_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `validator_id` int(11) DEFAULT NULL,
  `unloaded_at` datetime DEFAULT NULL,
  `roster_updated_at` datetime DEFAULT NULL,
  `template_file_name` varchar(255) DEFAULT NULL,
  `template_content_type` varchar(255) DEFAULT NULL,
  `template_file_size` int(11) DEFAULT NULL,
  `template_updated_at` datetime DEFAULT NULL,
  `in_progress` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sdac_roster_searching_idx` (`district_id`,`year`,`quarter`),
  KEY `index_sdac_provider_roster_files_on_district_id` (`district_id`),
  KEY `roster_queue_idx` (`validated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_provider_roster_files`
--

LOCK TABLES `sdac_provider_roster_files` WRITE;
/*!40000 ALTER TABLE `sdac_provider_roster_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_provider_roster_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_random_moments`
--

DROP TABLE IF EXISTS `sdac_random_moments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_random_moments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `random_moment` datetime DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `rejection_timestamp` datetime DEFAULT NULL,
  `qa_flag` tinyint(1) DEFAULT NULL,
  `qa_timestamp` datetime DEFAULT NULL,
  `qa_checker_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL,
  `verification_flag` tinyint(1) DEFAULT NULL,
  `verification_timestamp` datetime DEFAULT NULL,
  `verifier_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `sdac_position_id` int(11) DEFAULT NULL,
  `sdac_job_title` varchar(255) DEFAULT NULL,
  `sdac_position_description` varchar(255) DEFAULT NULL,
  `esp_id` int(11) DEFAULT NULL,
  `valid_flag` tinyint(1) DEFAULT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `school_state_id` varchar(255) DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `district_name` varchar(255) DEFAULT NULL,
  `completed_timestamp` datetime DEFAULT NULL,
  `currently_invalid` tinyint(1) DEFAULT NULL,
  `inavlid_reason` varchar(255) DEFAULT NULL,
  `rejected_timestamp` datetime DEFAULT NULL,
  `sent_back_timestamp` datetime DEFAULT NULL,
  `status_cache` varchar(255) DEFAULT NULL,
  `redo_reason` text,
  `rejection_reason` text,
  `completed_comments` text,
  `used_in_ratio_calculations` tinyint(1) DEFAULT NULL,
  `original_recipient_id` int(11) DEFAULT NULL,
  `non_response` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_sdac_random_moments_on_non_response` (`non_response`),
  KEY `index_sdac_random_moments_on_provider_id` (`provider_id`),
  KEY `index_sdac_random_moments_on_qa_checker_id` (`qa_checker_id`),
  KEY `index_sdac_random_moments_on_random_moment` (`random_moment`),
  KEY `index_sdac_random_moments_on_survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_random_moments`
--

LOCK TABLES `sdac_random_moments` WRITE;
/*!40000 ALTER TABLE `sdac_random_moments` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_random_moments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_random_moments_surveys`
--

DROP TABLE IF EXISTS `sdac_random_moments_surveys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_random_moments_surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `initiated` datetime DEFAULT NULL,
  `completed` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `generated` datetime DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `sample_size` int(11) DEFAULT NULL,
  `seed_number` int(11) DEFAULT NULL,
  `authoritative` tinyint(1) DEFAULT NULL,
  `participant_list_count` int(11) DEFAULT NULL,
  `closed_out_at` datetime DEFAULT NULL,
  `cached_rms_data` longtext,
  `cached_count_admin_moments` int(11) DEFAULT NULL,
  `cached_moments_with_activities_count` int(11) DEFAULT NULL,
  `cached_count_non_admin_moments` int(11) DEFAULT NULL,
  `cached_redistributed_moments_sum` int(11) DEFAULT NULL,
  `cached_count_all_moments_adjusted` int(11) DEFAULT NULL,
  `cached_moments_percentage_excluding_admin_sum` decimal(6,2) DEFAULT NULL,
  `cached_moments_percentage_mer_and_ppr` decimal(6,2) DEFAULT NULL,
  `cached_moments_percentage_nondiscounted` decimal(6,2) DEFAULT NULL,
  `cached_moments_percentage_mer_only` decimal(6,2) DEFAULT NULL,
  `cached_claimable_rate_nondiscounted` decimal(6,2) DEFAULT NULL,
  `roster_due_on` date DEFAULT NULL,
  `costs_due_on` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sample_validation_file_name` varchar(255) DEFAULT NULL,
  `sample_validation_content_type` varchar(255) DEFAULT NULL,
  `sample_validation_file_size` int(11) DEFAULT NULL,
  `sample_validation_updated_at` datetime DEFAULT NULL,
  `manually_entered_rms_data` longtext,
  `disabled_administrative_fee` tinyint(1) DEFAULT '0',
  `invoice_template_file_name` varchar(255) DEFAULT NULL,
  `invoice_template_content_type` varchar(255) DEFAULT NULL,
  `invoice_template_file_size` int(11) DEFAULT NULL,
  `invoice_template_updated_at` datetime DEFAULT NULL,
  `administrative_fee` decimal(5,2) NOT NULL DEFAULT '5.00',
  `cost_pool_1_rms_data` longtext,
  `cost_pool_2_rms_data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_random_moments_surveys`
--

LOCK TABLES `sdac_random_moments_surveys` WRITE;
/*!40000 ALTER TABLE `sdac_random_moments_surveys` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_random_moments_surveys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_rms_forms`
--

DROP TABLE IF EXISTS `sdac_rms_forms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_rms_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `communication_timestamp` datetime DEFAULT NULL,
  `collection_timestamp` datetime DEFAULT NULL,
  `sdac_random_moment_id` int(11) DEFAULT NULL,
  `original_activity_description` text,
  `modified_activity_description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `signature_timestamp` datetime DEFAULT NULL,
  `submission_timestamp` datetime DEFAULT NULL,
  `sdac_position_id` int(11) DEFAULT NULL,
  `sdac_activity_id` int(11) DEFAULT NULL,
  `submission_method` varchar(255) DEFAULT NULL,
  `other_position_name` varchar(255) DEFAULT NULL,
  `manual_communication_reason` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_sdac_rms_forms_on_sdac_activity_id` (`sdac_activity_id`),
  KEY `index_sdac_rms_forms_on_sdac_position_id` (`sdac_position_id`),
  KEY `index_sdac_rms_forms_on_sdac_random_moment_id` (`sdac_random_moment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_rms_forms`
--

LOCK TABLES `sdac_rms_forms` WRITE;
/*!40000 ALTER TABLE `sdac_rms_forms` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_rms_forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_roster_statuses`
--

DROP TABLE IF EXISTS `sdac_roster_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_roster_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(1000) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `filename` varchar(1000) DEFAULT NULL,
  `exit_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_roster_statuses`
--

LOCK TABLES `sdac_roster_statuses` WRITE;
/*!40000 ALTER TABLE `sdac_roster_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_roster_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_salary_recipients`
--

DROP TABLE IF EXISTS `sdac_salary_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_salary_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_cost_file_id` int(11) DEFAULT NULL,
  `alt_id` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `benefits` decimal(12,2) DEFAULT NULL,
  `total_salary` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_recipient_normal_idx` (`sdac_cost_file_id`,`alt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_salary_recipients`
--

LOCK TABLES `sdac_salary_recipients` WRITE;
/*!40000 ALTER TABLE `sdac_salary_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_salary_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_student_roster_files`
--

DROP TABLE IF EXISTS `sdac_student_roster_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_student_roster_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roster_file_size` int(11) DEFAULT NULL,
  `roster_content_type` varchar(255) DEFAULT NULL,
  `roster_file_name` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `redo_reason` text,
  `sent_back_at` datetime DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `error_message` text,
  PRIMARY KEY (`id`),
  KEY `sdac_student_roster_searching_index` (`district_id`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_student_roster_files`
--

LOCK TABLES `sdac_student_roster_files` WRITE;
/*!40000 ALTER TABLE `sdac_student_roster_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_student_roster_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_survey_logs`
--

DROP TABLE IF EXISTS `sdac_survey_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_survey_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `sdac_random_moments_survey_id` int(11) DEFAULT NULL,
  `action_reason` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `survey_log_blame_index` (`provider_id`,`sdac_random_moments_survey_id`),
  KEY `index_sdac_survey_logs_on_provider_id` (`provider_id`),
  KEY `index_sdac_survey_logs_on_sdac_random_moments_survey_id` (`sdac_random_moments_survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_survey_logs`
--

LOCK TABLES `sdac_survey_logs` WRITE;
/*!40000 ALTER TABLE `sdac_survey_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_survey_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_training_completions`
--

DROP TABLE IF EXISTS `sdac_training_completions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_training_completions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `training_type` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_training_completions`
--

LOCK TABLES `sdac_training_completions` WRITE;
/*!40000 ALTER TABLE `sdac_training_completions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_training_completions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_training_records`
--

DROP TABLE IF EXISTS `sdac_training_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_training_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `pageviews` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_sdac_training_records_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_training_records`
--

LOCK TABLES `sdac_training_records` WRITE;
/*!40000 ALTER TABLE `sdac_training_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_training_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_training_video_completion_records`
--

DROP TABLE IF EXISTS `sdac_training_video_completion_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_training_video_completion_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sdac_training_video_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sdac_training_provider_idx` (`provider_id`),
  KEY `sdac_training_vid_idx` (`sdac_training_video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_training_video_completion_records`
--

LOCK TABLES `sdac_training_video_completion_records` WRITE;
/*!40000 ALTER TABLE `sdac_training_video_completion_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_training_video_completion_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sdac_training_videos`
--

DROP TABLE IF EXISTS `sdac_training_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sdac_training_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paper_required` tinyint(1) DEFAULT NULL,
  `video_file` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `electronic_required` tinyint(1) DEFAULT NULL,
  `captions_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sdac_training_videos`
--

LOCK TABLES `sdac_training_videos` WRITE;
/*!40000 ALTER TABLE `sdac_training_videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `sdac_training_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_appointments`
--

DROP TABLE IF EXISTS `service_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `service_transaction_id` int(11) NOT NULL,
  `time_spent_type` varchar(255) DEFAULT NULL,
  `minutes_spent` int(11) DEFAULT NULL,
  `modifier` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_service_appointments_on_service_id` (`service_id`),
  KEY `svc_trans_idx` (`service_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_appointments`
--

LOCK TABLES `service_appointments` WRITE;
/*!40000 ALTER TABLE `service_appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_rates`
--

DROP TABLE IF EXISTS `service_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `billable_amount_per_unit` decimal(12,2) DEFAULT NULL,
  `active_from` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_service_rates_on_service_id_and_active_from` (`service_id`,`active_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_rates`
--

LOCK TABLES `service_rates` WRITE;
/*!40000 ALTER TABLE `service_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_requests`
--

DROP TABLE IF EXISTS `service_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `discipline` varchar(100) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `group` tinyint(1) DEFAULT NULL,
  `minutes` varchar(10) DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `service_requests_student_id_foreign` (`student_id`),
  KEY `service_requests_requester_id_foreign` (`requester_id`),
  CONSTRAINT `service_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `providers` (`id`),
  CONSTRAINT `service_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_requests`
--

LOCK TABLES `service_requests` WRITE;
/*!40000 ALTER TABLE `service_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_transaction_auto_savings`
--

DROP TABLE IF EXISTS `service_transaction_auto_savings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_transaction_auto_savings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `options` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_transaction_auto_savings`
--

LOCK TABLES `service_transaction_auto_savings` WRITE;
/*!40000 ALTER TABLE `service_transaction_auto_savings` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_transaction_auto_savings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_transactions`
--

DROP TABLE IF EXISTS `service_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_at` datetime DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `repeat` varchar(255) DEFAULT NULL,
  `repeat_stop_date` datetime DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `alert_text` varchar(1000) DEFAULT NULL,
  `copying_to` date DEFAULT NULL,
  `telemedicine` tinyint(1) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `bill_tracking_record_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_service_transactions_on_origin` (`origin`),
  KEY `copying_to_index` (`provider_id`,`copying_to`),
  KEY `st_reporting_index` (`provider_id`,`start_at`,`status`),
  KEY `index_service_transactions_on_school_id` (`school_id`),
  KEY `index_service_transactions_on_start_datetime` (`start_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_transactions`
--

LOCK TABLES `service_transactions` WRITE;
/*!40000 ALTER TABLE `service_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `billable_amount_per_unit` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_billable` tinyint(1) DEFAULT NULL,
  `is_therapy` tinyint(1) DEFAULT NULL,
  `is_evaluation` tinyint(1) DEFAULT NULL,
  `attendance_type` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `plans_mask` int(11) DEFAULT NULL,
  `is_support` tinyint(1) DEFAULT '0',
  `minutes_per_year` int(11) DEFAULT '0',
  `minutes_per_day` int(11) DEFAULT '0',
  `minutes_per_week` int(11) DEFAULT '0',
  `minutes_per_month` int(11) DEFAULT '0',
  `minutes_per_unit` int(11) DEFAULT '0',
  `ot_position` int(11) DEFAULT NULL,
  `st_position` int(11) DEFAULT NULL,
  `pt_position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_services_on_is_billable` (`is_billable`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Therapeutic Exercises',10.00,NULL,'2018-03-21 06:16:34','97110','PT/OT',1,1,0,'INDIVIDUAL',1,55,0,240,0,0,0,15,2,NULL,2),(2,'Neuromuscular Re-Education',10.00,NULL,'2018-03-21 06:16:34','97112','PT/OT',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,8,NULL,20),(3,'Therapeutic Activities',10.00,NULL,'2018-03-21 06:16:34','97530','PT/OT',1,1,0,'INDIVIDUAL',1,55,0,240,0,0,0,15,1,NULL,1),(4,'Cognitive Skills Development',10.00,NULL,'2018-07-09 19:21:57','97532','OCCUPATIONAL',0,1,0,'INDIVIDUAL',0,51,0,240,0,0,0,15,7,NULL,NULL),(5,'Sensory Integration',10.00,NULL,'2018-03-21 06:16:34','97533','OCCUPATIONAL',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,4,NULL,NULL),(6,'Self Care Management Training',10.00,NULL,'2018-03-21 06:16:34','97535','PT/OT',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,3,NULL,4),(7,'Speech/Language Evaluation (For services before 01/01/14)',10.00,NULL,'2018-03-21 06:16:34','92506','SPEECH',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,29,NULL),(8,'Speech/Language Therapy - Individual',10.00,NULL,'2018-03-21 06:16:34','92507','SPEECH',1,1,0,'INDIVIDUAL',1,55,0,240,0,0,0,15,NULL,2,NULL),(9,'Speech/Language Therapy - Group',3.00,NULL,'2018-03-21 06:16:34','92508','SPEECH',1,1,0,'GROUP',1,55,0,240,0,0,0,15,NULL,1,NULL),(10,'PT Evaluation (For services before 01/01/17)',10.00,NULL,'2018-08-14 19:40:40','97001','PHYSICAL',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,NULL,11),(11,'PT Re-evaluation (For services before 01/01/17)',10.00,NULL,'2018-08-14 19:41:01','97002','PHYSICAL',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,NULL,13),(12,'OT Evaluation (For services before 01/01/17)',10.00,NULL,'2019-01-25 19:40:46','97003','OCCUPATIONAL',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,12,NULL,NULL),(13,'OT Re-evaluation (For services before 01/01/17)',10.00,NULL,'2018-11-07 19:41:03','97004','OCCUPATIONAL',1,0,0,'INDIVIDUAL',0,3,0,240,0,0,0,15,14,NULL,NULL),(14,'Staff Consultation/Training',0.00,NULL,'2020-04-03 16:28:21','0','ANY',0,0,0,'GROUP',1,55,0,240,0,0,0,15,17,15,15),(15,'IEP Meeting',0.00,NULL,'2019-05-03 19:53:00','0','ANY',0,0,0,'INDIVIDUAL',1,55,1,240,0,0,0,15,38,35,39),(16,'Staffing (reporting results)',0.00,NULL,'2018-03-21 06:16:34','0','ANY',0,0,0,'GROUP',1,51,0,240,0,0,0,15,18,7,16),(17,'Screening Meeting',0.00,NULL,'2018-03-21 06:16:34','0','ANY',0,0,0,'GROUP',1,51,0,240,0,0,0,15,21,9,18),(18,'Pre-screening',0.00,NULL,'2018-03-21 06:16:34','0','ANY',0,0,0,'GROUP',1,51,0,240,0,0,0,15,25,10,26),(19,'Individual Student Planning',0.00,NULL,'2018-03-21 06:16:34','0','ANY',0,0,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,26,8,25),(20,'Collateral',0.00,NULL,'2018-03-21 06:16:34','0','ANY',0,0,0,'GROUP',1,51,0,240,0,0,0,15,27,16,27),(21,'Gait Training',10.00,NULL,'2018-03-21 06:16:34','97116','PHYSICAL',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,5),(26,'Group Therapy (non-billable)',0.00,'2009-01-14 20:31:46','2018-03-21 06:16:34','0','PT/OT',0,1,0,'GROUP',1,55,0,240,0,0,0,15,15,NULL,28),(27,'Substitute/Student Teacher',0.00,'2010-02-16 19:38:11','2020-04-17 19:43:12','0','ANY',0,1,0,'GROUP',1,55,0,240,0,0,0,15,20,22,21),(28,'Nursing Care',7.27,'2011-06-09 16:58:27','2018-03-21 06:16:34','T1000','NURSING',1,1,0,'INDIVIDUAL',1,51,0,720,0,0,0,15,NULL,NULL,NULL),(29,'Wheelchairmanagement/propulsion training',10.00,'2012-10-10 17:17:16','2018-03-21 06:16:34','97542','PT/OT',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,5,NULL,6),(30,'Speech/Language Re-evaluation (For services before 01/01/14)',10.00,'2013-02-20 17:09:26','2018-03-21 06:16:34','S9152','SPEECH',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,30,NULL),(31,'Personal Care',4.24,'2013-05-28 17:26:24','2018-03-21 06:16:34','T1019','NURSING',1,1,0,'INDIVIDUAL',1,51,0,720,0,0,0,15,NULL,NULL,NULL),(33,'Co-Therapy',0.00,'2013-11-22 18:43:34','2019-04-23 14:33:41','','ANY',0,1,0,'GROUP',1,51,0,240,0,0,0,15,16,14,3),(34,'Evaluation Of Fluency',10.00,'2014-01-15 17:05:17','2018-03-21 06:16:34','92521','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,6,NULL),(35,'Eval of speech sound production',10.00,'2014-01-15 17:05:50','2018-03-21 06:16:34','92522','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,4,NULL),(36,'Eval of speech sound production WITH eval of lang comprehension and expression ',10.00,'2014-01-15 17:06:36','2018-03-21 06:16:34','92523','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,5,NULL),(37,'Behavioral and qualitative analysis of voice and resonance',10.00,'2014-01-15 17:08:35','2018-03-21 06:16:34','92524','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,17,NULL),(38,'Eval of lang comprehension and expression ONLY',10.00,'2015-05-28 16:16:33','2018-03-21 06:16:34','92523','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,3,NULL),(39,'Eval for script of speech-generating AAC device, 30 addl minutes',10.00,'2015-08-19 17:34:05','2018-03-21 06:16:34','92608','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,11,NULL),(40,'Programming/Modifying/Training of speech generated device ONLY',10.00,'2015-08-19 17:36:06','2018-03-21 06:16:34','92609','SPEECH',0,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,24,NULL),(41,'Prosthetic Training',10.00,'2015-08-26 20:41:39','2018-03-21 06:16:34','97520','PHYSICAL',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,17),(42,'Orthotic Training',10.00,'2015-08-26 20:46:28','2018-03-21 06:16:34','97504','PHYSICAL',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,14),(43,'Manual Therapy',10.00,'2015-08-26 20:47:28','2018-03-21 06:16:34','97140','PHYSICAL',1,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,19),(44,'Therapeutic services for use of non-speech generating device, incl. prog and mod',10.00,'2015-08-26 21:44:10','2018-03-21 06:16:34','92606','SPEECH',0,1,0,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,23,NULL),(45,'Eval for script of speech-generating AAC device, 1st hour',10.00,'2015-08-26 21:45:02','2018-03-21 06:16:34','92607','SPEECH',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,12,NULL),(46,'OCCUPATIONAL THERAPY (IEP Meeting)',10.00,'2015-08-28 04:39:27','2018-03-21 06:16:34','97003','PT/OT',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,33,NULL,34),(47,'Non-Billable Group',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','00006','PT/OT',0,1,0,'GROUP',0,51,0,240,0,0,0,15,32,NULL,33),(48,'SPEECH/LANGUAGE (IEP Meeting)',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','92506','SPEECH',0,0,0,'GROUP',0,51,0,240,0,0,0,15,NULL,28,NULL),(49,'Therapeutic services for non-speech-generative device, including programming and modification',10.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','92606','SPEECH',1,1,0,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,31,NULL),(50,'Eval for SPEECH-generating augmentative and alternative communication device, first hour',10.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','92607','SPEECH',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,27,NULL),(51,'PHYSICAL THERAPY (IEP Meeting)',10.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','97001','PT/OT',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,34,NULL,35),(52,'Unlisted modality/ Direct',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','97039','ANY',0,0,0,'GROUP',1,51,0,240,0,0,0,15,23,25,23),(53,'Therapeutic procedure(s)(GROUP)(Non-Billable)',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','97150','PT/OT',0,0,0,'GROUP',1,51,0,240,0,0,0,15,22,NULL,22),(54,'Unlisted physical medicine/rehabilitation service',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','97799','ANY',0,0,0,'GROUP',1,51,0,240,0,0,0,15,24,26,24),(55,'OT Consult Only',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','C9013','PT/OT',0,0,0,'GROUP',1,51,1,240,0,0,0,15,19,NULL,29),(56,'PT Consult Only',0.00,'2015-08-28 05:29:11','2018-03-21 06:16:34','C9013','PT/OT',0,0,0,'GROUP',1,63,1,240,0,0,0,15,28,NULL,7),(58,'Eval for speech-generating AAC device, first hour',10.00,'2015-10-28 08:59:16','2019-08-22 23:09:52','92607','SPEECH',1,0,1,'INDIVIDUAL',0,51,0,240,0,0,0,15,NULL,13,NULL),(59,'Instruction in Academics',0.00,'2016-08-17 19:54:19','2018-03-21 06:16:34','0','SPEECH',0,1,0,'GROUP',1,51,0,240,0,0,0,15,NULL,18,NULL),(60,'Psychiatric Diagnostic Evaluation ',90.08,'2016-08-20 20:36:58','2018-10-03 15:32:42','90791','PSYCHIATRIST',1,0,1,'INDIVIDUAL',1,127,0,180,0,0,0,30,NULL,NULL,NULL),(61,'Psychiatric Diagnostic Eval with Medical Services ',90.08,'2016-08-20 20:37:44','2018-10-03 15:32:29','90792','PSYCHIATRIST',1,0,1,'INDIVIDUAL',1,127,0,180,0,0,0,30,NULL,NULL,NULL),(62,'Psychotherapy, 30 min ',41.86,'2016-08-20 20:38:36','2018-10-03 15:34:55','90832','PSYCHIATRIST',1,1,0,'INDIVIDUAL',1,127,0,0,30,0,150,30,NULL,NULL,NULL),(63,'Psychotherapy, 45 min ',71.50,'2016-08-20 20:40:26','2018-03-21 06:16:34','90834','PSYCHIATRIST',1,1,0,'INDIVIDUAL',1,63,0,0,45,0,225,45,NULL,NULL,NULL),(64,'Psych Testing by Psychologist ',66.00,'2016-08-20 20:41:32','2019-08-21 19:21:17','96101','PSYCHIATRIST',0,1,0,'GROUP',0,127,0,240,0,0,0,60,NULL,NULL,NULL),(65,'Psych Testing by Computer',23.76,'2016-08-20 20:42:10','2019-10-24 01:14:28','96103','PSYCHIATRIST',1,1,0,'GROUP',0,127,0,240,0,0,0,60,NULL,NULL,NULL),(66,'Assessment of Aphasia',42.86,'2016-08-20 20:42:53','2018-10-03 14:20:37','96105','PSYCHIATRIST',1,1,0,'GROUP',1,127,0,240,0,0,0,60,NULL,NULL,NULL),(67,'Developmental Testing',78.33,'2016-08-20 20:43:21','2019-10-24 00:51:56','96111','PSYCHIATRIST',1,1,0,'GROUP',0,127,0,240,0,0,0,60,NULL,NULL,NULL),(68,'Neurobehavioral Status Exam; first hour',58.59,'2016-08-20 20:44:01','2019-10-24 01:33:31','96116','PSYCHIATRIST',1,1,0,'GROUP',1,127,0,240,0,0,0,60,NULL,NULL,NULL),(69,'Psychiatric Diagnostic Evaluation ',24.00,'2016-08-20 20:44:49','2018-10-03 15:32:52','90791','SOCIAL WORKER',1,0,1,'INDIVIDUAL',1,127,0,180,0,0,0,30,NULL,NULL,NULL),(70,'Psychotherapy, 30 min ',24.00,'2016-08-20 20:57:04','2018-10-03 15:35:03','90832','SOCIAL WORKER',1,1,0,'INDIVIDUAL',1,127,0,0,30,0,150,30,NULL,NULL,NULL),(71,'Psychotherapy, 45 min ',48.00,'2016-08-20 20:57:27','2018-10-03 15:35:27','90834','SOCIAL WORKER',1,1,0,'INDIVIDUAL',1,127,0,0,45,0,225,45,NULL,NULL,NULL),(72,'Psychiatric Diagnostic Evaluation ',24.00,'2016-08-20 20:58:06','2018-10-03 15:33:06','90791','COUNSELOR',1,0,1,'INDIVIDUAL',1,127,0,180,0,0,0,30,NULL,NULL,NULL),(73,'Psychotherapy, 30 min ',24.00,'2016-08-20 20:58:32','2018-10-03 15:35:11','90832','COUNSELOR',1,1,0,'INDIVIDUAL',1,127,0,0,30,0,150,30,NULL,NULL,NULL),(74,'Psychotherapy, 45 min ',48.00,'2016-08-20 20:58:50','2018-10-03 15:35:36','90834','COUNSELOR',1,1,0,'INDIVIDUAL',1,127,0,0,45,0,225,45,NULL,NULL,NULL),(75,'Psychiatric Diagnostic Evaluation ',30.00,'2016-08-20 20:59:25','2018-10-03 15:34:18','90791','PSYCHOLOGIST',1,0,1,'INDIVIDUAL',1,127,0,180,0,0,0,30,NULL,NULL,NULL),(76,'Psychotherapy, 30 min ',30.00,'2016-08-20 20:59:48','2018-10-03 15:35:19','90832','PSYCHOLOGIST',1,1,0,'GROUP',1,127,0,0,30,0,150,30,NULL,NULL,NULL),(77,'Psychotherapy, 45 min ',60.00,'2016-08-20 21:00:28','2018-10-03 15:35:42','90834','PSYCHOLOGIST',1,1,0,'INDIVIDUAL',1,127,0,0,45,0,225,45,NULL,NULL,NULL),(78,'Psych Testing by Psychologist ',60.00,'2016-08-20 21:01:20','2019-08-21 19:21:28','96101','PSYCHOLOGIST',0,1,0,'GROUP',0,127,0,240,0,0,0,60,NULL,NULL,NULL),(79,'Psych Testing by Computer',20.00,'2016-08-20 21:01:47','2019-10-24 01:14:41','96103','PSYCHOLOGIST',1,1,0,'GROUP',0,127,0,240,0,0,0,60,NULL,NULL,NULL),(80,'Assessment of Aphasia',35.00,'2016-08-20 21:02:10','2018-10-03 14:20:53','96105','PSYCHOLOGIST',1,1,0,'GROUP',1,127,0,240,0,0,0,60,NULL,NULL,NULL),(81,'Developmental Testing',35.00,'2016-08-20 21:02:29','2019-10-24 00:52:09','96111','PSYCHOLOGIST',1,1,0,'GROUP',0,127,0,240,0,0,0,60,NULL,NULL,NULL),(82,'Neurobehavioral Status Exam; first hour',35.00,'2016-08-20 21:02:53','2019-10-24 01:33:37','96116','PSYCHOLOGIST',1,1,0,'GROUP',1,127,0,240,0,0,0,60,NULL,NULL,NULL),(83,'Family Psychotherapy without Patient, 50 mins',24.00,'2016-08-21 13:56:06','2019-10-24 01:44:45','90846','SOCIAL WORKER',1,1,0,'GROUP',1,127,0,0,0,0,500,50,NULL,NULL,NULL),(84,'Family Psychotherapy with Patient, 50 mins',24.00,'2016-08-21 14:01:47','2019-10-24 01:48:45','90847','SOCIAL WORKER',1,1,0,'GROUP',1,127,0,0,0,0,500,50,NULL,NULL,NULL),(85,'Group Psychotherapy ',10.00,'2016-08-21 14:02:45','2018-10-03 14:21:58','90853','SOCIAL WORKER',1,1,0,'GROUP',1,127,0,0,90,0,450,30,NULL,NULL,NULL),(86,'Psychotherapy for Crisis, 60 minutes',48.00,'2016-08-21 14:03:13','2018-10-03 15:34:26','90839','SOCIAL WORKER',1,0,0,'GROUP',1,127,0,360,0,0,0,60,NULL,NULL,NULL),(87,'Support School Personnel',0.00,'2016-08-21 19:17:42','2018-10-03 15:36:06','2','PSYCHIATRIST',0,0,0,'GROUP',1,127,1,240,0,0,0,15,NULL,NULL,NULL),(88,'Support School Personnel',0.00,'2016-08-21 19:18:04','2018-10-03 15:36:17','2','PSYCHOLOGIST',0,0,0,'GROUP',1,127,1,240,0,0,0,15,NULL,NULL,NULL),(89,'Support School Personnel',0.00,'2016-08-21 19:18:39','2018-10-03 15:36:56','2','SOCIAL WORKER',0,0,0,'GROUP',1,127,1,240,0,0,0,15,NULL,NULL,NULL),(90,'Support School Personnel',0.00,'2016-08-21 19:19:01','2018-10-03 15:36:30','2','COUNSELOR',0,0,0,'GROUP',1,127,1,240,0,0,0,15,NULL,NULL,NULL),(91,'Review of Existing Data Meeting',0.00,'2016-10-13 18:32:37','2018-03-21 06:16:35','0','ANY',0,0,0,'GROUP',1,51,1,240,0,0,0,15,29,19,30),(92,'PT Evaluation (Low) ',NULL,'2017-01-05 23:07:03','2018-03-21 06:16:35','97161','PHYSICAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,9),(93,'PT Evaluation (Moderate)',NULL,'2017-01-05 23:15:24','2018-03-21 06:16:35','97162','PHYSICAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,10),(94,'PT Evaluation (High)',NULL,'2017-01-05 23:17:05','2018-03-21 06:16:35','97163','PHYSICAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,8),(95,'PT Re-evaluation',NULL,'2017-01-05 23:18:03','2018-03-21 06:16:35','97164','PHYSICAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,NULL,NULL,12),(96,'OT Evaluation (Low)',NULL,'2017-01-05 23:19:42','2018-03-21 06:16:35','97165','OCCUPATIONAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,10,NULL,NULL),(97,'OT Evaluation (Moderate)',NULL,'2017-01-05 23:20:52','2018-03-21 06:16:35','97166','OCCUPATIONAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,11,NULL,NULL),(98,'OT Evaluation (High)',NULL,'2017-01-05 23:21:44','2018-03-21 06:16:35','97167','OCCUPATIONAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,9,NULL,NULL),(99,'OT Re-evaluation ',NULL,'2017-01-05 23:23:01','2018-03-21 06:16:35','97168','OCCUPATIONAL',1,0,1,'INDIVIDUAL',1,51,0,240,0,0,0,15,13,NULL,NULL),(100,'RTI - Group',NULL,'2017-01-23 22:16:39','2017-03-03 10:59:45','00001','ANY',0,1,0,'GROUP',1,14,0,240,0,0,0,15,30,20,31),(101,'RTI - Individual',NULL,'2017-01-23 22:17:39','2017-03-03 10:59:45','00002','ANY',0,1,0,'INDIVIDUAL',1,14,0,240,0,0,0,15,31,21,32),(102,'Informal Assessment/Observation',NULL,'2017-08-04 16:14:43','2018-10-03 15:30:28','0','SOCIAL WORKER',0,0,0,'GROUP',1,127,1,240,0,0,0,15,NULL,NULL,NULL),(103,'Support School Personnel',NULL,'2017-08-07 17:33:21','2018-03-21 06:16:35','','SPEECH',0,0,0,'GROUP',1,63,1,240,0,0,0,15,NULL,32,NULL),(105,'Support School Personnel',NULL,'2017-08-07 17:36:38','2018-03-21 06:16:35','','PT/OT',0,0,0,'GROUP',1,63,1,240,0,0,0,15,35,NULL,36),(106,'Family Psychotherapy without Patient, 50 mins',NULL,'2017-09-01 15:08:25','2019-10-24 01:47:34','90846','COUNSELOR',1,1,0,'GROUP',1,127,0,0,0,0,500,50,NULL,NULL,NULL),(107,'Family Psychotherapy with Patient, 50 mins',NULL,'2017-09-01 15:12:48','2019-10-24 01:48:19','90847','COUNSELOR',1,1,0,'GROUP',1,127,0,0,0,0,500,50,NULL,NULL,NULL),(108,'Group Psychotherapy ',NULL,'2017-09-01 15:19:19','2018-10-03 14:22:19','90853','COUNSELOR',1,1,0,'GROUP',1,127,0,0,90,0,450,30,NULL,NULL,NULL),(109,'Psychotherapy for Crisis, 60 minutes',NULL,'2017-09-01 15:25:12','2018-10-03 15:34:33','90839','COUNSELOR',1,1,0,'GROUP',1,127,0,360,0,0,0,60,NULL,NULL,NULL),(110,'Supervision',NULL,'2017-09-07 23:24:56','2018-03-21 06:16:35','','ANY',0,0,0,'GROUP',1,55,1,0,0,0,1000,15,36,33,37),(111,'Development of cognitive skills to improve attention, memory',NULL,'2018-07-09 19:20:28','2018-07-09 19:20:28','G0515','OCCUPATIONAL',1,1,0,'INDIVIDUAL',1,3,0,240,0,0,0,15,6,NULL,NULL),(112,'Lunch',NULL,'2018-10-05 15:47:57','2019-07-26 15:07:33','0','SpEd',0,0,0,'INDIVIDUAL',1,55,1,240,0,0,0,15,NULL,NULL,NULL),(113,'Plan Time',NULL,'2018-10-05 15:51:04','2018-10-08 15:31:39','0','ANY',0,0,0,'INDIVIDUAL',1,7,0,240,0,0,0,15,37,34,38),(114,'ECSE - Physical',NULL,'2019-05-03 18:33:42','2019-05-03 18:33:42','E1','ESCE',0,1,0,'GROUP',1,63,0,240,0,0,0,15,NULL,NULL,NULL),(115,'ECSE – Cognitive',NULL,'2019-05-03 18:34:19','2019-05-03 18:34:19','E2','ESCE',0,1,0,'GROUP',1,127,0,240,0,0,0,15,NULL,NULL,NULL),(116,'ECSE – Communication',NULL,'2019-05-03 18:34:59','2019-05-03 18:34:59','E3','ESCE',0,1,0,'GROUP',1,127,0,240,0,0,0,15,NULL,NULL,NULL),(117,'ECSE – Social/Emotional',NULL,'2019-05-03 18:36:12','2019-05-03 19:33:35','E4','ESCE',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(118,'Specialized Instruction in Reading',NULL,'2019-05-03 19:36:37','2019-05-03 19:36:37','S1','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(119,'Specialized Instruction in Written Expression',NULL,'2019-05-03 19:38:06','2019-05-03 19:38:06','S2','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(120,'Specialized Instruction in Math',NULL,'2019-05-03 19:38:42','2019-05-03 19:38:42','S3','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(121,'Specialized Instruction in Oral Expression',NULL,'2019-05-03 19:39:40','2019-05-03 19:39:40','S4','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(122,'Specialized Instruction Listening Comprehension',NULL,'2019-05-03 19:40:11','2019-05-03 19:40:11','S5','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(123,'Specialized Instruction in Social/Behavioral',NULL,'2019-05-03 19:40:56','2019-05-03 19:40:56','S6','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(124,'Specialized Instruction in Personal Management',NULL,'2019-05-03 19:41:29','2019-05-03 19:41:29','S7','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(125,'Specialized Instruction in Daily Living/Life Skills',NULL,'2019-05-03 19:42:28','2019-05-03 19:42:28','S8','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(126,'Specialized Instruction in Transition',NULL,'2019-05-03 19:42:54','2019-05-03 19:42:54','S9','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(127,'Co-teaching/Collaborative Teaching',NULL,'2019-05-03 19:43:31','2019-05-03 19:43:31','S10','SpEd',0,1,0,'GROUP',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(128,'Psychological testing evaluation, first hour',NULL,'2019-08-21 19:28:41','2019-08-21 19:34:44','96130','PSYCHOLOGIST',1,0,1,'INDIVIDUAL',1,127,0,240,0,0,0,60,NULL,NULL,NULL),(129,'Psychological testing evaluation, first hour',NULL,'2019-08-21 19:31:45','2019-08-21 19:35:06','96130','PSYCHIATRIST',1,0,1,'INDIVIDUAL',1,127,0,240,0,0,0,60,NULL,NULL,NULL),(130,'Auditory function, 60 minutes',NULL,'2019-08-21 20:01:44','2019-08-21 20:01:44','92620','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,60,0,0,60,NULL,NULL,NULL),(131,'Auditory function + 15 mins',NULL,'2019-08-21 20:03:23','2019-08-21 20:03:23','92621','ANY',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,39,36,40),(132,'Aud Rehab Pre-Ling Hearing Loss',NULL,'2019-08-21 20:05:26','2019-08-21 20:07:18','92630','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(133,'Aud Rehab Post-Ling Hearing loss',NULL,'2019-08-21 20:06:52','2019-08-29 16:47:31','92633','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(134,'Fitting/Orientation/Checking of Hearing Aid',NULL,'2019-08-21 20:09:00','2019-08-21 20:09:00','V5011','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(135,'Repair/Modification of a Hearing Aid (No Longer Under Warranty)',NULL,'2019-08-21 20:10:32','2019-08-21 20:10:32','V5014','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(136,'Battery For Use in Hearing Device, IEP',NULL,'2019-08-21 20:12:59','2019-08-21 20:12:59','V5266','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(137,'Hearing Aid Device/Supplies/Accessories',NULL,'2019-08-21 20:15:22','2019-08-21 20:15:22','V5267','AUDIO',1,1,0,'INDIVIDUAL',1,127,0,0,240,0,0,15,NULL,NULL,NULL),(138,'Developmental test, first hour',NULL,'2019-10-24 00:51:40','2019-10-24 01:42:40','96112','PSYCHIATRIST',1,1,0,'INDIVIDUAL',1,127,0,0,60,0,0,60,NULL,NULL,NULL),(139,'Developmental test, first hour',NULL,'2019-10-24 00:52:51','2019-10-24 01:42:50','97112','PSYCHOLOGIST',1,1,0,'INDIVIDUAL',1,127,0,0,60,0,0,60,NULL,NULL,NULL),(140,'Developmental test; additional 30 minutes',NULL,'2019-10-24 00:55:48','2019-10-24 01:41:47','96113','PSYCHIATRIST',1,1,0,'INDIVIDUAL',1,127,0,0,180,0,0,30,NULL,NULL,NULL),(141,'Developmental test; additional 30 minutes',NULL,'2019-10-24 00:57:24','2019-10-24 01:42:21','96113','PSYCHOLOGIST',1,1,0,'INDIVIDUAL',1,127,0,0,180,0,0,30,NULL,NULL,NULL),(144,'Neurobehavioral Status Exam; addt\'l hour',NULL,'2019-10-24 01:31:51','2019-10-24 01:33:25','96121','PSYCHIATRIST',1,1,0,'INDIVIDUAL',1,127,0,0,180,0,0,60,NULL,NULL,NULL),(145,'Neurobehavioral Status Exam; addt\'l hour',NULL,'2019-10-24 01:33:00','2019-10-24 01:33:00','96121','PSYCHOLOGIST',1,1,0,'INDIVIDUAL',1,127,0,0,180,0,0,60,NULL,NULL,NULL),(146,'Psychological testing evaluation, addt\'l hour',NULL,'2019-10-24 01:36:30','2019-10-24 01:38:03','96131','PSYCHIATRIST',1,0,1,'INDIVIDUAL',1,127,0,0,180,0,0,60,NULL,NULL,NULL),(147,'Psychological testing evaluation, addt\'l hour',NULL,'2019-10-24 01:37:11','2019-10-24 01:38:49','96131','PSYCHOLOGIST',1,0,1,'INDIVIDUAL',1,127,0,0,180,0,0,60,NULL,NULL,NULL),(148,'Evaluation for use and/or fitting of voice prosthetic device to supplement oral speech',NULL,'2020-03-16 15:06:33','2020-03-16 15:06:33','92597','SPEECH',1,0,1,'INDIVIDUAL',1,127,0,0,240,0,0,60,NULL,37,NULL),(149,'Caregiver Consult',NULL,'2020-03-31 17:38:01','2020-03-31 19:47:12','','ANY',0,0,0,'INDIVIDUAL',1,127,1,0,240,0,0,15,40,38,41);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signatures`
--

DROP TABLE IF EXISTS `signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_transaction_id` int(11) DEFAULT NULL,
  `vector_array` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `image_file_name` varchar(255) DEFAULT NULL,
  `image_content_type` varchar(255) DEFAULT NULL,
  `image_file_size` int(11) DEFAULT NULL,
  `image_updated_at` datetime DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `compressed_vector_array` blob,
  `document_type` varchar(255) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `index_signatures_on_document_id_and_document_type` (`document_id`,`document_type`),
  KEY `index_signatures_on_provider_id` (`provider_id`),
  KEY `index_signatures_on_service_transaction_id` (`service_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signatures`
--

LOCK TABLES `signatures` WRITE;
/*!40000 ALTER TABLE `signatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `signatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statement_of_needs`
--

DROP TABLE IF EXISTS `statement_of_needs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statement_of_needs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `prescriptions_id` bigint(20) DEFAULT NULL,
  `plan_id` bigint(20) DEFAULT NULL,
  `provider_id` bigint(20) DEFAULT NULL,
  `therapy_type` varchar(255) DEFAULT NULL,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_statement_of_needs_on_plan_id` (`plan_id`),
  KEY `index_statement_of_needs_on_prescriptions_id` (`prescriptions_id`),
  KEY `index_statement_of_needs_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statement_of_needs`
--

LOCK TABLES `statement_of_needs` WRITE;
/*!40000 ALTER TABLE `statement_of_needs` DISABLE KEYS */;
/*!40000 ALTER TABLE `statement_of_needs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `abbreviation` varchar(2) DEFAULT NULL,
  `medicaid_name` varchar(255) DEFAULT NULL,
  `ein` varchar(15) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `ftp_host` varchar(255) DEFAULT NULL,
  `ftp_username` varchar(255) DEFAULT NULL,
  `ftp_password` varchar(255) DEFAULT NULL,
  `contact_info` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_states_on_ein` (`ein`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,'Missouri','MO','MO HealthNet',NULL,'P. O. Box 6500','Jefferson City','651026500','','','',NULL,'2013-06-03 23:07:02','2013-06-03 23:07:02'),(2,'Alabama','AL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(3,'Alaska','AK',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(4,'American Samoa','AS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(5,'Arizona','AZ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(6,'Arkansas','AR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(7,'California','CA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(8,'Colorado','CO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(9,'Connecticut','CT',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(10,'Delaware','DE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(11,'District Of Columbia','DC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(12,'Federated States Of Micronesia','FM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(13,'Florida','FL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(14,'Georgia','GA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(15,'Guam','GU',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(16,'Hawaii','HI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(17,'Idaho','ID',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(18,'Illinois','IL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(19,'Indiana','IN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(20,'Iowa','IA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(21,'Kansas','KS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(22,'Kentucky','KY',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(23,'Louisiana','LA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(24,'Maine','ME',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(25,'Marshall Islands','MH',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(26,'Maryland','MD',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(27,'Massachusetts','MA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(28,'Michigan','MI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(29,'Minnesota','MN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(30,'Mississippi','MS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(31,'Montana','MT',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(32,'Nebraska','NE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(33,'Nevada','NV',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(34,'New Hampshire','NH',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(35,'New Jersey','NJ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(36,'New Mexico','NM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(37,'New York','NY',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(38,'North Carolina','NC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(39,'North Dakota','ND',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(40,'Northern Mariana Islands','MP',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(41,'Ohio','OH',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(42,'Oklahoma','OK',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(43,'Oregon','OR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(44,'Palau','PW',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(45,'Pennsylvania','PA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(46,'Puerto Rico','PR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(47,'Rhode Island','RI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(48,'South Carolina','SC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(49,'South Dakota','SD',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(50,'Tennessee','TN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(51,'Texas','TX',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(52,'Utah','UT',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(53,'Vermont','VT',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(54,'Virgin Islands','VI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(55,'Virginia','VA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(56,'Washington','WA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(57,'West Virginia','WV',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(58,'Wisconsin','WI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03'),(59,'Wyoming','WY',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2020-09-18 07:43:03','2020-09-18 07:43:03');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointment_components`
--

DROP TABLE IF EXISTS `student_appointment_components`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointment_components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_appointment_goal_id` int(11) DEFAULT NULL,
  `component_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sa_components_component_id` (`component_id`),
  KEY `idx_sa_components_goal_id` (`student_appointment_goal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointment_components`
--

LOCK TABLES `student_appointment_components` WRITE;
/*!40000 ALTER TABLE `student_appointment_components` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointment_components` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointment_files`
--

DROP TABLE IF EXISTS `student_appointment_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointment_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_appointment_id` int(11) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_student_appointment_files_on_student_appointment_id` (`student_appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointment_files`
--

LOCK TABLES `student_appointment_files` WRITE;
/*!40000 ALTER TABLE `student_appointment_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointment_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointment_goals`
--

DROP TABLE IF EXISTS `student_appointment_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointment_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_appointment_id` int(11) DEFAULT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `progress` varchar(255) DEFAULT NULL,
  `doc_percentage_base` int(11) DEFAULT NULL,
  `doc_percentage_numerator` int(11) DEFAULT NULL,
  `legacy_activity_one_id` int(11) DEFAULT NULL,
  `legacy_activity_two_id` int(11) DEFAULT NULL,
  `benchmark_one_id` int(11) DEFAULT NULL,
  `benchmark_two_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `activity_one` varchar(255) DEFAULT NULL,
  `activity_two` varchar(255) DEFAULT NULL,
  `activity` text,
  `goal_benchmark_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `benchmark_one_index` (`benchmark_one_id`),
  KEY `benchmark_two_index` (`benchmark_two_id`),
  KEY `index_student_appointment_goals_on_goal_benchmark_id` (`goal_benchmark_id`),
  KEY `goal_index` (`goal_id`),
  KEY `activity_one_index` (`legacy_activity_one_id`),
  KEY `activity_two_index` (`legacy_activity_two_id`),
  KEY `student_appointment_index` (`student_appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointment_goals`
--

LOCK TABLES `student_appointment_goals` WRITE;
/*!40000 ALTER TABLE `student_appointment_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointment_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointment_payments`
--

DROP TABLE IF EXISTS `student_appointment_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointment_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claim_payment_id` int(11) DEFAULT NULL,
  `student_appointment_id` int(11) DEFAULT NULL,
  `payment_amount` decimal(12,2) DEFAULT NULL,
  `charged_amount` decimal(12,2) DEFAULT NULL,
  `allowed_amount` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `data_cache` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_student_appointment_payments_on_claim_payment_id` (`claim_payment_id`),
  KEY `index_student_appointment_payments_on_student_appointment_id` (`student_appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointment_payments`
--

LOCK TABLES `student_appointment_payments` WRITE;
/*!40000 ALTER TABLE `student_appointment_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointment_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointment_submissions`
--

DROP TABLE IF EXISTS `student_appointment_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointment_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_appointment_id` int(11) DEFAULT NULL,
  `claim_submission_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `procedure_identifier` varchar(255) DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT NULL,
  `units` int(11) DEFAULT NULL,
  `service_facility` text,
  `rendering_provider` text,
  `requires_rendering_provider` tinyint(1) DEFAULT '1',
  `diagnosis_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_student_appointment_submissions_on_claim_submission_id` (`claim_submission_id`),
  KEY `index_student_appointment_submissions_on_student_appointment_id` (`student_appointment_id`),
  KEY `index_student_appointment_submissions_on_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointment_submissions`
--

LOCK TABLES `student_appointment_submissions` WRITE;
/*!40000 ALTER TABLE `student_appointment_submissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointment_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointment_tracking_data`
--

DROP TABLE IF EXISTS `student_appointment_tracking_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointment_tracking_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `session_type` varchar(255) NOT NULL,
  `started_at` datetime NOT NULL,
  `ended_at` datetime NOT NULL,
  `goals` text,
  `events` mediumtext,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `appointment_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointment_tracking_data`
--

LOCK TABLES `student_appointment_tracking_data` WRITE;
/*!40000 ALTER TABLE `student_appointment_tracking_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointment_tracking_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_appointments`
--

DROP TABLE IF EXISTS `student_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `service_appointment_id` int(11) NOT NULL,
  `attended` tinyint(1) DEFAULT NULL,
  `comments` varchar(5110) DEFAULT NULL,
  `legacy_goal_id` int(11) DEFAULT NULL,
  `legacy_activity_id` int(11) DEFAULT NULL,
  `claim_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `progress` varchar(255) DEFAULT NULL,
  `in_time` datetime DEFAULT NULL,
  `out_time` datetime DEFAULT NULL,
  `on_time` tinyint(1) DEFAULT NULL,
  `reassigned_from` int(11) DEFAULT NULL,
  `legacy_benchmark_one_id` int(11) DEFAULT NULL,
  `legacy_benchmark_two_id` int(11) DEFAULT NULL,
  `legacy_benchmark_three_id` int(11) DEFAULT NULL,
  `legacy_benchmark_four_id` int(11) DEFAULT NULL,
  `legacy_goal_two_id` int(11) DEFAULT NULL,
  `legacy_activity_two_id` int(11) DEFAULT NULL,
  `legacy_activity_three_id` int(11) DEFAULT NULL,
  `legacy_activity_four_id` int(11) DEFAULT NULL,
  `legacy_goal_three_id` int(11) DEFAULT NULL,
  `legacy_benchmark_five_id` int(11) DEFAULT NULL,
  `legacy_benchmark_six_id` int(11) DEFAULT NULL,
  `legacy_activity_five_id` int(11) DEFAULT NULL,
  `legacy_activity_six_id` int(11) DEFAULT NULL,
  `billed_amount` float DEFAULT NULL,
  `paid_amount` float DEFAULT NULL,
  `units_paid` int(11) DEFAULT NULL,
  `legacy_doc_percentage_base` int(11) DEFAULT NULL,
  `legacy_doc_percentage_numerator` int(11) DEFAULT NULL,
  `voided_batch_id` int(11) DEFAULT NULL,
  `unbillable_reasons` mediumtext,
  `needs_replacement_billing` tinyint(1) DEFAULT NULL,
  `billable` tinyint(1) NOT NULL DEFAULT '0',
  `documented_at` datetime DEFAULT NULL,
  `marked_for_void_at` datetime DEFAULT NULL,
  `voider_id` int(11) DEFAULT NULL,
  `legacy_status` varchar(255) DEFAULT NULL,
  `never_bill` tinyint(1) DEFAULT '0',
  `treatment_plan_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `new_billing_index` (`billable`,`status`,`claim_id`,`documented_at`),
  KEY `index_student_appointments_on_claim_id` (`claim_id`),
  KEY `index_student_appointments_on_in_time` (`in_time`),
  KEY `resubmitting_index` (`needs_replacement_billing`,`claim_id`),
  KEY `svc_appt_idx` (`service_appointment_id`),
  KEY `index_student_appointments_on_status` (`status`),
  KEY `index_student_appointments_on_student_id` (`student_id`),
  KEY `index_student_appointments_on_treatment_plan_id` (`treatment_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_appointments`
--

LOCK TABLES `student_appointments` WRITE;
/*!40000 ALTER TABLE `student_appointments` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_eligibility_batch_transactions`
--

DROP TABLE IF EXISTS `student_eligibility_batch_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_eligibility_batch_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_eligibility_batch_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `accepted` tinyint(1) DEFAULT NULL,
  `ack_messages` varchar(1024) DEFAULT NULL,
  `string` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `se_batch_idx` (`student_eligibility_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_eligibility_batch_transactions`
--

LOCK TABLES `student_eligibility_batch_transactions` WRITE;
/*!40000 ALTER TABLE `student_eligibility_batch_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_eligibility_batch_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_eligibility_batches`
--

DROP TABLE IF EXISTS `student_eligibility_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_eligibility_batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `data_cache_file_name` varchar(255) DEFAULT NULL,
  `data_cache_file_size` int(11) DEFAULT NULL,
  `data_cache_content_type` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_student_eligibility_batches_on_year_and_month` (`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_eligibility_batches`
--

LOCK TABLES `student_eligibility_batches` WRITE;
/*!40000 ALTER TABLE `student_eligibility_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_eligibility_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_eligibility_records`
--

DROP TABLE IF EXISTS `student_eligibility_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_eligibility_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `eligible` tinyint(1) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `eligibility_mask` int(11) DEFAULT '0',
  `covered_service_type_codes` varchar(1500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_by_all_three_columns` (`student_id`,`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_eligibility_records`
--

LOCK TABLES `student_eligibility_records` WRITE;
/*!40000 ALTER TABLE `student_eligibility_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_eligibility_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_eligibility_request_records`
--

DROP TABLE IF EXISTS `student_eligibility_request_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_eligibility_request_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_eligibility_record_id` int(11) DEFAULT NULL,
  `student_eligibility_batch_transaction_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_eligibility_request_records`
--

LOCK TABLES `student_eligibility_request_records` WRITE;
/*!40000 ALTER TABLE `student_eligibility_request_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_eligibility_request_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_mergers`
--

DROP TABLE IF EXISTS `student_mergers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_mergers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_student_id` int(11) DEFAULT NULL,
  `to_student_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_mergers`
--

LOCK TABLES `student_mergers` WRITE;
/*!40000 ALTER TABLE `student_mergers` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_mergers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_script_requests`
--

DROP TABLE IF EXISTS `student_script_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_script_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `script_request_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `close_date` datetime DEFAULT NULL,
  `script_types` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_student_script_requests_on_close_date` (`close_date`),
  KEY `index_student_script_requests_on_script_request_id` (`script_request_id`),
  KEY `index_student_script_requests_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_script_requests`
--

LOCK TABLES `student_script_requests` WRITE;
/*!40000 ALTER TABLE `student_script_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_script_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `contact_phone_number` varchar(255) DEFAULT NULL,
  `contact_alt_number` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_alt_email` varchar(255) DEFAULT NULL,
  `text_notify_condition` int(11) DEFAULT NULL,
  `email_notify_condition` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `social_security_number` varchar(255) DEFAULT NULL,
  `medicaid_number` varchar(255) DEFAULT NULL,
  `mosis_number` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `served_at_home_district` tinyint(1) DEFAULT NULL,
  `reeval_date` date DEFAULT NULL,
  `iep_date` date DEFAULT NULL,
  `legacy_has_prescription` tinyint(1) DEFAULT NULL,
  `middle_initial` varchar(255) DEFAULT NULL,
  `alt_id` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `never_bill` tinyint(1) DEFAULT NULL,
  `legacy_script_expiration_date` date DEFAULT NULL,
  `transportation_billing` tinyint(1) DEFAULT NULL,
  `transportation_purpose` varchar(255) DEFAULT NULL,
  `transportation_trips_per_day` int(11) DEFAULT NULL,
  `eligible_until` date DEFAULT NULL,
  `iep_file_file_name` varchar(255) DEFAULT NULL,
  `iep_file_content_type` varchar(255) DEFAULT NULL,
  `iep_file_file_size` int(11) DEFAULT NULL,
  `iep_file_updated_at` datetime DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `iep_file_effective_date` date DEFAULT NULL,
  `billing_first_name` varchar(255) DEFAULT NULL,
  `billing_last_name` varchar(255) DEFAULT NULL,
  `billing_birth_date` date DEFAULT NULL,
  `billing_middle_initial` varchar(255) DEFAULT NULL,
  `lock_billing_data` tinyint(1) DEFAULT '0',
  `lte_active` tinyint(1) DEFAULT '1',
  `managed_care_plan` varchar(255) DEFAULT NULL,
  `managed_care_member_id` varchar(255) DEFAULT NULL,
  `managed_care_group_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_students_on_alt_id` (`alt_id`),
  KEY `district_idx` (`district_id`),
  KEY `index_students_on_mosis_number` (`mosis_number`)
) ENGINE=InnoDB AUTO_INCREMENT=329 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,'Tom','Smith','','','','',NULL,NULL,'',NULL,0,0,'2020-08-10 22:36:11','2020-08-10 22:36:11','2010-01-01','M',NULL,'','',1,1,NULL,NULL,NULL,'','','',1,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tom','Smith','2010-01-01','',0,1,'',NULL,NULL),(2,'David','Cooper','Amanda Dunkin','','','',NULL,NULL,'amanda@teleteachers.com',NULL,0,0,'2020-08-10 22:39:07','2020-09-30 18:44:16','2020-02-01','M',NULL,'','',1,1,NULL,NULL,NULL,'','','',1,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'David','Cooper','2020-02-01','',0,1,'','',''),(3,'Taylor','Jones','','','','',NULL,NULL,'',NULL,0,0,'2020-08-10 22:42:10','2020-08-10 22:42:10','2010-03-01','F',NULL,'','',1,1,NULL,NULL,NULL,'','','',1,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Taylor','Jones','2010-03-01','',0,1,'',NULL,NULL),(4,'Blake','Wise','','','','',NULL,NULL,'',NULL,0,0,'2020-08-18 14:46:10','2020-08-18 14:46:10','2002-12-27','M',NULL,'','',2,1,'2020-01-09',NULL,NULL,'','','',1,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Blake','Wise','2002-12-27','',0,1,'',NULL,NULL),(5,'Matthew','Willsey',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2016-05-02','M',NULL,NULL,NULL,4,1,'2022-04-08',NULL,NULL,NULL,'133381',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Matthew','Willsey','2016-05-02',NULL,0,1,NULL,NULL,NULL),(6,'Jackson','Wile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2015-05-26','M',NULL,NULL,NULL,4,1,'2021-05-03',NULL,NULL,NULL,'130622',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jackson','Wile','2015-05-26',NULL,0,1,NULL,NULL,NULL),(7,'Ava','Miksch',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2016-03-15','F',NULL,NULL,NULL,4,1,'2022-02-22',NULL,NULL,NULL,'133247',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ava','Miksch','2016-03-15',NULL,0,1,NULL,NULL,NULL),(8,'Marcus','Sanjana',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2016-01-26','M',NULL,NULL,NULL,4,1,'2022-04-10',NULL,NULL,NULL,'133427',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Marcus','Sanjana','2016-01-26',NULL,0,1,NULL,NULL,NULL),(9,'Donald','Dale',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2014-01-14','M',NULL,NULL,NULL,4,1,'2023-01-24',NULL,NULL,NULL,'133579',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Donald','Dale','2014-01-14',NULL,0,1,NULL,NULL,NULL),(10,'Layla ','Jarrett',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2014-05-06','F',NULL,NULL,NULL,4,1,'2022-12-19',NULL,NULL,NULL,'133618',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Layla ','Jarrett','2014-05-06',NULL,0,1,NULL,NULL,NULL),(11,'Wade ','Davidson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2014-05-16','M',NULL,NULL,NULL,4,1,'2020-10-11',NULL,NULL,NULL,'130027',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Wade ','Davidson','2014-05-16',NULL,0,1,NULL,NULL,NULL),(12,'Kennedy','Hale',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-09-06','F',NULL,NULL,NULL,4,1,'2023-03-31',NULL,NULL,NULL,'136037',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kennedy','Hale','2013-09-06',NULL,0,1,NULL,NULL,NULL),(13,'Ryan ','Mercadante',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-08-18','M',NULL,NULL,NULL,4,1,'2022-09-20',NULL,NULL,NULL,'134399',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ryan ','Mercadante','2013-08-18',NULL,0,1,NULL,NULL,NULL),(14,'Summer','Pillai',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-08-26','F',NULL,NULL,NULL,4,1,'2021-11-30',NULL,NULL,NULL,'133039',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Summer','Pillai','2013-08-26',NULL,0,1,NULL,NULL,NULL),(15,'Austin ','Wile',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-05-31','M',NULL,NULL,NULL,4,1,'2022-10-29',NULL,NULL,NULL,'133588',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Austin ','Wile','2013-05-31',NULL,0,1,NULL,NULL,NULL),(16,'Jamison','Sims',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2012-12-29','M',NULL,NULL,NULL,4,1,'2020-10-06',NULL,NULL,NULL,'129774',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jamison','Sims','2012-12-29',NULL,0,1,NULL,NULL,NULL),(17,'Ridge ','Doyen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2012-02-18','M',NULL,NULL,NULL,4,1,'2020-10-27',NULL,NULL,NULL,'127902',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ridge ','Doyen','2012-02-18',NULL,0,1,NULL,NULL,NULL),(18,'Geoffrey','Dale',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2010-08-05','M',NULL,NULL,NULL,4,1,'2023-01-24',NULL,NULL,NULL,'125112',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Geoffrey','Dale','2010-08-05',NULL,0,1,NULL,NULL,NULL),(19,'Levi ','Herrera',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2011-06-30','M',NULL,NULL,NULL,4,1,'2021-09-27',NULL,NULL,NULL,'122315',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Levi ','Herrera','2011-06-30',NULL,0,1,NULL,NULL,NULL),(20,'Kristen','Nguyen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-03-23','F',NULL,NULL,NULL,4,1,'2022-12-19',NULL,NULL,NULL,'130784',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kristen','Nguyen','2013-03-23',NULL,0,1,NULL,NULL,NULL),(21,'Willie','Conyer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-07-03','M',NULL,NULL,NULL,4,1,'2021-05-17',NULL,NULL,NULL,'130636',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Willie','Conyer','2013-07-03',NULL,0,1,NULL,NULL,NULL),(22,'Liora \"Zawadi\"','Kaume',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-05-06','F',NULL,NULL,NULL,4,1,'2022-04-14',NULL,NULL,NULL,'127006',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Liora \"Zawadi\"','Kaume','2013-05-06',NULL,0,1,NULL,NULL,NULL),(23,'Alana','Gonzalez-Gatica',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2013-01-15','F',NULL,NULL,NULL,4,1,'2022-05-14',NULL,NULL,NULL,'132487',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Alana','Gonzalez-Gatica','2013-01-15',NULL,0,1,NULL,NULL,NULL),(24,'Kherington ','Evans',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2011-08-31','F',NULL,NULL,NULL,4,1,'2022-04-30',NULL,NULL,NULL,'121789',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kherington ','Evans','2011-08-31',NULL,0,1,NULL,NULL,NULL),(25,'Addison ','Bradshaw',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2010-07-02','F',NULL,NULL,NULL,4,1,'2021-11-11',NULL,NULL,NULL,'123083',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Addison ','Bradshaw','2010-07-02',NULL,0,1,NULL,NULL,NULL),(26,'Jason','Aryal',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2010-01-14','M',NULL,NULL,NULL,4,1,'2021-09-23',NULL,NULL,NULL,'131306',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jason','Aryal','2010-01-14',NULL,0,1,NULL,NULL,NULL),(27,'Ezekiel ','Howard',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:04','2020-10-29 12:10:45','2009-10-12','M',NULL,NULL,NULL,4,1,'2023-01-12',NULL,NULL,NULL,'117353',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ezekiel ','Howard','2009-10-12',NULL,0,1,NULL,NULL,NULL),(28,'Haley','Hutto',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2010-01-26','F',NULL,NULL,NULL,4,1,'2022-06-30',NULL,NULL,NULL,'123068',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Haley','Hutto','2010-01-26',NULL,0,1,NULL,NULL,NULL),(29,'Khloe','Johnson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2009-09-28','F',NULL,NULL,NULL,4,1,'2022-05-08',NULL,NULL,NULL,'131920',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Khloe','Johnson','2009-09-28',NULL,0,1,NULL,NULL,NULL),(30,'Daniel','Kapologwe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2009-05-18','M',NULL,NULL,NULL,4,1,'2022-03-22',NULL,NULL,NULL,'126417',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Daniel','Kapologwe','2009-05-18',NULL,0,1,NULL,NULL,NULL),(31,'Sophia ','Lezama',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2009-02-02','F',NULL,NULL,NULL,4,1,'2023-01-24',NULL,NULL,NULL,'120143',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sophia ','Lezama','2009-02-02',NULL,0,1,NULL,NULL,NULL),(32,'Preston','Hughbanks',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2010-08-02','M',NULL,NULL,NULL,4,1,'2023-01-08',NULL,NULL,NULL,'129671',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Preston','Hughbanks','2010-08-02',NULL,0,1,NULL,NULL,NULL),(33,'Nicole','Marcelino',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2008-09-15','F',NULL,NULL,NULL,4,1,'2021-05-16',NULL,NULL,NULL,'129867',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nicole','Marcelino','2008-09-15',NULL,0,1,NULL,NULL,NULL),(34,'Kevin ','Martinez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:45','2009-02-20','M',NULL,NULL,NULL,4,1,'2021-02-10',NULL,NULL,NULL,'118439',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kevin ','Martinez','2009-02-20',NULL,0,1,NULL,NULL,NULL),(35,'Joshua','Murphy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2009-10-06','M',NULL,NULL,NULL,4,1,'2021-10-13',NULL,NULL,NULL,'124306',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Joshua','Murphy','2009-10-06',NULL,0,1,NULL,NULL,NULL),(36,'Eileen','Ngo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2008-12-04','F',NULL,NULL,NULL,4,1,'2022-04-26',NULL,NULL,NULL,'115261',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Eileen','Ngo','2008-12-04',NULL,0,1,NULL,NULL,NULL),(37,'Ellen','Ngo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2008-12-04','F',NULL,NULL,NULL,4,1,'2022-04-26',NULL,NULL,NULL,'115259',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ellen','Ngo','2008-12-04',NULL,0,1,NULL,NULL,NULL),(38,'Amelia','Nix',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2010-03-04','F',NULL,NULL,NULL,4,1,'2022-03-13',NULL,NULL,NULL,'117603',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Amelia','Nix','2010-03-04',NULL,0,1,NULL,NULL,NULL),(39,'Kobichukwurah','Okonkwo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2010-01-08','M',NULL,NULL,NULL,4,1,'2021-10-05',NULL,NULL,NULL,'119715',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kobichukwurah','Okonkwo','2010-01-08',NULL,0,1,NULL,NULL,NULL),(40,'Angel','Rodriguez Camarillo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2009-01-21','M',NULL,NULL,NULL,4,1,'2022-03-26',NULL,NULL,NULL,'118579',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Angel','Rodriguez Camarillo','2009-01-21',NULL,0,1,NULL,NULL,NULL),(41,'Mishael','Rogers',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2008-09-10','F',NULL,NULL,NULL,4,1,'2022-10-24',NULL,NULL,NULL,'127132',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mishael','Rogers','2008-09-10',NULL,0,1,NULL,NULL,NULL),(42,'Josue','Saravia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2010-06-21','M',NULL,NULL,NULL,4,1,'2021-12-18',NULL,NULL,NULL,'132721',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Josue','Saravia','2010-06-21',NULL,0,1,NULL,NULL,NULL),(43,'Selena ','Mendoza',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2009-12-27','F',NULL,NULL,NULL,4,1,'2022-02-13',NULL,NULL,NULL,'127478',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Selena ','Mendoza','2009-12-27',NULL,0,1,NULL,NULL,NULL),(44,'Raina','Tennant',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2010-02-23','F',NULL,NULL,NULL,4,1,'2021-03-23',NULL,NULL,NULL,'120028',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Raina','Tennant','2010-02-23',NULL,0,1,NULL,NULL,NULL),(45,'Genesis','Velazquez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2009-10-29','F',NULL,NULL,NULL,4,1,'2021-09-21',NULL,NULL,NULL,'121447',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Genesis','Velazquez','2009-10-29',NULL,0,1,NULL,NULL,NULL),(46,'Kyla','Flies',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2009-07-09','F',NULL,NULL,NULL,4,1,'2022-10-02',NULL,NULL,NULL,'135041',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kyla','Flies','2009-07-09',NULL,0,1,NULL,NULL,NULL),(47,'Carson','Scholz',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:09:05','2020-10-29 12:10:46','2009-11-13','M',NULL,NULL,NULL,4,1,'2021-10-09',NULL,NULL,NULL,'124642',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Carson','Scholz','2009-11-13',NULL,0,1,NULL,NULL,NULL),(48,'Haasim','Abdellah',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-01-13',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'130483',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Haasim','Abdellah','2013-01-13',NULL,0,1,NULL,NULL,NULL),(49,'Areesha','Abdulrahman',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-03-27',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'125039',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Areesha','Abdulrahman','2013-03-27',NULL,0,1,NULL,NULL,NULL),(50,'Mamoon','Abdulrahman',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-03-26',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'125040',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mamoon','Abdulrahman','2013-03-26',NULL,0,1,NULL,NULL,NULL),(51,'Levi','Ackles',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2010-09-28',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'119612',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Levi','Ackles','2010-09-28',NULL,0,1,NULL,NULL,NULL),(52,'Xavier','Adame',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2008-07-09','M',NULL,NULL,NULL,4,1,'2022-01-15',NULL,NULL,NULL,'118279',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Xavier','Adame','2008-07-09',NULL,0,1,NULL,NULL,NULL),(53,'Emanuel','Aguilar-Garcia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-04-08','M',NULL,NULL,NULL,4,1,'2022-01-30',NULL,NULL,NULL,'129569',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Emanuel','Aguilar-Garcia','2013-04-08',NULL,0,1,NULL,NULL,NULL),(54,'Shayan','Akhand',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2012-10-11',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'137946',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Shayan','Akhand','2012-10-11',NULL,0,1,NULL,NULL,NULL),(55,'Landree','Aldrich',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2012-04-03',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'121983',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Landree','Aldrich','2012-04-03',NULL,0,1,NULL,NULL,NULL),(56,'Yousef','Allan',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2011-02-11',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'130382',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Yousef','Allan','2011-02-11',NULL,0,1,NULL,NULL,NULL),(57,'Shawnwilliam','Ambe',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2015-06-21','M',NULL,NULL,NULL,4,1,'2022-12-15',NULL,NULL,NULL,'135545',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Shawnwilliam','Ambe','2015-06-21',NULL,0,1,NULL,NULL,NULL),(58,'William','Appleby',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2012-08-08','M',NULL,NULL,NULL,4,1,'2023-04-01',NULL,NULL,NULL,'128187',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'William','Appleby','2012-08-08',NULL,0,1,NULL,NULL,NULL),(59,'Jayleen','Arias',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2012-10-17','F',NULL,NULL,NULL,4,1,'2023-01-27',NULL,NULL,NULL,'131513',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jayleen','Arias','2012-10-17',NULL,0,1,NULL,NULL,NULL),(60,'Joseph','Atemkeng',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-09-25','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'127290',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Joseph','Atemkeng','2013-09-25',NULL,0,1,NULL,NULL,NULL),(61,'Ramlah','Athar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-05-07','F',NULL,NULL,NULL,4,1,'2021-09-04',NULL,NULL,NULL,'136377',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ramlah','Athar','2013-05-07',NULL,0,1,NULL,NULL,NULL),(62,'Braeden','Barnett',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2015-03-31','M',NULL,NULL,NULL,4,1,'2023-07-28',NULL,NULL,NULL,'130464',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Braeden','Barnett','2015-03-31',NULL,0,1,NULL,NULL,NULL),(63,'Magnus','Barrios',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-04-20','M',NULL,NULL,NULL,4,1,'2021-12-03',NULL,NULL,NULL,'131311',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Magnus','Barrios','2013-04-20',NULL,0,1,NULL,NULL,NULL),(64,'Kensley','Bishop',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2013-10-01',NULL,NULL,NULL,NULL,4,1,'2022-07-14',NULL,NULL,NULL,'133768',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kensley','Bishop','2013-10-01',NULL,0,1,NULL,NULL,NULL),(65,'Joliette','Brunet',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2015-05-09','F',NULL,NULL,NULL,4,1,'2022-05-08',NULL,NULL,NULL,'133955',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Joliette','Brunet','2015-05-09',NULL,0,1,NULL,NULL,NULL),(66,'Adrian','Campean',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:25','2020-10-29 12:10:46','2015-12-10',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'135162',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adrian','Campean','2015-12-10',NULL,0,1,NULL,NULL,NULL),(67,'Malachi','Carpenter',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2007-11-21','M',NULL,NULL,NULL,4,1,'2023-04-03',NULL,NULL,NULL,'118337',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Malachi','Carpenter','2007-11-21',NULL,0,1,NULL,NULL,NULL),(68,'Judah','Chacko',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-10-07',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'137849',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Judah','Chacko','2012-10-07',NULL,0,1,NULL,NULL,NULL),(69,'Scarlett','Chavez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2011-01-12','F',NULL,NULL,NULL,4,1,'2020-10-20',NULL,NULL,NULL,'129024',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Scarlett','Chavez','2011-01-12',NULL,0,1,NULL,NULL,NULL),(70,'Devin','Chirongoma',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2013-05-11','M',NULL,NULL,NULL,4,1,'2022-12-01',NULL,NULL,NULL,'130222',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Devin','Chirongoma','2013-05-11',NULL,0,1,NULL,NULL,NULL),(71,'Ryan','Clever',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2009-05-20','M',NULL,NULL,NULL,4,1,'2021-03-19',NULL,NULL,NULL,'126717',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ryan','Clever','2009-05-20',NULL,0,1,NULL,NULL,NULL),(72,'Sean','Colley',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2007-02-15','M',NULL,NULL,NULL,4,1,'2022-01-10',NULL,NULL,NULL,'121583',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sean','Colley','2007-02-15',NULL,0,1,NULL,NULL,NULL),(73,'Corey','Conyer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2006-10-25','M',NULL,NULL,NULL,4,1,'2021-05-20',NULL,NULL,NULL,'130592',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Corey','Conyer','2006-10-25',NULL,0,1,NULL,NULL,NULL),(74,'Luis','Cordero',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2014-08-22','M',NULL,NULL,NULL,4,1,'2023-04-01',NULL,NULL,NULL,'131042',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Luis','Cordero','2014-08-22',NULL,0,1,NULL,NULL,NULL),(75,'Kayden','Cortez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2007-11-08','M',NULL,NULL,NULL,4,1,'2021-04-27',NULL,NULL,NULL,'116127',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kayden','Cortez','2007-11-08',NULL,0,1,NULL,NULL,NULL),(76,'Robert','Cruz',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2016-10-01','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'135628',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Robert','Cruz','2016-10-01',NULL,0,1,NULL,NULL,NULL),(77,'Jotham','Daniel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2013-09-04',NULL,NULL,NULL,NULL,4,1,'2022-09-19',NULL,NULL,NULL,'127016',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jotham','Daniel','2013-09-04',NULL,0,1,NULL,NULL,NULL),(78,'Jacob','Daniel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2010-02-04','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'121835',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jacob','Daniel','2010-02-04',NULL,0,1,NULL,NULL,NULL),(79,'Calvin','Davis',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2013-01-18','M',NULL,NULL,NULL,4,1,'2022-03-27',NULL,NULL,NULL,'132550',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Calvin','Davis','2013-01-18',NULL,0,1,NULL,NULL,NULL),(80,'Evan','Davis',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2008-07-21','M',NULL,NULL,NULL,4,1,'2021-03-19',NULL,NULL,NULL,'125854',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Evan','Davis','2008-07-21',NULL,0,1,NULL,NULL,NULL),(81,'Liam','Daza',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2015-04-29','M',NULL,NULL,NULL,4,1,'2022-08-29',NULL,NULL,NULL,'130602',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Liam','Daza','2015-04-29',NULL,0,1,NULL,NULL,NULL),(82,'Hensley','Dodson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2010-11-28','M',NULL,NULL,NULL,4,1,'2023-05-10',NULL,NULL,NULL,'121026',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hensley','Dodson','2010-11-28',NULL,0,1,NULL,NULL,NULL),(83,'Ephraim','Douglas',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2008-08-26','M',NULL,NULL,NULL,4,1,'2022-05-06',NULL,NULL,NULL,'117555',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ephraim','Douglas','2008-08-26',NULL,0,1,NULL,NULL,NULL),(84,'Ditza','Duarte',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2006-05-18','F',NULL,NULL,NULL,4,1,'2021-05-09',NULL,NULL,NULL,'129520',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ditza','Duarte','2006-05-18',NULL,0,1,NULL,NULL,NULL),(85,'Jolynn','Elgersma',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2008-07-24','F',NULL,NULL,NULL,4,1,'2022-10-01',NULL,NULL,NULL,'121414',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jolynn','Elgersma','2008-07-24',NULL,0,1,NULL,NULL,NULL),(86,'Azlan','Faraz',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2015-02-20','M',NULL,NULL,NULL,4,1,'2021-02-01',NULL,NULL,NULL,'130412',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Azlan','Faraz','2015-02-20',NULL,0,1,NULL,NULL,NULL),(87,'Yaphet','Fekadu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2014-07-21',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'129860',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Yaphet','Fekadu','2014-07-21',NULL,0,1,NULL,NULL,NULL),(88,'Nathan','Fekadu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2014-07-21','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'129850',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nathan','Fekadu','2014-07-21',NULL,0,1,NULL,NULL,NULL),(89,'Luke','Fuller',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2006-08-21','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'103039',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Luke','Fuller','2006-08-21',NULL,0,1,NULL,NULL,NULL),(90,'Connor','Gannaway',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-03-13','M',NULL,NULL,NULL,4,1,'2022-09-30',NULL,NULL,NULL,'134920',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Connor','Gannaway','2012-03-13',NULL,0,1,NULL,NULL,NULL),(91,'Andrea','Garcia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2006-11-04','F',NULL,NULL,NULL,4,1,'2022-03-25',NULL,NULL,NULL,'133264',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Andrea','Garcia','2006-11-04',NULL,0,1,NULL,NULL,NULL),(92,'Azriel','Garcia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2009-11-15','M',NULL,NULL,NULL,4,1,'2021-02-12',NULL,NULL,NULL,'126275',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Azriel','Garcia','2009-11-15',NULL,0,1,NULL,NULL,NULL),(93,'Austin','Gaudet',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2017-02-23',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'136205',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Austin','Gaudet','2017-02-23',NULL,0,1,NULL,NULL,NULL),(94,'Gianluca \"Gigi\"','Gimmillaro',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-06-23','M',NULL,NULL,NULL,4,1,'2021-03-19',NULL,NULL,NULL,'130266',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gianluca \"Gigi\"','Gimmillaro','2012-06-23',NULL,0,1,NULL,NULL,NULL),(95,'Jonah','Gonzales',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2011-08-23','M',NULL,NULL,NULL,4,1,'2022-10-18',NULL,NULL,NULL,'125625',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jonah','Gonzales','2011-08-23',NULL,0,1,NULL,NULL,NULL),(96,'Manveer','Grewal',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2011-12-21','M',NULL,NULL,NULL,4,1,'2021-04-30',NULL,NULL,NULL,'124603',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Manveer','Grewal','2011-12-21',NULL,0,1,NULL,NULL,NULL),(97,'Adley','Hardy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-08-05','F',NULL,NULL,NULL,4,1,'2021-12-11',NULL,NULL,NULL,'127666',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adley','Hardy','2012-08-05',NULL,0,1,NULL,NULL,NULL),(98,'David','Hernandez-Salmoran',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2011-09-17','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'137342',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'David','Hernandez-Salmoran','2011-09-17',NULL,0,1,NULL,NULL,NULL),(99,'Sophia','Horton',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2013-11-30','F',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'129973',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sophia','Horton','2013-11-30',NULL,0,1,NULL,NULL,NULL),(100,'Joshiah','Howard',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2005-10-07','M',NULL,NULL,NULL,4,1,'2023-04-03',NULL,NULL,NULL,'124028',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Joshiah','Howard','2005-10-07',NULL,0,1,NULL,NULL,NULL),(101,'Gabriel','Howard',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-09-12','M',NULL,NULL,NULL,4,1,'2021-09-21',NULL,NULL,NULL,'132456',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gabriel','Howard','2012-09-12',NULL,0,1,NULL,NULL,NULL),(102,'Gavin','Hurley',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2008-07-12','M',NULL,NULL,NULL,4,1,'2021-03-05',NULL,NULL,NULL,'117814',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gavin','Hurley','2008-07-12',NULL,0,1,NULL,NULL,NULL),(103,'Khang','Huynh',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2010-06-13','M',NULL,NULL,NULL,4,1,'2021-05-03',NULL,NULL,NULL,'122561',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Khang','Huynh','2010-06-13',NULL,0,1,NULL,NULL,NULL),(104,'Rida','Jiwani',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2009-03-22','F',NULL,NULL,NULL,4,1,'2021-03-19',NULL,NULL,NULL,'119710',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Rida','Jiwani','2009-03-22',NULL,0,1,NULL,NULL,NULL),(105,'Christopher ','Jones',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2014-07-23','M',NULL,NULL,NULL,4,1,'2022-10-17',NULL,NULL,NULL,'135214',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Christopher ','Jones','2014-07-23',NULL,0,1,NULL,NULL,NULL),(106,'Navroop','Kaur',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2015-04-26','F',NULL,NULL,NULL,4,1,'2022-11-14',NULL,NULL,NULL,'131552',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Navroop','Kaur','2015-04-26',NULL,0,1,NULL,NULL,NULL),(107,'Kayla','Chavez','','','','',NULL,NULL,'',NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-08-22','F',NULL,'','',4,1,'2023-01-30',NULL,NULL,'','129484','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kayla','Chavez','2012-08-22','',0,1,'',NULL,NULL),(108,'Yousuf','Kazi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2008-09-26',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'119339',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Yousuf','Kazi','2008-09-26',NULL,0,1,NULL,NULL,NULL),(109,'Darryl','King',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2006-08-14','M',NULL,NULL,NULL,4,1,'2022-05-20',NULL,NULL,NULL,'103449',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Darryl','King','2006-08-14',NULL,0,1,NULL,NULL,NULL),(110,'Aaron','Knight',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2014-07-30','M',NULL,NULL,NULL,4,1,'2022-05-03',NULL,NULL,NULL,'129621',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aaron','Knight','2014-07-30',NULL,0,1,NULL,NULL,NULL),(111,'Mia','Kukic',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-12-24','F',NULL,NULL,NULL,4,1,'2022-04-16',NULL,NULL,NULL,'133486',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mia','Kukic','2012-12-24',NULL,0,1,NULL,NULL,NULL),(112,'Lincoln','Lavanh',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2012-09-11','M',NULL,NULL,NULL,4,1,'2021-01-21',NULL,NULL,NULL,'128829',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lincoln','Lavanh','2012-09-11',NULL,0,1,NULL,NULL,NULL),(113,'Nathan','Le',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:26','2020-10-29 12:10:46','2007-05-27','M',NULL,NULL,NULL,4,1,'2021-11-28',NULL,NULL,NULL,'117477',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nathan','Le','2007-05-27',NULL,0,1,NULL,NULL,NULL),(114,'Manuel','Leguizamo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2014-03-19','M',NULL,NULL,NULL,4,1,'2023-02-03',NULL,NULL,NULL,'132349',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Manuel','Leguizamo','2014-03-19',NULL,0,1,NULL,NULL,NULL),(115,'Lukas','Light',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2008-05-01','M',NULL,NULL,NULL,4,1,'2023-02-19',NULL,NULL,NULL,'117732',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lukas','Light','2008-05-01',NULL,0,1,NULL,NULL,NULL),(116,'Nathan','Lyall',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2015-05-28','M',NULL,NULL,NULL,4,1,'2022-01-07',NULL,NULL,NULL,'133090',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nathan','Lyall','2015-05-28',NULL,0,1,NULL,NULL,NULL),(117,'Charlie','Mancilla',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2011-03-07','M',NULL,NULL,NULL,4,1,'2021-03-07',NULL,NULL,NULL,'128499',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Charlie','Mancilla','2011-03-07',NULL,0,1,NULL,NULL,NULL),(118,'Adam','Martin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2012-07-12','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'128042',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adam','Martin','2012-07-12',NULL,0,1,NULL,NULL,NULL),(119,'Thalia','Martinez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2013-05-06','F',NULL,NULL,NULL,4,1,'2022-10-17',NULL,NULL,NULL,'131253',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Thalia','Martinez','2013-05-06',NULL,0,1,NULL,NULL,NULL),(120,'Juan','Martinez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2012-01-09','M',NULL,NULL,NULL,4,1,'2021-11-25',NULL,NULL,NULL,'128604',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Juan','Martinez','2012-01-09',NULL,0,1,NULL,NULL,NULL),(121,'Ethan','Marucut',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2006-09-10','M',NULL,NULL,NULL,4,1,'2023-04-29',NULL,NULL,NULL,'126342',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ethan','Marucut','2006-09-10',NULL,0,1,NULL,NULL,NULL),(122,'Ian Carl','Marucut',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2013-02-24','M',NULL,NULL,NULL,4,1,'2023-04-22',NULL,NULL,NULL,'127549',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ian Carl','Marucut','2013-02-24',NULL,0,1,NULL,NULL,NULL),(123,'Mason','Yow','','','','',NULL,NULL,'',NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2011-12-19','M',NULL,'','',4,1,'2022-12-19',NULL,NULL,'','128486','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mason','Yow','2011-12-19','',0,1,'',NULL,NULL),(124,'Landry','McCall',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2008-12-23','M',NULL,NULL,NULL,4,1,'2021-02-05',NULL,NULL,NULL,'120676',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Landry','McCall','2008-12-23',NULL,0,1,NULL,NULL,NULL),(125,'Reagan','McCall',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2008-12-23','F',NULL,NULL,NULL,4,1,'2021-02-05',NULL,NULL,NULL,'120677',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Reagan','McCall','2008-12-23',NULL,0,1,NULL,NULL,NULL),(126,'Nathan','Mekonnen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2014-02-15','M',NULL,NULL,NULL,4,1,'2023-02-06',NULL,NULL,NULL,'134171',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nathan','Mekonnen','2014-02-15',NULL,0,1,NULL,NULL,NULL),(127,'Kaleb','Mekonnen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2014-02-15','M',NULL,NULL,NULL,4,1,'2023-02-06',NULL,NULL,NULL,'134172',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kaleb','Mekonnen','2014-02-15',NULL,0,1,NULL,NULL,NULL),(128,'Emmanuel','Mendoza',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2006-11-02','M',NULL,NULL,NULL,4,1,'2022-04-14',NULL,NULL,NULL,'127477',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Emmanuel','Mendoza','2006-11-02',NULL,0,1,NULL,NULL,NULL),(129,'Sydney','Mensch',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2013-02-02','F',NULL,NULL,NULL,4,1,'2022-09-09',NULL,NULL,NULL,'133916',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sydney','Mensch','2013-02-02',NULL,0,1,NULL,NULL,NULL),(130,'Birdie','Miller',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2013-07-06','F',NULL,NULL,NULL,4,1,'2022-01-25',NULL,NULL,NULL,'131267',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Birdie','Miller','2013-07-06',NULL,0,1,NULL,NULL,NULL),(131,'Morgan','Molly',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2013-04-18','F',NULL,NULL,NULL,4,1,'2021-11-13',NULL,NULL,NULL,'131074',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Morgan','Molly','2013-04-18',NULL,0,1,NULL,NULL,NULL),(132,'Ricardo','Mora',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:46','2013-07-19','M',NULL,NULL,NULL,4,1,'2023-01-29',NULL,NULL,NULL,'132491',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ricardo','Mora','2013-07-19',NULL,0,1,NULL,NULL,NULL),(133,'Mathias','Nebiyou','','','','',NULL,NULL,'',NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2015-06-16','M',NULL,'','',4,1,'2022-11-14',NULL,NULL,'','132827','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mathias','Nebiyou','2015-06-16','',0,1,'','',''),(134,'Shivi','Negi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2006-06-22','F',NULL,NULL,NULL,4,1,'2023-04-27',NULL,NULL,NULL,'111661',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Shivi','Negi','2006-06-22',NULL,0,1,NULL,NULL,NULL),(135,'Trinity','Nguyen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2007-11-21','F',NULL,NULL,NULL,4,1,'2022-11-01',NULL,NULL,NULL,'103408',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Trinity','Nguyen','2007-11-21',NULL,0,1,NULL,NULL,NULL),(136,'Brianna','Nguyen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2014-11-22','F',NULL,NULL,NULL,4,1,'2023-01-20',NULL,NULL,NULL,'135455',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brianna','Nguyen','2014-11-22',NULL,0,1,NULL,NULL,NULL),(137,'Justin','Nguyen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2011-02-02','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'137354',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Justin','Nguyen','2011-02-02',NULL,0,1,NULL,NULL,NULL),(138,'Brian','Nwandilobi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2010-10-20','M',NULL,NULL,NULL,4,1,'2022-03-17',NULL,NULL,NULL,'119659',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brian','Nwandilobi','2010-10-20',NULL,0,1,NULL,NULL,NULL),(139,'Galilea','Ontiveros Salazar',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2015-05-23','F',NULL,NULL,NULL,4,1,'2021-05-21',NULL,NULL,NULL,'131266',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Galilea','Ontiveros Salazar','2015-05-23',NULL,0,1,NULL,NULL,NULL),(140,'Faith','Ozagu',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2010-11-12','F',NULL,NULL,NULL,4,1,'2022-03-08',NULL,NULL,NULL,'119708',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Faith','Ozagu','2010-11-12',NULL,0,1,NULL,NULL,NULL),(141,'Frida ','Perez Rodriguez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2006-03-27','F',NULL,NULL,NULL,4,1,'2023-03-02',NULL,NULL,NULL,'112161',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Frida ','Perez Rodriguez','2006-03-27',NULL,0,1,NULL,NULL,NULL),(142,'Issac','Phan',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2012-07-13','M',NULL,NULL,NULL,4,1,'2023-03-02',NULL,NULL,NULL,'128395',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Issac','Phan','2012-07-13',NULL,0,1,NULL,NULL,NULL),(143,'Nolan','Pho',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2014-03-22','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'131619',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nolan','Pho','2014-03-22',NULL,0,1,NULL,NULL,NULL),(144,'Robert','Placke',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2006-09-04','M',NULL,NULL,NULL,4,1,'2022-04-21',NULL,NULL,NULL,'119858',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Robert','Placke','2006-09-04',NULL,0,1,NULL,NULL,NULL),(145,'Audrina','Postel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2011-01-06','F',NULL,NULL,NULL,4,1,'2022-10-18',NULL,NULL,NULL,'123881',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Audrina','Postel','2011-01-06',NULL,0,1,NULL,NULL,NULL),(146,'Mysha','Qureshi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2008-10-20','F',NULL,NULL,NULL,4,1,'2023-04-22',NULL,NULL,NULL,'136220',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mysha','Qureshi','2008-10-20',NULL,0,1,NULL,NULL,NULL),(147,'Stefen','Ray',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2013-07-19','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'120751',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Stefen','Ray','2013-07-19',NULL,0,1,NULL,NULL,NULL),(148,'Rennah','McCommas','','','','',NULL,NULL,'',NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2012-08-27','F',NULL,'','',4,1,'2023-05-03',NULL,NULL,'','128309','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Rennah','McCommas','2012-08-27','',0,1,'',NULL,NULL),(149,'John','Rigby',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2004-12-16','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'98884',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'John','Rigby','2004-12-16',NULL,0,1,NULL,NULL,NULL),(150,'Harrison','Ring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2013-03-27','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'125038',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Harrison','Ring','2013-03-27',NULL,0,1,NULL,NULL,NULL),(151,'Abiha \"Daanya\"','Rizvi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2012-11-04',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'124619',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Abiha \"Daanya\"','Rizvi','2012-11-04',NULL,0,1,NULL,NULL,NULL),(152,'Tatiana','Rodriguez',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2011-11-11','F',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'121995',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tatiana','Rodriguez','2011-11-11',NULL,0,1,NULL,NULL,NULL),(153,'Lucca','Romano',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2013-07-28','M',NULL,NULL,NULL,4,1,'2022-01-16',NULL,NULL,NULL,'127430',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lucca','Romano','2013-07-28',NULL,0,1,NULL,NULL,NULL),(154,'Safaa','Saleem',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2013-10-09','F',NULL,NULL,NULL,4,1,'2022-04-16',NULL,NULL,NULL,'131739',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Safaa','Saleem','2013-10-09',NULL,0,1,NULL,NULL,NULL),(155,'Ziyad','Saleem',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2012-07-16','M',NULL,NULL,NULL,4,1,'2021-04-10',NULL,NULL,NULL,'131280',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ziyad','Saleem','2012-07-16',NULL,0,1,NULL,NULL,NULL),(156,'Irfan','Saleh',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2008-05-03',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'115150',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Irfan','Saleh','2008-05-03',NULL,0,1,NULL,NULL,NULL),(157,'Farhan','Saleh',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2008-05-03',NULL,NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'115149',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Farhan','Saleh','2008-05-03',NULL,0,1,NULL,NULL,NULL),(158,'Diego','Santamaria',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2005-12-29','M',NULL,NULL,NULL,4,1,'2020-12-07',NULL,NULL,NULL,'129955',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Diego','Santamaria','2005-12-29',NULL,0,1,NULL,NULL,NULL),(159,'Roman','Smith',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2005-11-16','M',NULL,NULL,NULL,4,1,'2021-05-15',NULL,NULL,NULL,'103528',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Roman','Smith','2005-11-16',NULL,0,1,NULL,NULL,NULL),(160,'Isaiah \"Miles\"','Stephens',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2011-12-08','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'128298',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Isaiah \"Miles\"','Stephens','2011-12-08',NULL,0,1,NULL,NULL,NULL),(161,'Jayden','Thompson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:27','2020-10-29 12:10:47','2013-08-13','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'136758',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jayden','Thompson','2013-08-13',NULL,0,1,NULL,NULL,NULL),(162,'Scott','Trannguyen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2014-09-23','M',NULL,NULL,NULL,4,1,'2021-01-08',NULL,NULL,NULL,'130105',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Scott','Trannguyen','2014-09-23',NULL,0,1,NULL,NULL,NULL),(163,'Ram','Vekaria',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2015-02-19','M',NULL,NULL,NULL,4,1,'2021-04-01',NULL,NULL,NULL,'130529',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ram','Vekaria','2015-02-19',NULL,0,1,NULL,NULL,NULL),(164,'Katarina','Vicks',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2011-04-27','F',NULL,NULL,NULL,4,1,'2021-10-09',NULL,NULL,NULL,'125516',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Katarina','Vicks','2011-04-27',NULL,0,1,NULL,NULL,NULL),(165,'Jacqueline','Vidal',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2010-04-20','F',NULL,NULL,NULL,4,1,'2023-04-24',NULL,NULL,NULL,'121446',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jacqueline','Vidal','2010-04-20',NULL,0,1,NULL,NULL,NULL),(166,'Logan','Vo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2008-07-04','M',NULL,NULL,NULL,4,1,'2021-05-15',NULL,NULL,NULL,'116285',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Logan','Vo','2008-07-04',NULL,0,1,NULL,NULL,NULL),(167,'Donald','Walts',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2013-11-21','M',NULL,NULL,NULL,4,1,'2022-12-04',NULL,NULL,NULL,'137772',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Donald','Walts','2013-11-21',NULL,0,1,NULL,NULL,NULL),(168,'Derek','Whitlow',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2016-02-27','M',NULL,NULL,NULL,4,1,'2022-01-28',NULL,NULL,NULL,'133239',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Derek','Whitlow','2016-02-27',NULL,0,1,NULL,NULL,NULL),(169,'Kylah','Wilson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2012-06-13','F',NULL,NULL,NULL,4,1,'2022-01-08',NULL,NULL,NULL,'129334',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kylah','Wilson','2012-06-13',NULL,0,1,NULL,NULL,NULL),(170,'Jackson','Wright',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2010-02-07','M',NULL,NULL,NULL,4,1,NULL,NULL,NULL,NULL,'135223',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jackson','Wright','2010-02-07',NULL,0,1,NULL,NULL,NULL),(171,'Gavin','Yow',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-08-19 19:53:28','2020-10-29 12:10:47','2009-10-09','M',NULL,NULL,NULL,4,1,'2021-02-17',NULL,NULL,NULL,'123615',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gavin','Yow','2009-10-09',NULL,0,1,NULL,NULL,NULL),(172,'Alan','Vargas','','','','',NULL,NULL,'',NULL,0,0,'2020-08-19 19:54:41','2020-10-29 12:10:47','2014-09-13','M',NULL,'','',4,1,'2022-02-07',NULL,NULL,'','133252','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Alan','Vargas','2014-09-13','',0,1,'','',''),(173,'Natalie','Martinez','','','','',NULL,NULL,'',NULL,0,0,'2020-08-26 19:09:34','2020-10-29 12:10:47','2009-08-04','F',NULL,'','',4,1,NULL,NULL,NULL,'','118534','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Natalie','Martinez','2009-08-04','',0,1,'',NULL,NULL),(174,'Mahi','Patel','','','','',NULL,NULL,'',NULL,0,0,'2020-08-26 19:10:17','2020-10-29 12:10:47','2009-06-26','F',NULL,'','',4,1,'2019-01-17',NULL,NULL,'','134421','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mahi','Patel','2009-06-26','',0,1,'','',''),(175,'Adam','Adams','','','','',NULL,NULL,'',NULL,0,0,'2020-08-31 09:06:57','2020-08-31 09:06:57','2010-01-05','M',NULL,'','',1,1,'2021-08-01',NULL,NULL,'','','',1,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adam','Adams','2010-01-05','',0,1,'',NULL,NULL),(176,'Luke ','Peterson','','','','',NULL,NULL,'',NULL,0,0,'2020-09-03 17:59:18','2020-10-29 12:10:47','2011-10-17','M',NULL,'','',4,1,'2021-10-11',NULL,NULL,'','121939','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Luke','Peterson','2011-10-17','',0,1,'',NULL,NULL),(177,'Pierce','Saulter','','','','',NULL,NULL,'',NULL,0,0,'2020-09-03 18:00:52','2020-10-29 12:10:47','2011-03-09','M',NULL,'','',4,1,'2022-12-17',NULL,NULL,'','134374','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Pierce','Saulter','2011-03-09','',0,1,'',NULL,NULL),(178,'Myla','Holthusen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2015-03-02','F',NULL,NULL,NULL,11,1,'2023-03-04',NULL,NULL,NULL,'10774',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Myla','Holthusen','2015-03-02',NULL,0,1,NULL,NULL,NULL),(179,'Jasmine','Needham',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2012-08-04','F',NULL,NULL,NULL,11,1,'2021-11-29',NULL,NULL,NULL,'10891',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jasmine','Needham','2012-08-04',NULL,0,1,NULL,NULL,NULL),(180,'Sabian','Needham',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2011-07-10','M',NULL,NULL,NULL,11,1,'2021-11-29',NULL,NULL,NULL,'10890',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sabian','Needham','2011-07-10',NULL,0,1,NULL,NULL,NULL),(181,'Brayden','Neuschwander',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2013-05-29','M',NULL,NULL,NULL,11,1,'2022-04-16',NULL,NULL,NULL,'10731',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brayden','Neuschwander','2013-05-29',NULL,0,1,NULL,NULL,NULL),(182,'Mauer','Nordby',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2013-03-27','M',NULL,NULL,NULL,11,1,'2023-03-19',NULL,NULL,NULL,'10730',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mauer','Nordby','2013-03-27',NULL,0,1,NULL,NULL,NULL),(183,'Kyla Jo','Nordvick',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2014-01-13','F',NULL,NULL,NULL,11,1,'2020-11-19',NULL,NULL,NULL,'10746',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kyla Jo','Nordvick','2014-01-13',NULL,0,1,NULL,NULL,NULL),(184,'Autumn ','Osse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2009-08-03','F',NULL,NULL,NULL,11,1,'2021-02-12',NULL,NULL,NULL,'10704',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Autumn ','Osse','2009-08-03',NULL,0,1,NULL,NULL,NULL),(185,'Parker','Osse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2012-02-09','M',NULL,NULL,NULL,11,1,'2022-02-07',NULL,NULL,NULL,'10706',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Parker','Osse','2012-02-09',NULL,0,1,NULL,NULL,NULL),(186,'Chloe','Osse',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:20','2020-10-29 12:10:47','2004-12-25','F',NULL,NULL,NULL,11,1,'2020-10-26',NULL,NULL,NULL,'10669',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Chloe','Osse','2004-12-25',NULL,0,1,NULL,NULL,NULL),(187,'Silas','Symington',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2010-05-11','M',NULL,NULL,NULL,5,1,'2022-01-17',NULL,NULL,NULL,'2346776632',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Silas','Symington','2010-05-11',NULL,0,1,NULL,NULL,NULL),(188,'Joshua','Bakke',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2012-05-08','M',NULL,NULL,NULL,5,1,'2023-03-04',NULL,NULL,NULL,'202573',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Joshua','Bakke','2012-05-08',NULL,0,1,NULL,NULL,NULL),(189,'Katelyn','Gatheridge',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2008-03-18','F',NULL,NULL,NULL,5,1,NULL,NULL,NULL,NULL,'202163',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Katelyn','Gatheridge','2008-03-18',NULL,0,1,NULL,NULL,NULL),(190,'Espen','Haugstad',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2012-02-10','M',NULL,NULL,NULL,5,1,'2021-01-14',NULL,NULL,NULL,'202292',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Espen','Haugstad','2012-02-10',NULL,0,1,NULL,NULL,NULL),(191,'Knox','Peterson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2011-11-03','M',NULL,NULL,NULL,5,1,'2021-05-10',NULL,NULL,NULL,'202296',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Knox','Peterson','2011-11-03',NULL,0,1,NULL,NULL,NULL),(192,'Ireland','Preble',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2014-02-20','F',NULL,NULL,NULL,5,1,'2021-01-26',NULL,NULL,NULL,'202452',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ireland','Preble','2014-02-20',NULL,0,1,NULL,NULL,NULL),(193,'Lily','Jans',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:32','2020-10-29 12:10:47','2015-05-13','F',NULL,NULL,NULL,5,1,'2021-04-29',NULL,NULL,NULL,'202480',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lily','Jans','2015-05-13',NULL,0,1,NULL,NULL,NULL),(194,'Toby','Alme',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2012-11-29','M',NULL,NULL,NULL,6,1,'2022-01-08',NULL,NULL,NULL,'310469',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Toby','Alme','2012-11-29',NULL,0,1,NULL,NULL,NULL),(195,'Dahlia','Carlson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2014-05-30','F',NULL,NULL,NULL,6,1,'2021-03-22',NULL,NULL,NULL,'320470',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dahlia','Carlson','2014-05-30',NULL,0,1,NULL,NULL,NULL),(196,'Sophie','Fredrickson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2012-03-06','F',NULL,NULL,NULL,6,1,'2022-02-10',NULL,NULL,NULL,'300348',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sophie','Fredrickson','2012-03-06',NULL,0,1,NULL,NULL,NULL),(197,'Eli','Gohman',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2014-05-26','M',NULL,NULL,NULL,6,1,'2022-12-19',NULL,NULL,NULL,'320511',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Eli','Gohman','2014-05-26',NULL,0,1,NULL,NULL,NULL),(198,'Leah','Lupien',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2014-07-29','F',NULL,NULL,NULL,6,1,'2022-02-27',NULL,NULL,NULL,'320461',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Leah','Lupien','2014-07-29',NULL,0,1,NULL,NULL,NULL),(199,'Gage ','Nordin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2013-12-20','M',NULL,NULL,NULL,6,1,'2021-03-13',NULL,NULL,NULL,'320414',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gage ','Nordin','2013-12-20',NULL,0,1,NULL,NULL,NULL),(200,'Vincent','Peterson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2013-03-07','M',NULL,NULL,NULL,6,1,'2020-10-15',NULL,NULL,NULL,'310475',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Vincent','Peterson','2013-03-07',NULL,0,1,NULL,NULL,NULL),(201,'Ty','Schmaltz',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2013-06-26','M',NULL,NULL,NULL,6,1,'2022-04-29',NULL,NULL,NULL,'467031',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ty','Schmaltz','2013-06-26',NULL,0,1,NULL,NULL,NULL),(202,'Ellie','Sjostrand',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2007-04-05','F',NULL,NULL,NULL,6,1,'2022-03-24',NULL,NULL,NULL,'250303',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ellie','Sjostrand','2007-04-05',NULL,0,1,NULL,NULL,NULL),(203,'Carter','Sojolik',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2010-09-24','M',NULL,NULL,NULL,6,1,'2023-03-15',NULL,NULL,NULL,'300427',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Carter','Sojolik','2010-09-24',NULL,0,1,NULL,NULL,NULL),(204,'Courtney','Webster',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2007-11-12','F',NULL,NULL,NULL,6,1,'2021-05-01',NULL,NULL,NULL,'260486',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Courtney','Webster','2007-11-12',NULL,0,1,NULL,NULL,NULL),(205,'Samuel','Webster',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:43','2020-10-29 12:10:47','2009-03-23','M',NULL,NULL,NULL,6,1,'2021-05-01',NULL,NULL,NULL,'270486',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Samuel','Webster','2009-03-23',NULL,0,1,NULL,NULL,NULL),(206,'Adam','Berg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2012-03-18','M',NULL,NULL,NULL,8,1,'2022-02-14',NULL,NULL,NULL,'25362',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adam','Berg','2012-03-18',NULL,0,1,NULL,NULL,NULL),(207,'Isabella','Flodstrom',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2011-05-06','F',NULL,NULL,NULL,8,1,'2022-11-21',NULL,NULL,NULL,'25774',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Isabella','Flodstrom','2011-05-06',NULL,0,1,NULL,NULL,NULL),(208,'Levi','Lund',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2012-02-15','M',NULL,NULL,NULL,8,1,'2022-02-12',NULL,NULL,NULL,'25554',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Levi','Lund','2012-02-15',NULL,0,1,NULL,NULL,NULL),(209,'Aaden','Guerrero',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2010-07-06','M',NULL,NULL,NULL,8,1,'2022-03-29',NULL,NULL,NULL,'25466',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aaden','Guerrero','2010-07-06',NULL,0,1,NULL,NULL,NULL),(210,'Jacob','Merrill',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2008-04-02','M',NULL,NULL,NULL,8,1,'2021-03-06',NULL,NULL,NULL,'25358',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jacob','Merrill','2008-04-02',NULL,0,1,NULL,NULL,NULL),(211,'Randy','Schoon',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2009-06-19','M',NULL,NULL,NULL,8,1,'2021-01-18',NULL,NULL,NULL,'25435',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Randy','Schoon','2009-06-19',NULL,0,1,NULL,NULL,NULL),(212,'Caden','Audette',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-03 18:55:55','2020-10-29 12:10:47','2007-01-10','M',NULL,NULL,NULL,8,1,'2022-11-05',NULL,NULL,NULL,'25330',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Caden','Audette','2007-01-10',NULL,0,1,NULL,NULL,NULL),(213,'Nathaniel','Reyes','','','','',NULL,NULL,'',NULL,0,0,'2020-09-05 02:32:39','2020-10-29 12:10:47','2008-07-16','M',NULL,'','',4,1,'2022-05-02',NULL,NULL,'','118862','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nathaniel','Reyes','2008-07-16','',0,1,'','',''),(214,'Genesis','Zelaya','','','','',NULL,NULL,'',NULL,0,0,'2020-09-05 02:34:15','2020-10-29 12:10:47','2007-03-18','F',NULL,'','',4,1,'2021-09-17',NULL,NULL,'','137377','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Genesis','Zenlaya','2007-03-18','',0,1,'','',''),(215,'Mara','Hapka','','','','',NULL,NULL,'',NULL,0,0,'2020-09-08 19:59:59','2020-10-29 12:10:47','2015-04-09','F',NULL,'','',5,1,'2022-05-19',NULL,NULL,'','202546','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mara','Hapka','2015-04-09','',0,1,'',NULL,NULL),(216,'Olga','Mendoza','','','','',NULL,NULL,'',NULL,0,0,'2020-09-08 20:01:24','2020-10-29 12:10:47','2008-02-28','F',NULL,'','',5,1,NULL,NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Olga','Mendoza','2008-02-28','',0,1,'',NULL,NULL),(217,'Carolynn','Armstrong',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2003-12-09','F',NULL,NULL,NULL,13,1,'2023-09-29',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Carolynn','Armstrong','2003-12-09',NULL,0,1,NULL,NULL,NULL),(218,'Philip','Armstrong',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2005-01-08','M',NULL,NULL,NULL,13,1,'2023-09-29',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Philip','Armstrong','2005-01-08',NULL,0,1,NULL,NULL,NULL),(219,'Ronnie','Bogden',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2007-02-22','M',NULL,NULL,NULL,13,1,'2022-01-28',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ronnie','Bogden','2007-02-22',NULL,0,1,NULL,NULL,NULL),(220,'Gabe','Butcherine',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2004-01-27','M',NULL,NULL,NULL,13,1,'2022-01-06',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gabe','Butcherine','2004-01-27',NULL,0,1,NULL,NULL,NULL),(221,'Connor','Ciavarella',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2005-05-13','M',NULL,NULL,NULL,13,1,'2022-12-15',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Connor','Ciavarella','2005-05-13',NULL,0,1,NULL,NULL,NULL),(222,'Hayden','Cline',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2007-01-19','M',NULL,NULL,NULL,13,1,'2021-01-08',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hayden','Cline','2007-01-19',NULL,0,1,NULL,NULL,NULL),(223,'Liam','McMahan ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2007-03-18','M',NULL,NULL,NULL,13,1,'2022-12-07',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Liam','McMahan ','2007-03-18',NULL,0,1,NULL,NULL,NULL),(224,'Zachary','Stacy',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:47','2005-05-11','M',NULL,NULL,NULL,13,1,'2020-11-30',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Zachary','Stacy','2005-05-11',NULL,0,1,NULL,NULL,NULL),(225,'Grant','Wiscott',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-09 16:56:28','2020-10-29 12:10:48','2007-04-29','M',NULL,NULL,NULL,13,1,'2023-01-20',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Grant','Wiscott','2007-04-29',NULL,0,1,NULL,NULL,NULL),(226,'Aiden','Symington','','','','',NULL,NULL,'',NULL,0,0,'2020-09-10 17:24:23','2020-10-29 12:10:48','2011-01-06','M',NULL,'','',5,1,'2021-12-29',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aiden','Symington','2011-01-06','',0,1,'',NULL,NULL),(227,'Brooke','Mcdonald','','','','',NULL,NULL,'',NULL,0,0,'2020-09-11 17:48:11','2020-10-29 12:10:48','2014-02-12','F',NULL,'','',5,1,'2022-03-03',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brooke','Mcdonald','2014-02-12','',0,1,'',NULL,NULL),(228,'Austin','Bonneau',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2006-02-05','M',NULL,NULL,'1019533056',18,1,'2022-03-28',NULL,NULL,NULL,'1019533056',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Austin','Bonneau','2006-02-05',NULL,0,1,NULL,NULL,NULL),(229,'Thomas','Brown',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2013-02-16','M',NULL,NULL,NULL,18,1,'2021-04-05',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Thomas','Brown','2013-02-16',NULL,0,1,NULL,NULL,NULL),(230,'Miko','Davis-Karczewski',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2013-09-18','M',NULL,NULL,'4055388211',18,1,'2022-04-29',NULL,NULL,NULL,'4055388211',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Miko','Davis-Karczewski','2013-09-18',NULL,0,1,NULL,NULL,NULL),(231,'Alan','Gonstead',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2014-06-28','M',NULL,NULL,NULL,18,1,'2022-05-13',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Alan','Gonstead','2014-06-28',NULL,0,1,NULL,NULL,NULL),(232,'Ariel','Gurda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2004-07-25','F',NULL,NULL,'1017960062',18,1,'2020-05-25',NULL,NULL,NULL,'1017960062',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ariel','Gurda','2004-07-25',NULL,0,1,NULL,NULL,NULL),(233,'Emmanuel','Gurda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2006-05-28','M',NULL,NULL,'1021544744',18,1,'2022-02-04',NULL,NULL,NULL,'1021544744',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Emmanuel','Gurda','2006-05-28',NULL,0,1,NULL,NULL,NULL),(234,'Kyler','Lindgren',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2012-04-09','M',NULL,NULL,'4176548855',18,1,'2023-03-08',NULL,NULL,NULL,'4176548855',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Kyler','Lindgren','2012-04-09',NULL,0,1,NULL,NULL,NULL),(235,'Brady','Neuendorf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2003-02-28','M',NULL,NULL,'1018206833',18,1,'2020-12-20',NULL,NULL,NULL,'1018206833',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brady','Neuendorf','2003-02-28',NULL,0,1,NULL,NULL,NULL),(236,'Brenstin','Osier',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2010-05-08','M',NULL,NULL,'1025204247',18,1,'2021-10-03',NULL,NULL,NULL,'1025204247',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brenstin','Osier','2010-05-08',NULL,0,1,NULL,NULL,NULL),(237,'Carson','Osier',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2008-03-15','M',NULL,NULL,'1022946374',18,1,'2021-11-15',NULL,NULL,NULL,'1022946374',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Carson','Osier','2008-03-15',NULL,0,1,NULL,NULL,NULL),(238,'Xavier','Panzer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2004-04-01','M',NULL,NULL,'1019707143',18,1,'2022-12-18',NULL,NULL,NULL,'1019707143',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Xavier','Panzer','2004-04-01',NULL,0,1,NULL,NULL,NULL),(239,'Nathan','Peterson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2007-11-21','M',NULL,NULL,'1023489163',18,1,'2021-03-06',NULL,NULL,NULL,'1023489163',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nathan','Peterson','2007-11-21',NULL,0,1,NULL,NULL,NULL),(240,'Lilah','Podewils',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2011-04-13','F',NULL,NULL,'1026568137',18,1,'2023-03-09',NULL,NULL,NULL,'1026568137',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lilah','Podewils','2011-04-13',NULL,0,1,NULL,NULL,NULL),(241,'Riley','Robinson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2006-08-29','M',NULL,NULL,'1021797812',18,1,'2021-01-22',NULL,NULL,NULL,'1021797812',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Riley','Robinson','2006-08-29',NULL,0,1,NULL,NULL,NULL),(242,'Jacob','Robson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-16 16:10:07','2020-10-29 12:10:48','2013-01-15','M',NULL,NULL,'1027483305',18,1,'2021-09-17',NULL,NULL,NULL,'1027483305',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jacob','Robson','2013-01-15',NULL,0,1,NULL,NULL,NULL),(243,'Lila','Jans','','','','',NULL,NULL,'',NULL,0,0,'2020-09-18 20:23:55','2020-10-29 12:10:48','2015-05-13','F',NULL,'','',5,1,'2023-02-27',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lila','Jans','2015-05-13','',0,1,'',NULL,NULL),(244,'Hudson','Spindler','','','','',NULL,NULL,'',NULL,0,0,'2020-09-18 20:25:26','2020-10-29 12:10:48','2015-01-21','M',NULL,'','',5,1,'2023-01-17',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hudson','Spindler','2015-01-21','',0,1,'',NULL,NULL),(245,'Cameron','Adams','','','','',NULL,NULL,'',NULL,0,0,'2020-09-22 15:49:24','2020-09-22 15:49:24','2005-01-01','M',NULL,'','',1,1,NULL,NULL,NULL,'','','',1,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cameron','Adams','2005-01-01','',0,1,'',NULL,NULL),(246,'Connor','Liu','','','','',NULL,NULL,'',NULL,0,0,'2020-09-24 18:22:15','2020-10-29 12:10:48','2009-07-31','M',NULL,'','',4,1,'2021-03-17',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Connor','Liu','2009-07-31','',0,1,'',NULL,NULL),(247,'Alijah','Damon',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2017-01-30','M',NULL,NULL,'1082924346',19,1,'2022-12-10',NULL,NULL,NULL,'1082924346',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Alijah','Damon','2017-01-30',NULL,0,1,NULL,NULL,NULL),(248,'Hailey ','Gavin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2017-06-26','F',NULL,NULL,NULL,19,1,'2023-05-31',NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hailey ','Gavin','2017-06-26',NULL,0,1,NULL,NULL,NULL),(249,'Hope ','Stone',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2015-11-07','F',NULL,NULL,'1042409542',19,1,'2021-10-30',NULL,NULL,NULL,'1042409542',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hope ','Stone','2015-11-07',NULL,0,1,NULL,NULL,NULL),(250,'Purv','Patel',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2016-09-30','M',NULL,NULL,'1016022148',19,1,'2022-09-10',NULL,NULL,NULL,'1016022148',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Purv','Patel','2016-09-30',NULL,0,1,NULL,NULL,NULL),(251,'Benjamin','Pane',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2015-10-20','M',NULL,NULL,'1089303546',19,1,'2021-10-16',NULL,NULL,NULL,'1089303546',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Benjamin','Pane','2015-10-20',NULL,0,1,NULL,NULL,NULL),(252,'Carter','Lefort',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2016-12-19','M',NULL,NULL,'1096323941',19,1,'2023-01-21',NULL,NULL,NULL,'1096323941',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Carter','Lefort','2016-12-19',NULL,0,1,NULL,NULL,NULL),(253,'Daniel','Steenbruggen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2015-06-18','M',NULL,NULL,'1057094831',19,1,'2021-04-03',NULL,NULL,NULL,'1057094831',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Daniel','Steenbruggen','2015-06-18',NULL,0,1,NULL,NULL,NULL),(254,'Mason ','Fitzsimmons',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2014-12-01','M',NULL,NULL,'1038065938',19,1,'2020-11-28',NULL,NULL,NULL,'1038065938',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mason ','Fitzsimmons','2014-12-01',NULL,0,1,NULL,NULL,NULL),(255,'Anthony','Robinson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2014-09-11','M',NULL,NULL,'1040890735',19,1,'2021-04-24',NULL,NULL,NULL,'1040890735',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Anthony','Robinson','2014-09-11',NULL,0,1,NULL,NULL,NULL),(256,'Bryce','Godfrey',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2014-06-23','M',NULL,NULL,'1026562634',19,1,'2023-03-10',NULL,NULL,NULL,'1026562634',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bryce','Godfrey','2014-06-23',NULL,0,1,NULL,NULL,NULL),(257,'Tevor','Cooper',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2013-09-16','M',NULL,NULL,'1024637938',19,1,'2022-04-02',NULL,NULL,NULL,'1024637938',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tevor','Cooper','2013-09-16',NULL,0,1,NULL,NULL,NULL),(258,'Ryker','Briggs',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2013-09-11','M',NULL,NULL,'1038851537',19,1,'2023-01-13',NULL,NULL,NULL,'1038851537',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ryker','Briggs','2013-09-11',NULL,0,1,NULL,NULL,NULL),(259,'Joannah ','Melara',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2014-05-28','F',NULL,NULL,'1008970334',19,1,'2022-03-20',NULL,NULL,NULL,'1008970334',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Joannah ','Melara','2014-05-28',NULL,0,1,NULL,NULL,NULL),(260,'Aidyn','Comeau',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2014-05-05','M',NULL,NULL,'1032899038',19,1,'2023-05-10',NULL,NULL,NULL,'1032899038',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aidyn','Comeau','2014-05-05',NULL,0,1,NULL,NULL,NULL),(261,'Nolan ','Libby',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2013-09-04','M',NULL,NULL,'1056950537',19,1,'2022-09-18',NULL,NULL,NULL,'1056950537',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nolan ','Libby','2013-09-04',NULL,0,1,NULL,NULL,NULL),(262,'Declan','Blounts',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2013-07-24','M',NULL,NULL,'1059532830',19,1,'2022-05-30',NULL,NULL,NULL,'1059532830',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Declan','Blounts','2013-07-24',NULL,0,1,NULL,NULL,NULL),(263,'Alexander','Hawkins-Croak',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2012-11-05','M',NULL,NULL,'1016130238',19,1,'2021-10-04',NULL,NULL,NULL,'1016130238',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Alexander','Hawkins-Croak','2012-11-05',NULL,0,1,NULL,NULL,NULL),(264,'Owen','Rivera',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2012-06-12','M',NULL,NULL,'1079555534',19,1,'2022-04-29',NULL,NULL,NULL,'1079555534',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Owen','Rivera','2012-06-12',NULL,0,1,NULL,NULL,NULL),(265,'Michael','Franklin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2012-05-18','M',NULL,NULL,'1031166336',19,1,'2021-02-04',NULL,NULL,NULL,'1031166336',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Michael','Franklin','2012-05-18',NULL,0,1,NULL,NULL,NULL),(266,'Ella ','Smith',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2012-08-14','F',NULL,NULL,'1057994730',19,1,'2022-01-15',NULL,NULL,NULL,'1057994730',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ella ','Smith','2012-08-14',NULL,0,1,NULL,NULL,NULL),(267,'Mohamed','Camara',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2011-09-09','M',NULL,NULL,'1018711631',19,1,'2021-11-07',NULL,NULL,NULL,'1018711631',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Mohamed','Camara','2011-09-09',NULL,0,1,NULL,NULL,NULL),(268,'Harrison','Spencer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2011-07-11','M',NULL,NULL,'1027112534',19,1,'2023-02-04',NULL,NULL,NULL,'1027112534',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Harrison','Spencer','2011-07-11',NULL,0,1,NULL,NULL,NULL),(269,'Marija','Taylor',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2010-01-26','F',NULL,NULL,'1013951430',19,1,'2021-05-21',NULL,NULL,NULL,'1013951430',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Marija','Taylor','2010-01-26',NULL,0,1,NULL,NULL,NULL),(270,'Marley','Johnson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2010-09-28','F',NULL,NULL,'1079604033',19,1,'2022-03-21',NULL,NULL,NULL,'1079604033',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Marley','Johnson','2010-09-28',NULL,0,1,NULL,NULL,NULL),(271,'Milton','Johnson',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2010-09-29','M',NULL,NULL,'1038605038',19,1,'2022-03-20',NULL,NULL,NULL,'1038605038',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Milton','Johnson','2010-09-29',NULL,0,1,NULL,NULL,NULL),(272,'Bryson','Nihill',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2011-05-23','M',NULL,NULL,'1096003337',19,1,'2021-05-21',NULL,NULL,NULL,'1096003337',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bryson','Nihill','2011-05-23',NULL,0,1,NULL,NULL,NULL),(273,'Noah',' Bartell',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2009-11-17','M',NULL,NULL,'1022999321',19,1,'2021-01-09',NULL,NULL,NULL,'1022999321',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Noah',' Bartell','2009-11-17',NULL,0,1,NULL,NULL,NULL),(274,'Deno','Del Sesto',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2010-01-08','M',NULL,NULL,'1036106932',19,1,'2020-04-25',NULL,NULL,NULL,'1036106932',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Deno','Del Sesto','2010-01-08',NULL,0,1,NULL,NULL,NULL),(275,'Michael','Del Gosso',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:38','2020-10-29 12:10:48','2010-08-11','M',NULL,NULL,'1072308936',19,1,'2020-11-29',NULL,NULL,NULL,'1072308936',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Michael','Del Gosso','2010-08-11',NULL,0,1,NULL,NULL,NULL),(276,'Addison ','Wilbur',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:39','2020-10-29 12:10:48','2010-03-14','F',NULL,NULL,'1032198423',19,1,'2020-03-08',NULL,NULL,NULL,'1032198423',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Addison ','Wilbur','2010-03-14',NULL,0,1,NULL,NULL,NULL),(277,'Richard','Mahoney',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:39','2020-10-29 12:10:48','2008-04-28','M',NULL,NULL,'1038828925',19,1,'2020-01-31',NULL,NULL,NULL,'1038828925',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Richard','Mahoney','2008-04-28',NULL,0,1,NULL,NULL,NULL),(278,'Franklin','Seltzer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:39','2020-10-29 12:10:48','2008-09-11','M',NULL,NULL,'1006917535',19,1,'2020-05-04',NULL,NULL,NULL,'1006917535',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Franklin','Seltzer','2008-09-11',NULL,0,1,NULL,NULL,NULL),(279,'Lily','Godfrey',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:39','2020-10-29 12:10:48','2009-04-09','F',NULL,NULL,'1001492324',19,1,'2022-04-07',NULL,NULL,NULL,'1001492324',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lily','Godfrey','2009-04-09',NULL,0,1,NULL,NULL,NULL),(280,'Erin ','Godfrey',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:39','2020-10-29 12:10:48','2009-04-09','F',NULL,NULL,'1061491320',19,1,'2022-04-07',NULL,NULL,NULL,'1061491320',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Erin ','Godfrey','2009-04-09',NULL,0,1,NULL,NULL,NULL),(281,'Luella','Lefebvre',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:06:39','2020-10-29 12:10:48','2007-01-25','F',NULL,NULL,'1046813725',19,1,'2021-11-13',NULL,NULL,NULL,'1046813725',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Luella','Lefebvre','2007-01-25',NULL,0,1,NULL,NULL,NULL),(282,'Autumn','Feeney',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2013-07-26','F',NULL,NULL,'1092660232',19,1,'2022-10-21',NULL,NULL,NULL,'1092660232',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Autumn','Feeney','2013-07-26',NULL,0,1,NULL,NULL,NULL),(283,'Vanessa','DePina',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2012-12-27','F',NULL,NULL,'1027378438',19,1,'2022-09-12',NULL,NULL,NULL,'1027378438',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Vanessa','DePina','2012-12-27',NULL,0,1,NULL,NULL,NULL),(284,'Blake','Sbardella',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2010-05-28','M',NULL,NULL,'1026090628',19,1,'2021-04-10',NULL,NULL,NULL,'1026090628',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Blake','Sbardella','2010-05-28',NULL,0,1,NULL,NULL,NULL),(285,'Elijah','Odom',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2009-04-23','M',NULL,NULL,'1095638927',19,1,'2021-04-04',NULL,NULL,NULL,'1095638927',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Elijah','Odom','2009-04-23',NULL,0,1,NULL,NULL,NULL),(286,'Samuel ','Gordon',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2009-06-16','M',NULL,NULL,'1097913532',19,1,'2020-12-18',NULL,NULL,NULL,'1097913532',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Samuel ','Gordon','2009-06-16',NULL,0,1,NULL,NULL,NULL),(287,'Antonio','Cardona',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2007-09-17','M',NULL,NULL,'1081733523',19,1,'2021-11-29',NULL,NULL,NULL,'1081733523',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Antonio','Cardona','2007-09-17',NULL,0,1,NULL,NULL,NULL),(288,'Autumn','Comey',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2007-06-30','F',NULL,NULL,'1096948023',19,1,'2020-03-07',NULL,NULL,NULL,'1096948023',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Autumn','Comey','2007-06-30',NULL,0,1,NULL,NULL,NULL),(289,'Shawn ','Flanagan',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2007-05-02','M',NULL,NULL,'1029837723',19,1,'2022-03-12',NULL,NULL,NULL,'1029837723',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Shawn ','Flanagan','2007-05-02',NULL,0,1,NULL,NULL,NULL),(290,'Cameron','LeRoux',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2004-07-02','M',NULL,NULL,'1008899414',19,1,'2021-01-29',NULL,NULL,NULL,'1008899414',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Cameron','LeRoux','2004-07-02',NULL,0,1,NULL,NULL,NULL),(291,'Tyler','Furtado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'2020-09-25 19:12:12','2020-10-29 12:10:48','2002-07-22','M',NULL,NULL,'1031385914',19,1,'2022-01-08',NULL,NULL,NULL,'1031385914',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tyler','Furtado','2002-07-22',NULL,0,1,NULL,NULL,NULL),(292,'Gracelyn','Kopkie','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2013-02-24','F',NULL,'','',20,1,'2020-05-02',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gracelyn','Kopkie','2013-02-24','',0,1,'',NULL,NULL),(293,'Sebastian','Gibbons','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2015-01-03','M',NULL,'','',20,1,'2020-08-09',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Sebastian','Gibbons','2015-01-03','',0,1,'',NULL,NULL),(294,'Gabriella','Arellano','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2009-12-23','F',NULL,'','',20,1,'2020-09-18',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Gabriella','Arellano','2009-12-23','',0,1,'','',''),(295,'Camryn ','Cowden','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2007-03-12','F',NULL,'','',20,1,'2020-10-05',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Camryn ','Cowden','2007-03-12','',0,1,'',NULL,NULL),(296,'Maddyson','Brakke','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2003-07-06','F',NULL,'','',20,1,'2020-10-11',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Maddyson','Brakke','2003-07-06','',0,1,'',NULL,NULL),(297,'Dalton','Haig','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2006-03-31','M',NULL,'','',20,1,'2020-10-17',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dalton','Haig','2006-03-31','',0,1,'',NULL,NULL),(298,'Paul','Avenson','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:51','2020-10-29 12:10:48','2014-09-04','M',NULL,'','',20,1,'2020-10-20',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Paul','Avenson','2014-09-04','',0,1,'',NULL,NULL),(299,'Hunter','Crenshaw','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2007-09-21','M',NULL,'','',20,1,'2020-10-23',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hunter','Crenshaw','2007-09-21','',0,1,'',NULL,NULL),(300,'Tori','Horn','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2004-10-20','F',NULL,'','',20,1,'2020-11-03',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tori','Horn','2004-10-20','',0,1,'',NULL,NULL),(301,'Benjamin','Martin','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2010-12-30','M',NULL,'','',20,1,'2020-11-07',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Benjamin','Martin','2010-12-30','',0,1,'',NULL,NULL),(302,'Greyson','Trujillo','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2015-01-11','M',NULL,'','',20,1,'2020-11-07',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Greyson','Trujillo','2015-01-11','',0,1,'',NULL,NULL),(303,'Amy','Scouton','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2006-05-30','F',NULL,'','',20,1,'2020-11-15',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Amy','Scouton','2006-05-30','',0,1,'',NULL,NULL),(304,'Justin','Robbins','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2006-05-12','M',NULL,'','',20,1,'2020-12-04',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Justin','Robbins','2006-05-12','',0,1,'',NULL,NULL),(305,'Nariah','Miller','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2005-07-20','F',NULL,'','',20,1,'2020-12-06',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nariah','Miller','2005-07-20','',0,1,'',NULL,NULL),(306,'Autum','Harris','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2008-07-29','F',NULL,'','',20,1,'2020-12-14',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Autum','Harris','2008-07-29','',0,1,'',NULL,NULL),(307,'Izaiah','Paulson','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2006-04-06','M',NULL,'','',20,1,'2021-01-05',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Izaiah','Paulson','2006-04-06','',0,1,'',NULL,NULL),(308,'Devin','Arola-Johnson','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2002-12-02','M',NULL,'','',20,1,'2021-01-07',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Devin','Arola-Johnson','2002-12-02','',0,1,'','',''),(309,'Maggie','Stevenson','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:48','2002-12-06','F',NULL,'','',20,1,'2021-01-08',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Maggie','Stevenson','2002-12-06','',0,1,'',NULL,NULL),(310,'Taylor','Hoppe','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2011-02-03','F',NULL,'','',20,1,'2021-01-16',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Taylor','Hoppe','2011-02-03','',0,1,'',NULL,NULL),(311,'Ava','Opsal','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2008-10-18','F',NULL,'','',20,1,'2021-01-18',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ava','Opsal','2008-10-18','',0,1,'',NULL,NULL),(312,'Hunter','White','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2011-09-29','M',NULL,'','',20,1,'2021-01-19',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Hunter','White','2011-09-29','',0,1,'',NULL,NULL),(313,'Lucas','Gentry','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2007-10-25','M',NULL,'','',20,1,'2021-01-23',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Lucas','Gentry','2007-10-25','',0,1,'',NULL,NULL),(314,'Morgan ','Monroe','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2007-05-11','F',NULL,'','',20,1,'2021-01-25',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Morgan ','Monroe','2007-05-11','',0,1,'',NULL,NULL),(315,'Adam','Habedank','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2007-12-06','M',NULL,'','',20,1,'2021-01-26',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adam','Habedank','2007-12-06','',0,1,'',NULL,NULL),(316,'Makenna','Frazier','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2003-12-19','F',NULL,'','',20,1,'2021-02-01',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Makenna','Frazier','2003-12-19','',0,1,'',NULL,NULL),(317,'Natile ','Proffit','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2006-04-30','F',NULL,'','',20,1,'2021-02-09',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Natile ','Proffit','2006-04-30','',0,1,'',NULL,NULL),(318,'Jacob','Couch','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2002-06-24','M',NULL,'','',20,1,'2021-02-13',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jacob','Couch','2002-06-24','',0,1,'',NULL,NULL),(319,'Eli','Miller','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2008-09-28','M',NULL,'','',20,1,'2021-02-22',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Eli','Miller','2008-09-28','',0,1,'',NULL,NULL),(320,'April','Buckley-Litzau','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2011-02-16','F',NULL,'','',20,1,'2021-02-23',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'April','Buckley-Litzau','2011-02-16','',0,1,'',NULL,NULL),(321,'Tyler','George','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2009-08-05','M',NULL,'','',20,1,'2021-02-23',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Tyler','George','2009-08-05','',0,1,'',NULL,NULL),(322,'Natalie','Viloria','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2004-10-14','F',NULL,'','',20,1,'2021-03-29',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Natalie','Viloria','2004-10-14','',0,1,'',NULL,NULL),(323,'Aliazah','Paulson','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2008-01-24','F',NULL,'','',20,1,'2021-04-05',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Aliazah','Paulson','2008-01-24','',0,1,'',NULL,NULL),(324,'Madison','Goochey','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2007-02-26','F',NULL,'','',20,1,'2021-04-09',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Madison','Goochey','2007-02-26','',0,1,'',NULL,NULL),(325,'Levi','Nelson-Selseth','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2008-03-14','M',NULL,'','',20,1,'2021-04-12',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Levi','Nelson-Selseth','2008-03-14','',0,1,'',NULL,NULL),(326,'Adrian','Mclaury','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2011-05-20','M',NULL,'','',20,1,'2021-04-19',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Adrian','Mclaury','2011-05-20','',0,1,'',NULL,NULL),(327,'Patrick','McCormick','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 20:35:52','2020-10-29 12:10:49','2010-02-04','M',NULL,'','',20,1,NULL,NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Patrick','McCormick','2010-02-04','',0,1,'',NULL,NULL),(328,'Nakhi','Hollingsworth','','','','',NULL,NULL,'',NULL,0,0,'2020-09-28 21:00:40','2020-10-29 12:10:49','2004-04-26','F',NULL,'','',20,1,'2021-04-26',NULL,NULL,'','','',0,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Nakhi','Hollingsworth','2004-04-26','',0,1,'',NULL,NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telemedicine_meeting_participants`
--

DROP TABLE IF EXISTS `telemedicine_meeting_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telemedicine_meeting_participants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `telemedicine_meeting_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telemedicine_meeting_participants`
--

LOCK TABLES `telemedicine_meeting_participants` WRITE;
/*!40000 ALTER TABLE `telemedicine_meeting_participants` DISABLE KEYS */;
/*!40000 ALTER TABLE `telemedicine_meeting_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telemedicine_meetings`
--

DROP TABLE IF EXISTS `telemedicine_meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telemedicine_meetings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_transaction_id` int(11) DEFAULT NULL,
  `zoomus_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `zoomus_user_id` varchar(255) DEFAULT NULL,
  `option_use_pmi` tinyint(1) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `error` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_telemedicine_meetings_on_service_transaction_id` (`service_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telemedicine_meetings`
--

LOCK TABLES `telemedicine_meetings` WRITE;
/*!40000 ALTER TABLE `telemedicine_meetings` DISABLE KEYS */;
/*!40000 ALTER TABLE `telemedicine_meetings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `telemedicine_schools`
--

DROP TABLE IF EXISTS `telemedicine_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `telemedicine_schools` (
  `provider_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  KEY `index_telemedicine_schools_on_provider_id_and_school_id` (`provider_id`,`school_id`),
  KEY `index_telemedicine_schools_on_school_id` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `telemedicine_schools`
--

LOCK TABLES `telemedicine_schools` WRITE;
/*!40000 ALTER TABLE `telemedicine_schools` DISABLE KEYS */;
/*!40000 ALTER TABLE `telemedicine_schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_files`
--

DROP TABLE IF EXISTS `temp_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `temp_file_size` int(11) DEFAULT NULL,
  `temp_content_type` varchar(255) DEFAULT NULL,
  `temp_file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `temp_fingerprint` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_files`
--

LOCK TABLES `temp_files` WRITE;
/*!40000 ALTER TABLE `temp_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temporary_student_data`
--

DROP TABLE IF EXISTS `temporary_student_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temporary_student_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `medicaid_number` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `ignored` tinyint(1) NOT NULL DEFAULT '0',
  `middle_initial` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_temporary_student_data_on_birth_date` (`birth_date`),
  KEY `index_temporary_student_data_on_first_name` (`first_name`),
  KEY `index_temporary_student_data_on_ignored` (`ignored`),
  KEY `index_temporary_student_data_on_last_name` (`last_name`),
  KEY `index_temporary_student_data_on_medicaid_number` (`medicaid_number`),
  KEY `index_temporary_student_data_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temporary_student_data`
--

LOCK TABLES `temporary_student_data` WRITE;
/*!40000 ALTER TABLE `temporary_student_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `temporary_student_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `therapist_groups`
--

DROP TABLE IF EXISTS `therapist_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `therapist_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_therapist_groups_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `therapist_groups`
--

LOCK TABLES `therapist_groups` WRITE;
/*!40000 ALTER TABLE `therapist_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `therapist_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `third_party_liability_records`
--

DROP TABLE IF EXISTS `third_party_liability_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `third_party_liability_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `insurance_type` varchar(255) DEFAULT NULL,
  `coverage_description` varchar(255) DEFAULT NULL,
  `plan_number` varchar(255) DEFAULT NULL,
  `group_or_policy_number` varchar(255) DEFAULT NULL,
  `member_identification_number` varchar(255) DEFAULT NULL,
  `family_unit_number` varchar(255) DEFAULT NULL,
  `group_number` varchar(255) DEFAULT NULL,
  `referral_number` varchar(255) DEFAULT NULL,
  `employee_id_number` varchar(255) DEFAULT NULL,
  `health_insurance_claim_number` varchar(255) DEFAULT NULL,
  `prior_auth_number` varchar(255) DEFAULT NULL,
  `policy_number` varchar(255) DEFAULT NULL,
  `plan_network_id` varchar(255) DEFAULT NULL,
  `medicaid_recipient_id` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `insurer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_third_party_liability_records_on_insurer_id` (`insurer_id`),
  KEY `index_third_party_liability_records_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `third_party_liability_records`
--

LOCK TABLES `third_party_liability_records` WRITE;
/*!40000 ALTER TABLE `third_party_liability_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_liability_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tos_acceptances`
--

DROP TABLE IF EXISTS `tos_acceptances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tos_acceptances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `accepted_text` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `school_year` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_tos_acceptances_on_created_at` (`created_at`),
  KEY `idx_tos_acceptances_provider_student_year` (`provider_id`,`student_id`,`school_year`),
  KEY `index_tos_acceptances_on_provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tos_acceptances`
--

LOCK TABLES `tos_acceptances` WRITE;
/*!40000 ALTER TABLE `tos_acceptances` DISABLE KEYS */;
/*!40000 ALTER TABLE `tos_acceptances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tpl_denials`
--

DROP TABLE IF EXISTS `tpl_denials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tpl_denials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_date` date DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `denial_type` varchar(255) DEFAULT NULL,
  `third_party_liability_record_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `tpl_denial_file_name` varchar(255) DEFAULT NULL,
  `tpl_denial_content_type` varchar(255) DEFAULT NULL,
  `tpl_denial_file_size` int(11) DEFAULT NULL,
  `tpl_denial_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tpl_denials`
--

LOCK TABLES `tpl_denials` WRITE;
/*!40000 ALTER TABLE `tpl_denials` DISABLE KEYS */;
/*!40000 ALTER TABLE `tpl_denials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_billing_details`
--

DROP TABLE IF EXISTS `transportation_billing_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_billing_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `total_offsite_expenses` decimal(12,2) DEFAULT NULL,
  `average_cost_per_trip` float DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_transportation_billing_details_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_billing_details`
--

LOCK TABLES `transportation_billing_details` WRITE;
/*!40000 ALTER TABLE `transportation_billing_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_billing_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_claim_files`
--

DROP TABLE IF EXISTS `transportation_claim_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_claim_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transportation_claim_id` int(11) DEFAULT NULL,
  `attachment_file_size` int(11) DEFAULT NULL,
  `attachment_content_type` varchar(255) DEFAULT NULL,
  `attachment_file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_claim_files`
--

LOCK TABLES `transportation_claim_files` WRITE;
/*!40000 ALTER TABLE `transportation_claim_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_claim_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_claims`
--

DROP TABLE IF EXISTS `transportation_claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_claims` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_claim` decimal(12,4) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `transportation_billing_detail_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `total_students` int(11) DEFAULT NULL,
  `total_dates` int(11) DEFAULT NULL,
  `total_one_way_trips` int(11) DEFAULT NULL,
  `total_therapy_minutes` int(11) DEFAULT NULL,
  `total_services` int(11) DEFAULT NULL,
  `total_trip_minutes` int(11) DEFAULT NULL,
  `total_nemt_cost` decimal(12,4) DEFAULT NULL,
  `total_reimbursement` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_transportation_claims_on_district_id` (`district_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_claims`
--

LOCK TABLES `transportation_claims` WRITE;
/*!40000 ALTER TABLE `transportation_claims` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_claims` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_eligibility_records`
--

DROP TABLE IF EXISTS `transportation_eligibility_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_eligibility_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `quarter` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `trips_per_day` int(11) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `eligible` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_transportation_eligibility_records_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_eligibility_records`
--

LOCK TABLES `transportation_eligibility_records` WRITE;
/*!40000 ALTER TABLE `transportation_eligibility_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_eligibility_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_log_entries`
--

DROP TABLE IF EXISTS `transportation_log_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_log_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transportation_log_id` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `trips` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `trip1_scanned_at` datetime DEFAULT NULL,
  `trip2_scanned_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `txp_entries_uniq_idx` (`transportation_log_id`,`day`),
  KEY `index_transportation_log_entries_on_transportation_log_id` (`transportation_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_log_entries`
--

LOCK TABLES `transportation_log_entries` WRITE;
/*!40000 ALTER TABLE `transportation_log_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_log_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_logs`
--

DROP TABLE IF EXISTS `transportation_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `onsite` tinyint(1) NOT NULL DEFAULT '1',
  `log_location` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `txp_log_onsite_date_route_idx` (`student_id`,`year`,`month`,`onsite`,`route_id`),
  KEY `txp_log_onsite_date_idx` (`student_id`,`year`,`month`,`onsite`),
  KEY `txp_log_student_date_idx` (`student_id`,`year`,`month`),
  KEY `index_transportation_logs_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_logs`
--

LOCK TABLES `transportation_logs` WRITE;
/*!40000 ALTER TABLE `transportation_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_route_requests`
--

DROP TABLE IF EXISTS `transportation_route_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_route_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `data` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_route_requests`
--

LOCK TABLES `transportation_route_requests` WRITE;
/*!40000 ALTER TABLE `transportation_route_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_route_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_route_students`
--

DROP TABLE IF EXISTS `transportation_route_students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_route_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_route_students`
--

LOCK TABLES `transportation_route_students` WRITE;
/*!40000 ALTER TABLE `transportation_route_students` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_route_students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_route_summaries`
--

DROP TABLE IF EXISTS `transportation_route_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_route_summaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_route_summaries`
--

LOCK TABLES `transportation_route_summaries` WRITE;
/*!40000 ALTER TABLE `transportation_route_summaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_route_summaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transportation_routes`
--

DROP TABLE IF EXISTS `transportation_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transportation_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transportation_routes`
--

LOCK TABLES `transportation_routes` WRITE;
/*!40000 ALTER TABLE `transportation_routes` DISABLE KEYS */;
/*!40000 ALTER TABLE `transportation_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treatment_plan_goals`
--

DROP TABLE IF EXISTS `treatment_plan_goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treatment_plan_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `treatment_plan_id` int(11) DEFAULT NULL,
  `name` varchar(10000) DEFAULT NULL,
  `ailment_id` int(11) DEFAULT NULL,
  `icd10_ailment_id` int(11) DEFAULT NULL,
  `gle` varchar(255) DEFAULT NULL,
  `target_percentage` varchar(255) DEFAULT NULL,
  `goal_type` varchar(255) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `plan_type` varchar(255) DEFAULT NULL,
  `treatment_plan_goal_id` int(11) DEFAULT NULL,
  `sourceable_type` varchar(255) DEFAULT NULL,
  `sourceable_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `baseline` int(11) DEFAULT NULL,
  `problem` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_treatment_plan_goals_on_treatment_plan_id` (`treatment_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treatment_plan_goals`
--

LOCK TABLES `treatment_plan_goals` WRITE;
/*!40000 ALTER TABLE `treatment_plan_goals` DISABLE KEYS */;
/*!40000 ALTER TABLE `treatment_plan_goals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treatment_plans`
--

DROP TABLE IF EXISTS `treatment_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treatment_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `initiation_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `medication` varchar(255) DEFAULT NULL,
  `dose` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `dsm` varchar(255) DEFAULT NULL,
  `other_agencies` varchar(2000) DEFAULT NULL,
  `coordination_plan` varchar(2000) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `components` text,
  `review_dates` text,
  `medications` text,
  `services_needed_beyond_scope` varchar(2000) DEFAULT NULL,
  `education_dx` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_treatment_plans_on_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treatment_plans`
--

LOCK TABLES `treatment_plans` WRITE;
/*!40000 ALTER TABLE `treatment_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `treatment_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tsheet_records`
--

DROP TABLE IF EXISTS `tsheet_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tsheet_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_transaction_id` int(11) DEFAULT NULL,
  `tsheet_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_tsheet_records_on_calendar_transaction_id` (`calendar_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tsheet_records`
--

LOCK TABLES `tsheet_records` WRITE;
/*!40000 ALTER TABLE `tsheet_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `tsheet_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `videos` (
  `id` varchar(36) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `file_file_name` varchar(255) DEFAULT NULL,
  `file_content_type` varchar(255) DEFAULT NULL,
  `file_file_size` int(11) DEFAULT NULL,
  `file_updated_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-11-04 23:29:55