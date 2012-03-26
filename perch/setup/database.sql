CREATE TABLE IF NOT EXISTS `__PREFIX__users` (
  `userID` int(10) unsigned NOT NULL auto_increment,
  `userUsername` varchar(255) NOT NULL default '',
  `userPassword` varchar(255) NOT NULL default '',
  `userCreated` datetime NOT NULL default '2000-01-01 00:00:00',
  `userUpdated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `userLastLogin` datetime NOT NULL default '2000-01-01 00:00:00',
  `userGivenName` varchar(255) NOT NULL default '',
  `userFamilyName` varchar(255) NOT NULL default '',
  `userEmail` varchar(255) NOT NULL default '',
  `userEnabled` tinyint(1) unsigned NOT NULL default '0',
  `userHash` char(32) NOT NULL default '',
  `userRole` enum('Editor','Admin') NOT NULL default 'Editor',
  PRIMARY KEY  (`userID`),
  KEY `idx_enabled` (`userEnabled`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX__contentItems` (
  `contentID` int(10) unsigned NOT NULL auto_increment,
  `contentKey` varchar(255) NOT NULL default '',
  `contentPage` varchar(255) character set latin1 NOT NULL default '*',
  `contentHTML` mediumtext NOT NULL,
  `contentNew` tinyint(1) unsigned NOT NULL default '1',
  `contentOrder` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `contentTemplate` varchar(255) NOT NULL default '',
  `contentMultiple` tinyint(1) unsigned NOT NULL default '0',
  `contentAddToTop` tinyint(1) unsigned NOT NULL default '0',
  `contentJSON` mediumtext NOT NULL,
  `contentHistory` mediumtext NOT NULL,
  `contentOptions` text NOT NULL,
  `contentSearchable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY  (`contentID`),
  KEY `idx_page` (`contentPage`),
  KEY `idx_key` (`contentKey`),
  FULLTEXT KEY `contentJSON` (`contentJSON`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `__PREFIX__settings` (
  `settingID` varchar(60) NOT NULL default '',
  `settingValue` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`settingID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `__PREFIX__settings` (`settingID`,`settingValue`)
VALUES
	('headerColour','#FFFFFF'),
	('headerLinkColour','#000000'),
	('logoPath','__PERCH_LOGINPATH__/assets/img/logo.png');