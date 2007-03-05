-- MySQL dump 10.9
--
-- Host: localhost    Database: 17_dokeos_main
-- ------------------------------------------------------
-- Server version	4.1.14

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `user_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `user_id` (`user_id`)
);

--
-- Dumping data for table `admin`
--


/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
LOCK TABLES `admin` WRITE;
INSERT INTO `admin` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;

--
-- Table structure for table `basic_right`
--

DROP TABLE IF EXISTS `basic_right`;
CREATE TABLE `basic_right` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(250) default '',
  `description` text,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `basic_right`
--


/*!40000 ALTER TABLE `basic_right` DISABLE KEYS */;
LOCK TABLES `basic_right` WRITE;
INSERT INTO `basic_right` VALUES (1,'ViewRight','ViewRightDescription'),(2,'EditRight','EditRightDescription'),(3,'AddRight','AddRightDescription'),(4,'DeleteRight','DeleteRightDescription');
UNLOCK TABLES;
/*!40000 ALTER TABLE `basic_right` ENABLE KEYS */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
CREATE TABLE `class` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `code` varchar(40) default '',
  `name` text NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `class`
--


/*!40000 ALTER TABLE `class` DISABLE KEYS */;
LOCK TABLES `class` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `class` ENABLE KEYS */;

--
-- Table structure for table `class_user`
--

DROP TABLE IF EXISTS `class_user`;
CREATE TABLE `class_user` (
  `class_id` mediumint(8) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`class_id`,`user_id`)
);

--
-- Dumping data for table `class_user`
--


/*!40000 ALTER TABLE `class_user` DISABLE KEYS */;
LOCK TABLES `class_user` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `class_user` ENABLE KEYS */;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `code` varchar(40) NOT NULL default '',
  `directory` varchar(40) default NULL,
  `db_name` varchar(40) default NULL,
  `course_language` varchar(20) default NULL,
  `title` varchar(250) default NULL,
  `description` text,
  `category_code` varchar(40) default NULL,
  `visibility` tinyint(4) default '0',
  `show_score` int(11) NOT NULL default '1',
  `tutor_name` varchar(200) default NULL,
  `visual_code` varchar(40) default NULL,
  `department_name` varchar(30) default NULL,
  `department_url` varchar(180) default NULL,
  `disk_quota` int(10) unsigned default NULL,
  `last_visit` datetime default NULL,
  `last_edit` datetime default NULL,
  `creation_date` datetime default NULL,
  `expiration_date` datetime default NULL,
  `target_course_code` varchar(40) default NULL,
  `subscribe` tinyint(4) NOT NULL default '1',
  `unsubscribe` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`code`)
);

--
-- Dumping data for table `course`
--


/*!40000 ALTER TABLE `course` DISABLE KEYS */;
LOCK TABLES `course` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `course` ENABLE KEYS */;

--
-- Table structure for table `course_category`
--

DROP TABLE IF EXISTS `course_category`;
CREATE TABLE `course_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `code` varchar(40) NOT NULL default '',
  `parent_id` varchar(40) default NULL,
  `tree_pos` int(10) unsigned default NULL,
  `children_count` smallint(6) default NULL,
  `auth_course_child` enum('TRUE','FALSE') default 'TRUE',
  `auth_cat_child` enum('TRUE','FALSE') default 'TRUE',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`),
  KEY `tree_pos` (`tree_pos`)
);

--
-- Dumping data for table `course_category`
--


/*!40000 ALTER TABLE `course_category` DISABLE KEYS */;
LOCK TABLES `course_category` WRITE;
INSERT INTO `course_category` VALUES (1,'Language skills','LANG',NULL,1,0,'TRUE','TRUE'),(2,'PC Skills','PC',NULL,2,0,'TRUE','TRUE'),(3,'Projects','PROJ',NULL,3,0,'TRUE','TRUE');
UNLOCK TABLES;
/*!40000 ALTER TABLE `course_category` ENABLE KEYS */;

--
-- Table structure for table `course_module`
--

DROP TABLE IF EXISTS `course_module`;
CREATE TABLE `course_module` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `image` varchar(100) default NULL,
  `row` int(10) unsigned NOT NULL default '0',
  `column` int(10) unsigned NOT NULL default '0',
  `position` varchar(20) NOT NULL default 'basic',
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `course_module`
--


/*!40000 ALTER TABLE `course_module` DISABLE KEYS */;
LOCK TABLES `course_module` WRITE;
INSERT INTO `course_module` VALUES (1,'calendar_event','calendar/agenda.php','agenda.gif',1,1,'basic'),(2,'link','link/link.php','links.gif',4,1,'basic'),(3,'document','document/document.php','documents.gif',3,1,'basic'),(4,'student_publication','work/work.php','works.gif',3,2,'basic'),(5,'announcement','announcements/announcements.php','valves.gif',2,1,'basic'),(6,'user','user/user.php','members.gif',2,3,'basic'),(7,'bb_forum','phpbb/index.php','forum.gif',1,2,'basic'),(8,'quiz','exercice/exercice.php','quiz.gif',2,2,'basic'),(9,'group','group/group.php','group.gif',3,3,'basic'),(10,'course_description','course_description/','info.gif',1,3,'basic'),(11,'chat','chat/chat.php','chat.gif',0,0,'external'),(12,'dropbox','dropbox/index.php','dropbox.gif',4,2,'basic'),(13,'tracking','tracking/courseLog.php','statistics.gif',1,3,'courseadmin'),(14,'homepage_link','link/link.php?action=addlink','npage.gif',1,1,'courseadmin'),(15,'course_setting','course_info/infocours.php','reference.gif',1,2,'courseadmin'),(16,'External','','external.gif',0,0,'external'),(17,'AddedLearnpath','','scormbuilder.gif',0,0,'external'),(18,'conference','online/online.php','conf.gif',0,0,'external'),(19,'backup','coursecopy/backup.php','backup.gif',2,1,'courseadmin'),(20,'copy_course_content','coursecopy/copy_course.php','copy.gif',2,2,'courseadmin'),(21,'recycle_course','coursecopy/recycle_course.php','recycle.gif',2,3,'courseadmin'),(22,'learnpath','scorm/scormdocument.php','scorm.gif',5,1,'basic'),(23,'course_rights','course_info/course_rights.php','reference.gif',2,3,'courseadmin');
UNLOCK TABLES;
/*!40000 ALTER TABLE `course_module` ENABLE KEYS */;

--
-- Table structure for table `course_rel_class`
--

DROP TABLE IF EXISTS `course_rel_class`;
CREATE TABLE `course_rel_class` (
  `course_code` char(40) NOT NULL default '',
  `class_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`course_code`,`class_id`)
);

