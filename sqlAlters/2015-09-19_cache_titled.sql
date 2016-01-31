-- 2015-06-17 Regions for titled caches
-- @author: triPPer


CREATE TABLE `cache_titled` (
`cache_id` int( 11 ) NOT NULL ,
`rate` float NOT NULL ,
`ratio` float NOT NULL ,
`rating` int( 11 ) NOT NULL ,
`found` int( 11 ) NOT NULL ,
`days` int( 11 ) NOT NULL ,
`date_alg` date NOT NULL ,
`log_id` int( 11 ) NOT NULL ,
PRIMARY KEY ( `cache_id` )
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
