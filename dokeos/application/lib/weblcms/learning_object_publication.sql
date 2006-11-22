CREATE TABLE `%prefix%learning_object_publication` (
  `id` int(10) unsigned NOT NULL default '0',
  `learning_object` int(10) unsigned NOT NULL default '0',
  `course` varchar(255) NOT NULL default '',
  `tool` varchar(255) NOT NULL default '',
  `category` int(10) unsigned NOT NULL default '0',
  `from_date` int(10) unsigned NOT NULL default '0',
  `to_date` int(10) unsigned NOT NULL default '0',
  `hidden` int(1) unsigned NOT NULL default '0',
  `publisher` int(10) unsigned NOT NULL default '0',
  `published` int(10) unsigned NOT NULL default '0',
  `display_order` int(10) unsigned NOT NULL default '0',
  `email_sent` int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `from_date` (`from_date`,`to_date`),
  KEY `hidden` (`hidden`),
  KEY `course` (`course`),
  KEY `category` (`category`),
  KEY `tool` (`tool`),
  KEY `publisher` (`publisher`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `%prefix%learning_object_publication_group` (
  `publication` int(10) unsigned NOT NULL default '0',
  `group` int(10) unsigned NOT NULL default '0',
  KEY `publication` (`publication`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `%prefix%learning_object_publication_user` (
  `publication` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  KEY `publication` (`publication`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `%prefix%learning_object_publication_category` (
  `id` int(10) unsigned NOT NULL default '0',
  `title` text NOT NULL,
  `course` varchar(255) NOT NULL default '',
  `tool` varchar(255) NOT NULL default '',
  `parent` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`),
  KEY `tool` (`tool`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE `%prefix%course_module` (
  `course_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL default '0',
  `section` varchar(50) NOT NULL default 'basic',
  PRIMARY KEY  (`course_code`,`name`)
) CHARACTER SET utf8 COLLATE utf8_unicode_ci;