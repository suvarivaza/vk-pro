<?php

namespace Service\Users;

class Controller_State_Client_Tasks extends Controller_State_Client
{
    protected $_titles = [
        'likes' => [
            'title' => 'Поставить лайк',
            'vkTypes' => [
                'post' => 'Лайкнуть запись на стене',
                'photo' => 'Лайкнуть фотографию',
                'video' => 'Лайкнуть видеозапись',
                'comment' => 'Лайкнуть комментарий',
            ],
        ],
        'reposts' => [
            'title' => 'Сделать репост',
            'vkTypes' => [
                'post' => 'Репостнуть запись на стене',
                'photo' => 'Репостнуть фотографию',
                'video' => 'Репостнуть видеозапись',
                'comment' => 'Репостнуть комментарий',
            ],
        ],
        'comments' => [
            'title' => 'Оставить комментарий',
        ],
        'join' => [
            'title' => 'Подписаться',
        ],
        'friends' => [
            'title' => 'Добавить в друзья',
        ],
        'polls' => [
            'title' => 'Участвовать в опросе',
        ],
        'views' => [
            'title' => 'Просмотреть запись на стене',
        ],
        'video' => [
            'title' => 'Просмотреть видео',
        ],
    ];

    public function actionPrepare()
    {
        $this->_application->userPage = 'tasks';
        $this->_application->Title->Title = 'История заданий';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-tasks.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-tasks.png',
            'type' => 'image/png',
        ]);

        $reponse = parent::actionPrepare();

        if ($reponse !== null) {
            return $reponse;
        }

        if (!$this->_application->UserIsAuth()) {
            return $this->_response->setLocation('/users/login');
        }

        return null;
    }

    public function actionGet()
    {
        $page = $this->_request->get['p']->int(1);

        $query = $this->factoryTasks->tasks->query()->sort('dateCreate',
            'DESC')->limit($this->_limit)->offset(($page - 1) * $this->_limit)->sqlCalcFoundRows(true);
        $query->filter->fieldValue('userId', '=', $this->_application->User->userId);

        $it = $query->iterator();

        $pageslink = \Lib_Html::GetNavigationPagesNumber(
            $this->_limit,
            4,
            $it->getTotal(),
            $page,
            '/users/tasks?p=@p@',
            1
        );
        $list = [];

        foreach ($it as $task) {
            $list[] = $task;
        }

        $vars = [
            'titles' => $this->_titles,
            'user' => $this->_application->User,
            'list' => $list,
            'pageslink' => $pageslink,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/tasks', $vars));
    }

    public function actionPost()
    {
    }
}
