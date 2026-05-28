<?php

namespace Service\Tasks;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;


class Controller_Shell_Test extends \System\Service_Controller_Shell
{


    function UpdateTask(){
        $taskId = 60813;

        $task = $this->factoryTasks->tasks->getById($taskId);
        $task->makeShadow();
        //$task->ownerType = 2;
        //$task->url = 'https://vk.com/wall-109450826_1488';
        $task->ownerId = '-76286103';
        $res = $this->factoryTasks->tasks->save($task);

        var_dump($res);
        die;
    }

    public function A_Test()
    {

        $this->UpdateTask();
        die;


        //для репостов комментариев нужно указывать тип wall и добавлять айди комментария _r761
        $type = 'wall';
        $owner_id = '-191477389';
        $item_id = '760';
        $item_id = '760_r761';
        $access_token ='13f29111f9fb2a63492624b7a71275951dbf4723f60e3da9b4b8db9e8bceb9d01ee5fe843d632b562a916';

        $ownerType = 1;

        $type = 'wall';
        $owner_id = '518369796';
        $item_id = '189';
        $access_token ='629e734f76fcb8dc10a365e23550ccb97a66e33d6a0348e8a01a1b8e1d8fd7bba9e8111b117fcb1e164bc';

        $Vk = new \Lib_VK();
        $res = $Vk->makeRepost($type, $owner_id, $item_id, $ownerType,$access_token);

        var_dump($res);
        die;


//        $factory = new \Service\System\Model_Factory();
//        $token = $factory->settings->getByName('token');
//
//        $task = $this->factoryTasks->tasks->getById(29755);
//
//        $response = $this->VK->api('polls.getVoters', [
//            'owner_id' => $task->ownerId,
//            'poll_id' => $task->pollId,
//            'answer_ids' => $task->answerId ?: $task->answerIds,
//            'offset' => 0,
//            'count' => 1000,
//            'sort' => 'desc',
//            'access_token' => 'b1760b326a33c540d0f3a7d1e90b31500abfb34123328b871dd4ee96fe20d88d4e3a88aafd0df57911a66',
//        ]);
//
//        if ($response['error'] && $response['error'] == 250) {
//            $response = $this->VK->api('polls.getById', [
//                'owner_id' => $task->ownerId,
//                'poll_id' => $task->pollId,
//                'access_token' => $token->value,
//            ]);
//        }
    }



    function checkSpeedGetProxy($proxyUrl){
        $timeResult = 0;
        $i = 0;
        do {
            $i++;
            $t = microtime(true);
            $proxy = file_get_contents($proxyUrl);
            $timeResult += microtime(true) - $t;
            usleep(200000);
        } while ($i < 100);
        return $timeResult;
    }
}