--
-- Dumping data for table `course_rel_class`
--


/*!40000 ALTER TABLE `course_rel_class` DISABLE KEYS */;
LOCK TABLES `course_rel_class` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `course_rel_class` ENABLE KEYS */;

--
-- Table structure for table `course_rel_user`
--

DROP TABLE IF EXISTS `course_rel_user`;
CREATE TABLE `course_rel_user` (
  `course_code` varchar(40) NOT NULL default '',
  `user_id` int(10) unsigned NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '5',
  `role` varchar(60) default NULL,
  `group_id` int(11) NOT NULL default '0',
  `tutor_id` int(10) unsigned NOT NULL default '0',
  `sort` int(11) default NULL,
  `user_course_cat` int(11) default '0',
  PRIMARY KEY  (`course_code`,`user_id`)
);

--
-- Dumping data for table `course_rel_user`
--


/*!40000 ALTER TABLE `course_rel_user` DISABLE KEYS */;
LOCK TABLES `course_rel_user` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `course_rel_user` ENABLE KEYS */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `original_name` varchar(255) default NULL,
  `english_name` varchar(255) default NULL,
  `isocode` varchar(10) default NULL,
  `dokeos_folder` varchar(250) default NULL,
  `available` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `language`
--


/*!40000 ALTER TABLE `language` DISABLE KEYS */;
LOCK TABLES `language` WRITE;
INSERT INTO `language` VALUES (18,'Melayu (Bahasa M.)','malay','ms','malay',0),(17,'Nihongo','japanese','ja','japanese',0),(16,'Italiano','italian','it','italian',1),(14,'Magyar','hungarian','hu','hungarian',1),(13,'Ellinika','greek','el','greek',0),(12,'Deutsch','german','de','german',1),(11,'Galego','galician','gl','galician',1),(15,'Indonesia (Bahasa I.)','indonesian','id','indonesian',1),(10,'Fran&ccedil;ais','french','fr','french',1),(9,'Suomi','finnish','fi','finnish',1),(8,'English','english','en','english',1),(7,'Nederlands','dutch','nl','dutch',1),(6,'Dansk','danish','da','danish',1),(5,'Hrvatski','croatian','hr','croatian',0),(4,'Catal&agrave;','catalan','ca','catalan',0),(3,'Balgarski','bulgarian','bg','bulgarian',1),(2,'Portugu&ecirc;s (Brazil)','brazilian','pt-BR','brazilian',1),(1,'Arabija (el)','arabic','ar','arabic',0),(19,'Polski','polish','pl','polish',1),(20,'Portugu&ecirc;s (Portugal)','portuguese','pt','portuguese',1),(21,'Russkij','russian','ru','russian',1),(22,'Chinese (simplified)','simpl_chinese','zh','simpl_chinese',1),(23,'Slovenscina','slovenian','sl','slovenian',1),(24,'Espa&ntilde;ol','spanish','es','spanish',1),(25,'Svenska','swedish','sv','swedish',1),(26,'Thai','thai','th','thai',1),(27,'T&uuml;rk&ccedil;e','turkce','tr','turkce',0),(28,'Vi&ecirc;t (Ti&ecirc;ng V.)','vietnamese','vi','vietnamese',0),(29,'Norsk','norwegian','no','norwegian',1),(30,'Farsi','persian','fa','persian',1),(31,'Srpski','serbian','sr','serbian',1),(32,'Bosanski','bosnian',NULL,'bosnian',1),(33,'Swahili (kiSw.)','swahili','sw','swahili',0),(34,'Esperanto','esperanto','eo','esperanto',1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `language` ENABLE KEYS */;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `location` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `location`
--

--
-- Disabled course_homepage location for now:
-- (12,'platform|courses|course,DEFAULT|tool,course_homepage'),
--

/*!40000 ALTER TABLE `location` DISABLE KEYS */;
LOCK TABLES `location` WRITE;
INSERT INTO `location` VALUES 
(1,'platform|courses|course,DEFAULT'),
(2,'platform|courses|course,DEFAULT|tool,announcement'),
(3,'platform|courses|course,DEFAULT|tool,backup'),
(4,'platform|courses|course,DEFAULT|tool,bb_forum'),
(5,'platform|courses|course,DEFAULT|tool,bb_post'),
(6,'platform|courses|course,DEFAULT|tool,bb_thread'),
(7,'platform|courses|course,DEFAULT|tool,calendar_event'),
(8,'platform|courses|course,DEFAULT|tool,chat'),
(9,'platform|courses|course,DEFAULT|tool,conference'),
(10,'platform|courses|course,DEFAULT|tool,copy_course_content'),
(11,'platform|courses|course,DEFAULT|tool,course_description'),
(13,'platform|courses|course,DEFAULT|tool,course_rights'),
(14,'platform|courses|course,DEFAULT|tool,course_setting'),
(15,'platform|courses|course,DEFAULT|tool,document'),
(16,'platform|courses|course,DEFAULT|tool,dropbox'),
(17,'platform|courses|course,DEFAULT|tool,group'),
(18,'platform|courses|course,DEFAULT|tool,homepage_link'),
(19,'platform|courses|course,DEFAULT|tool,learnpath'),
(20,'platform|courses|course,DEFAULT|tool,link'),
(21,'platform|courses|course,DEFAULT|tool,quiz'),
(22,'platform|courses|course,DEFAULT|tool,recycle_course'),
(23,'platform|courses|course,DEFAULT|tool,student_publication'),
(24,'platform|courses|course,DEFAULT|tool,tracking'),
(25,'platform|courses|course,DEFAULT|tool,user');
UNLOCK TABLES;
/*!40000 ALTER TABLE `location` ENABLE KEYS */;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(250) default '',
  `type` varchar(40) default 'global',
  `user_id` int(10) unsigned NOT NULL default '0',
  `description` text,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `role`
--


/*!40000 ALTER TABLE `role` DISABLE KEYS */;
LOCK TABLES `role` WRITE;
INSERT INTO `role` VALUES 
(1,'AnonymousVisitorRole','global',1,'AnonymousVisitorRoleDescription'),
(2,'StudentRole','global',1,'StudentRoleDescription'),
(3,'TeacherRole','global',1,'TeacherRoleDescription'),
(4,'PlatformAdminRole','global',1,'PlatformAdminRoleDescription'),
(5,'AnonymousGuestCourseMemberRole','local',1,'AnonymousGuestCourseMemberRoleDescription'),
(6,'RegisteredGuestCourseMemberRole','local',1,'RegisteredGuestCourseMemberRoleDescription'),
(7,'NormalCourseMemberRole','local',1,'NormalCourseMemberRoleDescription'),
(8,'TeachingAssistantRole','local',1,'TeachingAssistantRoleDescription'),
(9,'CourseAdminRole','local',1,'CourseAdminRoleDescription');
UNLOCK TABLES;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

--
-- Table structure for table `role_right_location`
--

DROP TABLE IF EXISTS `role_right_location`;
CREATE TABLE `role_right_location` (
  `role_id` mediumint(8) unsigned NOT NULL default '0',
  `right_id` mediumint(8) unsigned NOT NULL default '0',
  `location_id` mediumint(8) unsigned NOT NULL default '0',
  `value` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`role_id`,`right_id`,`location_id`)
);

