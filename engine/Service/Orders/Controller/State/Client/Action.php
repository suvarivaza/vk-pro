<?php

namespace Service\Orders;

class Controller_State_Client_Action extends Controller_State_Client
{
    public function actionGet()
    {
        return $this->_response->setLocation('/orders/buy');
    }

    public function actionPost()
    {
        $balance = $this->_request->post['balance']->int(0);
        $service = $this->_request->post['service']->enum(null, ['auto', 'posting', 'grabber', 'special']);
        $month = $this->_request->post['month']->int(0);
        $packId = $this->_request->post['packId']->int(0);

        $group = $this->_request->post['group']->bool(false);
        $user = $this->_application->User;
        $type = $this->_request->post['type']->string('');

        if ($type == 'karmaMinus') {
            $order = $this->factoryOrders->orders->getNew();
            $order->invoiceId = \Lib_Uuid::getNext();
            $order->userId = $user->userId;
            $order->type = $type;
            $order->packId = 0;
            $order->price = (float)($user->getKarmaPrice());

            $order->isOrdered = false;
            $order->isAuto = false;
            $order->isPosting = false;
            $order->isGrabber = false;
            $order->isSpecial = false;

            $order->isAutoMonth = 0;
            $order->isPostingMonth = 0;
            $order->isGrabberMonth = 0;
            $order->isSpecialMonth = 0;

            $order->balance = 0;
            $order->dateCreate = time();

            $order->isReferrer = false;

            if ($this->factoryOrders->orders->save($order)) {
                $order->makeShadow();

                if ($this->_activeOrder($order)) {
                    $this->_application->User->makeShadow();
                    $this->_application->User->balanceRef -= $order->price;
                    $this->factoryUsers->users->save($this->_application->User);
                }

                return $this->_response->setLocation('/orders/success?invoiceId=' . $order->invoiceId);
            } else {
                return $this->_response->setLocation('/orders/error?invoiceId=' . $order->invoiceId);
            }
        }

        if (!$packId && !$balance && !$service) {
            return $this->_response->setLocation('/orders/buy');
        }

        if ($packId > 0) {
            $pack = $this->factoryOrders->orders->packs->getById($packId);

            if ($pack === null) {
                return $this->_response->setLocation('/orders/buy');
            }
        }

        if ($packId > 0) {
            $query = $this->factoryOrders->orders->query()->limit(1);
            $query->filter->fieldValue('userId', '=', $user->userId)
                ->fieldValue('packId', '>', 0);
            $it = $query->iterator();
            $orders = [];

            foreach ($it as $order) {
                $orders[] = $order;
            }
            $first = false;

            if (!count($orders)) {
                $first = true;
            }

            $pack = $this->factoryOrders->orders->packs->getById($packId);

            if (!$pack) {
                return $this->_response->setLocation('/orders/buy');
            }

            $order = $this->factoryOrders->orders->getNew();
            $order->invoiceId = \Lib_Uuid::getNext();
            $order->userId = $user->userId;
            $order->packId = $pack->packId;

            if ($first) {
                $order->balance = $pack->balance + $pack->balance;
            } else {
                $order->balance = $pack->balance + $pack->bonus;
            }

            $order->price = (float)$pack->price;

            $order->isReferrer = $pack->isReferrer;

            $i = 0;

            if ($pack->serviceAll) {
                $order->isAuto = true;
                $order->isPosting = true;
                $order->isGrabber = true;
                $order->isSpecial = true;
                $order->isBot = true;

                $order->isAutoMonth = $pack->serviceMonth;
                $order->isPostingMonth = $pack->serviceMonth;
                $order->isGrabberMonth = $pack->serviceMonth;
                $order->isSpecialMonth = $pack->serviceMonth;
                $order->isBotMonth = $pack->serviceMonth;
            }

            for ($i = 0; $i < $pack->serviceCount; $i++) {
                $service = $this->_request->post['service' . ($i + 1)]->enum(null,
                    ['auto', 'posting', 'grabber', 'special']);

                if ($service == 'auto') {
                    if (!$order->isAuto) {
                        $order->isAuto = true;
                        $order->isAutoMonth = $pack->serviceMonth;
                    }
                } elseif ($service == 'posting') {
                    if (!$order->isPosting) {
                        $order->isPosting = true;
                        $order->isPostingMonth += $pack->serviceMonth;
                    }
                } elseif ($service == 'grabber') {
                    if (!$order->isGrabber) {
                        $order->isGrabber = true;
                        $order->isGrabberMonth += $pack->serviceMonth;
                    }
                } elseif ($service == 'special') {
                    if (!$order->isSpecial) {
                        $order->isSpecial = true;
                        $order->isSpecialMonth += $pack->serviceMonth;
                    }
                }
            }
        } elseif ($balance > 0) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);

