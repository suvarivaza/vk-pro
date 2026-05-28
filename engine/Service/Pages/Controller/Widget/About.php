<?php

namespace Service\Pages;

class Controller_Widget_About extends Controller_Widget
{
    public function actionGet()
    {
        $page = $this->factoryPages->pages->GetPageByAlias('about');
        $query = $this->factoryPages->prices->query()->limit(3)->sort('Order', 'DESC');
        $it = $query->iterator();
        $prices = [];

        foreach ($it as $price) {
            $prices[] = $price;
        }

        $vars = [
            'page' => $page,
            'prices' => $prices,
        ];

        return $this->_response->setBody(\STPL::Fetch('widgets/about', $vars));
    }
}
