CREATE TABLE `%prefix%learning_object_attachment` (
  `learning_object` int(10) unsigned NOT NULL default '0',
  `attachment` int(10) unsigned NOT NULL default '0',
  KEY `learning_object` (`learning_object`)
)CHARACTER SET utf8 COLLATE utf8_unicode_ci;