DROP TABLE IF EXISTS `#__frsettings`;

CREATE TABLE `#__frsettings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`color` varchar(25) NOT NULL,
	`article_class` varchar(25) NOT NULL,
	`pagetitle_sel` varchar(25) NOT NULL,
	`editicon_sel` varchar(40) NOT NULL,
	`module_edit` tinyint(1) NOT NULL,
	`menuitem_edit` tinyint(1) NOT NULL,
	`dragdrop_edit` tinyint(1) NOT NULL,
	`color_edit` tinyint(1) NOT NULL,
	`moduleparams_visible` tinyint(1) NOT NULL,
	`alias_edit` tinyint(1) NOT NULL,
	`toggle` tinyint(1) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__frsettings` (`color`,`article_class`,`pagetitle_sel`,`editicon_sel`,`module_edit`,`menuitem_edit`,`dragdrop_edit`,`color_edit`,`moduleparams_visible`,`alias_edit`,`toggle`) VALUES ('caff91','.contentheading','.componentheading','.contentpaneopen img[alt=edit]',1,1,1,0,0,0,0);
