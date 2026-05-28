<?php

namespace Service\System;

class Controller_Widget_Menu extends Controller_Widget
{
    /**
     * Обработчик запросов.
     *
     * @return \System\HttpResponse|null
     */
    public function actionGet()
    {
        $query = $this->factoryPages->query();
        $query->filter->fieldValue('announce', '=', true);

        $it = $query->iterator();

        foreach ($it as $page) {
            $list[] = [
                'title' => $page->title,
                'photo' => '/images/articles/small/' . $page->photo,
            ];
        }

        $query = $this->factoryCatalog->items->query();
        $query->filter->fieldValue('accounce', '=', true);

        $vars = [
            'list' => $rubrics,
        ];

        return $this->_response->setBody(\STPL::Fetch('widgets/menu', $vars));
    }
}
