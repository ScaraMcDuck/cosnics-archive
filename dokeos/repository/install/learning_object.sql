CREATE TABLE `dokeos_learning_object` (
  `id` int(11) unsigned NOT NULL default '0',
  `owner` int(11) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `title` text NOT NULL,
  `description` text,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`)
);