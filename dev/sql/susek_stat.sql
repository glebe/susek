CREATE TABLE `susek_stat` (
`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`date_stamp` varchar(32) NOT NULL ,
`url` VARCHAR( 512 ) NOT NULL ,
`referrer` VARCHAR( 512 ) NULL ,
`agent` VARCHAR( 128 ) NULL ,
`ip` VARCHAR( 15 ) NOT NULL ,
`proxy` VARCHAR( 128 ) NULL ,
`forwarded` VARCHAR( 128 ) NULL ,
`host` VARCHAR( 128 ) NULL
) ENGINE = MYISAM ;