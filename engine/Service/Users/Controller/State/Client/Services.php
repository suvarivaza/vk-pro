<?php

namespace Service\Users;

class Controller_State_Client_Services extends Controller_State_Client
{
    public function actionPrepare()
    {
        $this->_application->userPage = 'services';
        $this->_application->Title->Title = 'Приобретенные функции';

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-services.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-services.png',
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
        $auto = $this->factoryAuto->auto->getByUserId($this->_application->UserID);
        $autoGroups = [];

        if ($auto) {
            $autoGroups = $this->factoryAuto->auto->groups->getByAutoId($auto->autoId);
        }

        $posting = $this->factoryPosting->postings->getByUserId($this->_application->UserID);
        $postingGroups = [];

        if ($posting) {
            $postingGroups = $this->factoryPosting->groups->getByUserId($this->_application->UserID);
        }

        $grabber = $this->factoryGrabber->grabbers->getByUserId($this->_application->UserID);
        $grabberGroups = [];

        if ($grabber) {
            $grabberGroups = $this->factoryGrabber->groups->getByGrabberId($grabber->grabberId);
        }

        $special = $this->factoryTasks->specials->getByUserId($this->_application->UserID);
        $specialGroups = [];

        if ($special) {
            $specialGroups = $this->factoryTasks->specialGroups->getByUserId($this->_application->UserID);
        }

        $list = [];

        foreach ($autoGroups as $group) {
            if (!isset($list[$group->ownerId])) {
                $list[$group->ownerId] = [
                    'title' => $group->title,
                    'photo' => $group->photo,
                    'isAuto' => true,
                    'isAutoValid' => $group->dateValid,
                    'isAutoId' => $group->autoGroupId,
                ];
            } else {
                $list[$group->ownerId]['isAuto'] = true;
                $list[$group->ownerId]['isAutoValid'] = $group->dateValid;
                $list[$group->ownerId]['isAutoId'] = $group->autoGroupId;
            }
        }

        foreach ($postingGroups as $group) {
            if (!isset($list[$group->ownerId])) {
                $list[$group->ownerId] = [
                    'title' => $group->title,
                    'photo' => $group->photo,
                    'isPosting' => true,
                    'isPostingValid' => $group->dateValid,
                    'isPostingId' => $group->groupId,
                ];
            } else {
                $list[$group->ownerId]['isPosting'] = true;
                $list[$group->ownerId]['isPostingValid'] = $group->dateValid;
                $list[$group->ownerId]['isPostingId'] = $group->groupId;
            }
        }

        foreach ($grabberGroups as $group) {
            if (!isset($list[$group->ownerId])) {
                $list[$group->ownerId] = [
                    'title' => $group->title,
                    'photo' => $group->photo,
                    'isGrabber' => true,
                    'isGrabberValid' => $group->dateValid,
                    'isGrabberId' => $group->groupId,
                ];
            } else {
                $list[$group->ownerId]['isGrabber'] = true;
                $list[$group->ownerId]['isGrabberValid'] = $group->dateValid;
                $list[$group->ownerId]['isGrabberId'] = $group->groupId;
            }
        }

        foreach ($specialGroups as $group) {
            if (!isset($list[$group->ownerId])) {
                $list[$group->ownerId] = [
                    'title' => $group->title,
                    'photo' => $group->photo,
                    'isSpecial' => true,
                    'isSpecialValid' => $group->dateValid,
                    'isSpecialId' => $group->groupId,
                ];
            } else {
                $list[$group->ownerId]['isSpecial'] = true;
                $list[$group->ownerId]['isSpecialValid'] = $group->dateValid;
                $list[$group->ownerId]['isSpecialId'] = $group->groupId;
            }
        }

        $vars = [
            'user' => $this->_application->User,
            'auto' => $auto,
            'posting' => $posting,
            'grabber' => $grabber,
            'special' => $special,
            'list' => $list,
            'autoGroups' => $autoGroups,
            'postingGroups' => $postingGroups,
            'grabberGroups' => $grabberGroups,
            'specialGroups' => $specialGroups,
        ];

        return $this->_response->setBody(\STPL::Fetch('client/services', $vars));
    }

    public function actionPost()
    {
    }
}
