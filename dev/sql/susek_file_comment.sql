CREATE TABLE `susek_file_comment` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `date_added` varchar(32) NOT NULL,
  `date_modified` varchar(32) default NULL,
  `subject` varchar(255) default NULL,
  `content` text NOT NULL,
  `author_id` int(11) default NULL,
  `author` varchar(255) default NULL,
  `author_email` varchar(255) default NULL,
  `private` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;