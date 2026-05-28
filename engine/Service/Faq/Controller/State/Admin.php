<?php

namespace Service\Faq;

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

        if (isset($this->_application->menu['faq'])) {
            $this->_application->menu['faq']['active'] = true;
        }

        return null;
    }
}
