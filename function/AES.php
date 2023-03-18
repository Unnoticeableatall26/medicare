<?php

class AES{

    private static $aes_key = 'bUYJ3nTV6VBasdJF'; //key

    static public function encrypt($str) {
        $data = openssl_encrypt($str, 'AES-128-ECB', self::$aes_key, OPENSSL_RAW_DATA);
        $data = base64_encode($data);

        return $data;
    }

    static public function decrypt($str) {
        $decrypted = openssl_decrypt(base64_decode($str), 'AES-128-ECB', self::$aes_key, OPENSSL_RAW_DATA);
        return $decrypted;
    }

}