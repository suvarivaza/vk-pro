<?php

namespace Service\Logs;

class Model_Config
{
    public const USER_CREATE = 'user-create';
    public const USER_LOGIN = 'user-login';
    public const USER_UPDATE = 'user-update';
    public const USER_ONLINE = 'user-online';
    public const USER_BALANCE = 'user-balance';

    public const ITEM_SAVE = 'item-save';
    public const SOURCE_ERROR = 'source-error';

    public const VK_API_SUCCESS = 'vk-api-success';
    public const VK_API_ERROR = 'vk-api-error';
    public const VK_API_EMPTY = 'vk-api-empty';
    public const VK_API_UNKNOWN_ERROR = 'vk-api-unknown-error';
    public const VK_API_PROXY = 'vk-api-proxy';

    public static $Actions = [
        self::USER_CREATE => 'Создание пользователя',
        self::USER_UPDATE => 'Обновление пользователя',
        self::USER_LOGIN => 'Авторизация пользователя',
        self::USER_ONLINE => 'Пользователь онлайн',
        self::USER_BALANCE => 'Баланс пользователя',
        self::ITEM_SAVE => 'Изменение задания',
        self::SOURCE_ERROR => 'Ошибка добавления источника в граббер',
        self::VK_API_SUCCESS => 'Успешный запрос в API',
        self::VK_API_ERROR => 'Запрос в API с ошибкой',
        self::VK_API_EMPTY => 'Пустой ответ API',
        self::VK_API_UNKNOWN_ERROR => 'Неизвестная ошибка API',
        self::VK_API_PROXY => 'Ошибка прокси',
    ];
}
