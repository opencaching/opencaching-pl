alter table `user` add column password_salt varchar(10) not null default '' after password;
alter table `user` add column password_hashing_rounds int(10) not null default 1 after password_salt;
