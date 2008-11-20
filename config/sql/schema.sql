#Cookbook sql generated on: 2008-07-28 20:07:20 : 1217271020

DROP TABLE IF EXISTS `attachments`;
DROP TABLE IF EXISTS `changes`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `nodes`;
DROP TABLE IF EXISTS `revisions`;


CREATE TABLE `attachments` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`class` varchar(30) NOT NULL,
	`foreign_id` varchar(36) NOT NULL,
	`filename` varchar(255) DEFAULT NULL,
	`ext` varchar(6) DEFAULT 'gif' NOT NULL,
	`dir` varchar(255) DEFAULT NULL,
	`mimetype` varchar(30) DEFAULT NULL,
	`filesize` int(11) DEFAULT NULL,
	`height` int(4) DEFAULT NULL,
	`width` int(4) DEFAULT NULL,
	`description` varchar(100) NOT NULL,
	`checksum` varchar(32) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	PRIMARY KEY  (`id`),
	KEY idxfk_foreign (`class`, `foreign_id`));

CREATE TABLE `changes` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`revision_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`author_id` int(11) NOT NULL,
	`status_from` varchar(10) NOT NULL,
	`status_to` varchar(10) NOT NULL,
	`comment` varchar(255) NOT NULL,
	`created` datetime NOT NULL,
	PRIMARY KEY  (`id`));

CREATE TABLE `comments` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`node_id` int(10) DEFAULT 0 NOT NULL,
	`user_id` int(10) NOT NULL,
	`revision_id` int(10) DEFAULT 0,
	`lang` varchar(2) NOT NULL,
	`title` varchar(150) DEFAULT NULL,
	`author` varchar(255) DEFAULT NULL,
	`email` varchar(255) DEFAULT NULL,
	`url` varchar(255) DEFAULT NULL,
	`body` text DEFAULT NULL,
	`published` tinyint(1) DEFAULT 1 NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	PRIMARY KEY  (`id`));

CREATE TABLE `nodes` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`lft` int(10) NOT NULL,
	`rght` int(10) NOT NULL,
	`parent_id` int(10) DEFAULT NULL,
	`status` int(2) DEFAULT 0 NOT NULL,
	`comment_level` int(4) DEFAULT 200 NOT NULL,
	`edit_level` int(4) DEFAULT 200 NOT NULL,
	`show_in_toc` tinyint(1) DEFAULT 1 NOT NULL,
	`depth` int(2) DEFAULT 0 NOT NULL,
	`sequence` varchar(20) NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	PRIMARY KEY  (`id`),
	KEY LFT_RGHT (`lft`, `rght`),
	KEY RGHT_LFT (`lft`, `rght`, `rght`, `lft`));

CREATE TABLE `revisions` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`node_id` int(10) NOT NULL,
	`under_node_id` int(10) DEFAULT NULL,
	`after_node_id` int(10) DEFAULT NULL,
	`status` varchar(30) DEFAULT 'pending' NOT NULL,
	`user_id` int(10) NOT NULL,
	`lang` varchar(3) DEFAULT NULL,
	`slug` varchar(50) DEFAULT NULL,
	`title` varchar(200) DEFAULT NULL,
	`content` text DEFAULT NULL,
	`type` varchar(50) DEFAULT NULL,
	`reason` varchar(300) DEFAULT NULL,
	`flags` varchar(100) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	PRIMARY KEY  (`id`),
	KEY node_id (`node_id`, `lang`, `status`));

