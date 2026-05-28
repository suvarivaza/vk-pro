<?php

namespace Service\Users;

//обновляет статистику пользователей по городам и странам (количество пользователей в городах и странах)

class Controller_Shell_Cities extends \System\Service_Controller_Shell
{
    public function A_Run()
    {

        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new \DateTime();
        $date = $date->format("Y-m-d H:i:s");
        echo "\naction=Users/Cities:Run";
        echo $date;

        $countAddCity =0;
        $countAddCountries =0;

        //выбираем города пользователей
        $cities = $this->factoryUsers->users->getCities();

        foreach ($cities as $row) {

            //получаем город
            $city = $this->factoryUsers->cities->getById($row['cityId'], true);

            //если у нас нет такого города создаем его
            if ($city === null) {
                $countAddCity++;
                $city = $this->factoryUsers->cities->getNew();
                $city->isVisible = false;
            }
            $city->cityId = intval($row['cityId']);
            $city->title = $row['title'];
            $city->count = intval($row['count']); //количество пользователей в городе
            $this->factoryUsers->cities->save($city);
        }

        $countries = $this->factoryUsers->users->getCountries();

        foreach ($countries as $row) {

            $country = $this->factoryUsers->countries->getById($row['countryId'], true);

            if ($country === null) {
                $countAddCountries++;
                $country = $this->factoryUsers->countries->getNew();
                $country->isVisible = false;
            }

            $country->countryId = intval($row['countryId']);
            $country->title = $row['title'];
            $country->count = intval($row['count']);
            $this->factoryUsers->countries->save($country);
        }


        echo "\nВремя выполнения: " . round((time() - $tm) / 60, 2);
        echo "\nКоличество городов: " . count($cities);
        echo "\nГородов добавлено: " . $countAddCity;
        echo "\nКоличество стран: " . count($countries);
        echo "\nСтран добавлено: " . $countAddCountries;
        echo "\n";


    }
}
