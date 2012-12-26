# phpMyAdmin SQL Dump
# version 2.5.7-pl1
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Oct 10, 2005 at 10:41 PM
# Server version: 4.0.24
# PHP Version: 4.3.11
#
# Database : `nhtam`
#

# --------------------------------------------------------

#
# Table structure for table `plugin_NTP_checklist`
#

CREATE TABLE `plugin_NTP_checklist` (
  `UID` int(11) NOT NULL auto_increment,
  `ei_UID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`UID`),
  UNIQUE KEY `ei_UID` (`ei_UID`)
);

# --------------------------------------------------------

#
# Table structure for table `plugin_NTP_last_result`
#

CREATE TABLE `plugin_NTP_last_result` (
  `ei_UID` int(11) NOT NULL default '0',
  `strResult` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`ei_UID`)
);

# --------------------------------------------------------

#
# Table structure for table `plugin_NTP_history`
#

CREATE TABLE `plugin_NTP_history` (
  `UID` int(11) NOT NULL auto_increment,
  `ei_UID` int(11) NOT NULL default '0',
  `datTimeStamp` timestamp(14) NOT NULL default '',
  `strResult` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`UID`),
  KEY `ei_UID` (`ei_UID`)
);

