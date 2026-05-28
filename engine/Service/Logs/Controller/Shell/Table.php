<?php

namespace Service\Logs;

use Database_Logs;
use Database_Main;
use Lib_DB_Factory;
use System\Service_Controller_Shell;

class Controller_Shell_Table extends Service_Controller_Shell
{
    public function A_AddBotColumn()
    {
        $db = Lib_DB_Factory::GetInstance(new Database_Logs());

        $sql = "SHOW TABLES LIKE 'users_karma_2018%'";
        $res = $db->query($sql);

        while ($row = $res->fetch_row()) {
            $sql = 'ALTER TABLE `' . $row[0] . '` ADD COLUMN `isBot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `taskId`';
            $db->query($sql);
        }

        $sql = "SHOW TABLES LIKE 'users_balance_2018%'";
        $res = $db->query($sql);

        while ($row = $res->fetch_row()) {
            $sql = 'ALTER TABLE `' . $row[0] . '` ADD COLUMN `isBot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `isTask`';
            $db->query($sql);
        }
    }

    public function A_Create()
    {
        $table = 'log_' . date('Y') . '_' . date('m');

        $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (';
        $sql .= '`logId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,';
        $sql .= '`domainId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= "`action` VARCHAR(255) NOT NULL DEFAULT '',";
        $sql .= "`url` VARCHAR(255) NOT NULL DEFAULT '',";
        $sql .= "`title` VARCHAR(255) NOT NULL DEFAULT '',";
        $sql .= '`date` TIMESTAMP NULL DEFAULT NULL,';
        $sql .= '`objectId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`priceId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`itemId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`statusId` tinyint(4) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`userId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= "`ip` VARCHAR(15) NOT NULL DEFAULT '',";
        $sql .= '`params` TEXT,';
        $sql .= 'PRIMARY KEY (`logId`),';
        $sql .= 'KEY `i_action` (`action`),';
        $sql .= 'KEY `i_objectId` (`objectId`)';
        $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $db = Lib_DB_Factory::GetInstance(new Database_Logs());

        $db->query($sql);

        $table = 'users_karma_' . date('Y_m');
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (';
        $sql .= '`karmaId` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,';
        $sql .= '`userId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`taskId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isBot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`karma` DECIMAL(15,2) NOT NULL DEFAULT 0.0,';
        $sql .= '`karmaFrom` DECIMAL(15,2) NOT NULL DEFAULT 0.0,';
        $sql .= '`karmaTo` DECIMAL(15,2) NOT NULL DEFAULT 0.0,';
        $sql .= "`comment` VARCHAR(255) NOT NULL DEFAULT '',";
        $sql .= '`dateCreate` TIMESTAMP NULL DEFAULT NULL,';
        $sql .= 'PRIMARY KEY (`karmaId`),';
        $sql .= 'KEY `i_userId` (`userId`)';
        $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $db = Lib_DB_Factory::GetInstance(new Database_Main());

        $db->query($sql);

        $table = 'users_balance_' . date('Y_m');
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (';
        $sql .= '`balanceId` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,';
        $sql .= '`userId` INT(11) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isBonus` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isCompensation` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isPenalty` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isTask` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isBot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`isReferrer` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,';
        $sql .= '`balance` DECIMAL(15,2) NOT NULL DEFAULT 0.0,';
        $sql .= '`balanceFrom` DECIMAL(15,2) NOT NULL DEFAULT 0.0,';
        $sql .= '`balanceTo` DECIMAL(15,2) NOT NULL DEFAULT 0.0,';
        $sql .= "`comment` VARCHAR(255) NOT NULL DEFAULT '',";
        $sql .= '`dateCreate` TIMESTAMP NULL DEFAULT NULL,';
        $sql .= 'PRIMARY KEY (`balanceId`),';
        $sql .= 'KEY `i_userId` (`userId`),';
        $sql .= 'KEY `i_userId_isBonus` (`userId`, `isBonus`),';
        $sql .= 'KEY `i_userId_isCompensation` (`userId`, `isCompensation`),';
        $sql .= 'KEY `i_userId_isPenalty` (`userId`, `isPenalty`),';
        $sql .= 'KEY `i_userId_isTask` (`userId`, `isTask`),';
        $sql .= 'KEY `i_userId_isReferrer` (`userId`, `isReferrer`)';
        $sql .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8';

        $db = Lib_DB_Factory::GetInstance(new Database_Main());

        $db->query($sql);
    }

    public function A_Alter()
    {
        $sql = "SHOW TABLES LIKE 'log_%'";

        $db = Lib_DB_Factory::GetInstance(null);

        $res = $db->query($sql);

        while ($row = $res->fetch_row()) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            $sql = 'ALTER TABLE `' . $table . "` ADD COLUMN `url` VARCHAR(255) NOT NULL DEFAULT '' AFTER `action`";
            $db->query($sql);
        }
    }
}
