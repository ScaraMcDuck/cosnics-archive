CREATE TABLE `%prefix%forum` (
  `id` int(10) unsigned NOT NULL,
  `topics` int(10) NOT NULL default '0',
  `posts` int(10) NOT NULL default '0',
  `last_post_id` int(10) NOT NULL default '0',
  `cat_id` int(10) default NULL,
  `forum_type` int(10) default '0',
  PRIMARY KEY  (`id`)
);