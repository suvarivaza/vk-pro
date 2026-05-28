<?php

namespace Service\Faq;

use STPL;
use System\HttpResponse;

class Controller_State_Admin_My extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->Title->addScripts(['/js/faq/faq.min.js']);

        $this->_application->page = 'users';
        $this->_application->userPage = 'faq';

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryFaq->questions->query()->sort('qId', 'ASC');
        $query->filter->fieldValue('isNewQuestion', '=', true);

        $it = $query->iterator();

        $list = [];
        /** @var Model_Questions_Question $question */
        foreach ($it as $question) {
            $list[] = $question;
        }

        $arr = $this->factoryFaq->rubrics->getAll();
        $rubrics = [];

        foreach ($arr as $rubric) {
            $rubrics[$rubric->rubricId] = $rubric;
        }

        $vars = [
            'list' => $list,
            'rubrics' => $rubrics,
        ];

        return $this->_response->setBody(STPL::Fetch('admin/my', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string('');

        switch ($action) {
            case 'edit':
                return $this->_edit();
            case 'done':
                return $this->_done();
        }

        return $this->_response->setStatus(HttpResponse::S4_METHOD_NOT_ALLOWED);
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

        $questionText = $this->_request->post['question']->string('');

        if (!$questionText) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите текст ответа']);
        }
        $chat = $question->getChat();
        $chat[] = [
            'date' => time(),
            'type' => 'admin',
            'title' => 'Служба техподдержки',
            'text' => $questionText,
        ];

        $question->isNew = true;
        $question->isNewQuestion = false;
        $question->isNewAnswer = true;
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

    private function _done()
    {
        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }
        $qId = $this->_request->post['qId']->int(0);

        $question = $this->factoryFaq->questions->getById($qId, true);

        if ($question === null) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Вопрос не найден']);
        }

        $question->isNew = true;
        $question->isNewQuestion = false;
        $question->isNewAnswer = false;

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
