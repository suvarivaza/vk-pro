<?php

namespace Service\Users;

use DateTime;
use http\Exception;
use ImapMailboxException;

class Controller_Shell_Email extends \System\Service_Controller_Shell
{
    public const CONNECT = '{imap.yandex.ru:993/imap/ssl/novalidate-cert}INBOX';
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
    protected $_services = [
        'auto' => 'Автоведение',
        'bot' => 'Автобот',
        'grabber' => 'Граббер',
        'posting' => 'Автопостинг',
        'special' => 'Спецзадания',
    ];
    private $_emails = [
        [
            'login' => 'no-reply@vk-pro.top',
            'pass' => '3%T&C5lyF3GC',
        ],
        [
            'login' => 'info@vk-pro.top',
            'pass' => 'yNDf2UvJ',
        ],
    ];

    public function __construct()
    {
        \STPL::PathRegister(ENGINE_PATH . 'engine/Service/Users/Template/');

        return parent::__construct();
    }

    public function A_Prepare()
    {
        $this->A_PaidSend();
        $this->A_NewSend();
        $this->A_TaskSend();
    }

    public function A_PaidSend()
    {
        $uuid = \Lib_Uuid::getNext();

        $list = $this->factoryUsers->users->getPaidUsers();
        /** @var Model_Users_User $user */
        foreach ($list as $user) {
            $query = $this->factoryTasks->tasks->query()->limit(10)->sort('taskId', 'DESC');
            $query->filter->fieldValue('userId', '=', $user->userId);
            $query->filter->fieldValue('countRemain', '=', 0);
            $it = $query->iterator();
            $tasks = [];

            foreach ($it as $task) {
                $tasks[] = $task;
            }

            $email = $this->factoryUsers->emails->getNew();
            $email->uuid = $uuid;
            $email->userId = $user->userId;
            $email->userEmail = $user->email;
            $email->dateCreate = time();
            $email->title = '[VK-PRO.TOP] Новости. Новый функционал!';
            $html = \STPL::Fetch('mail/task_paid', [
                'user' => $user,
                'tasks' => $tasks,
                'titles' => $this->_titles,
            ]);

            $email->text = $html;
            $email->isSent = false;
            $email->isSentDate = null;
            $this->factoryUsers->emails->save($email);
        }
    }

    public function A_NewSend()
    {
        $uuid = \Lib_Uuid::getNext();

        $list = $this->factoryUsers->users->getNewUsers();

        foreach ($list as $user) {
            $tasks = [];
            $email = $this->factoryUsers->emails->getNew();
            $email->uuid = $uuid;
            $email->userId = $user->userId;
            $email->userEmail = $user->email;
            $email->dateCreate = time();
            $email->title = '[VK-PRO.TOP] Новости. Новый функционал!';
            $html = \STPL::Fetch('mail/task_new', [
                'user' => $user,
                'tasks' => $tasks,
                'titles' => $this->_titles,
            ]);

            $email->text = $html;
            $email->isSent = false;
            $email->isSentDate = null;
            $this->factoryUsers->emails->save($email);
        }
    }

    public function A_TaskSend()
    {
        $uuid = \Lib_Uuid::getNext();

        $list = $this->factoryUsers->users->getTaskUsers();

        foreach ($list as $user) {
            $query = $this->factoryTasks->tasks->query()->limit(10)->sort('taskId', 'DESC');
            $query->filter->fieldValue('userId', '=', $user->userId);
            $query->filter->fieldValue('countRemain', '=', 0);
            $it = $query->iterator();
            $tasks = [];

            foreach ($it as $task) {
                $tasks[] = $task;
            }

            $email = $this->factoryUsers->emails->getNew();
            $email->uuid = $uuid;
            $email->userId = $user->userId;
            $email->userEmail = $user->email;
            $email->dateCreate = time();
            $email->title = '[VK-PRO.TOP] Новости. Новый функционал!';
            $html = \STPL::Fetch('mail/task_task', [
                'user' => $user,
                'tasks' => $tasks,
                'titles' => $this->_titles,
            ]);

            $email->text = $html;
            $email->isSent = false;
            $email->isSentDate = null;
            $this->factoryUsers->emails->save($email);
        }
    }

    public function A_Test()
    {
    }

