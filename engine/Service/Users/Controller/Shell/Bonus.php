<?php

namespace Service\Users;

use DateTime;

class Controller_Shell_Bonus extends \System\Service_Controller_Shell
{
    private $_penatly = null;

    public function A_Once()
    {
        $query = $this->factoryUsers->users->query();
        $query->limit(1000)->sort('userId', 'ASC');

        $it = $query->iteratorForSave();

        foreach ($it as $user) {
            $sql = 'SELECT MAX(`karmaTo`) FROM `users_karma_2017_12` WHERE `userId` = ' . $user->userId;
            $res = $this->factoryUsers->db->query($sql);
            $row = $res->fetch_row();

            if ($row[0] == 0) {
                continue;
            }

            if ($row[0] > $user->karma) {
                $user->karma = floatval($row[0]);
                $this->factoryUsers->users->save($user);
            }
        }
    }

    public function A_DayTask()
    {
        $sql = 'UPDATE `users` SET `bonus` = 2 WHERE `ban` = 0 AND `bonus` = 0 AND `lastLogin` > NOW() - INTERVAL 1 DAY';
        $this->factoryUsers->db->query($sql);
    }

    public function A_Day()
    {

        //Установим время и дату для записи в лог скрипта
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");
        $tm = time();

        //Зададим переменные для отслеживаемых метрик скрипта
        $countUsersBonus = 0;
        $sumBonus = 0;

        $mConfig = \Service\Messages\Model_Config::GetConfig();
        $page = 0;
        $bonusNew = \Service\Users\Model_Config::GetBonusSettings();
        $this->_penatly = json_decode(file_get_contents(Model_Config::$karmaPath), true);
        $bonusSettings = $bonusNew['apply'] ?? $bonusNew;

        if (!is_array($bonusSettings)) {
            $bonusSettings = $bonusNew;
        }

        do {
            $count = 0;
            $query = $this->factoryUsers->users->query();
            $query->limit(1000)->offset($page * 1000)->sort('userId', 'ASC');
            $query->filter->fieldValue('karma', '>', 0.0);
            //$query->filter->fieldValue('userId', '=', \Config::$adminId); //для тестов

            $it = $query->iteratorForSave();
            /** @var \Service\Users\Model_Users_User $user */
            foreach ($it as $user) {
                $count++;

                //статистика по выполненным заданиям за вчерашний день
                $statistic = $this->factoryTasks->users->getStatisticByUserId($user->userId, strtotime('yesterday'));
                $found = false;

                //сверяем количество выполненных заданий с установленными дневными лимитами для начисления бонуса
                foreach ($bonusSettings['min'] as $type => $limit) {
                    if ($statistic[$type] < $limit) {
                        $found = true;
                    }
                }

                if ($found) {
                    $karma = $this->factoryUsers->users->karma->getNew();
                    $karma->userId = $user->userId;
                    $karma->dateCreate = time();
                    $karma->karma = -floatval($this->_penatly['penalty_day']);
                    $karma->karmaFrom = $user->karma;

                    $user->karma -= $this->_penatly['penalty_day'];

                    if ($user->karma < 0) {
                        $user->karma = 0.0;
                    }
                    $karma->karmaTo = $user->karma;
                    $karma->comment = 'Ежедневная норма не выполнена';

                    if ($this->factoryUsers->users->karma->save($karma)) {
                        $this->factoryUsers->users->save($user);
                    }
                } else {
                    $countUsersBonus++;
                    //Начисляем бонус
                    error_log($user->userId . "\t" . $user->login . "\t" . 'bonus');
                    $balance = $this->factoryUsers->users->balance->getNew();
                    $balance->userId = $user->userId;
                    $balance->isBonus = true;
                    $balance->comment = 'Ежедневный бонус';
                    $balance->balance = floatval($bonusSettings['day']);
                    $balance->balanceFrom = $user->balance;
                    $user->balance += $balance->balance;
                    $balance->balanceTo = $user->balance;
                    $balance->dateCreate = time();

                    $sumBonus += $balance->balance;

                    if ($this->factoryUsers->users->balance->save($balance)) {
                        $bonus = $this->factoryUsers->users->bonuses->getNew();
                        $bonus->userId = $user->userId;
                        $bonus->year = intval(date('Y'));
                        $bonus->week = intval(date('W'));
                        $bonus->day = intval(date('N'));
                        $bonus->type = Model_Config::BONUS_DAY;
                        $bonus->balanceId = $balance->balanceId;
                        $this->factoryUsers->users->bonuses->save($bonus);

                        $message = $this->factoryMessages->users->getNew();
                        $message->userId = $user->userId;
                        $message->isDone = false;
                        $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                        $text = $mConfig['bonus']['types']['day']['text'];
                        $text = str_replace('%balance%', $balance->balance, $text);
                        $message->text = $text;
                        $message->icon = 'bonus';
                        $this->factoryMessages->users->save($message);

                        $factoryMessages = new \Service\Messages\Model_Factory();
                        $mConfig = \Service\Messages\Model_Config::GetConfig();

                        $message = $factoryMessages->users->getNew();
                        $message->userId = $user->userId;
                        $message->isDone = false;
                        $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                        $text = $mConfig['bonus']['types']['day']['text'];
                        $text = str_replace('%balance%', $balance->balance, $text);
                        $text = str_replace('%balance_week%', floatval($bonus->week), $text);
                        $message->text = $text;

                        $message->icon = 'bonus';
                        $factoryMessages->users->save($message);

                        $this->factoryUsers->users->save($user);
                    }
                }
            }
            $page++;
        } while ($count);
        \Service\Users\Model_Config::SetBonusSettings($bonusSettings);

        //Если есть начисленные бонусы выведем для записи в лог скрипта
        if($sumBonus){
            echo $date;
            echo "\n action=Users/Bonus:Day";
            echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
            echo "\nКол-во пользователей получивших бонус: " . $countUsersBonus;
            echo "\nВсего начислено бонусов: " . $sumBonus;
            echo "\n";
        }

    }

    public function A_Week()
    {
        //здесь нужно добавить начисление недельных бонусов
    }


}
