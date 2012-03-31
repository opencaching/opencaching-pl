ALTER TABLE user
ADD (last_login_mobile 	datetime not null DEFAULT "0000-00-00 00:00:00",
  	 uuid_mobile varchar(36) null);