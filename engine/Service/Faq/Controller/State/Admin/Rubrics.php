<?php

namespace Service\Faq;

/**
 * Class Controller_State_Admin
 *
 * @package Service\Faq
 */
abstract class Controller_State_Admin_Rubrics extends Controller_State_Admin
{
    /** @var Model_Rubrics_Rubric */
    protected $_rubric = null;

    protected function _edit()
    {
        $this->_rubric->title = $this->_request->post['title']->string();
        $this->factoryFaq->rubrics->save($this->_rubric);

        return $this->_response->setLocation('/admin/faq');
    }
}
