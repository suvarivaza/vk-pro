<?php

namespace Service\Pages;

class Controller_Client_My_List extends Controller_Admin
{
    protected $_title = 'Новости';
    protected $_add = 'Добавить новость';

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $query = $this->pages->query();
        $query->limit($this->limit)->offset(($this->_params['page'] - 1) * $this->limit)->sqlCalcFoundRows(true);
        $query->filter->fieldValue('isNew', '=', true);
        $query->filter->fieldValue('userId', '=', $this->_application->UserID);

        $it = $query->iterator();
        $list = [];

        /** @var Model_Pages_Page $item */
        foreach ($it as $item) {
            $list[] = [
                'pageId' => $item->pageId,
                'alias' => $item->alias,
                'title' => $item->title,
                'brief' => $item->describe,
                'date' => $item->dateCreate,
                'restricted' => $item->strict,
            ];
        }

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->limit,
            4,
            $it->getTotal(),
            $this->_params['page'],
            '/news/my/@p@.php',
            1
        );

        $vars = [
            'backUrl' => $this->_request->server['REDIRECT_URL']->string(),
            'title' => $this->_title,
            'add' => $this->_add,
            'page' => 'news',
            'list' => $list,
            'pageslink' => $pageslink,
            'visible' => $this->_application->User->visible || ($this->_application->User->userType == \Service\Users\Model_Config::TYPE_ADMIN),
        ];

        return $this->_response->setBody(\Stpl::Fetch('client/my/list', $vars));
    }

    /**
     * Обработчик POST-запросов
     *
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        // TODO: Implement actionPost() method.
    }
}
