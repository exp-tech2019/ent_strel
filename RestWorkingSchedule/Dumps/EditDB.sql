/*  */
ALTER TABLE `ent`.`manualdolgnost`
ADD COLUMN `Algorithm` VARCHAR(1) NULL AFTER `NalogPercent`;
ALTER TABLE `ent`.`manualdolgnost`

  COMMENT = 'N-naryad I-ИТР H-почасовщики' ;

/* Таблица начисления часов */
CREATE TABLE `ent`.`workingschedule` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idWorker` INT NOT NULL,
  `DatePayment` DATE NOT NULL,
  `CountHour` INT NOT NULL,
  `Cost` DECIMAL(9,2) NOT NULL,
  PRIMARY KEY (`id`));

/* Группа уникальных полей idWorker и DatePayment */
ALTER TABLE `ent`.`workingschedule`
  ADD UNIQUE INDEX `Unique_idWorker_DatePayment` (`idWorker` ASC, `DatePayment` ASC);

/* Справочник сумм */
CREATE TABLE `ent`.`manualschedulecost` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idDolgnost` INT NULL,
  `Cost` DECIMAL(9,2) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idDolgnost_UNIQUE` (`idDolgnost` ASC));
