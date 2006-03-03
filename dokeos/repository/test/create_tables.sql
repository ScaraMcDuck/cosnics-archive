-- phpMyAdmin SQL Dump
-- version 2.6.4-pl1-Debian-1ubuntu1.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 03, 2006 at 03:17 PM
-- Server version: 4.0.24
-- PHP Version: 5.0.5-2ubuntu1.1
-- 
-- Database: `dokeoslcms`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `dokeos_document`
-- 

CREATE TABLE `dokeos_document` (
  `id` int(11) unsigned NOT NULL default '0',
  `path` text NOT NULL,
  `filename` text NOT NULL,
  `filesize` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `dokeos_document`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `dokeos_learning_object`
-- 

CREATE TABLE `dokeos_learning_object` (
  `id` int(11) unsigned NOT NULL default '0',
  `owner` int(11) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `title` text NOT NULL,
  `description` text,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `dokeos_learning_object`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `dokeos_link`
-- 

CREATE TABLE `dokeos_link` (
  `id` int(10) unsigned NOT NULL default '0',
  `url` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `dokeos_link`
-- 

