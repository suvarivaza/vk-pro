<?php

namespace Service\Users;

class Controller_Shell_Test extends \System\Service_Controller_Shell
{
    public function A_Test()
    {
        $userOnline = $this->factoryUsers->online->getNew();
        $userOnline->date = date('Y-m');
        $userOnline->count = 1;
        $this->factoryUsers->online->save($userOnline);
    }
}
