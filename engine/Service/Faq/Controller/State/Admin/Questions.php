<?php

namespace Service\Faq;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
abstract class Controller_State_Admin_Questions extends Controller_State_Admin
{
    /** @var Model_Rubrics_Rubric */
    protected $_rubric = null;

    /** @var Model_Questions_Question */
    protected $_question = null;

    protected function _edit()
    {
        $this->_question->isVisible = Model_Config::IS_VISIBLE;

        $this->_question->userId = 0;
        $this->_question->rubricId = $this->_rubric->rubricId;
        $this->_question->adminId = $this->_application->UserID;
        $this->_question->question = $this->_request->post['question']->string();
        $this->_question->answer = $this->_request->post['answer']->string();
        $this->_question->isNewQuestion = false;
        $this->_question->isNewAnswer = false;
        $this->_question->isNew = false;

        $this->_question->setChat([]);
        $this->factoryFaq->questions->save($this->_question);

        return $this->_response->setLocation('/admin/faq/rubrics/' . $this->_rubric->rubricId . '/list');
    }
}
