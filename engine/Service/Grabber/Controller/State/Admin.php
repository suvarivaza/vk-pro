<?php

namespace Service\Grabber;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
abstract class Controller_State_Admin extends \System\Service_Controller_State
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['grabber'])) {
            $this->_application->menu['grabber']['active'] = true;
        }

        return null;
    }
}
