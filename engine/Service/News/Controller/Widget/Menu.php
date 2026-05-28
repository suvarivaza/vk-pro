<?php

namespace Service\News;

use STPL;

class Controller_Widget_Menu extends Controller_Widget
{
    public function actionGet()
    {
        $query = $this->factoryNews->news->query()->sort('dateUpdate', 'DESC');
        $query->filter->fieldValue('announce', '=', true);

        $it = $query->iterator();

        $list = [];

        foreach ($it as $new) {
            $list[] = $new;
        }

        $vars = [
            'list' => $list,
        ];

        return $this->_response->setBody(STPL::Fetch('widget/menu', $vars));
    }
}
