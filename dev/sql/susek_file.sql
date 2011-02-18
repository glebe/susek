CREATE TABLE `susek_file` (
`id` INT (11) UNSIGNED  NOT NULL AUTO_INCREMENT,
`author_id` INT (11) UNSIGNED  NOT NULL,
`guid` VARCHAR( 36 ) NOT NULL ,
`title` VARCHAR ( 255 ) NOT NULL,
`date_added` VARCHAR ( 32 ) NOT NULL,
`filename` VARCHAR( 255 ) NOT NULL ,
`description` TEXT NULL ,
`tags` TEXT NULL ,
PRIMARY KEY ( `id`,`guid` )
) ENGINE = MYISAM ;