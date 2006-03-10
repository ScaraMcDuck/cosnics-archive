CREATE TABLE `%prefix%forum_topic` (
  `id` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `replies` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `status` int(10) unsigned NOT NULL default '0',
  `notify` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);