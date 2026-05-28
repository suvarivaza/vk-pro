<?php

namespace Service\Api;

use Exception;
use System\HttpResponse;
use System\Service_Controller_State;

/**
 * Class Controller_State_Client
 *
 * @package Service\Auto
 */
abstract class Controller_State_Client extends Service_Controller_State
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $token = $this->_request->request['token']->string('');
        $user = $this->factoryUsers->users->getByToken($token, true);

        if ($user !== null) {
            $this->_application->User = $user;

            $login = strtotime('midnight', $user->lastLogin);
            $now = strtotime('midnight');

            if ($now > $login) {
                $this->_application->updateUserFromVK();

                $key = date('Y-m-d');
                $userOnline = $this->factoryUsers->online->getByKey($key, true);

                if (!$userOnline) {
                    $userOnline = $this->factoryUsers->online->getNew();
                    $userOnline->date = $key;
                    $userOnline->count = 0;
                }
                $userOnline->count++;
                $this->factoryUsers->online->save($userOnline);
            }

            $user->lastLogin = time();

            try {
                $this->factoryUsers->users->save($user);
            } catch (Exception $e) {
            }
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setStatus(HttpResponse::S4_FORBIDDEN);
        }

        return null;
    }
}