    public function A_CheckEmails()
    {

        $tm = time();

        echo "\naction=Users/Email:CheckEmails";
        echo "\nЧекаем невалидные Email адреса. Запуск каждые 2 часа.";
        echo "\nИдем на ящик no-reply@vk-pro.top забираем все письма от mailer-daemon@yandex.ru (сообщения от яндекса о недоставленных письмах)";
        //Идем на ящик no-reply@vk-pro.top забираем все письма от mailer-daemon@yandex.ru (сообщения от яндекса о недоставленных письмах)

        try {
            $imap = new \Lib_Imap(self::CONNECT, $this->_emails[0]['login'], $this->_emails[0]['pass']);
            $mails = $imap->searchMailbox('FROM "mailer-daemon@yandex.ru"');
            echo "\nЗагружено писем от mailer-daemon@yandex.ru: " . count($mails);

            $count_bad_mail = 0;
            foreach ($mails as $id => $mailId) {
                $item = $imap->getMail($mailId);
                $text = $item->textPlain;

                //распарсиваем email пользователя из письма о недоставленном сообщении
                if (preg_match('@Original-Recipient\:\ rfc822\;(.*)@', $text, $matches)) {
                    $email = $matches[1];
                    $email = mb_convert_encoding($email, 'utf-8', 'ASCII');
                    $email = preg_replace('/[\x00-\x1F\x7F]/u', '', $email);

                    //получаем пользователя по емейл
                    $user = $this->factoryUsers->users->getByEmail($email, true);
                    if (!$user or (($user instanceof Model_Users_User) === false)) continue;
                    $user->badEmail = true; //ставим запись в БД пользователю о том что плохой емейл для того чтобы не отправлять ему письма
                    $count_bad_mail++;
                    //запишем данные юзера в БД и сразу удалим письма о недоставленных сообщениях из почтового ящика no-reply@vk-pro.top
                    if ($this->factoryUsers->users->save($user)) {
                        $imap->deleteMail($item->id);
                        $imap->expungeDeletedMails();
                    }
                }
            }

        } catch (ImapMailboxException $e)  {
            echo 'ImapMailboxException: ', $e->getMessage(), "\n";
            die;
        } catch (Exception $e) {
            echo 'Exception: ', $e->getMessage(), "\n";
            die;
        }

        echo "\nРезультаты:";
        echo "\nПоставили записей badEmail: " . $count_bad_mail;
        echo "\nВремя выполнения скрипта: " . round((time() - $tm) / 60, 2);
        echo "\n";
    }

