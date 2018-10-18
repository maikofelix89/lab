DROP TABLE IF EXISTS `cd4rejectedreasons`;
CREATE TABLE IF NOT EXISTS `cd4rejectedreasons` (
  `id` int(10) NOT NULL UNSIGNED AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `active` int(50) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15;

INSERT INTO `cd4rejectedreasons` (`id`, `name`, `active`) VALUES
	(1, 'Incomplete/Missing Requisition  Form', 1),
	(2, 'Sample not received at Lab', 1),
	(3, 'Technical Problems at Lab', 1),
	(4, 'Other', 1);



DROP TABLE IF EXISTS `samplestatus`;
CREATE TABLE IF NOT EXISTS `samplestatus` (
  `id` int(10) NOT NULL UNSIGNED AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `active` int(10) DEFAULT '1',
  `forapproval` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7;

-- Dumping data for table cd4_db.samplestatus: 6 rows
/*!40000 ALTER TABLE `samplestatus` DISABLE KEYS */;
INSERT INTO `samplestatus` (`id`, `name`, `active`, `forapproval`) VALUES
	(1, 'In-Queue', 1, 0),
	(2, 'Rejected', 1, 0),
	(3, 'In-Process', 1, 0),
	(4, 'Tested', 1, 0),
	(5, 'Approved', 1, 0),
	(6, 'Dispatched', 1, 0);



DROP TABLE IF EXISTS `cd4worksheets`;
CREATE TABLE IF NOT EXISTS `cd4worksheets` (
  `id` int(10) NOT NULL UNSIGNED AUTO_INCREMENT,
  `status_id` TINYINTEGER UNSIGNED DEFAULT NULL,
  `lab_id` tinyint(3) unsigned NOT NULL,

  `createdby` int(10) unsigned DEFAULT NULL,
  `uploadedby` int(10) unsigned DEFAULT NULL,
  `reviewedby` int(10) unsigned DEFAULT NULL,
  `reviewedby2` int(10) unsigned DEFAULT NULL,
  `cancelledby` int(10) unsigned DEFAULT NULL,

  `TruCountLotno` varchar(50) DEFAULT NULL,
  `AntibodyLotno` varchar(50) DEFAULT NULL,
  `MulticheckLowLotno` varchar(50) DEFAULT NULL,
  `MulticheckNormalLotno` varchar(50) DEFAULT NULL,

  `daterun` DATE DEFAULT NULL,
  `dateuploaded` date DEFAULT NULL,
  `datereviewed` DATE DEFAULT NULL,
  `datereviewed2` DATE DEFAULT NULL,
  `datecancelled` date DEFAULT NULL,

  `dumped` tinyint(3) unsigned NOT NULL,

  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY(`status_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `cd4samples`;
CREATE TABLE IF NOT EXISTS `cd4samples` (
  `id` int(10) NOT NULL UNSIGNED AUTO_INCREMENT,
  `patient_id` int(10) unsigned NOT NULL,
  `worksheet_id` int(10) unsigned NOT NULL,
  `facility_id` int(10) unsigned NOT NULL,
  `parentid` int(10) unsigned NOT NULL,
  `serial_no` int(10) unsigned NOT NULL,

  `amrs_location` tinyint(4) DEFAULT NULL,
  `orderno` varchar(100) DEFAULT NULL,
  `run` tinyint(3) unsigned DEFAULT '1',
  # repeatt is action of cd4db
  `repeatt` tinyint(3) unsigned DEFAULT '0',

  `receivedstatus` tinyint(3) unsigned DEFAULT NULL,
  `rejectedreason` tinyint(3) unsigned DEFAULT NULL,


  `THelperSuppressor Ratio` varchar(100) DEFAULT NULL,
  `AVGCD3percentLymph` varchar(100) DEFAULT NULL,
  `AVGCD3AbsCnt` varchar(100) DEFAULT NULL,
  `AVGCD3CD4percentLymph` varchar(100) DEFAULT NULL,
  `AVGCD3CD4AbsCnt` varchar(100) DEFAULT NULL,
  `AVGCD3CD8percentLymph` varchar(100) DEFAULT NULL,
  `AVGCD3CD8AbsCnt` varchar(100) DEFAULT NULL,
  `AVGCD3CD4CD8percentLymph` varchar(100) DEFAULT NULL,
  `AVGCD3CD4CD8AbsCnt` varchar(100) DEFAULT NULL,
  `CD45AbsCnt` varchar(100) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL,


  `approvedby` int(10) unsigned DEFAULT NULL,
  `approvedby2` int(10) unsigned DEFAULT NULL,
  # On other side, user_id is registeredby
  # user_id of 0 will be for SYSTEM GENERATED
  # dateregistered is created_at here
  `user_id` int(10) unsigned DEFAULT NULL,

  `datecollected` date DEFAULT NULL,
  `datereceived` date DEFAULT NULL,
  `datetested` date DEFAULT NULL,
  `datemodified` date DEFAULT NULL,
  `dateapproved` date DEFAULT NULL,
  `dateapproved2` date DEFAULT NULL,
  `datedispatched` date DEFAULT NULL,

  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY(`status_id`)
) ENGINE=InnoDB;