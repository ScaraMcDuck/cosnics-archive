CREATE TABLE `%prefix%student_publication` (
  `id` int(10) unsigned NOT NULL default '0',
  `url` text NOT NULL,
  `author` int(10) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0',
  `accepted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);