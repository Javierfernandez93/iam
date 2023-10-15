<?php

namespace JFStudio;

class SimpleCipher {
    private static $OPENSSL_CIPHER_NAME_256 = "aes-256-cbc"; //Name of OpenSSL Cipher 
    private static $KEY_LENGHT = 256; //Name of OpenSSL Cipher 
    /**
     * Encrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $iv - initialization vector
     * @param type $data - data to encrypt
     * @return encrypted data in base64 encoding with iv attached at end after a :
     */
    static function getOpenSSLChiperName() {
        return self::$OPENSSL_CIPHER_NAME_256;
    }


    static function getDinamycKey($length = 0) {
        return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, min(256, max(256, self::$KEY_LENGHT)));
    }

    static function getIV() {
        return chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
    }

    static function encrypt($data) {
        $key = self::getDinamycKey();
        return ["token" => base64_encode(openssl_encrypt($data, self::$OPENSSL_CIPHER_NAME_256, $key, OPENSSL_RAW_DATA, self::getIV())),"key"=>$key];
    }

    /**
     * Decrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $data - data to be decrypted in base64 encoding with iv attached at the end after a :
     * @return decrypted data
     */
    static function decrypt($data) {
        if($data = openssl_decrypt(base64_decode($data['token']), self::$OPENSSL_CIPHER_NAME_256, $data['key'], OPENSSL_RAW_DATA, self::getIV()))
        {
            return $data;
        }

        return false;
    }
}