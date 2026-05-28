<?php

namespace Service\Messages;

/**
 * Class Controller_State_Admin
 *
 * @package Service\News
 */
abstract class Controller_State_Admin extends \System\Service_Controller_State
{
    /** @var Model_Messages_Message */
    protected $_message = null;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if (isset($this->_application->menu['messages'])) {
            $this->_application->menu['messages']['active'] = true;
        }

        if (null !== $response) {
            return $response;
        }

        return null;
    }

    protected function _edit()
    {
        $this->_message->userId = $this->_application->User->userId;
        $this->_message->text = $this->_request->post['text']->string('', \System\HttpRequest::OUT_HTML);
        $this->_message->dateCreate = time();
        $this->_message->isDone = false;
        $this->_message->type = 1;

        if ($this->factoryMessages->messages->save($this->_message)) {
            $page = 0;
            do {
                $count = 0;
                $query = $this->factoryUsers->users->query()->sort('userId', 'ASC');
                $query->limit(1000)->offset($page * 1000);

                $it = $query->iterator();

                foreach ($it as $user) {
                    $count++;

                    $messageUser = $this->factoryMessages->users->getByMessageIdUserId($this->_message->messageId,
                        $user->userId, true);

                    if ($messageUser === null) {
                        $messageUser = $this->factoryMessages->users->getNew();
                        $messageUser->userId = $user->userId;
                        $messageUser->messageId = $this->_message->messageId;
                        $messageUser->isDone = false;
                        $messageUser->isDoneDate = null;
                    }

                    $messageUser->text = $this->_message->text;

                    $this->factoryMessages->users->save($messageUser);

                }
                $page++;
            } while ($count);
        }

        return $this->_response->setLocation('/admin/messages/');
    }
}
