CREATE TABLE `%prefix%student_publication` (
  `id` int(10) unsigned NOT NULL,
  `url` varchar(200) default NULL,
  `author` varchar(200) default NULL,
  `active` tinyint(4) default NULL,
  `accepted` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
);