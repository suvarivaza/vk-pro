<?php

namespace Service\System;

class Controller_Admin_Settings_Sitemap extends Controller_Admin
{
    private $_sitemap = '';
    private $_reload = false;

    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_sitemap = file_get_contents(ENGINE_PATH . 'sitemap.xml');

        return null;
    }

    public function actionGet()
    {
        $saved = false;

        if ($this->_reload === true) {
            $saved = true;
            $this->_sitemap = file_get_contents(ENGINE_PATH . 'sitemap.xml');
        }

        $vars = [
            'saved' => $saved,
            'sitemap' => $this->_sitemap,
        ];

        return $this->_response->setBody(\STPL::Fetch('admin/settings/sitemap', $vars));
    }

    public function actionPost()
    {
        $action = $this->_request->post['action']->string();

        switch ($action) {
            case 'generate':
                return $this->_generateSitemap();
            case 'save':
                return $this->_save();
        }

        return $this->_response->setStatus(\System\HttpResponse::S4_METHOD_NOT_ALLOWED);
    }

    private function _generateSitemap()
    {
        $urls = [];

        $factoryPages = new \Service\Pages\Model_Pages();

        $query = $factoryPages->query()->limit(1000);
        $it = $query->iterator();

        foreach ($it as $page) {
            if ($page->alias == 'main') {
                continue;
            }
            $urls[] = [
                'loc' => 'http://' . DOMAIN . '/' . $page->alias,
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ];
        }

        $string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($urls as $url) {
            $string .= "\t<url>\n";

            foreach ($url as $name => $val) {
                $string .= "\t\t<" . $name . '>' . $val . '</' . $name . ">\n";
            }
            $string .= "\t</url>\n";
        }
        $string .= '</urlset>';

        file_put_contents(ENGINE_PATH . 'sitemap.xml', $string);
        $this->_reload = true;

        return null;
    }

    private function _save()
    {
        $string = $this->_request->post['sitemap']->string('');
        file_put_contents(ENGINE_PATH . 'sitemap.xml', $string);
        $this->_reload = true;

        return $this->_response->setLocation('/admin/system/settings');
    }
}
