DROP TABLE IF EXISTS skills;

CREATE TABLE IF NOT EXISTS skills
(
  id        INT          NOT NULL PRIMARY KEY auto_increment,
  userid	INT 		 NOT NULL,
  skill		VARCHAR(16)	 NOT NULL,
  years		SMALLINT	 NULL,
  rate		SMALLINT	 NULL,
 
  INDEX(skill),
  FOREIGN KEY (userid) REFERENCES users(id)
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE utf8_bin;
