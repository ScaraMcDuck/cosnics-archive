CREATE TABLE `%prefix%calendar_event` (
  `id` int(11) unsigned NOT NULL default '0',
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  PRIMARY KEY  (`id`)
);