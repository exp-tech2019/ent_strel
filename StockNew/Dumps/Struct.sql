/*Таблица групп номеклатуры*/
CREATE TABLE `ent`.`st_goodgroups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `GroupName` VARCHAR(45) NOT NULL,
  `Step` INT NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `ent`.`st_goodgroups`
ADD UNIQUE INDEX `Unique` (`GroupName` ASC);

ALTER TABLE `ent`.`st_goodgroups`
ADD COLUMN `AutoUnset` BIT NOT NULL AFTER `Step`;

/*Таблица товаров*/
CREATE TABLE `ent`.`st_goods` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idGroup` INT NOT NULL,
  `Article` VARCHAR(45) NULL,
  `GoodName` VARCHAR(45) NOT NULL,
  `BarCode` VARCHAR(45) NULL,
  `Unit` INT NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `ent`.`st_goods`
ADD UNIQUE INDEX `Unique` (`idGroup` ASC, `GoodName` ASC);

/*Таблица поставщиков*/
CREATE TABLE `ent`.`st_suppliers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `SupplierName` VARCHAR(45) NOT NULL,
  `INN` VARCHAR(45) NOT NULL,
  `Adress` TEXT NULL,
  `Phone` VARCHAR(45) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `Uniquie` (`SupplierName` ASC, `INN` ASC),
  INDEX `SupplierName` (`SupplierName` ASC),
  INDEX `SupplierINN` (`INN` ASC));

/*Приход
 */
CREATE TABLE `ent`.`st_arrival` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idSupplier` INT NOT NULL,
  `DateArrival` DATE NULL,
  `NumArrival` VARCHAR(45) NULL,
  `Note` TEXT NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `ent`.`st_arrivalgoods` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idArrival` INT NOT NULL,
  `TextGood` VARCHAR(100) NULL,
  `idGood` INT NOT NULL,
  `Count` FLOAT NOT NULL,
  `Price` DECIMAL(9,2) NOT NULL,
  PRIMARY KEY (`id`));

/*Основной склад*/
CREATE TABLE `ent`.`st_stockmain` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idArrival` INT NULL,
  `idGood` INT NULL,
  `Price` DECIMAL(9,2) NULL,
  `Count` FLOAT NULL,
  `CountOld` FLOAT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `ent`.`st_stockmain`
ADD COLUMN `idArrivalGood` INT NULL AFTER `idArrival`;


/*Производственный склад*/
CREATE TABLE `ent`.`st_stockent` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idStock` INT NULL,
  `idGood` INT NULL,
  `Price` DECIMAL(9,2) NULL,
  `Count` FLOAT NULL,
  `CountOld` FLOAT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `ent`.`st_stockent`
ADD COLUMN `idWorker` VARCHAR(45) NULL AFTER `CountOld`;

ALTER TABLE `ent`.`st_stockent`
CHANGE COLUMN `idWorker` `idWorker` INT NULL DEFAULT NULL ,
ADD COLUMN `idLogin` INT NULL AFTER `idWorker`;

//Передача в производство
CREATE TABLE `ent`.`st_actinent` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `DateCreate` DATE NOT NULL,
  `idLogin` INT NOT NULL,
  `idWorker` INT NOT NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `ent`.`st_actinentgoods` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idAct` INT NOT NULL,
  `idGood` INT NOT NULL,
  `Count` FLOAT NOT NULL,
  PRIMARY KEY (`id`));
ALTER TABLE `ent`.`st_stockent`
ADD COLUMN `idInEnt` INT NULL AFTER `idLogin`;

/*-----Спецификация---------*/
CREATE TABLE `ent`.`spe_common` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idDoor` INT NOT NULL,
  `idGroup` INT NOT NULL,
  `Count` FLOAT NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `ent`.`spe_detail` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idCommon` INT NOT NULL,
  `idGood` INT NOT NULL,
  PRIMARY KEY (`id`));

/*--------Списание--------*/
CREATE TABLE `ent`.`st_naryadcomplite` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idDoor` INT NOT NULL,
  `idDetail` INT NOT NULL,
  `idGood` INT NOT NULL,
  `idEnt` INT NULL,
  `Price` DECIMAL(9,2) NULL,
  `Count` FLOAT NOT NULL,
  PRIMARY KEY (`id`));
 */

/*------Выдача сотруднику по наряду-----*/
CREATE TABLE `ent`.`st_actissueworker` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idLogin` INT NOT NULL,
  `idWorker` INT NOT NULL,
  `idDoor` INT NOT NULL,
  `idNaryad` INT NOT NULL,
  `DateCreate` DATE NOT NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `ent`.`st_actissueworkergoods` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idAct` INT NOT NULL,
  `idGood` INT NOT NULL,
  `CountIssue` FLOAT NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `ent`.`st_actissueworker_tr1` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idActGoods` INT NOT NULL,
  `idMain` INT NOT NULL,
  `idEnt` INT NOT NULL,
  PRIMARY KEY (`id`));

 */

/*------ Акт отгрузки ----------*/
CREATE TABLE `ent`.`st_actshpt` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idOrder` INT NOT NULL,
  `DateCreate` DATE NOT NULL,
  `idManager` INT NOT NULL,
  `DateShpt` DATE NULL,
  `idStockManager` INT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `ent`.`st_actshptnaryads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idAct` INT NOT NULL,
  `idNaryad` INT NOT NULL,
  PRIMARY KEY (`id`));
  ALTER TABLE `ent`.`st_actshptnaryads`
RENAME TO  `ent`.`st_acthptnaryads` ;

ALTER TABLE `ent`.`st_acthptnaryads`
RENAME TO  `ent`.`st_actshptnaryads` ;

CREATE TABLE `ent`.`st_actshpt_tr1` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idAct` INT NOT NULL,
  `idMain` INT NOT NULL,
  `idEnt` INT NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `ent`.`st_actshpt_tr2` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idAct` INT NOT NULL,
  `idEnt` INT NOT NULL,
  `idNC` INT NOT NULL,
  PRIMARY KEY (`id`));
 */

/*------ Конструкция спецификации --------*/
CREATE TABLE `ent`.`spe_constructtypedoors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `TypeDoor` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `ent`.`spe_constructtypedoors`
ADD UNIQUE INDEX `TypeDoor_UNIQUE` (`TypeDoor` ASC);

CREATE TABLE `ent`.`spe_constructgroups` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idTypeDoor` INT NOT NULL,
  `idGroup` INT NOT NULL,
  `TypeCalc` INT NOT NULL,
  `Count` FLOAT NULL,
  `DependConstruct` BIT NULL,
  `Petlya` BIT NULL,
  PRIMARY KEY (`id`));

 */

/*----- Возврат --------*/
CREATE TABLE `ent`.`st_norollback` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idDetail` INT NOT NULL,
  `idGood` INT NOT NULL,
  `Count` FLOAT NOT NULL,
  PRIMARY KEY (`id`));
 */