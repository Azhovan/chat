<?php

namespace Chat\Encryptions;


class EncryptFactory
{
    /**
     * Encrypt a message with encryption key
     *
     * @param string $encryption_key
     * @param string $message
     * @return string
     * @throws \Exception
     */
    public static function encrypt(string $encryption_key, string $message): string
    {
        $encryption = new Encrypter($encryption_key);
        return $encryption->encrypt($message);
    }

    /**
     * Decrypt a message with encryption key
     *
     * @param string $encryption_key
     * @param string $message
     * @return string
     * @throws \Exception
     */
    public static function decrypt(string $encryption_key, string $message): string
    {
        $encryption = new Encrypter($encryption_key);
        return $encryption->decrypt($message);
    }

}