<?php

namespace Chat\Encryptions;


class EncryptFactory
{
    /**
     * Encrypt conversation with encryption key
     *
     * @param string $encryption_key
     * @param string $message
     * @return string
     * @throws \Exception
     */
    public static function create(string $encryption_key, string $message): string
    {
        $encryption = new Encrypter($encryption_key);
        return $encryption->encrypt($message);
    }

}