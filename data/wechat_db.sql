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

DROP TABLE IF EXISTS `xyz_user_open_info`;
CREATE TABLE `xyz_user_open_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Auto increase id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'UserId related from user_info',
  `open_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1: Wechat',
  `open_app_id` varchar(200) NOT NULL DEFAULT '' COMMENT 'Open appid',
  `open_user_id` varchar(200) NOT NULL DEFAULT '' COMMENT 'user_id or openid in Open platform',
  `create_ts` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Create timestamp',
  PRIMARY KEY (`id`),
  KEY `open_user_id` (`open_user_id`),
  KEY `create_ts` (`create_ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User of open platform';


DROP TABLE IF EXISTS `xyz_wechat_access_token`;

CREATE TABLE `xyz_wechat_access_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Auto increase id',
  `app_id` varchar(128) NOT NULL DEFAULT '' COMMENT 'Wechat appid',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'TYPE. 1=ACCESS_TOKEN, 2=JSAPI_TICKET, ...',
  `access_token` varchar(256) NOT NULL DEFAULT '' COMMENT 'access_token from wechat api',
  `create_ts` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Create time seconds',
  `expire_ts` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Expire time seconds',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wechat access_token maintenance table.';