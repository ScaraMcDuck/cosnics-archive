CREATE TABLE `%prefix%learning_object_publication` (
  `id` int(10) unsigned NOT NULL default '0',
  `learning_object` int(10) unsigned NOT NULL default '0',
  `course` varchar(255) NOT NULL default '',
  `category` int(10) unsigned NOT NULL default '0',
  `from_date` int(10) unsigned NOT NULL default '0',
  `to_date` int(10) unsigned NOT NULL default '0',
  `hidden` int(1) unsigned NOT NULL default '0',
  `display_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `from_date` (`from_date`,`to_date`),
  KEY `hidden` (`hidden`),
  KEY `course` (`course`),
  KEY `category` (`category`)
);

CREATE TABLE `%prefix%learning_object_publication_group` (
  `publication` int(10) unsigned NOT NULL default '0',
  `group` int(10) unsigned NOT NULL default '0',
  KEY `publication` (`publication`)
);

CREATE TABLE `%prefix%learning_object_publication_user` (
  `publication` int(10) unsigned NOT NULL default '0',
  `user` int(10) unsigned NOT NULL default '0',
  KEY `publication` (`publication`)
);