<?php

namespace Service\Faq;

use STPL;
use System\HttpRequest;
use System\HttpResponse;

class Controller_State_Client_Default extends Controller_State_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if ($this->_request->get['isDel']->int(0) > 0) {
            $rubric = $this->factoryFaq->rubrics->getById($this->_request->get['isDel']->int(0), true);

            if ($rubric !== null) {
                $this->factoryFaq->rubrics->delete($rubric);
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }

        $this->_application->Title->addScripts(['/js/faq/faq.min.js']);

        return null;
    }

    public function actionGet()
    {
        $query = $this->factoryFaq->rubrics->query()->limit(1000);
        $it = $query->iterator();

        $list = [];
        /** @var Model_Rubrics_Rubric $rubric */
        foreach ($it as $rubric) {
            $list[] = $rubric;
        }

        $vars = [
            'list' => $list,
        ];

        return $this->_response->setBody(STPL::Fetch('client/rubrics/list', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string('');

        switch ($action) {
            case 'add':
                return $this->_add();
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
}
