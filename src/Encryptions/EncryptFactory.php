<?php

namespace Chat\Encryptions;


use Chat\Models\Conversation;

class EncryptFactory
{
    /**
     * Encrypt a message with encryption key
     *
     * @param string $encryptionKey
     * @param string $message
     * @return string
     *
     * @throws \Exception
     */
    public static function encrypt(string $encryptionKey, string $message): string
    {
        $encryption = new Encrypter($encryptionKey);
        return $encryption->encrypt($message);
    }

    /**
     * Decrypt array of messages within a chat
     * Second parameter is passed by reference to minimize the memory usage
     *
     * @param array $chats
     * @return array
     *
     * @throws \Exception
     */
    public static function decryptAll(array &$chats): array
    {
        foreach ($chats as $key => &$messages) {
            // find the conversation encryption key
            $encryptionKey = Conversation::find($messages[0]['conversation_id'])->encryption_key;

            // decrypt every single message
            foreach ($messages as &$message) {
                $message['message'] = EncryptFactory::decrypt($encryptionKey, $message['message']);
            }
        }

        return $chats;
    }

    /**
     * Decrypt a message with encryption key
     *
     * @param string $encryptionKey
     * @param string $message
     * @return string
     *
     * @throws \Exception
     */
    public static function decrypt(string $encryptionKey, string $message): string
    {
        $encryption = new Encrypter($encryptionKey);
        return $encryption->decrypt($message);
    }

    /**
     * Generate secure random bytes
     *
     * @return string
     * @throws \Exception
     */
    public static function generateUuid(): string
    {
        return Encrypter::salt(false);
    }

}