<?php

namespace Service\News;

use Lib_Html;
use Stpl;
use System\HttpResponse;

class Controller_State_Client_Default extends Controller_State_Client
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

        $query = $this->factoryNews->news->query();
        $query->limit(10)->offset($page * 10)->sqlCalcFoundRows(true)->sort('dateCreate', 'DESC');

        $title = 'Новости';
        $description = 'Новости и просто полезная информация';
        $keywords = 'Реклама, советы, новости, полезная информация, вывеска, монтаж, дизайн, маркетинг, лайфак';
        $url = '/news';

        $this->_application->Title->Title = $title;
        $this->_application->Title->Description = $description;
        $this->_application->Title->Keywords = $keywords;

        $it = $query->iterator();

        $list = [];
        /** @var Model_News_New $item */
        foreach ($it as $item) {
            $list[] = $item;
        }

        $pageslink = Lib_Html::GetNavigationPagesNumber(
            10,
            4,
            $it->getTotal(),
            $this->_params['page'],
            '/articles?p=@p@',
            1
        );

        $vars = [
            'title' => $title,
            'url' => $url,
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
