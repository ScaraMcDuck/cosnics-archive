CREATE TABLE `dokeos_forum_topic` (
  `id` int(10) unsigned NOT NULL,
  `views` int(10) default NULL,
  `replies` int(10) NOT NULL default '0',
  `last_post_id` int(10) NOT NULL default '0',
  `forum_id` int(10) NOT NULL default '0',
  `status` int(10) NOT NULL default '0',
  `notify` tinyint(2) default '0',
  PRIMARY KEY  (`id`)
);