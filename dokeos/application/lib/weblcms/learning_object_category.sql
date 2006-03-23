CREATE TABLE `%prefix%learning_object_publication_category` (
  `id` int(10) unsigned NOT NULL default '0',
  `title` text NOT NULL,
  `course` varchar(255) NOT NULL default '',
  `tool` varchar(255) NOT NULL default '',
  `parent` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `course` (`course`),
  KEY `tool` (`tool`)
);