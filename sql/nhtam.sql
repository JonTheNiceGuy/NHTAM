# phpMyAdmin SQL Dump
# version 2.5.7-pl1
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Oct 28, 2005 at 04:15 PM
# Server version: 4.0.24
# PHP Version: 4.3.11
#
# Database : `nhtam`
#

# --------------------------------------------------------

#
# Table structure for table `equipment_inventory`
#

DROP TABLE IF EXISTS `equipment_inventory`;
CREATE TABLE `equipment_inventory` (
  `uid` int(11) NOT NULL auto_increment,
  `strIP` varchar(255) NOT NULL default '',
  `strDisplayName` varchar(255) NOT NULL default '',
  `intLocation` int(11) NOT NULL default '1',
  `intNetwork` int(11) NOT NULL default '1',
  `strFunction` varchar(255) default NULL,
  `intEquipmentType` int(11) default NULL,
  `intSupport` int(11) NOT NULL default '1',
  PRIMARY KEY  (`uid`),
  KEY `intLocation` (`intLocation`),
  KEY `intNetwork` (`intNetwork`),
  KEY `intEquipmentType` (`intEquipmentType`),
  KEY `intSupport` (`intSupport`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `equipment_inventory`
#

INSERT INTO `equipment_inventory` VALUES (1, '127.0.0.1', 'Localhost', 1, 1, 'Local Server', 4, 1);

# --------------------------------------------------------

#
# Table structure for table `equipment_locations`
#

DROP TABLE IF EXISTS `equipment_locations`;
CREATE TABLE `equipment_locations` (
  `intUID` int(11) NOT NULL auto_increment,
  `intSite` int(11) NOT NULL default '1',
  `strLocation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`intUID`),
  KEY `intSite` (`intSite`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `equipment_locations`
#

INSERT INTO `equipment_locations` VALUES (1, 1, 'Main Comms Room');

# --------------------------------------------------------

#
# Table structure for table `equipment_networks`
#

DROP TABLE IF EXISTS `equipment_networks`;
CREATE TABLE `equipment_networks` (
  `intUID` int(11) NOT NULL auto_increment,
  `strName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`intUID`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `equipment_networks`
#

INSERT INTO `equipment_networks` VALUES (1, 'Default');

# --------------------------------------------------------

#
# Table structure for table `equipment_sites`
#

DROP TABLE IF EXISTS `equipment_sites`;
CREATE TABLE `equipment_sites` (
  `intUID` int(11) NOT NULL auto_increment,
  `strSite` varchar(255) NOT NULL default '',
  `strSiteAddr1` varchar(255) NOT NULL default '',
  `strSiteAddr2` varchar(255) NOT NULL default '',
  `strSiteAddr3` varchar(255) NOT NULL default '',
  `strSiteAddr4` varchar(255) NOT NULL default '',
  `strSiteAddr5` varchar(255) NOT NULL default '',
  `strSitePostcode` varchar(50) NOT NULL default '',
  `strSiteCountry` varchar(255) NOT NULL default '',
  `strContactNum` varchar(50) NOT NULL default '',
  `strContactName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`intUID`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `equipment_sites`
#

INSERT INTO `equipment_sites` VALUES (1, 'Default', '1 Your Street', 'Your Business Park', 'Your Area', 'Your Town', 'Your City', 'AB12 3CD', 'United Kingdom', '01234 5678901', 'Mr. J. Bloggs');

# --------------------------------------------------------

#
# Table structure for table `equipment_support`
#

DROP TABLE IF EXISTS `equipment_support`;
CREATE TABLE `equipment_support` (
  `intUID` int(11) NOT NULL auto_increment,
  `strCompany` varchar(255) NOT NULL default '',
  `strAddr1` varchar(255) NOT NULL default '',
  `strAddr2` varchar(255) NOT NULL default '',
  `strAddr3` varchar(255) NOT NULL default '',
  `strAddr4` varchar(255) NOT NULL default '',
  `strAddr5` varchar(255) NOT NULL default '',
  `strPostcode` varchar(50) NOT NULL default '',
  `strCountry` varchar(255) NOT NULL default '',
  `strContactName` varchar(255) NOT NULL default '',
  `strContactNum` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`intUID`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `equipment_support`
#

INSERT INTO `equipment_support` VALUES (1, 'My Default Support Organization', '5 Their Parade', 'Their Business Park', 'Their Area', 'Their Town', 'Their Region', 'BC23 45DE', 'United Kingdom', 'Mr. S. Smith', '09876 5432101 x 123456');

# --------------------------------------------------------

#
# Table structure for table `equipment_type`
#

DROP TABLE IF EXISTS `equipment_type`;
CREATE TABLE `equipment_type` (
  `intHardwareType` int(11) NOT NULL auto_increment,
  `strHardwareType` varchar(255) NOT NULL default '',
  `strGraphicFilename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`intHardwareType`),
  UNIQUE KEY `strHardwareType` (`strHardwareType`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

#
# Dumping data for table `equipment_type`
#

INSERT INTO `equipment_type` VALUES (1, 'Domain Controller', 'address-book-new.png');
INSERT INTO `equipment_type` VALUES (2, 'File Server', 'network-server.png');
INSERT INTO `equipment_type` VALUES (3, 'Mail Server', 'mail-forward.png');
INSERT INTO `equipment_type` VALUES (4, 'Web Server', 'applications-internet.png');
INSERT INTO `equipment_type` VALUES (5, 'Other Server', 'computer.png');
INSERT INTO `equipment_type` VALUES (6, 'Printer', 'printer.png');
INSERT INTO `equipment_type` VALUES (7, 'Router', 'network-wired.png');
INSERT INTO `equipment_type` VALUES (8, 'Switch', 'network-wired.png');
INSERT INTO `equipment_type` VALUES (9, 'Firewall', 'system-lock-screen.png');
INSERT INTO `equipment_type` VALUES (10, 'WAN Link', 'network-wired.png');
INSERT INTO `equipment_type` VALUES (11, 'Terminal Server', 'network-workgroup.png');

# --------------------------------------------------------

#
# Table structure for table `nhtam_administrators`
#

DROP TABLE IF EXISTS `nhtam_administrators`;
CREATE TABLE `nhtam_administrators` (
  `intAdminID` int(11) NOT NULL auto_increment,
  `strUsername` varchar(50) NOT NULL default '',
  `strPassword` varchar(100) NOT NULL default '',
  `intLevel` int(1) NOT NULL default '0',
  `intNTAllowed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`intAdminID`),
  UNIQUE KEY `strUsername` (`strUsername`),
  KEY `intLevel` (`intLevel`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

#
# Dumping data for table `nhtam_administrators`
#

INSERT INTO `nhtam_administrators` VALUES (1, 'administrator', MD5('nhtam'), 9, 0);

