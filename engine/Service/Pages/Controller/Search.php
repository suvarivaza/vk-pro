<?php

namespace Service\Pages;

class Controller_Search extends \System\Service_Controller_State
{
    /**
     * @return mixed
     */
    public function actionGet()
    {
        $list = [];
        $q = $this->_request->get['q']->string();

        $pages = new Model_Pages();

        $query = $pages->query();
        $query->filter->aggregatorOpen('OR')
            ->fieldValue('title', 'LIKE', '%' . $q . '%')
            ->fieldValue('text', 'LIKE', '%' . $q . '%')
            ->aggregatorClose();

        $it = $query->iterator();
        /** @var Model_Pages_Page $page */
        foreach ($it as $page) {
            $text = strip_tags($page->text);

            $position = mb_strpos($text, $this->_request->get['q']->string());

            if ($position !== false) {
                if ($position > 100) {
                    $text = mb_substr($text, $position - 100, 300);
                }
                $position = mb_strpos($text, $this->_request->get['q']->string());
                $length = mb_strlen($this->_request->get['q']->string());
                $text = mb_substr($text, 0, $position) . '<strong>' . mb_substr($text, $position,
                        $length) . '</strong>' . mb_substr($text, $position + $length);
            } else {
                $text = mb_substr($text, 0, 300);
            }
            $photo = '';

            if ($page->photo) {
                $photo = '/images/articles/small/' . $page->photo;
            }
            $list[] = [
                'url' => '/' . $page->alias,
                'title' => $page->title,
                'text' => $text,
                'photo' => $photo,
            ];
        }

        return $list;
    }

    /**
     * @return void|\System\HttpResponse
     */
    public function actionPost()
    {
        return null;
    }
}
