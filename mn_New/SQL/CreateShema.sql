/*Поставщики*/
CREATE TABLE `ent`.`stn_customers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `CustomerName` VARCHAR(100) NOT NULL,
  `INN` VARCHAR(20) NOT NULL,
  `Adress` VARCHAR(255) NULL,
  `Phone1` VARCHAR(100) NULL,
  `Phone2` VARCHAR(100) NULL,
  `Email` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `INN_UNIQUE` (`INN` ASC));

