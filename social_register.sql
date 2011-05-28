#
# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(255) NOT NULL AUTO_INCREMENT,
  `real_name` tinytext NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL,
  `login_type` enum('facebook','twitter') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table users_facebook
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_facebook`;

CREATE TABLE `users_facebook` (
  `user_id` bigint(255) NOT NULL,
  `facebook_user_id` text NOT NULL,
  `email` tinytext,
  `bio` text,
  `handle` text,
  `profile_image_url` text,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table users_twitter
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_twitter`;

CREATE TABLE `users_twitter` (
  `user_id` bigint(255) NOT NULL,
  `twitter_user_id` text NOT NULL,
  `handle` tinytext,
  `real_name` tinytext,
  `bio` tinytext,
  `profile_image_url` tinytext,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;