DROP TABLE IF EXISTS `xyz_user_info`;
CREATE TABLE `xyz_user_info` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID, auto increment',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT 'User Name',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT 'Nick Name',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT 'Head Icon',
  `phone` varchar(50) NOT NULL DEFAULT '' COMMENT 'Phone Number',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0:OK;',
  `create_ts` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Create timestamp',
  PRIMARY KEY (`user_id`),
  KEY `status` (`status`),
  KEY `create_ts` (`create_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User Information';
