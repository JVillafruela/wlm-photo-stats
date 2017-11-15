-- -----------------------------------------------------
-- Schema wlmstats (sqlite3)
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` INTEGER  PRIMARY KEY  AUTOINCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `registration` DATETIME NOT NULL,
  `wpid` INTEGER NULL,
  `team` TINYINTEGER NULL DEFAULT 0);

CREATE UNIQUE INDEX `name_UNIQUE` ON `user` (`name` ASC);


-- -----------------------------------------------------
-- Table `monument`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `monument` (
  `id` INTEGER  PRIMARY KEY  AUTOINCREMENT,
  `heritage_id` VARCHAR(30) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `address` VARCHAR(100) NULL,
  `municipality` VARCHAR(100) NULL,
  `adm0` VARCHAR(30) NULL,
  `adm1` VARCHAR(30) NULL,
  `adm2` VARCHAR(30) NULL,
  `adm3` VARCHAR(30) NULL,
  `adm4` VARCHAR(30) NULL,
  `lat` DECIMAL(8,5) NULL,
  `lon` DECIMAL(8,5) NULL,
  `wikidata` VARCHAR(10) NULL,
  `commonscat` VARCHAR(100) NULL,
  `image` VARCHAR(100) NULL,
  `heritage_url` VARCHAR(200) NULL
  );


-- -----------------------------------------------------
-- Table `photo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `photo` (
  `id` INTEGER  PRIMARY KEY  AUTOINCREMENT,
  `file` VARCHAR(100) NULL,
  `wpid` INTEGER NULL,
  `user_id` INTEGER NOT NULL,
  `monument_id` INTEGER NULL,
  `date_wp` DATETIME NOT NULL,
  `date_exif` DATETIME NULL,
  CONSTRAINT `fk_photo_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_photo_monument1`
    FOREIGN KEY (`monument_id`)
    REFERENCES `monument` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE UNIQUE INDEX `file_UNIQUE` ON `photo` (`file` ASC);

CREATE INDEX `fk_photo_user_idx` ON `photo` (`user_id` ASC);

CREATE INDEX `fk_photo_monument1_idx` ON `photo` (`monument_id` ASC);


