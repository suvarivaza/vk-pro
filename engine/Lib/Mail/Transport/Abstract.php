<?php

abstract class Lib_Mail_Transport_Abstract
{
    /**
     * Отправка сообщения
     *
     * @abstract
     *
     * @param \Lib_Mail_Sender $sender
     */
    abstract public function Send(Lib_Mail_Sender $sender);

    /**
     * Очистка всевозможных данных, закрытие подключений
     *
     * @abstract
     */
    abstract public function Flush();
}
