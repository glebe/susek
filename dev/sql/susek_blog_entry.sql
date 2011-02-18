CREATE TABLE `susek_blog_entry` (
  `id` int(11) NOT NULL auto_increment,
  `date_added` varchar(32) NOT NULL,
  `date_modified` varchar(32) default NULL,
  `subject` varchar(255) default NULL,
  `content` text NOT NULL,
  `tags` text,
  `private` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;