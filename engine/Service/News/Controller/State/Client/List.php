<?php

namespace Service\News;

use Lib_Html;
use Stpl;
use System\HttpResponse;

class Controller_State_Client_List extends Controller_State_Admin
{
    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        $vars = [
            'list' => [],
        ];

        $page = $this->_request->get['p']->int(0);

        $query = $this->pages->query();
        $query->limit($this->limit)->offset($page * $this->limit)->sqlCalcFoundRows(true)->sort('dateCreate', 'DESC');

        if ($this->_page == 'news') {
            $title = 'Новости — «Товарищи» мастерская рекламы Челябинск';
            $description = 'Новости и просто полезная информация по рекламной сфере';
            $keywords = 'Реклама, советы, новости, полезная информация, вывеска, монтаж, дизайн, маркетинг, лайфак';
            $url = '/news/';
            $this->_application->menu['news']['active'] = true;
            $query->filter->fieldValue('isNew', '=', true);
        } else {
            $title = 'Полезная информация — «Товарищи» мастерская рекламы Челябинск';
            $description = 'Секреты, советы и просто полезная информация по рекламной сфере';
            $keywords = 'Реклама, советы, секреты, полезная информация, вывеска, монтаж, дизайн, маркетинг, лайфак';

            $url = '/articles';
            $this->_application->menu['articles']['active'] = true;
            $query->filter->fieldValue('isArticle', '=', true);
        }

        $this->_application->Title->Title = $title;
        $this->_application->Title->Description = $description;
        $this->_application->Title->Keywords = $keywords;

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

        $pageslink = Lib_Html::GetNavigationPagesNumber(
            $this->limit,
            4,
            $it->getTotal(),
            $this->_params['page'],
            '/articles?p=@p@',
            1
        );

        $vars = [
            'title' => $title,
            'url' => $url,
            'page' => $this->_page,
            'list' => $list,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(Stpl::Fetch('client/list', $vars));
    }

    /**
     * Обработчик POST-запросов
     *
     * @return void|HttpResponse
     */
    public function actionPost()
    {
        // TODO: Implement actionPost() method.
    }
}
