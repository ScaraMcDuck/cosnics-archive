CREATE TABLE `%prefix%learning_object_publication_category` (
  `id` int(10) unsigned NOT NULL default '0',
  `title` text NOT NULL,
  `course` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `parent` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`),
  KEY `type` (`type`)
);
CREATE TABLE `%prefix%learning_object_publication_category_link` (
  `publication` int(10) unsigned NOT NULL default '0',
  `category` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`publication`)
);