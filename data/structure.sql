-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2014 年 03 月 21 日 14:20
-- 服务器版本: 5.1.50-community
-- PHP 版本: 5.4.21

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `ass_develop`
--

-- --------------------------------------------------------

--
-- 表的结构 `atansuser_permission`
--

CREATE TABLE IF NOT EXISTS `atansuser_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- 转存表中的数据 `atansuser_permission`
--

INSERT INTO `atansuser_permission` (`id`, `name`, `description`) VALUES
(1, 'atansuser.admin.user.index'),
(2, 'atansuser.admin.user.add'),
(3, 'atansuser.admin.user.edit'),
(4, 'atansuser.admin.user.delete'),
(5, 'atansuser.admin.role.index'),
(6, 'atansuser.admin.role.add'),
(7, 'atansuser.admin.role.edit'),
(8, 'atansuser.admin.role.delete'),
(9, 'atansuser.admin.permission.index'),
(10, 'atansuser.admin.permission.add'),
(11, 'atansuser.permission.edit'),
(12, 'atansuser.permission.delete');

-- --------------------------------------------------------

--
-- 表的结构 `atansuser_role`
--

CREATE TABLE IF NOT EXISTS `atansuser_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(48) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_57698A6A5E237E06` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `atansuser_role`
--

INSERT INTO `atansuser_role` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'guest');

-- --------------------------------------------------------

--
-- 表的结构 `atansuser_role_children`
--

CREATE TABLE IF NOT EXISTS `atansuser_role_children` (
  `role_id` int(10) unsigned NOT NULL,
  `child_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`child_id`),
  KEY `IDX_8B44578BD60322AC` (`role_id`),
  KEY `IDX_8B44578BDD62C21B` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `atansuser_role_permissions`
--

CREATE TABLE IF NOT EXISTS `atansuser_role_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `IDX_6F7DF886D60322AC` (`role_id`),
  KEY `IDX_6F7DF886FED90CCA` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `atansuser_role_permissions`
--

INSERT INTO `atansuser_role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12);

-- --------------------------------------------------------

--
-- 表的结构 `atansuser_user`
--

CREATE TABLE IF NOT EXISTS `atansuser_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(128) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `atansuser_user`
--

INSERT INTO `atansuser_user` (`id`, `username`, `email`, `password`, `status`, `created`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$14$Z0ctNt2Q9KTs/nOflC6TM.XZwxhUIql0.8KenydDCrAvEcxJUo8Ei', 'active', '2014-03-06 16:44:46');

-- --------------------------------------------------------

--
-- 表的结构 `atansuser_user_roles`
--

CREATE TABLE IF NOT EXISTS `atansuser_user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_2DE8C6A3A76ED395` (`user_id`),
  KEY `IDX_2DE8C6A3D60322AC` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `atansuser_user_roles`
--

INSERT INTO `atansuser_user_roles` (`user_id`, `role_id`) VALUES
(1, 1);

--
-- 限制导出的表
--

--
-- 限制表 `atansuser_role_children`
--
ALTER TABLE `atansuser_role_children`
  ADD CONSTRAINT `FK_8B44578BDD62C21B` FOREIGN KEY (`child_id`) REFERENCES `atansuser_role` (`id`),
  ADD CONSTRAINT `FK_8B44578BD60322AC` FOREIGN KEY (`role_id`) REFERENCES `atansuser_role` (`id`);

--
-- 限制表 `atansuser_role_permissions`
--
ALTER TABLE `atansuser_role_permissions`
  ADD CONSTRAINT `FK_6F7DF886D60322AC` FOREIGN KEY (`role_id`) REFERENCES `atansuser_role` (`id`),
  ADD CONSTRAINT `FK_6F7DF886FED90CCA` FOREIGN KEY (`permission_id`) REFERENCES `atansuser_permission` (`id`);

--
-- 限制表 `atansuser_user_roles`
--
ALTER TABLE `atansuser_user_roles`
  ADD CONSTRAINT `FK_2DE8C6A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `atansuser_user` (`id`),
  ADD CONSTRAINT `FK_2DE8C6A3D60322AC` FOREIGN KEY (`role_id`) REFERENCES `atansuser_role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