--
-- Dumping data for table `role_right_location`
--


/*!40000 ALTER TABLE `role_right_location` DISABLE KEYS */;
LOCK TABLES `role_right_location` WRITE;
INSERT INTO `role_right_location` VALUES 
(6, 1, 1, '1'),
(6, 1, 7, '1'),
(6, 1, 11, '1'),
(6, 1, 12, '1'),
(6, 1, 15, '1'),
(6, 1, 19, '1'),
(6, 1, 20, '1'),
(7, 1, 1, '1'),
(7, 1, 7, '1'),
(7, 1, 11, '1'),
(7, 1, 12, '1'),
(7, 1, 15, '1'),
(7, 1, 19, '1'),
(7, 1, 20, '1'),
(7, 3, 23, '1'),
(8, 1, 1, '1'),
(8, 1, 2, '1'),
(8, 1, 3, '1'),
(8, 1, 4, '1'),
(8, 1, 5, '1'),
(8, 1, 6, '1'),
(8, 1, 7, '1'),
(8, 1, 8, '1'),
(8, 1, 9, '1'),
(8, 1, 10, '1'),
(8, 1, 11, '1'),
(8, 1, 12, '1'),
(8, 1, 15, '1'),
(8, 1, 16, '1'),
(8, 1, 17, '1'),
(8, 1, 18, '1'),
(8, 1, 19, '1'),
(8, 1, 20, '1'),
(8, 1, 21, '1'),
(8, 1, 23, '1'),
(8, 1, 24, '1'),
(8, 1, 25, '1'),
(8, 2, 4, '1'),
(8, 2, 5, '1'),
(8, 2, 6, '1'),
(8, 2, 17, '1'),
(8, 2, 23, '1'),
(8, 3, 4, '1'),
(8, 3, 5, '1'),
(8, 3, 6, '1'),
(8, 3, 17, '1'),
(8, 3, 18, '1'),
(8, 3, 20, '1'),
(8, 3, 21, '1'),
(8, 3, 23, '1'),
(8, 2, 2, '1'),
(8, 3, 2, '1'),
(8, 2, 7, '1'),
(8, 3, 7, '1'),
(8, 2, 8, '1'),
(8, 3, 8, '1'),
(8, 2, 9, '1'),
(8, 3, 9, '1'),
(8, 2, 10, '1'),
(8, 3, 10, '1'),
(8, 2, 11, '1'),
(8, 3, 11, '1'),
(8, 2, 15, '1'),
(8, 3, 15, '1'),
(8, 2, 16, '1'),
(8, 3, 16, '1'),
(8, 2, 18, '1'),
(8, 2, 19, '1'),
(8, 3, 19, '1'),
(8, 2, 20, '1'),
(8, 2, 21, '1'),
(8, 2, 25, '1'),
(8, 3, 25, '1'),
(9, 1, 1, '1'),
(9, 1, 2, '1'),
(9, 1, 3, '1'),
(9, 1, 4, '1'),
(9, 1, 5, '1'),
(9, 1, 6, '1'),
(9, 1, 7, '1'),
(9, 1, 8, '1'),
(9, 1, 9, '1'),
(9, 1, 10, '1'),
(9, 1, 11, '1'),
(9, 1, 12, '1'),
(9, 1, 13, '1'),
(9, 1, 14, '1'),
(9, 1, 15, '1'),
(9, 1, 16, '1'),
(9, 1, 17, '1'),
(9, 1, 18, '1'),
(9, 1, 19, '1'),
(9, 1, 20, '1'),
(9, 1, 21, '1'),
(9, 1, 22, '1'),
(9, 1, 23, '1'),
(9, 1, 24, '1'),
(9, 1, 25, '1'),
(9, 2, 1, '1'),
(9, 2, 2, '1'),
(9, 2, 3, '1'),
(9, 2, 4, '1'),
(9, 2, 5, '1'),
(9, 2, 6, '1'),
(9, 2, 7, '1'),
(9, 2, 8, '1'),
(9, 2, 9, '1'),
(9, 2, 10, '1'),
(9, 2, 11, '1'),
(9, 2, 12, '1'),
(9, 2, 13, '1'),
(9, 2, 14, '1'),
(9, 2, 15, '1'),
(9, 2, 16, '1'),
(9, 2, 17, '1'),
(9, 2, 18, '1'),
(9, 2, 19, '1'),
(9, 2, 20, '1'),
(9, 2, 21, '1'),
(9, 2, 22, '1'),
(9, 2, 23, '1'),
(9, 2, 24, '1'),
(9, 2, 25, '1'),
(9, 3, 1, '1'),
(9, 3, 2, '1'),
(9, 3, 3, '1'),
(9, 3, 4, '1'),
(9, 3, 5, '1'),
(9, 3, 6, '1'),
(9, 3, 7, '1'),
(9, 3, 8, '1'),
(9, 3, 9, '1'),
(9, 3, 10, '1'),
(9, 3, 11, '1'),
(9, 3, 12, '1'),
(9, 3, 13, '1'),
(9, 3, 14, '1'),
(9, 3, 15, '1'),
(9, 3, 16, '1'),
(9, 3, 17, '1'),
(9, 3, 18, '1'),
(9, 3, 19, '1'),
(9, 3, 20, '1'),
(9, 3, 21, '1'),
(9, 3, 22, '1'),
(9, 3, 23, '1'),
(9, 3, 24, '1'),
(9, 3, 25, '1'),
(9, 4, 1, '1'),
(9, 4, 2, '1'),
(9, 4, 3, '1'),
(9, 4, 4, '1'),
(9, 4, 5, '1'),
(9, 4, 6, '1'),
(9, 4, 7, '1'),
(9, 4, 8, '1'),
(9, 4, 9, '1'),
(9, 4, 10, '1'),
(9, 4, 11, '1'),
(9, 4, 12, '1'),
(9, 4, 13, '1'),
(9, 4, 14, '1'),
(9, 4, 15, '1'),
(9, 4, 16, '1'),
(9, 4, 17, '1'),
(9, 4, 18, '1'),
(9, 4, 19, '1'),
(9, 4, 20, '1'),
(9, 4, 21, '1'),
(9, 4, 22, '1'),
(9, 4, 23, '1'),
(9, 4, 24, '1'),
(9, 4, 25, '1');
UNLOCK TABLES;
/*!40000 ALTER TABLE `role_right_location` ENABLE KEYS */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `sess_id` varchar(32) NOT NULL default '',
  `sess_name` varchar(10) NOT NULL default '',
  `sess_time` int(11) NOT NULL default '0',
  `sess_start` int(11) NOT NULL default '0',
  `sess_value` text NOT NULL,
  PRIMARY KEY  (`sess_id`)
);

