<?php

namespace Service\Tasks;

/**
 * @property \Lib_Curl $_curl
 */
class Controller_Shell_Abuse extends Controller_Shell
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getSettings()
    {
        $factory = new \Service\System\Model_Factory();
        $it = $factory->settings->getAll();
        $list = [];

        foreach ($it as $setting) {
            $list[$setting->name] = $setting->value;
        }

        return $list;
    }

    public function A_Test()
    {
        $query = $this->factoryTasks->abuses->query();
        $query->filter->fieldValue('isDone', '=', false);
        $it = $query->iteratorForSave();

        foreach ($it as $abuse) {
            $task = $this->factoryTasks->tasks->getById($abuse->taskId);

            if ($task->isDel) {
                $list = $this->factoryTasks->abuses->getByTaskId($task->taskId, true);

                /**
                 * @var Model_Abuses_Abuse $abuseObj
                 */
                foreach ($list as $abuseObj) {
                    $abuseObj->isDone = true;
                    $this->factoryTasks->abuses->save($abuseObj);
                }

                continue;
            }

            if ($task->vkType == 'photo' || $task->vkType == 'user' || $task->vkType == 'group') {
                $this->params['abuseId'] = $abuse->abuseId;
                $this->A_Check();
                sleep(1);
            }
        }
    }

    public function A_Check()
    {
        $abuseId = intval($this->params['abuseId']);

        $abuse = $this->factoryTasks->abuses->getById($abuseId, true);

        $task = $this->factoryTasks->tasks->getById($abuse->taskId, true);

        $check_access_token = $this->getRandomCheckToken();

        switch ($task->vkType) {
            case 'post':

                $response = $this->VK->getPost($task->ownerId, $task->itemId,$task->ownerType, $check_access_token);

                //не удалось получить пост
                if (empty($response['items'])) {

                    $task->isDel = true;
                    $task->isDelDate = time();

                    if ($this->factoryTasks->tasks->save($task)) {

                        $it = $this->factoryTasks->abuses->getByTaskId($task->taskId, true);

                        /**
                         * @var Model_Abuses_Abuse $abuse
                         */
                        foreach ($it as $abuse) {
                            $abuse->isDone = true;
                            $this->factoryTasks->abuses->save($abuse);
                        }
                    }
                } elseif ($abuse->reason == 1) {
                    $abuse->isDone = true;
                    $this->factoryTasks->abuses->save($abuse);
                }
                break;
            case 'group':

                $response = $this->VK->getGroup($task->ownerId, $check_access_token);
                $paths = explode('/', $task->url);
                $path = array_pop($paths);

                if ($response[0]['screen_name'] != $path) {
                    $task->isDel = true;
                    $task->isDelDate = time();

                    if ($this->factoryTasks->tasks->save($task)) {

                        $it = $this->factoryTasks->abuses->getByTaskId($task->taskId, true);

                        /**
                         * @var Model_Abuses_Abuse $abuse
                         */
                        foreach ($it as $abuse) {
                            $abuse->isDone = true;
                            $this->factoryTasks->abuses->save($abuse);
                        }
                    }
                }
                break;
            case 'user':
                $response = $this->VK->getSubscriptions($task->ownerId, $check_access_token);

                if (isset($response['error']) && ($response['error'] == 15 || $response['error'] == 18)) {
                    $task->isDel = true;
                    $task->isDelDate = time();

                    if ($this->factoryTasks->tasks->save($task)) {
                        $it = $this->factoryTasks->abuses->getByTaskId($task->taskId, true);

                        /**
                         * @var Model_Abuses_Abuse $abuse
                         */
                        foreach ($it as $abuse) {
                            $abuse->isDone = true;
                            $this->factoryTasks->abuses->save($abuse);
                        }
                    }

                    if ($response['error'] == 18) {
                        $user = $this->factoryUsers->users->getById($task->userId, true);
                        $user->ban = true;
                        $user->banDate = time();
                        $karmaObj = $this->factoryUsers->users->karma->getNew();
                        $karmaObj->userId = $user->userId;
                        $karmaObj->karma = -500.00;
                        $karmaObj->karmaFrom = $user->karma;
                        $user->karma = -500.00;
                        $karmaObj->karmaTo = $user->karma;
                        $karmaObj->dateCreate = time();
                        $karmaObj->comment = 'Списывание кармы за бан в ВК';
                        $this->factoryUsers->users->karma->save($karmaObj);

                        $this->factoryMessages = new \Service\Messages\Model_Factory();
                        $message = $this->factoryMessages->users->getNew();
                        $message->userId = $user->userId;
                        $message->isDone = false;
                        $message->type = \Service\Messages\Model_Config::TYPE_SYSTEM;
                        $text = 'Вам был начислен штраф за бан страницы в ВК';
                        $message->text = $text;
                        $message->icon = 'vkpro';
                        $this->factoryMessages->users->save($message);
                        $this->factoryUsers->users->save($user);
                    }
                }
                break;
            case 'photo':

                //здесь в $task->vkType будет photo а мне нужно знать пользоатель или группа!
                $this->VK->getPhotoById($task->ownerId, $task->itemId, $task->ownerType, $check_access_token);

                if (isset($response['error']) && $response['error'] == 200) {
                    $task->isDel = true;
                    $task->isDelDate = time();

                    if ($this->factoryTasks->tasks->save($task)) {
                        $it = $this->factoryTasks->abuses->getByTaskId($task->taskId, true);

                        /**
                         * @var Model_Abuses_Abuse $abuse
                         */
                        foreach ($it as $abuse) {
                            $abuse->isDone = true;
                            $this->factoryTasks->abuses->save($abuse);
                        }
                    }
                }
                break;
            case 'video':
                $response = $this->VK->getVideo($task->ownerId, $task->itemId,$task->ownerType, $check_access_token);

                if (isset($response['error']) && $response['error'] == 15) {
                    $task->isDel = true;
                    $task->isDelDate = time();

                    if ($this->factoryTasks->tasks->save($task)) {
                        $it = $this->factoryTasks->abuses->getByTaskId($task->taskId, true);

                        /**
                         * @var Model_Abuses_Abuse $abuse
                         */
                        foreach ($it as $abuse) {
                            $abuse->isDone = true;
                            $this->factoryTasks->abuses->save($abuse);
                        }
                    }
                }
                break;
            case 'comment':
                break;
        }
    }
}
