<?php

namespace Service\Posting;

class Controller_Shell_Emoji extends Controller_Shell
{
    private $emoji = [
        [
            'file' => 'emoji',
            'title' => 'Эмоции',
        ],
        [
            'file' => 'people',
            'title' => 'Жесты и люди',
        ],
        [
            'file' => 'symbols',
            'title' => 'Символы',
        ],
        [
            'file' => 'animals',
            'title' => 'Животные и растения',
        ],
        [
            'file' => 'food',
            'title' => 'Еда и напитки',
        ],
        [
            'file' => 'sport',
            'title' => 'Спорт и активности',
        ],
        [
            'file' => 'transport',
            'title' => 'Путешествия и транспорт',
        ],
        [
            'file' => 'objects',
            'title' => 'Предметы',
        ],
        [
            'file' => 'flags',
            'title' => 'Флаги',
        ],
    ];

    public function A_GetEmoji()
    {
        foreach ($this->emoji as $group) {
            $codes = fopen(ENGINE_PATH . 'emoji/codes_' . $group['file'] . '.txt', 'r');

            while (!feof($codes)) {
                $code = fgets($codes);
                $code = trim($code);
                $file = file_get_contents('https://vk.com/images/emoji/' . $code . '.png');
                file_put_contents(IMAGES_PATH . 'emoji/' . $code . '.png', $file);
                //sleep(rand(0,2));
            }
        }
    }

    public function A_Generate()
    {
        $emoji = Model_Config::GetEmoji();
        $emoji_new = [];

        foreach ($emoji as $group) {
            foreach ($group['codes'] as $code => $text) {
                $group['replaces']['_' . $code . '_'] = $text;
            }
            $emoji_new[] = $group;
        }
        Model_Config::SetEmojiNew($emoji_new);
    }

    public function A_Run()
    {
        $json = [];

        foreach ($this->emoji as $group) {
            $texts = fopen(ENGINE_PATH . 'emoji/texts_' . $group['file'] . '.txt', 'r');
            $codes = fopen(ENGINE_PATH . 'emoji/codes_' . $group['file'] . '.txt', 'r');

            while (!feof($texts)) {
                $text = fgets($texts);
                $code = fgets($codes);
                $text = trim($text);
                $code = trim($code);
                $group['texts'][$text] = $code;
                $group['codes'][$code] = $text;
            }
            $json[] = $group;
            fclose($texts);
            fclose($codes);
        }
        file_put_contents(Model_Config::$emoji, json_encode($json, JSON_UNESCAPED_UNICODE));
    }
}
