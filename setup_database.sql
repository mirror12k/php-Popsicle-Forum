

-- forums table
CREATE TABLE `forums` (
	`id` int(11) NOT NULL auto_increment,
	`creatorid` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`threadcount` int(11) NOT NULL DEFAULT 0,
	`timecreated` DATETIME NOT NULL,
	`timeposted` DATETIME NOT NULL,
	`lastpostid` int(11) NOT NULL DEFAULT 0,
	`locked` bool NOT NULL DEFAULT 0,
	UNIQUE KEY `id` (`id`)
);


-- threads table
CREATE TABLE `threads` (
	`id` int(11) NOT NULL auto_increment,
	`creatorid` int(11) NOT NULL,
	`forumid` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`postcount` int(11) NOT NULL DEFAULT 0,
	`timecreated` DATETIME NOT NULL,
	`timeposted` DATETIME NOT NULL,
	`lastpostid` int(11) NOT NULL DEFAULT 0,
	`locked` bool NOT NULL DEFAULT 0,
	`stickied` bool NOT NULL DEFAULT 0,
	UNIQUE KEY `id` (`id`)
);



-- posts table
CREATE TABLE `posts` (
	`id` int(11) NOT NULL auto_increment,
	`creatorid` int(11) NOT NULL,
	`threadid` int(11) NOT NULL,
	`text` text NOT NULL,
	`timeposted` DATETIME NOT NULL,
	UNIQUE KEY `id` (`id`),
	FULLTEXT idx (`text`)
) ENGINE=MYISAM;



-- users table
CREATE TABLE `users` (
	`id` int(11) NOT NULL auto_increment,
	`classid` int(11) NOT NULL,
	`username` varchar(64) NOT NULL,
	-- 16 byte hex salt + 1 byte seperator + 64 byte hex sha256 hash
	`password` varchar(81) NOT NULL,
	`banned` bool NOT NULL DEFAULT 0,
	`muted` bool NOT NULL DEFAULT 0,
	UNIQUE KEY `id` (`id`)
);




-- class permissions
CREATE TABLE `classes` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(64) NOT NULL,
	`level` int(11) NOT NULL,
	`color` char(6) NOT NULL DEFAULT '000000',

	-- all of the permissions we have available
	`permission_create_forum` bool NOT NULL DEFAULT 0,
	`permission_lock_forum` bool NOT NULL DEFAULT 0,
	`permission_delete_forum` bool NOT NULL DEFAULT 0,

	`permission_create_thread` bool NOT NULL DEFAULT 0,
	`permission_lock_thread` bool NOT NULL DEFAULT 0,
	`permission_sticky_thread` bool NOT NULL DEFAULT 0,
	`permission_delete_thread` bool NOT NULL DEFAULT 0,

	`permission_create_post` bool NOT NULL DEFAULT 0,
	`permission_edit_post` bool NOT NULL DEFAULT 0,
	`permission_delete_post` bool NOT NULL DEFAULT 0,
	
	`permission_edit_lower_class` bool NOT NULL DEFAULT 0,
	`permission_mute_user` bool NOT NULL DEFAULT 0,
	`permission_ban_user` bool NOT NULL DEFAULT 0,

	UNIQUE KEY `id` (`id`)
);


