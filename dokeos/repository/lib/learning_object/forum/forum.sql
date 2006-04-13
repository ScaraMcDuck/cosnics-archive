CREATE TABLE `%prefix%forum` (
  `id` int(10) unsigned NOT NULL default '0',
  `topics` int(10) unsigned NOT NULL default '0',
  `posts` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `category_id` int(10) unsigned NOT NULL default '0',
  `forum_type` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);