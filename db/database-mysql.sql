-- MySQL Script generated by MySQL Workbench
-- Mon Dec 11 23:06:08 2017
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema wlmstats
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `registration` DATETIME NOT NULL,
  `wpid` INT NULL,
  `team` TINYINT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `name_UNIQUE` ON `user` (`name` ASC);


-- -----------------------------------------------------
-- Table `monument`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `monument` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `country` VARCHAR(10) NOT NULL,
  `lang` VARCHAR(10) NOT NULL,
  `heritage_id` VARCHAR(25) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `municipality` VARCHAR(255) NULL,
  `adm_level` VARCHAR(100) NULL,
  `lat` DECIMAL(9,6) NULL,
  `lon` DECIMAL(9,6) NULL,
  `wikidata` VARCHAR(10) NULL,
  `commonscat` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `ix_monumentsdb` ON `monument` (`heritage_id` ASC, `country` ASC, `lang` ASC);


-- -----------------------------------------------------
-- Table `photo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `photo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file` VARCHAR(255) NULL,
  `wpid` INT NULL,
  `user_id` INT NOT NULL,
  `monument_id` INT NULL,
  `date_wp` DATETIME NOT NULL,
  `date_exif` DATETIME NULL,
  `camera_brand` VARCHAR(100) NULL,
  `camera_model` VARCHAR(100) NULL,
  `lens` VARCHAR(100) NULL,
  `software` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_photo_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_photo_monument1`
    FOREIGN KEY (`monument_id`)
    REFERENCES `monument` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE UNIQUE INDEX `file_UNIQUE` ON `photo` (`file` ASC);

CREATE INDEX `fk_photo_user_idx` ON `photo` (`user_id` ASC);

CREATE INDEX `fk_photo_monument1_idx` ON `photo` (`monument_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;