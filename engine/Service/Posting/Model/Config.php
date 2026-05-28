<?php

namespace Service\Posting;

class Model_Config
{
    public static $emoji = ENGINE_PATH . 'engine/Service/Posting/Model/Emoji.json';

    public static function SetEmojiNew($json)
    {
        file_put_contents(self::$emoji, json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    public static function EmojiToHtml($text)
    {
        $emoji = self::GetEmoji();

        foreach ($emoji as $group) {
            foreach ($group['texts'] as $word => $code) {
                $text = str_replace($word, '<img src="/img/emoji/' . $code . '.png" />', $text);
            }
        }

        return $text;
    }

    public static function GetEmoji()
    {
        return json_decode(file_get_contents(self::$emoji), true);
    }
}
