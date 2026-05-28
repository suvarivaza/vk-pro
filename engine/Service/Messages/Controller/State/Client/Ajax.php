<?php

namespace Service\Messages;

class Controller_State_Client_Ajax extends Controller_State_Client
{
    public function actionGet()
    {
        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'get_messages':
                return $this->_get_messages();
            case 'removeMessage':
                return $this->_removeMessage();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_BAD_REQUEST);
    }

    private function _get_messages()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setJson([
                'success' => false,
                'errorText' => 'Метод доступен только для авторизованных',
            ]);
        }

        $list = [];

        $factoryMessages = new \Service\Messages\Model_Factory();
        $query = $factoryMessages->users->query()->limit(3)->sort('messageUserId', 'DESC');
        $query->filter
            ->fieldValue('userId', '=', $this->_application->UserID)
            ->fieldValue('isDone', '=', false);

        $it = $query->iterator();
        /** @var \Service\Messages\Model_Users_User $message */
        foreach ($it as $message) {
            $list[] = $message;
        }

        $messages = [];

        foreach ($it as $message) {
            $messages[$message->messageUserId] = [
                'messageUserId' => $message->messageUserId,
                'text' => $message->text,
            ];
        }

        $factoryFaq = new \Service\Faq\Model_Factory();
        $count = $factoryFaq->questions->getCount($this->_application->UserID);

        $vars = [
            'count' => $count,
            'list' => $list,
        ];

        return $this->_response->setJson([
            'success' => true,
            'messages' => $messages,
            'html' => \STPL::Fetch('client/ajax/messages', $vars),
        ]);
    }

    private function _removeMessage()
    {
        $messageUserId = $this->_request->post['messageUserId']->int(0);
        $messageUser = $this->factoryMessages->users->getById($messageUserId, true);

        if ($messageUser->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Уведомление не найдено']);
        }
        $messageUser->isDone = true;
        $messageUser->isDoneDate = time();
        $this->factoryMessages->users->save($messageUser);

        return $this->_response->setJson(['success' => true]);
    }
}
