<?php

namespace JFStudio;

class Cipher {
    private static $OPENSSL_CIPHER_NAME_128 = "aes-128-cbc"; //Name of OpenSSL Cipher 
    private static $OPENSSL_CIPHER_NAME_256 = "aes-256-cbc"; //Name of OpenSSL Cipher 
    private static $OPENSSL_CIPHER_NAME; //Name of OpenSSL Cipher 
    public static $CIPHER_KEY_LEN = 16; //128 bits
    private static $MODE = 0; // 0 => 128 bits 1 => 256
    /**
     * Encrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $iv - initialization vector
     * @param type $data - data to encrypt
     * @return encrypted data in base64 encoding with iv attached at end after a :
     */
    static function getOpenSSLChiperName() {
        return self::$MODE == 0 ? self::$OPENSSL_CIPHER_NAME_256 : self::$OPENSSL_CIPHER_NAME_128;
    }

    static function encrypt($key, $iv, $data,$MODE = 0) {
        self::$MODE = $MODE;

        if (strlen($key) < Cipher::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", Cipher::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > Cipher::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, Cipher::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }

        $encodedEncryptedData = base64_encode(openssl_encrypt($data, self::getOpenSSLChiperName(), $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData.":".$encodedIV;
        
        return $encryptedPayload;
    }

    /**
     * Decrypt data using AES Cipher (CBC) with 128 bit key
     * 
     * @param type $key - key to use should be 16 bytes long (128 bits)
     * @param type $data - data to be decrypted in base64 encoding with iv attached at the end after a :
     * @return decrypted data
     */
    static function decrypt($key,$data,$MODE = 0) {
        self::$MODE = $MODE;

        if (strlen($key) < Cipher::$CIPHER_KEY_LEN) {
            $key = str_pad("$key", Cipher::$CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > Cipher::$CIPHER_KEY_LEN) {
            $key = substr($str, 0, Cipher::$CIPHER_KEY_LEN); //truncate to 16 bytes
        }
        
        $parts = explode(':', $data); //Separate Encrypted data from iv.

        $decryptedData = openssl_decrypt(base64_decode($parts[0]), self::getOpenSSLChiperName(), $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
        
        return $decryptedData;
    }
}