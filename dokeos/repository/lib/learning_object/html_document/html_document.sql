CREATE TABLE `%prefix%html_document` (
  `id` int(11) unsigned NOT NULL default '0',
  `path` text NOT NULL,
  `filename` text NOT NULL,
  `filesize` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);