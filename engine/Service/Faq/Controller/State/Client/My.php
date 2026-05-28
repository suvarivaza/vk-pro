<?php

namespace Service\Faq;

use STPL;
use System\HttpRequest;
use System\HttpResponse;

class Controller_State_Client_My extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->Title->addScripts(['/js/faq/faq.min.js?v=1.1']);

        $this->_application->page = 'users';
        $this->_application->userPage = 'faq';

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryFaq->questions->query()->sort('isNewAnswer', 'DESC')->sort('qId', 'DESC');
        $query->filter->fieldValue('userId', '=', $this->_application->User->userId);

        $it = $query->iterator();

        $list = [];
        /** @var Model_Questions_Question $question */
        foreach ($it as $question) {
            $list[] = $question;
            $question->makeShadow();
            $question->isNewAnswer = false;
            $this->factoryFaq->questions->save($question);
        }

        $rubrics = $this->factoryFaq->rubrics->getAll();

        $vars = [
            'list' => $list,
            'rubrics' => $rubrics,
        ];

        return $this->_response->setBody(STPL::Fetch('client/my', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string('');

        switch ($action) {
            case 'add':
                return $this->_add();
            case 'edit':
                return $this->_edit();
        }

        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    private function _add()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        $question = $this->factoryFaq->questions->getNew();
        $question->userId = $this->_application->UserID;
        $question->rubricId = $this->_request->post['rubricId']->int(0);
        $question->question = $this->_request->post['question']->string('', HttpRequest::OUT_HTML_CLEAN);
        $chat = [];
        $chat[] = [
            'date' => time(),
            'type' => 'user',
            'title' => $this->_application->User->name,
            'text' => $question->question,
        ];
        $question->isNew = true;
        $question->isNewQuestion = true;
        $question->isNewAnswer = false;
        $question->setChat($chat);
        $question->isVisible |= Model_Config::IS_FROM_USER;
        $question->adminId = 0;
        $question->answer = '';

        if ($this->factoryFaq->questions->save($question)) {
            return $this->_response->setJson([
                'success' => true,
                'errorText' => 'Ваш вопрос успешно отправлен. Ответ придет в течение суток',
            ]);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => 'Не удалось отправить вопрос. Попробуйте позднее',
        ]);
    }

    private function _edit()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }
        $qId = $this->_request->post['qId']->int(0);

        $question = $this->factoryFaq->questions->getById($qId, true);

        if ($question === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Вопрос не найден']);
        }

        if ($question->userId != $this->_application->UserID) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Вопрос не найден']);
        }

        $questionText = $this->_request->post['question']->string('');

        if (!$questionText) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите текст ответа']);
        }
        $chat = $question->getChat();
        $chat[] = [
            'date' => time(),
            'type' => 'user',
            'title' => $this->_application->User->name,
            'text' => $questionText,
        ];
        $question->isNew = true;
        $question->isNewQuestion = true;
        $question->isNewAnswer = false;
        $question->setChat($chat);

        if ($this->factoryFaq->questions->save($question)) {
            return $this->_response->setJson([
                'success' => true,
                'errorText' => 'Ваш вопрос успешно отправлен. Ответ придет в течение суток',
            ]);
        }

        return $this->_response->setJson([
            'success' => false,
            'errorText' => 'Не удалось отправить вопрос. Попробуйте позднее',
        ]);
    }
}
