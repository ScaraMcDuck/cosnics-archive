CREATE TABLE `%prefix%forum_post` (
  `id` int(10) unsigned NOT NULL default '0',
  `parent_post` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;