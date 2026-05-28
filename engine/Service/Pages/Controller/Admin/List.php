<?php

namespace Service\Pages;

class Controller_Admin_List extends Controller_Admin
{
    protected $_title = 'Страницы';
    protected $_add = 'Добавить страницу';

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $query = $this->pages->query();
        $query->limit($this->limit)->offset(($this->_params['page'] - 1) * $this->limit)->sqlCalcFoundRows(true);

        switch ($this->_page) {
            case 'news':
                $query->filter->fieldValue('isNew', '=', true);
                break;
            case 'articles':
                $query->filter->fieldValue('isArticle', '=', true);
                break;
            default:
                $query->filter->fieldValue('isNew', '=', false)->fieldValue('isArticle', '=', false);
                break;
        }

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
                'photo' => $item->photo,
                'restricted' => $item->strict,
            ];
        }

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->limit,
            4,
            $it->getTotal(),
            $this->_params['page'],
            '/admin/pages/list/@p@.php',
            1
        );

        $vars = [
            'backUrl' => $this->_request->server['REDIRECT_URL']->string(),
            'title' => $this->_title,
            'add' => $this->_add,
            'page' => $this->_page,
            'list' => $list,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(\Stpl::Fetch('admin/list', $vars));
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
