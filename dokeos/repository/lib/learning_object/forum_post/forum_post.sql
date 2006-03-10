CREATE TABLE `%prefix%forum_post` (
  `id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  `topic_notify` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);