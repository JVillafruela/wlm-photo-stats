CREATE DATABASE `monumentsdb`  CHARACTER SET UTF8; 

create user 'monumentsdb'@'localhost' identified by 'monumentsdb';

GRANT ALL ON monumentsdb.* TO 'monumentsdb'@'localhost';


CREATE DATABASE `wlmstats`  CHARACTER SET UTF8; 

create user 'wlmstats'@'localhost' identified by 'wlmstats';

GRANT ALL ON wlmstats.* TO 'wlmstats'@'localhost';

GRANT SELECT ON monumentsdb.* TO 'wlmstats'@'localhost';

flush privileges;
