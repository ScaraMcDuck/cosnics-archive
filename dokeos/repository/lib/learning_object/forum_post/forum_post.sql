CREATE TABLE `dokeos_forum_post` (
  `id` int(10) unsigned NOT NULL,
  `topic_id` int(10) default '0',
  `topic_notify` tinyint(2) default NULL,
  PRIMARY KEY  (`id`)
);