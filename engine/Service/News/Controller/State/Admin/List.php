<?php

namespace Service\News;

use Lib_Html;
use Stpl;
use System\HttpResponse;

class Controller_State_Admin_List extends Controller_State_Admin
{
    protected $_title = 'Новости';
    protected $_add = 'Добавить новость';

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        if (isset($this->_application->menu['news']['menu']['list'])) {
            $this->_application->menu['news']['menu']['list']['active'] = true;
        }

        return null;
    }

    /**
     * Обработчик GET-запросов
     *
     * @return mixed
     */
    public function actionGet()
    {
        if ($this->_request->get['toggle']->int(0)) {
            $new = $this->factoryNews->news->getById($this->_request->get['toggle']->int(0), true);

            if ($new !== null) {
                $new->announce = !$new->announce;
                $this->factoryNews->news->save($new);
            }

            return $this->_response->setLocation($this->_request->server['REDIRECT_URL']->string());
        }
        $query = $this->factoryNews->news->query();
        $query->limit($this->limit)->offset(($this->_params['page'] - 1) * $this->limit)->sqlCalcFoundRows(true);

        $it = $query->iterator();
        $list = [];

        /** @var Model_News_New $item */
        foreach ($it as $item) {
            $list[] = $item;
        }

        $pageslink = Lib_Html::GetNavigationPagesNumber(
            $this->limit,
            4,
            $it->getTotal(),
            $this->_params['page'],
            '/admin/news/list/@p@.php',
            1
        );

        $vars = [
            'backUrl' => $this->_request->server['REDIRECT_URL']->string(),
            'title' => $this->_title,
            'add' => $this->_add,
            'list' => $list,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(Stpl::Fetch('admin/list', $vars));
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
