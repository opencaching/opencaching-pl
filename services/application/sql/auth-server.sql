CREATE TABLE `oauth_session_scopes` (
  `session_id` int(11) NOT NULL,
  `access_token` text,
  `scope` varchar(64) NOT NULL default ''
);

CREATE TABLE `oauth_sessions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `client_id` varchar(32) NOT NULL default '',
  `redirect_uri` text NOT NULL,
  `user_id` varchar(64) default NULL,
  `code` text,
  `access_token` text,
  `stage` enum('request','granted') NOT NULL default 'request',
  `first_requested` int(10) unsigned NOT NULL,
  `last_updated` int(10) unsigned NOT NULL,
  `limited` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
);