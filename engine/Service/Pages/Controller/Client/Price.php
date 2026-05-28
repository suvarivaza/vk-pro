<?php

namespace Service\Pages;

class Controller_Client_Price extends Controller_Client
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response != null) {
            return $response;
        }

        if (isset($this->_application->menu['price'])) {
            $this->_application->menu['price']['active'] = true;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function actionGet()
    {
        $query = $this->factory->prices->query()->limit(3)->sort('Order', 'DESC');
        $it = $query->iterator();
        $prices = [];

        $list = [];

        /** @var Model_Prices_Price $price */
        foreach ($it as $price) {
            $prices[] = $price;

            foreach ($price->getPositions() as $position) {
                $list[$price->Alias][$position->Row][$position->Column] = $position->Value;
            }
        }

        $price = $this->factory->prices->GetByAlias('price-' . $this->_params['path']);

        if (!$price) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        if (isset($this->_application->menu['prices'])) {
            $this->_application->menu['prices']['active'] = true;
        }

        $this->_application->Title->Title = $price->TitleMain;
        $this->_application->Title->Description = $price->TitleMain;
        $this->_application->Title->Keywords = $price->TitleMain;

        $this->_application->Title->add('meta', [
            'name' => 'og:type',
            'content' => 'website',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:title',
            'content' => $price->TitleMain,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:description',
            'content' => $price->TitleMain,
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image',
            'content' => 'http://insbeton.ru/img/logo.png',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:width',
            'content' => '250',
        ]);
        $this->_application->Title->add('meta', [
            'name' => 'og:image:height',
            'content' => '163',
        ]);

        $vars = [
            'app' => $this->_application,
            'prices' => $prices,
            'list' => $list,
            'price' => $price,
            'print' => isset($_GET['print']),
        ];

        return $this->_response->setBody(\STPL::Fetch('client/prices', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'commentAdd':
                return $this->_commentAdd();
        }

        return parent::actionPost();
    }

    private function _commentAdd()
    {
        $pages = new Model_Pages();
        $factory = new \Service\Catalog\Model_Factory();

        $page = $pages->GetPageByAlias($this->_params['path']);

        if ($page === null) {
            return $this->_response->setStatus(\System\HttpResponse::S4_NOT_FOUND);
        }

        $userName = $this->_request->post['userName']->string('', \System\HttpRequest::OUT_HTML_CLEAN);

        if (!$userName) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите имя']);
        }
        $textQuestion = $this->_request->post['textQuestion']->string('', \System\HttpRequest::OUT_HTML_CLEAN);

        if (!$textQuestion) {
            return $this->_response->setJson(['success' => false, 'errorText' => 'Укажите текст комментария']);
        }

        $comment = $factory->comments->getNewItem();
        $comment->uniqueId = $page->uniqueId;
        $comment->userId = $this->_application->UserID ?: 0;
        $comment->userName = $userName;
        $comment->textQuestion = $textQuestion;
        $comment->visible = 0;
        $comment->dateCreate = time();

        $factory->comments->save($comment);

        return $this->_response->setJson(['success' => true]);
    }
}
