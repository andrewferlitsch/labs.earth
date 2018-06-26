DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users
(
  id        INT          NOT NULL PRIMARY KEY auto_increment,
  email		VARCHAR(64)	 NOT NULL UNIQUE,
  password	VARCHAR(32)	 NOT NULL,
  name		VARCHAR(32)	 NOT NULL,
  tel		VARCHAR(16)	 NULL,
  summary	TEXT		 NULL,
  position	VARCHAR(16)	 NULL,
  level		VARCHAR(16)	 NULL,
  degree	VARCHAR(16)	 NULL,
  leadership VARCHAR(16) NULL,
  retired   TINYINT		 NOT NULL DEFAULT 0,
  created	DATETIME	 NOT NULL,
  lastlogin	DATETIME	 NULL,
  active	TINYINT		 NOT NULL DEFAULT 1,
 
  INDEX(email),
  INDEX(name)
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE utf8_bin;
