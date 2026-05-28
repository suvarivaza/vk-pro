<?php

class Lib_Mail_Transport_PHPMail extends Lib_Mail_Transport_Abstract
{
    /**
     * @param Lib_Mail_Sender $sender
     *
     * @throws Lib_Exception_Runtime_Backtraced
     * @throws Lib_Exception_InvalidArgument
     */
    public function Send(Lib_Mail_Sender $sender)
    {
        // Адресаты
        $to = $sender->GetAddress('to');

        if (sizeof($to) < 1) {
            throw new Lib_Exception_InvalidArgument('Field TO is empty', Lib_Mail_Sender::ERR_FIELD_TO_IS_EMPTY);
        }
        $to = implode(', ', $to);

        // Тема
        $subject = $sender->GetHeader('Subject');

        // Тело
        $body = $sender->PrepareBody();

        // Дополнительные заголовки
        $additional_headers = $sender->PrepareAdditionalHeaders();

        if (!mail($to, $subject, $body, $additional_headers)) {
            throw new Lib_Exception_Runtime_Backtraced(var_export([
                'to' => $to,
                'subject' => $subject,
                'additional_headers' => $additional_headers,
                'body_len' => strlen($body),
            ], true), Lib_Mail_Sender::ERR_SEND_ERROR);
        }
    }

    /**
     * Очистка всевозможных данных, закрытие подключений
     */
    public function Flush()
    {
    }
}
