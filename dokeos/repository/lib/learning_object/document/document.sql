CREATE TABLE `%prefix%document` (
  `id` int(10) unsigned NOT NULL default '0',
  `path` text NOT NULL,
  `filename` text NOT NULL,
  `filesize` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;