    public function A_Promo()
    {
        $uuid = \Lib_Uuid::getNext();

        $page = 0;

        do {
            $count = 0;
            $query = $this->factoryUsers->users->query()->limit(100)->offset($page * 100)->sort('lastLogin', 'ASC');
            $query->filter->fieldValue('lastLogin', '<', time() - 86400 * 7);
            $query->filter->fieldValue('email', '!=', '');
            $it = $query->iterator();

            foreach ($it as $user) {
                $count++;
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Не упусти свой шанс получить больше баллов!';
                $html = \STPL::Fetch('mail/promo', [
                    'user' => $user,
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);
                $email->makeShadow();

                $mail = new \PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.yandex.ru';
                $mail->SMTPAuth = true;
                $data = $this->_emails[1];
                $mail->Username = $data['login'];
                $mail->Password = $data['pass'];
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->setFrom($data['login']);
                $mail->addAddress($email->userEmail);
                $mail->Subject = $email->title;

                $mail->CharSet = 'UTF-8';

                $mail->msgHTML($email->text);

                $mail->SMTPDebug = true;

                $mail->smtpConnect([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ]);

                try {
                    if ($mail->send()) {
                        $email->isSent = true;
                        $email->isSentDate = time();
                        $this->factoryUsers->emails->save($email);
                    } elseif ($mail->ErrorInfo == 'SMTP Error: data not accepted.' || strpos($mail->ErrorInfo,
                            'The following recipients failed') !== false || $mail->ErrorInfo == 'You must provide at least one recipient email address.') {
                        $email->isSent = true;
                        $email->isSentDate = null;
                        $this->factoryUsers->emails->save($email);
                    } else {
                        error_log($mail->ErrorInfo);
                    }
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                }
                //error_log('EMAIL: ' . $email->userEmail);
                sleep(rand(1, 2));
            }
            $page++;
        } while ($count);
    }

    public function A_isBot()
    {
        $uuid = \Lib_Uuid::getNext();

        $query = $this->factoryBot->bots->query();
        $query->filter->fieldValue('isActive', '=', true);
        $bots = $query->iterator();

        /** @var \Service\Bot\Model_Bots_Bot $bot */
        foreach ($bots as $bot) {
            $user = $this->factoryUsers->users->getById($bot->userId);

            if ($user->access_token == '' && $user->email) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Автоматическое выполнение заданий приостановлено';
                $html = \STPL::Fetch('mail/is_bot_access_token', [
                    'user' => $user,
                    'titles' => $this->_titles,
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);
            }
        }
    }

    public function A_DoneSend()
    {
        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");

        //Зададим переменные для отслеживаемых метрик скрипта
        $countAllEmails = 0;
        $countSendEmails = 0;

        $uuid = \Lib_Uuid::getNext();
        $list = $this->factoryUsers->users->getTaskUsersDone();

        foreach ($list as $user) {
            $query = $this->factoryTasks->tasks->query()->limit(10)->sort('taskId', 'DESC');
            $query->filter
                ->fieldValue('userId', '=', $user->userId)
                ->fieldValue('countRemain', '=', 0)
                ->fieldValue('dateCreate', '>', time() - 86400);

            $it = $query->iterator();
            $tasks = [];

            foreach ($it as $task) {
                $tasks[] = $task;
            }

            $email = $this->factoryUsers->emails->getNew();
            $email->uuid = $uuid;
            $email->userId = $user->userId;
            $email->userEmail = $user->email;
            $email->dateCreate = time();
            $email->title = '[VK-PRO.TOP] Завершены задания';
            $html = \STPL::Fetch('mail/task_done', [
                'user' => $user,
                'tasks' => $tasks,
                'titles' => $this->_titles,
            ]);

            $email->text = $html;
            $email->isSent = false;
            $email->isSentDate = null;
            $this->factoryUsers->emails->save($email);

            //Не вижу где сама отправка???
        }
    }

    /***
     * Ежедневное оповещение со статистикой, сколько заданий сделано, сколько баллов получено.
     * @throws \Lib_Exception_InvalidArgument_Backtraced
     * @throws \Lib_Exception_Logic_Backtraced
     * @throws \phpmailerException
     */
    public function A_Send()
    {

        //Установим время и дату для записи в лог скрипта
        $tm = time();
        $date = new DateTime();
        $date = $date->format("Y-m-d H:i:s");

        //Зададим переменные для отслеживаемых метрик скрипта
        $countAllEmails = 0;
        $countSendEmails = 0;


        //Берем 100 писем за раз
        $query = $this->factoryUsers->emails->query()->limit(100);
        $query->filter->fieldValue('isSent', '=', false);
        $it = $query->iteratorForSave();

        $time = time();
        /** @var Model_Emails_Email $email */
        foreach ($it as $email) {
            $countAllEmails++;

            $user = $this->factoryUsers->users->getById($email->userId);

            // если плохой емей не отправляем письмо, но делаем запись что отправлено
            // ставим isSent = true для того чтобы в следующий раз не выбирать эти письма на отправку
            if ($user->badEmail) {
                $email->isSent = true;
                $email->isSentDate = time();
                $this->factoryUsers->emails->save($email);
                continue;
            }

            //Работа с кэшэм
            if (time() - $time > 60) {
                $time = time();
                \Lib_HSocket_Factory::Flush();
                \Lib_DB_Factory::Flush();
            }

            //Формируем данные для письма
            $mail = new \PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.yandex.ru';
            $mail->SMTPAuth = true;
            $data = $this->_emails[0];
            $mail->Username = $data['login'];
            $mail->Password = $data['pass'];
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom($data['login']);
            $mail->addAddress($email->userEmail);
            $mail->Subject = $email->title;

            $mail->CharSet = 'UTF-8';

            $mail->msgHTML($email->text);

            $mail->SMTPDebug = false;

            $mail->smtpConnect([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ]);

            try {
                //Пробуем отправлять
                if ($mail->send()) {
                    $countSendEmails++;
                    $email->isSent = true;
                    $email->isSentDate = time();
                    $this->factoryUsers->emails->save($email);
                } elseif ($mail->ErrorInfo == 'SMTP Error: data not accepted.' || strpos($mail->ErrorInfo,
                        'The following recipients failed') !== false || $mail->ErrorInfo == 'You must provide at least one recipient email address.') {
                    $email->isSent = true;
                    $email->isSentDate = null;
                    $this->factoryUsers->emails->save($email);
                } else {
                    error_log($mail->ErrorInfo);
                }
            } catch (\Exception $e) {
                //Если ошибка то залогируем
                error_log($e->getMessage());
            }
            //error_log('EMAIL: ' . $email->userEmail);
            //задержка между отправлениями от 5 до 30 секунд
            sleep(rand(5, 30));

        }


        //Если что либо отправлено выведем для записи в лог скрипта
        if($countSendEmails){
            echo "\naction=Users/Email:Send ";
            echo $date;
            echo "\nПисем на отправку: " . $countAllEmails;
            echo "\nОтправлено писем: " . $countSendEmails;
            echo "\nВремя выполнения скрипта: " . round((time() - $tm) / 60, 2) . ' мин.';
            echo "\n";
        }

    }

    /*
     * Выводит системные сообщения в правом сайдбаре на сайте
     */
    public function A_ServiceSend()
    {

        foreach ($this->_services as $service => $title) {
            switch ($service) {
                case 'auto':
                    $this->_checkAuto($service);
                    break;
                case 'bot':
                    $this->_checkBot($service);
                    break;
                case 'grabber':
                    $this->_checkGrabber($service);
                    break;
                case 'posting':
                    $this->_checkPosting($service);
                    break;
                case 'special':
                    $this->_checkSpecial($service);
                    break;
            }
        }

        return;
    }

    private function _checkAuto($service)
    {
        $uuid = \Lib_Uuid::getNext();

        $query = $this->factoryAuto->auto->groups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 4);
        $query->filter->fieldValue('dateValid', '>', time() + 86400 * 3);
        $it = $query->iteratorForSave();
        /** @var \Service\Auto\Model_Autos_Groups_Group $group */
        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->autoGroupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия автоведения для группы "' . $group->title . '" осталось 3 дня';
                $html = \STPL::Fetch('mail/service3', [
                    'user' => $user,
                    'service' => 'auto',
                    'title' => 'Автоведение',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY3;
                $notification->service = $service;
                $notification->objectId = $group->autoGroupId;
                $notification->title = 'До истечения срока действия автоведения для группы "' . $group->title . '" осталось 3 дня';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        $query = $this->factoryAuto->auto->groups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 2);
        $query->filter->fieldValue('dateValid', '>', time() + 86400);
        $it = $query->iteratorForSave();
        /** @var \Service\Auto\Model_Autos_Groups_Group $group */
        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->autoGroupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия автоведения для группы "' . $group->title . '" остался 1 день';
                $html = \STPL::Fetch('mail/service1', [
                    'user' => $user,
                    'service' => 'auto',
                    'title' => 'Автоведение',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY1;
                $notification->service = $service;
                $notification->objectId = $group->autoGroupId;
                $notification->title = 'До истечения срока действия автоведения для группы "' . $group->title . '" остался 1 день';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->autoGroupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        $query = $this->factoryAuto->auto->groups->query();
        //$query->filter->fieldValue('dateValid', '>', time() - 86400);
        $query->filter->fieldValue('dateValid', '<', time());
        $it = $query->iteratorForSave();
        /** @var \Service\Auto\Model_Autos_Groups_Group $group */
        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY0)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->autoGroupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Срок действия автоведения для группы "' . $group->title . '" истек';
                $html = \STPL::Fetch('mail/service0', [
                    'user' => $user,
                    'service' => 'auto',
                    'title' => 'Автоведение',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY0;
                $notification->service = $service;
                $notification->objectId = $group->autoGroupId;
                $notification->title = 'Срок действия автоведения для группы "' . $group->title . '" истек';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->autoGroupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->autoGroupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }
    }

    private function _checkBot($service)
    {
        $uuid = \Lib_Uuid::getNext();

        $query = $this->factoryBot->bots->query();
        $query->filter->fieldValue('isPro', '=', true);
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 4);
        $query->filter->fieldValue('dateValid', '>', time() + 86400 * 3);
        $it = $query->iteratorForSave();
        /** @var \Service\Bot\Model_Bots_Bot $bot */
        foreach ($it as $bot) {
            $user = $this->factoryUsers->users->getById($bot->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $bot->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $bot->botId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия автобота осталось 3 дня';
                $html = \STPL::Fetch('mail/service3', [
                    'user' => $user,
                    'service' => 'bot',
                    'title' => 'Автобот',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $bot->userId;
                $notification->type = Model_Notifications::TYPE_DAY3;
                $notification->service = $service;
                $notification->objectId = $bot->botId;
                $notification->title = 'До истечения срока действия автобота осталось 3 дня';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        $query = $this->factoryBot->bots->query();
        $query->filter->fieldValue('isPro', '=', true);
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 2);
        $query->filter->fieldValue('dateValid', '>', time() + 86400);
        $it = $query->iteratorForSave();

        foreach ($it as $bot) {
            $user = $this->factoryUsers->users->getById($bot->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $bot->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $bot->botId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия автобота остался 1 день';
                $html = \STPL::Fetch('mail/service1', [
                    'user' => $user,
                    'service' => 'bot',
                    'title' => 'Автобот',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $bot->userId;
                $notification->type = Model_Notifications::TYPE_DAY1;
                $notification->service = $service;
                $notification->objectId = $bot->botId;
                $notification->title = 'До истечения срока действия автобота остался 1 день';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $bot->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $bot->botId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        $query = $this->factoryBot->bots->query();
        //$query->filter->fieldValue('dateValid', '>', time() - 86400);
        $query->filter->fieldValue('dateValid', '<', time());
        $it = $query->iteratorForSave();

        foreach ($it as $bot) {
            $user = $this->factoryUsers->users->getById($bot->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $bot->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY0)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $bot->botId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $user->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Срок действия автобота истек';
                $html = \STPL::Fetch('mail/service0', [
                    'user' => $user,
                    'service' => 'bot',
                    'title' => 'Автобот',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $bot->userId;
                $notification->type = Model_Notifications::TYPE_DAY0;
                $notification->service = $service;
                $notification->objectId = $bot->botId;
                $notification->title = 'Срок действия автобота истек';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $bot->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $bot->botId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $bot->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $bot->botId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }
    }

    private function _checkGrabber($service)
    {
        $uuid = \Lib_Uuid::getNext();

        $query = $this->factoryGrabber->groups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 4);
        $query->filter->fieldValue('dateValid', '>', time() + 86400 * 3);
        $it = $query->iteratorForSave();
        /** @var \Service\Grabber\Model_Groups_Group $group */
        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия граббера для группы ' . $group->title . ' осталось 3 дня';
                $html = \STPL::Fetch('mail/service3', [
                    'user' => $user,
                    'service' => 'grabber',
                    'title' => 'Граббер',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY3;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'До истечения срока действия граббера для группы ' . $group->title . ' осталось 3 дня';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        $query = $this->factoryGrabber->groups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 2);
        $query->filter->fieldValue('dateValid', '>', time() + 86400);
        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия граббера для группы ' . $group->title . ' остался 1 день';
                $html = \STPL::Fetch('mail/service1', [
                    'user' => $user,
                    'service' => 'grabber',
                    'title' => 'Граббер',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY1;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'До истечения срока действия граббера для группы ' . $group->title . ' остался 1 день';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        $query = $this->factoryGrabber->groups->query();
        //$query->filter->fieldValue('dateValid', '>', time() - 86400);
        $query->filter->fieldValue('dateValid', '<', time());
        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY0)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Срок действия граббера для группы ' . $group->title . ' истек';
                $html = \STPL::Fetch('mail/service0', [
                    'user' => $user,
                    'service' => 'grabber',
                    'title' => 'Граббер',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY0;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'Срок действия граббера для группы ' . $group->title . ' истек';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }
    }

    private function _checkPosting($service)
    {
        $uuid = \Lib_Uuid::getNext();
        // --- За три дня ---
        $query = $this->factoryPosting->groups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 4);
        $query->filter->fieldValue('dateValid', '>', time() + 86400 * 3);
        $it = $query->iteratorForSave();
        /** @var \Service\Posting\Model_Groups_Group $group */
        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия автопостинга для группы ' . $group->title . ' осталось 3 дня';
                $html = \STPL::Fetch('mail/service3', [
                    'user' => $user,
                    'service' => 'posting',
                    'title' => 'Автопостинг',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY3;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'До истечения срока действия автопостинга для группы ' . $group->title . ' осталось 3 дня';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        // --- За день ---
        $query = $this->factoryPosting->groups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 2);
        $query->filter->fieldValue('dateValid', '>', time() + 86400);
        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия автопостинга для группы ' . $group->title . ' остался 1 день';
                $html = \STPL::Fetch('mail/service1', [
                    'user' => $user,
                    'service' => 'posting',
                    'title' => 'Автопостинг',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY1;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'До истечения срока действия автопостинга для группы ' . $group->title . ' остался 1 день';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        // --- Протухло ---
        $query = $this->factoryPosting->groups->query();
        //$query->filter->fieldValue('dateValid', '>', time() - 86400);
        $query->filter->fieldValue('dateValid', '<', time());
        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY0)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Срок действия автопостинга для группы ' . $group->title . ' истек';
                $html = \STPL::Fetch('mail/service0', [
                    'user' => $user,
                    'service' => 'posting',
                    'title' => 'Автопостинг',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY0;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'Срок действия автопостинга для группы ' . $group->title . ' истек';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }
    }

    private function _checkSpecial($service)
    {
        $uuid = \Lib_Uuid::getNext();

        // --- За три дня ---
        $query = $this->factoryTasks->specialGroups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 4);
        $query->filter->fieldValue('dateValid', '>', time() + 86400 * 3);
        $it = $query->iteratorForSave();
        /** @var \Service\Tasks\Model_Specials_Groups_Group $group */
        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия спецзаданий для группы ' . $group->title . ' осталось 3 дня';
                $html = \STPL::Fetch('mail/service3', [
                    'user' => $user,
                    'service' => 'tasks/special',
                    'title' => 'Спецзадания',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY3;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'До истечения срока действия спецзаданий для группы ' . $group->title . ' осталось 3 дня';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        // --- За день ---
        $query = $this->factoryTasks->specialGroups->query();
        $query->filter->fieldValue('dateValid', '<', time() + 86400 * 2);
        $query->filter->fieldValue('dateValid', '>', time() + 86400);
        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] До истечения срока действия спецзаданий для группы ' . $group->title . ' остался 1 день';
                $html = \STPL::Fetch('mail/service1', [
                    'user' => $user,
                    'service' => 'tasks/special',
                    'title' => 'Спецзадания',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY1;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'До истечения срока действия спецзаданий для группы ' . $group->title . ' остался 1 день';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }

        // --- Протухло ---
        $query = $this->factoryTasks->specialGroups->query();
        //$query->filter->fieldValue('dateValid', '>', time() - 86400);
        $query->filter->fieldValue('dateValid', '<', time());
        $it = $query->iteratorForSave();

        foreach ($it as $group) {
            $user = $this->factoryUsers->users->getById($group->userId);
            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY0)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            /** @var Model_Notifications_Notification $notification */
            $notification = $it->current();

            if (!$notification) {
                $email = $this->factoryUsers->emails->getNew();
                $email->uuid = $uuid;
                $email->userId = $group->userId;
                $email->userEmail = $user->email;
                $email->dateCreate = time();
                $email->title = '[VK-PRO.TOP] Срок действия спецзаданий для группы ' . $group->title . ' истек';
                $html = \STPL::Fetch('mail/service0', [
                    'user' => $user,
                    'service' => 'tasks/special',
                    'title' => 'Спецзадания',
                ]);

                $email->text = $html;
                $email->isSent = false;
                $email->isSentDate = null;
                $this->factoryUsers->emails->save($email);

                $notification = $this->factoryUsers->notifications->getNew();
                $notification->userId = $group->userId;
                $notification->type = Model_Notifications::TYPE_DAY0;
                $notification->service = $service;
                $notification->objectId = $group->groupId;
                $notification->title = 'Срок действия спецзаданий для группы ' . $group->title . ' истек';
                $this->factoryUsers->notifications->save($notification);
            } else {
                if ($notification->status == 2) {
                    $notification->status = 0;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY3)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }

            $query = $this->factoryUsers->notifications->query();
            $query->filter->fieldValue('userId', '=', $group->userId)
                ->fieldValue('type', '=', Model_Notifications::TYPE_DAY1)
                ->fieldValue('service', '=', $service)
                ->fieldValue('objectId', '=', $group->groupId);
            $it = $query->iteratorForSave();
            $notification = $it->current();

            if ($notification) {
                if ($notification->status == 0) {
                    $notification->makeShadow();
                    $notification->status = 1;
                    $this->factoryUsers->notifications->save($notification);
                }
            }
        }
    }
}
