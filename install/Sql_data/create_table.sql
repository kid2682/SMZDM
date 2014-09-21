-- --------------------------------------------------------
-- Host:                         221.12.127.62
-- Server version:               5.0.45 - Source distribution
-- Server OS:                    redhat-linux-gnu
-- HeidiSQL Version:             7.0.0.4331
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table zdm.pin_ad
CREATE TABLE IF NOT EXISTS `pin_ad` (
  `id` int(5) NOT NULL auto_increment,
  `board_id` smallint(5) NOT NULL,
  `type` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `extimg` varchar(255) NOT NULL,
  `extval` varchar(200) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `start_time` int(10) NOT NULL,
  `end_time` int(10) NOT NULL,
  `clicks` int(10) NOT NULL default '0',
  `add_time` int(10) NOT NULL default '0',
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_adboard
CREATE TABLE IF NOT EXISTS `pin_adboard` (
  `id` smallint(5) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `tpl` varchar(20) NOT NULL,
  `width` smallint(5) NOT NULL,
  `height` smallint(5) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_admin
CREATE TABLE IF NOT EXISTS `pin_admin` (
  `id` mediumint(6) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role_id` smallint(5) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `last_time` int(10) NOT NULL default '0',
  `email` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_name` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_admin_auth
CREATE TABLE IF NOT EXISTS `pin_admin_auth` (
  `role_id` tinyint(3) NOT NULL,
  `menu_id` smallint(6) NOT NULL,
  KEY `role_id` (`role_id`,`menu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_admin_role
CREATE TABLE IF NOT EXISTS `pin_admin_role` (
  `id` tinyint(3) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `remark` text NOT NULL,
  `ordid` tinyint(3) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_article
CREATE TABLE IF NOT EXISTS `pin_article` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `cate_id` smallint(4) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `colors` varchar(10) NOT NULL,
  `author` varchar(100) NOT NULL,
  `tags` varchar(100) NOT NULL,
  `img` varchar(255) NOT NULL,
  `intro` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `comments` int(10) unsigned NOT NULL default '0' COMMENT '评论数',
  `hits` int(10) unsigned NOT NULL default '0' COMMENT '浏览数',
  `ordid` tinyint(3) unsigned NOT NULL default '255' COMMENT '排序值',
  `add_time` int(10) unsigned NOT NULL default '0',
  `last_time` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_article_cate
CREATE TABLE IF NOT EXISTS `pin_article_cate` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `type` tinyint(1) NOT NULL default '0',
  `name` varchar(100) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `img` varchar(255) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL default '0',
  `spid` varchar(50) NOT NULL,
  `ordid` smallint(4) unsigned NOT NULL default '255',
  `status` tinyint(1) NOT NULL default '1',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_article_page
CREATE TABLE IF NOT EXISTS `pin_article_page` (
  `cate_id` smallint(4) unsigned NOT NULL default '0',
  `title` varchar(120) NOT NULL,
  `info` text NOT NULL,
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  `last_time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_article_re
CREATE TABLE IF NOT EXISTS `pin_article_re` (
  `article_id` int(10) NOT NULL default '0',
  `cate_id` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_auto_user
CREATE TABLE IF NOT EXISTS `pin_auto_user` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `users` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_badword
CREATE TABLE IF NOT EXISTS `pin_badword` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `word_type` tinyint(1) unsigned NOT NULL default '1' COMMENT '1：禁用；2：替换；3：审核',
  `badword` varchar(100) NOT NULL,
  `replaceword` varchar(100) NOT NULL,
  `add_time` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_flink
CREATE TABLE IF NOT EXISTS `pin_flink` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `img` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `cate_id` smallint(5) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_flink_cate
CREATE TABLE IF NOT EXISTS `pin_flink_cate` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_ipban
CREATE TABLE IF NOT EXISTS `pin_ipban` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `expires_time` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_item_site
CREATE TABLE IF NOT EXISTS `pin_item_site` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `author` varchar(50) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `status` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_mall
CREATE TABLE IF NOT EXISTS `pin_mall` (
  `id` int(10) NOT NULL auto_increment,
  `aid` varchar(50) NOT NULL,
  `title` varchar(50) default NULL,
  `domain` varchar(255) NOT NULL,
  `abst` varchar(255) NOT NULL,
  `info` text,
  `url_dm` varchar(255) NOT NULL,
  `url_yqf` varchar(255) NOT NULL,
  `url_other` varchar(255) NOT NULL,
  `url` varchar(255) default NULL,
  `cps` varchar(255) default NULL,
  `img` varchar(255) default NULL,
  `cid` int(11) default '0',
  `status` int(11) default '1',
  `ordid` int(11) default '0',
  `add_time` int(11) default '0',
  `seo_title` varchar(255) default NULL,
  `seo_keys` varchar(255) default NULL,
  `seo_desc` text,
  `index` varchar(2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_mall_cate
CREATE TABLE IF NOT EXISTS `pin_mall_cate` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL default '0',
  `spid` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_mall_comment
CREATE TABLE IF NOT EXISTS `pin_mall_comment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `mall_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `uname` varchar(20) NOT NULL,
  `info` text NOT NULL,
  `add_time` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_menu
CREATE TABLE IF NOT EXISTS `pin_menu` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `pid` smallint(6) NOT NULL,
  `module_name` varchar(20) NOT NULL,
  `action_name` varchar(20) NOT NULL,
  `data` varchar(120) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `often` tinyint(1) NOT NULL default '0',
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `display` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_message
CREATE TABLE IF NOT EXISTS `pin_message` (
  `id` int(10) NOT NULL auto_increment,
  `ftid` int(10) unsigned NOT NULL,
  `from_id` int(10) NOT NULL,
  `from_name` varchar(50) NOT NULL,
  `to_id` int(10) NOT NULL,
  `to_name` varchar(50) NOT NULL,
  `add_time` int(10) NOT NULL,
  `info` text NOT NULL,
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_message_tpl
CREATE TABLE IF NOT EXISTS `pin_message_tpl` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(20) NOT NULL,
  `is_sys` tinyint(1) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL COMMENT '别名',
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_nav
CREATE TABLE IF NOT EXISTS `pin_nav` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `type` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `alias` varchar(20) NOT NULL,
  `link` varchar(255) NOT NULL,
  `target` tinyint(1) NOT NULL default '1',
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `mod` varchar(20) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_oauth
CREATE TABLE IF NOT EXISTS `pin_oauth` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `config` text NOT NULL,
  `desc` text NOT NULL,
  `author` varchar(50) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `status` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post
CREATE TABLE IF NOT EXISTS `pin_post` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `post_key` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `tcolor` varchar(10) default NULL,
  `prices` varchar(50) NOT NULL,
  `info` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `uname` varchar(20) NOT NULL,
  `uid` int(10) NOT NULL,
  `mall_id` int(10) unsigned NOT NULL,
  `add_time` int(10) unsigned NOT NULL,
  `post_time` int(10) unsigned NOT NULL,
  `rate_best` int(10) NOT NULL,
  `rate_good` int(10) NOT NULL,
  `rate_bad` int(10) NOT NULL,
  `favs` int(10) NOT NULL,
  `hits` int(11) NOT NULL,
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `is_hot` tinyint(1) NOT NULL default '0',
  `is_recommend` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '1',
  `seo_title` varchar(255) default NULL,
  `comments` int(10) NOT NULL,
  `seo_keys` varchar(255) default NULL,
  `seo_desc` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_baoliao
CREATE TABLE IF NOT EXISTS `pin_post_baoliao` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(10) default '0',
  `title` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `orig` int(11) default '0',
  `web` varchar(255) default NULL,
  `info` text,
  `email` varchar(255) default NULL,
  `add_time` int(10) default '0',
  `type` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_cate
CREATE TABLE IF NOT EXISTS `pin_post_cate` (
  `id` smallint(4) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `pid` smallint(4) unsigned NOT NULL default '0',
  `spid` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  `ordid` tinyint(3) unsigned NOT NULL default '255',
  `seo_title` varchar(255) NOT NULL,
  `seo_keys` varchar(255) NOT NULL,
  `seo_desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_cate_re
CREATE TABLE IF NOT EXISTS `pin_post_cate_re` (
  `post_id` int(10) NOT NULL,
  `cate_id` smallint(4) NOT NULL,
  UNIQUE KEY `post_id` (`post_id`,`cate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_comment
CREATE TABLE IF NOT EXISTS `pin_post_comment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL,
  `pid` int(10) default '0',
  `uid` int(10) unsigned NOT NULL,
  `uname` varchar(20) default NULL,
  `info` text,
  `digg` int(10) NOT NULL,
  `burn` int(10) NOT NULL,
  `add_time` int(10) default NULL,
  `status` tinyint(1) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_favs
CREATE TABLE IF NOT EXISTS `pin_post_favs` (
  `id` int(10) NOT NULL auto_increment,
  `post_id` int(10) NOT NULL,
  `rate` tinyint(1) NOT NULL COMMENT '1:best,2-good,3-bad',
  `uid` int(10) NOT NULL,
  `add_time` int(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_rate
CREATE TABLE IF NOT EXISTS `pin_post_rate` (
  `id` int(10) NOT NULL,
  `post_id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `add_time` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_post_tag
CREATE TABLE IF NOT EXISTS `pin_post_tag` (
  `post_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `post_id` (`post_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_score_log
CREATE TABLE IF NOT EXISTS `pin_score_log` (
  `id` int(10) NOT NULL auto_increment,
  `uid` int(10) NOT NULL,
  `uname` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `score` int(10) NOT NULL,
  `add_time` int(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_setting
CREATE TABLE IF NOT EXISTS `pin_setting` (
  `name` varchar(100) NOT NULL,
  `data` text NOT NULL,
  `remark` varchar(255) NOT NULL,
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_tag
CREATE TABLE IF NOT EXISTS `pin_tag` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_user
CREATE TABLE IF NOT EXISTS `pin_user` (
  `id` int(10) NOT NULL auto_increment,
  `uc_uid` int(10) unsigned NOT NULL default '0',
  `username` varchar(20) NOT NULL default '0',
  `password` varchar(32) NOT NULL default '0',
  `email` varchar(50) NOT NULL,
  `avatar` varchar(50) NOT NULL,
  `gender` tinyint(1) NOT NULL default '0' COMMENT '1男，0女',
  `tags` varchar(50) NOT NULL COMMENT '个人标签',
  `intro` varchar(500) NOT NULL,
  `byear` smallint(4) unsigned NOT NULL,
  `bmonth` tinyint(2) unsigned NOT NULL,
  `bday` tinyint(2) unsigned NOT NULL,
  `province` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `score` int(10) unsigned NOT NULL default '0',
  `score_level` int(10) unsigned NOT NULL default '0',
  `cover` varchar(255) NOT NULL,
  `reg_ip` varchar(15) NOT NULL,
  `reg_time` int(10) unsigned NOT NULL default '0',
  `last_time` int(10) unsigned NOT NULL default '0',
  `last_ip` varchar(15) default '0',
  `status` tinyint(1) NOT NULL default '1',
  `shares` int(10) unsigned default '0',
  `likes` int(10) unsigned default '0',
  `follows` int(10) unsigned default '0',
  `fans` int(10) unsigned default '0',
  `albums` int(10) unsigned default '0',
  `daren` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_user_bind
CREATE TABLE IF NOT EXISTS `pin_user_bind` (
  `uid` int(10) unsigned NOT NULL,
  `type` varchar(60) NOT NULL,
  `keyid` varchar(100) NOT NULL,
  `info` text NOT NULL,
  KEY `uid` (`uid`),
  KEY `uid_type` (`uid`,`type`),
  KEY `type_keyid` (`type`,`keyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table zdm.pin_user_stat
CREATE TABLE IF NOT EXISTS `pin_user_stat` (
  `uid` int(10) unsigned NOT NULL,
  `action` varchar(20) NOT NULL,
  `num` int(10) unsigned NOT NULL,
  `last_time` int(10) unsigned NOT NULL,
  UNIQUE KEY `uid_type` (`uid`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
