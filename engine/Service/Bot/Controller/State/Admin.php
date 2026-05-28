<?php

namespace Service\Bot;

use System\Service_Controller_State;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
abstract class Controller_State_Admin extends Service_Controller_State
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['bot'])) {
            $this->_application->menu['bot']['active'] = true;
        }

        return null;
    }
}
