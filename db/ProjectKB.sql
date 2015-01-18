-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`categories` (
  `catid` INT NOT NULL AUTO_INCREMENT,
  `catname` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`catid`),
  UNIQUE INDEX `catid_UNIQUE` (`catid` ASC),
  UNIQUE INDEX `catname_UNIQUE` (`catname` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`tags` (
  `tagid` INT NOT NULL AUTO_INCREMENT,
  `catid` INT(11) NOT NULL,
  `tagname` VARCHAR(25) NOT NULL,
  PRIMARY KEY (`tagid`),
  UNIQUE INDEX `idtags_UNIQUE` (`tagid` ASC),
  UNIQUE INDEX `tagname_UNIQUE` (`tagname` ASC),
  INDEX `fk_tags_categories_idx` (`catid` ASC),
  CONSTRAINT `fk_tags_categories`
    FOREIGN KEY (`catid`)
    REFERENCES `mydb`.`categories` (`catid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`posts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`posts` (
  `postid` INT NOT NULL AUTO_INCREMENT,
  `postcontent` VARCHAR(2000) NULL,
  `postdatetime` DATETIME NOT NULL,
  PRIMARY KEY (`postid`),
  UNIQUE INDEX `postid_UNIQUE` (`postid` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`posts_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`posts_tags` (
  `posts_postid` INT NOT NULL,
  `tags_tagid` INT NOT NULL,
  PRIMARY KEY (`posts_postid`, `tags_tagid`),
  INDEX `fk_posts_has_tags_tags1_idx` (`tags_tagid` ASC),
  INDEX `fk_posts_has_tags_posts1_idx` (`posts_postid` ASC),
  CONSTRAINT `fk_posts_tags_posts1`
    FOREIGN KEY (`posts_postid`)
    REFERENCES `mydb`.`posts` (`postid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_posts_tags_tags1`
    FOREIGN KEY (`tags_tagid`)
    REFERENCES `mydb`.`tags` (`tagid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`users` (
  `userid` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `hash` VARCHAR(100) NULL,
  PRIMARY KEY (`userid`),
  UNIQUE INDEX `userid_UNIQUE` (`userid` ASC),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