--
-- Dumping data for table `session`
--


/*!40000 ALTER TABLE `session` DISABLE KEYS */;
LOCK TABLES `session` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;

--
-- Table structure for table `settings_current`
--

DROP TABLE IF EXISTS `settings_current`;
CREATE TABLE `settings_current` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `variable` varchar(255) default NULL,
  `subkey` varchar(255) default NULL,
  `type` varchar(255) default NULL,
  `category` varchar(255) default NULL,
  `selected_value` varchar(255) default NULL,
  `title` varchar(255) NOT NULL default '',
  `comment` varchar(255) default NULL,
  `scope` varchar(50) default NULL,
  `subkeytext` varchar(255) default NULL,
  UNIQUE KEY `id` (`id`)
);

--
-- Dumping data for table `settings_current`
--


/*!40000 ALTER TABLE `settings_current` DISABLE KEYS */;
LOCK TABLES `settings_current` WRITE;
INSERT INTO `settings_current` VALUES (1,'Institution',NULL,'textfield','Platform','{ORGANISATIONNAME}','InstitutionTitle','InstitutionComment','platform',NULL),(2,'InstitutionUrl',NULL,'textfield','Platform','{ORGANISATIONURL}','InstitutionUrlTitle','InstitutionUrlComment',NULL,NULL),(3,'siteName',NULL,'textfield','Platform','{CAMPUSNAME}','SiteNameTitle','SiteNameComment',NULL,NULL),(4,'emailAdministrator',NULL,'textfield','Platform','{ADMINEMAIL}','emailAdministratorTitle','emailAdministratorComment',NULL,NULL),(5,'administratorSurname',NULL,'textfield','Platform','{ADMINLASTNAME}','administratorSurnameTitle','administratorSurnameComment',NULL,NULL),(6,'administratorName',NULL,'textfield','Platform','{ADMINFIRSTNAME}','administratorNameTitle','administratorNameComment',NULL,NULL),(7,'show_administrator_data',NULL,'radio','Platform','true','ShowAdministratorDataTitle','ShowAdministratorDataComment',NULL,NULL),(8,'homepage_view',NULL,'radio','Course','default','HomepageViewTitle','HomepageViewComment',NULL,NULL),(9,'show_toolshortcuts',NULL,'radio','Course','false','ShowToolShortcutsTitle','ShowToolShortcutsComment',NULL,NULL),(10,'show_student_view',NULL,'radio','Course','true','ShowStudentViewTitle','ShowStudentViewComment',NULL,NULL),(11,'allow_group_categories',NULL,'radio','Course','false','AllowGroupCategories','AllowGroupCategoriesComment',NULL,NULL),(12,'server_type',NULL,'radio','Platform','production','ServerStatusTitle','ServerStatusComment',NULL,NULL),(13,'platformLanguage',NULL,'link','Languages','{PLATFORMLANGUAGE}','PlatformLanguageTitle','PlatformLanguageComment',NULL,NULL),(14,'showonline','world','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineWorld'),(15,'showonline','users','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineUsers'),(16,'showonline','course','checkbox','Platform','true','ShowOnlineTitle','ShowOnlineComment',NULL,'ShowOnlineCourse'),(17,'profile','name','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'name'),(18,'profile','officialcode','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'officialcode'),(19,'profile','email','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'Email'),(20,'profile','picture','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'UserPicture'),(21,'profile','login','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'Login'),(22,'profile','password','checkbox','User','false','ProfileChangesTitle','ProfileChangesComment',NULL,'UserPassword'),(23,'profile','language','checkbox','User','true','ProfileChangesTitle','ProfileChangesComment',NULL,'Language'),(24,'default_document_quotum',NULL,'textfield','Course','50000000','DefaultDocumentQuotumTitle','DefaultDocumentQuotumComment',NULL,NULL),(25,'registration','officialcode','checkbox','User','true','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'OfficialCode'),(26,'registration','email','checkbox','User','true','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'Email'),(27,'registration','language','checkbox','User','true','RegistrationRequiredFormsTitle','RegistrationRequiredFormsComment',NULL,'Language'),(28,'default_group_quotum',NULL,'textfield','Course','5000000','DefaultGroupQuotumTitle','DefaultGroupQuotumComment',NULL,NULL),(29,'allow_registration',NULL,'radio','Platform','{ALLOWSELFREGISTRATION}','AllowRegistrationTitle','AllowRegistrationComment',NULL,NULL),(30,'allow_registration_as_teacher',NULL,'radio','Platform','{ALLOWTEACHERSELFREGISTRATION}','AllowRegistrationAsTeacherTitle','AllowRegistrationAsTeacherComment',NULL,NULL),(31,'allow_lostpassword',NULL,'radio','Platform','true','AllowLostPasswordTitle','AllowLostPasswordComment',NULL,NULL),(32,'allow_user_headings',NULL,'radio','Course','false','AllowUserHeadings','AllowUserHeadingsComment',NULL,NULL),(33,'course_create_active_tools','course_description','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'CourseDescription'),(34,'course_create_active_tools','agenda','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Agenda'),(35,'course_create_active_tools','documents','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Documents'),(36,'course_create_active_tools','learning_path','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'LearningPath'),(37,'course_create_active_tools','links','checkbox','Tools','true','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Links'),(38,'course_create_active_tools','announcements','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Announcements'),(39,'course_create_active_tools','forums','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Forums'),(40,'course_create_active_tools','dropbox','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Dropbox'),(41,'course_create_active_tools','quiz','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Quiz'),(42,'course_create_active_tools','users','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Users'),(43,'course_create_active_tools','groups','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Groups'),(44,'course_create_active_tools','chat','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'Chat'),(45,'course_create_active_tools','online_conference','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'OnlineConference'),(46,'course_create_active_tools','student_publications','checkbox','Tools','false','CourseCreateActiveToolsTitle','CourseCreateActiveToolsComment',NULL,'StudentPublications'),(47,'allow_personal_agenda',NULL,'radio','User','false','AllowPersonalAgendaTitle','AllowPersonalAgendaComment',NULL,NULL),(48,'display_coursecode_in_courselist',NULL,'radio','Platform','true','DisplayCourseCodeInCourselistTitle','DisplayCourseCodeInCourselistComment',NULL,NULL),(49,'display_teacher_in_courselist',NULL,'radio','Platform','true','DisplayTeacherInCourselistTitle','DisplayTeacherInCourselistComment',NULL,NULL),(50,'use_document_title',NULL,'radio','Tools','true','UseDocumentTitleTitle','UseDocumentTitleComment',NULL,NULL),(51,'permanently_remove_deleted_files','NULL','radio','Tools','false','PermanentlyRemoveFilesTitle','PermanentlyRemoveFilesComment',NULL,NULL),(52,'dropbox_allow_overwrite',NULL,'radio','Tools','true','DropboxAllowOverwriteTitle','DropboxAllowOverwriteComment',NULL,NULL),(53,'dropbox_max_filesize',NULL,'textfield','Tools','100000000','DropboxMaxFilesizeTitle','DropboxMaxFilesizeComment',NULL,NULL),(54,'dropbox_allow_just_upload',NULL,'radio','Tools','true','DropboxAllowJustUploadTitle','DropboxAllowJustUploadComment',NULL,NULL),(55,'dropbox_allow_student_to_student',NULL,'radio','Tools','true','DropboxAllowStudentToStudentTitle','DropboxAllowStudentToStudentComment',NULL,NULL),(56,'dropbox_allow_group',NULL,'radio','Tools','true','DropboxAllowGroupTitle','DropboxAllowGroupComment',NULL,NULL),(57,'dropbox_allow_mailing',NULL,'radio','Tools','false','DropboxAllowMailingTitle','DropboxAllowMailingComment',NULL,NULL),(58,'administratorTelephone',NULL,'textfield','Platform','(000) 001 02 03','administratorTelephoneTitle','administratorTelephoneComment',NULL,NULL),(59,'extended_profile',NULL,'radio','User','false','ExtendedProfileTitle','ExtendedProfileComment',NULL,NULL),(61,'show_navigation_menu',NULL,'radio','Course','true','ShowNavigationMenuTitle','ShowNavigationMenuComment',NULL,NULL),(62,'show_icons_in_navigation_menu',NULL,'radio','course','false','ShowIconsInNavigationsMenuTitle','ShowIconsInNavigationsMenuComment',NULL,NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `settings_current` ENABLE KEYS */;

--
-- Table structure for table `settings_options`
--

DROP TABLE IF EXISTS `settings_options`;
CREATE TABLE `settings_options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `variable` varchar(255) default NULL,
  `value` varchar(255) default NULL,
  `display_text` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
);

--
-- Dumping data for table `settings_options`
--


/*!40000 ALTER TABLE `settings_options` DISABLE KEYS */;
LOCK TABLES `settings_options` WRITE;
INSERT INTO `settings_options` VALUES (11,'show_administrator_data','true','Yes'),(12,'show_administrator_data','false','No'),(13,'homepage_view','default','HomepageViewDefault'),(14,'homepage_view','basic_tools_fixed','HomepageViewFixed'),(15,'show_toolshortcuts','true','Yes'),(16,'show_toolshortcuts','false','No'),(17,'show_student_view','true','Yes'),(18,'show_student_view','false','No'),(19,'allow_group_categories','true','Yes'),(20,'allow_group_categories','false','No'),(22,'server_type','production','ProductionServer'),(23,'server_type','test','TestServer'),(26,'allow_name_change','true','Yes'),(27,'allow_name_change','false','No'),(28,'allow_officialcode_change','true','Yes'),(29,'allow_officialcode_change','false','No'),(30,'allow_registration','true','Yes'),(31,'allow_registration','false','No'),(32,'allow_registration_as_teacher','true','Yes'),(33,'allow_registration_as_teacher','false','No'),(34,'allow_lostpassword','true','Yes'),(35,'allow_lostpassword','false','No'),(36,'allow_user_headings','true','Yes'),(37,'allow_user_headings','false','No'),(38,'allow_personal_agenda','true','Yes'),(39,'allow_personal_agenda','false','No'),(40,'display_coursecode_in_courselist','true','Yes'),(41,'display_coursecode_in_courselist','false','No'),(42,'display_teacher_in_courselist','true','Yes'),(43,'display_teacher_in_courselist','false','No'),(44,'use_document_title','true','Yes'),(45,'use_document_title','false','No'),(46,'permanently_remove_deleted_files','true','Yes'),(47,'permanently_remove_deleted_files','false','No'),(48,'dropbox_allow_overwrite','true','Yes'),(49,'dropbox_allow_overwrite','false','No'),(50,'dropbox_allow_just_upload','true','Yes'),(51,'dropbox_allow_just_upload','false','No'),(52,'dropbox_allow_student_to_student','true','Yes'),(53,'dropbox_allow_student_to_student','false','No'),(54,'dropbox_allow_group','true','Yes'),(55,'dropbox_allow_group','false','No'),(56,'dropbox_allow_mailing','true','Yes'),(57,'dropbox_allow_mailing','false','No'),(58,'extended_profile','true','Yes'),(59,'extended_profile','false','No'),(62,'show_navigation_menu','true','Yes'),(63,'show_navigation_menu','false','No'),(64,'show_icons_in_navigation_menu','true','Yes'),(65,'show_icons_in_navigation_menu','false','No');
UNLOCK TABLES;
/*!40000 ALTER TABLE `settings_options` ENABLE KEYS */;

--
-- Table structure for table `sys_announcement`
--

DROP TABLE IF EXISTS `sys_announcement`;
CREATE TABLE `sys_announcement` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date_start` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible_teacher` enum('0','1') NOT NULL default '0',
  `visible_student` enum('0','1') NOT NULL default '0',
  `visible_guest` enum('0','1') NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
);

--
-- Dumping data for table `sys_announcement`
--


/*!40000 ALTER TABLE `sys_announcement` DISABLE KEYS */;
LOCK TABLES `sys_announcement` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `sys_announcement` ENABLE KEYS */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `lastname` varchar(60) default NULL,
  `firstname` varchar(60) default NULL,
  `username` varchar(20) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `auth_source` varchar(50) default 'platform',
  `email` varchar(100) default NULL,
  `status` tinyint(4) NOT NULL default '5',
  `official_code` varchar(40) default NULL,
  `phone` varchar(30) default NULL,
  `picture_uri` varchar(250) default NULL,
  `creator_id` int(10) unsigned default NULL,
  `competences` text,
  `diplomas` text,
  `openarea` text,
  `teach` text,
  `productions` varchar(250) default NULL,
  `chatcall_user_id` int(10) unsigned NOT NULL default '0',
  `chatcall_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `chatcall_text` varchar(50) NOT NULL default '',
  `language` varchar(40) default NULL,
  `disk_quota` int(10) unsigned NOT NULL default '200000000',
  `database_quota` int(10) unsigned NOT NULL default '300',
  `version_quota` int(10) unsigned NOT NULL default '20',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`)
);

--
-- Dumping data for table `user`
--


/*!40000 ALTER TABLE `user` DISABLE KEYS */;
LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'{ADMINLASTNAME}','{ADMINFIRSTNAME}','{ADMINLOGIN}','{ADMINPASSWORD}','{PLATFORM_AUTH_SOURCE}','{ADMINEMAIL}',1,'ADMIN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'0000-00-00 00:00:00','',NULL,'200000000','300');
UNLOCK TABLES;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `role_id` mediumint(8) unsigned NOT NULL default '0',
  `location_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`role_id`,`location_id`)
);

--
-- Dumping data for table `user_role`
--


/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
LOCK TABLES `user_role` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

