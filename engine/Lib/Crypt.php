<?php

class Lib_Crypt
{
    private static $key = '';

    private static $key_size = 0;

    private static $iv_size = 0;

    private static $iv = 10;

    private static function _prepare()
    {
        self::$key = pack('H*', 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3');
        self::$key_size = strlen(self::$key);

        self::$iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
        self::$iv = mcrypt_create_iv(self::$iv_size, MCRYPT_RAND);
    }

    public static function Encrypt($string)
    {
        self::_prepare();

        /**
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, self::$key,
            $string, MCRYPT_MODE_CBC, self::$iv); */
        $ciphertext_base64 = base64_encode($string);

        return $ciphertext_base64;
    }

    public static function DeCrypt($string)
    {
        self::_prepare();

        $ciphertext_dec = base64_decode($string);

        /*
        $iv_dec = substr($ciphertext_dec, 0, self::$iv_size);
        $ciphertext_dec = substr($ciphertext_dec, self::$iv_size);

        return @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::$key,
            $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
         * */
        return $ciphertext_dec;
    }
}
