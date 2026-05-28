<?php

namespace Service\Tasks;

class Controller_State_Client_List_Bonus extends Controller_State_Client_List
{
    public function actionGet()
    {
        if (!$this->_application->User->bonus) {
            return $this->_response->setLocation('/tasks/all');
        }

        $task = $this->factoryTasks->tasks->getItemBonus($this->_application->User);

        $bonus = \Service\Users\Model_Config::GetBonusSettings();

        if ($this->_application->User->bonus == 1) {
            $bonus_balance = $bonus['register'];
        } elseif ($this->_application->User->bonus == 2) {
            $bonus_balance = $bonus['day_one'];
        }

        $list = [$task];

        $vars = [
            'karmaParams' => $this->karmaParams,
            'bonus' => null,
            'bonus_balance' => $bonus_balance,
            'errors' => $this->_errors,
            'user' => $this->_application->User,
            'prices' => $this->_application->settings,
            'type' => 'all',
            'titles' => $this->_titles,
            'list' => $list,
            'types' => $this->_types,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/list', $vars));
    }
}
