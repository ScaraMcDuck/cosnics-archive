CREATE TABLE `%prefix%calendar_event` (
  `id` int(10) unsigned NOT NULL default '0',
  `start_date` int(10) unsigned NOT NULL default '0',
  `end_date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;