            $order = $this->factoryOrders->orders->getNew();
            $order->invoiceId = \Lib_Uuid::getNext();
            $order->userId = $user->userId;
            $order->packId = 0;
            $order->balance = $balance;
            $order->price = (float)(($balance / 10) * $settings['balance']['price']);
        } elseif ($service != null) {
            $settings = json_decode(file_get_contents(Model_Config::$settings), true);

            $order = $this->factoryOrders->orders->getNew();
            $order->invoiceId = \Lib_Uuid::getNext();
            $order->userId = $user->userId;
            $order->packId = 0;
            $order->balance = 0;

            if ($group) {
                $order->price = (float)($settings[$service]['prices'][$month]);
            } else {
                $order->price = (float)($settings[$service]['groups'][$month]);
            }

            $order->monthId = $month;

            switch ($service) {
                case 'auto':
                    $order->isAuto = true;
                    $order->isAutoMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'posting':
                    $order->isPosting = true;
                    $order->isPostingMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'grabber':
                    $order->isGrabber = true;
                    $order->isGrabberMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'special':
                    $order->isSpecial = true;
                    $order->isSpecialMonth = intval($settings[$service]['months'][$month]);
                    break;
                case 'bot':
                    $order->isBot = true;
                    $order->isBotMonth = intval($settings[$service]['months'][$month]);
            }
        } else {
            return $this->_response->setLocation('/orders/buy');
        }

        if ($this->factoryOrders->orders->save($order)) {
            $order->makeShadow();

            if ($this->_activeOrder($order)) {
                $this->_application->User->makeShadow();
                $this->_application->User->balanceRef -= $order->price;
                $this->factoryUsers->users->save($this->_application->User);
            }

            return $this->_response->setLocation('/orders/success?invoiceId=' . $order->invoiceId);
        } else {
            return $this->_response->setLocation('/orders/error?invoiceId=' . $order->invoiceId);
        }
    }

    protected function _activeOrder(Model_Orders_Order $order)
    {
        if ($order->price > $this->_application->User->balanceRef) {
            return false;
        }

        if ($order->type == 'karmaMinus') {
            $user = $this->factoryUsers->users->getById($order->userId, true);
            $order->isBuy = true;
            $order->isBuyDate = time();

            $karma = $this->factoryUsers->users->karma->getNew();
            $karma->userId = $user->userId;
            $karma->dateCreate = time();
            $karma->karma = 50.0 + abs($user->karma);
            $karma->karmaFrom = $user->karma;
            $user->karma = 50.0;
            $karma->karmaTo = $user->karma;
            $karma->comment = 'Очистка кармы';
            $this->factoryUsers->users->karma->save($karma);

            if ($this->factoryUsers->users->save($user)) {
                if ($this->factoryOrders->orders->save($order)) {
                    $message = $this->factoryMessages->users->getNew();
                    $message->userId = $user->userId;
                    $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                    $message->isDone = false;
                    $message->text = 'Очистка кармы';
                    $this->factoryMessages->users->save($message);

                    return true;
                }
            }

            return false;
        }

        $pack = null;

        if ($order->packId) {
            $pack = $this->factoryOrders->orders->packs->getById($order->packId);
        }
        $user = $this->factoryUsers->users->getById($order->userId, true);

        $settings = json_decode(file_get_contents(Model_Config::$settings), true);

        $user->balance += $order->balance;

        if ($order->isReferrer) {
            $settingsRef = json_decode(file_get_contents(\Service\Users\Model_Config::$referrersPath), true);

            if ($user->parentId) {
                $parent = $this->factoryUsers->users->getById($user->parentId, true);
                $referrer = $this->factoryUsers->users->referrers->getNew();
                $referrer->userId = $user->parentId;
                $referrer->from = $user->userId;
                $referrer->dateCreate = time();
                $percent = $user->isRefferer ? $settingsRef['percent']['parentId']['all'] : $settingsRef['percent']['parentId']['first'];
                $referrer->balanceRef = $pack->price * ($percent / 100);
                $referrer->balanceRefFrom = $parent->balanceRef;
                $referrer->comment = $user->isRefferer ? ('Покупка реферрером пакета <strong>' . $pack->title . '</strong>') : ('Первая покупка реферрером пакета <strong>' . $pack->title . '</strong>');
                $parent->balanceRef += $referrer->balanceRef;
                $referrer->balanceRefTo = $parent->balanceRef;

                if ($this->factoryUsers->users->save($parent)) {
                    $this->factoryUsers->users->referrers->save($referrer);
                }
            }

            if ($user->pParentId) {
                $pParent = $this->factoryUsers->users->getById($user->pParentId, true);
                $referrer = $this->factoryUsers->users->referrers->getNew();
                $referrer->userId = $user->parentId;
                $referrer->from = $user->userId;
                $referrer->dateCreate = time();
                $percent = $user->isRefferer ? $settingsRef['percent']['pParentId']['all'] : $settingsRef['percent']['pParentId']['first'];
                $referrer->balanceRef = $pack->price * ($percent / 100);
                $referrer->balanceRefFrom = $pParent->balanceRef;
                $referrer->comment = $user->isRefferer ? ('Покупка реферрером второго уровня пакета <strong>' . $pack->title . '</strong>') : ('Первая покупка реферрером второго уровня пакета <strong>' . $pack->title . '</strong>');
                $pParent->balanceRef += $referrer->balanceRef;
                $referrer->balanceRefTo = $pParent->balanceRef;

                if ($this->factoryUsers->users->save($pParent)) {
                    $this->factoryUsers->users->referrers->save($referrer);
                }
            }

            if ($user->ppParentId) {
                $ppParent = $this->factoryUsers->users->getById($user->ppParentId, true);
                $referrer = $this->factoryUsers->users->referrers->getNew();
                $referrer->userId = $user->parentId;
                $referrer->from = $user->userId;
                $referrer->dateCreate = time();
                $percent = $user->isRefferer ? $settingsRef['percent']['ppParentId']['all'] : $settingsRef['percent']['ppParentId']['first'];
                $referrer->balanceRef = $pack->price * ($percent / 100);
                $referrer->balanceRefFrom = $ppParent->balanceRef;
                $referrer->comment = $user->isRefferer ? ('Покупка реферрером третьего уровня пакета <strong>' . $pack->title . '</strong>') : ('Первая покупка реферрером третьего уровня пакета <strong>' . $pack->title . '</strong>');
                $ppParent->balanceRef += $referrer->balanceRef;
                $referrer->balanceRefTo = $ppParent->balanceRef;

                if ($this->factoryUsers->users->save($ppParent)) {
                    $this->factoryUsers->users->referrers->save($referrer);
                }
            }

            $user->isRefferer = true;
        }

        $this->factoryUsers->users->save($user);

        if ($order->isAuto) {
            $auto = $this->factoryAuto->auto->getByUserId($order->userId, true);

            if ($auto === null) {
                $auto = $this->factoryAuto->auto->getNew();
                $auto->userId = $order->userId;
                $auto->dateCreate = time();
            }
            $auto->isActive = true;
            $auto->dateValid += strtotime('+' . $order->isAutoMonth . ' MONTH');
            $slots = $auto->getSlots();

            if ($order->isSlot) {
                $slots[] = $order->isAutoMonth;
            } else {
                for ($i = 0; $i < $settings['auto']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isAutoMonth;
                }
            }

            $auto->setSlots($slots);
            $this->factoryAuto->auto->save($auto);
        }

        if ($order->isPosting) {
            $posting = $this->factoryPosting->postings->getByUserId($order->userId, true);

            if ($posting === null) {
                $posting = $this->factoryPosting->postings->getNew();
                $posting->userId = $order->userId;
                $posting->dateCreate = time();
            }
            $posting->isActive = true;
            $posting->dateValid += strtotime('+' . $order->isPostingMonth . ' MONTH');
            $slots = $posting->getSlots();

            if ($order->isSlot) {
                $slots[] = $order->isPostingMonth;
            } else {
                for ($i = 0; $i < $settings['posting']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isPostingMonth;
                }
            }

            $posting->setSlots($slots);
            $this->factoryPosting->postings->save($posting);
        }

        if ($order->isGrabber) {
            $grabber = $this->factoryGrabber->grabbers->getByUserId($order->userId, true);

            if ($grabber === null) {
                $grabber = $this->factoryGrabber->grabbers->getNew();
                $grabber->userId = $order->userId;
                $grabber->dateCreate = time();
            }
            $grabber->isActive = true;
            $grabber->dateValid += strtotime('+' . $order->isGrabberMonth . ' MONTH');
            $slots = $grabber->getSlots();

            if ($order->isSlot) {
                $slots[] = $order->isGrabberMonth;
            } else {
                for ($i = 0; $i < $settings['grabber']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isGrabberMonth;
                }
            }

            $grabber->setSlots($slots);
            $this->factoryGrabber->grabbers->save($grabber);
        }

        if ($order->isSpecial) {
            $special = $this->factoryTasks->specials->getByUserId($order->userId, true);

            if ($special === null) {
                $special = $this->factoryTasks->specials->getNew();
                $special->userId = $order->userId;
                $special->dateCreate = time();
            }
            $special->isActive = true;
            $special->dateValid += strtotime('+' . $order->isSpecialMonth . ' MONTH');
            $slots = $special->getSlots();

            if ($order->isSlot) {
                $slots[] = $order->isSpecialMonth;
            } else {
                for ($i = 0; $i < $settings['special']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isSpecialMonth;
                }
            }

            $special->setSlots($slots);
            $this->factoryTasks->specials->save($special);
        }

        $order->isBuy = true;
        $order->isBuyDate = time();

        $this->factoryOrders->orders->save($order);

        return true;
    }
}
