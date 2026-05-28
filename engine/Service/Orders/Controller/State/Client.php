<?php

namespace Service\Orders;

abstract class Controller_State_Client extends Controller_State
{
    public function actionPrepare()
    {
        $response = parent::actionPrepare();

        if ($response !== null) {
            return $response;
        }

        $this->_application->Title->add('link', [
            'rel' => 'icon',
            'href' => '/img/icons/32/icon-money.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->add('link', [
            'rel' => 'shortcut icon',
            'href' => '/img/icons/32/icon-money.png',
            'type' => 'image/png',
        ]);

        $this->_application->Title->Title = 'Заказы';
    }

    public function actionPost()
    {

        return $this->actionPost();
    }

    protected function _activeOrder(Model_Orders_Order $order)
    {
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

                if ($parent->isRefferer) {
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
            }

            if ($user->pParentId) {
                $pParent = $this->factoryUsers->users->getById($user->pParentId, true);

                if ($pParent->isRefferer) {
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

            if ($order->packId > 0) {
                for ($i = 0; $i < $settings['auto']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isAutoMonth;
                }
            } else {
                $slots[] = $order->isAutoMonth;
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

            if ($order->packId > 0) {
                for ($i = 0; $i < $settings['posting']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isPostingMonth;
                }
            } else {
                $slots[] = $order->isPostingMonth;
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

            if ($order->packId > 0) {
                for ($i = 0; $i < $settings['grabber']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isGrabberMonth;
                }
            } else {
                $slots[] = $order->isGrabberMonth;
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

            if ($order->packId > 0) {
                for ($i = 0; $i < $settings['special']['limits'][$order->monthId]; $i++) {
                    $slots[] = $order->isSpecialMonth;
                }
            } else {
                $slots[] = $order->isSpecialMonth;
            }

            $special->setSlots($slots);
            $this->factoryTasks->specials->save($special);
        }

        if ($order->isBot) {
            $bot = $this->factoryBot->bots->getByUserId($order->userId, true);

            if ($bot === null) {
                $bot = $this->factoryBot->bots->getNew();
                $bot->userId = $order->userId;
                $bot->dateCreate = time();
                $bot->dateValid = time();
                $bot->isPro = true;
                $bot->isBot = 0;
            }
            $bot->isActive = true;
            $bot->isPro = true;
            $bot->dateValid = max($bot->dateValid, $bot->dateCreate, time());
            $bot->dateValid = strtotime('+' . $order->isBotMonth . ' MONTH', $bot->dateValid);
            $this->factoryBot->bots->save($bot);
        }

        $order->isBuy = true;
        $order->isBuyDate = time();

        $this->factoryOrders->orders->save($order);

        return true;
    }
}
