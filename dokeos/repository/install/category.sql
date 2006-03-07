CREATE TABLE `%prefix%category` (
  `id` int(10) unsigned NOT NULL default '0',
  `title` text NOT NULL,
  `parent` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);