<?php

namespace Chat\Encryptions;


use Chat\Models\Conversation;
use Chat\Models\User;

class EncryptFactory
{

    /**
     * A Hash-Map of users in a conversation.
     * this data structure helps us to reduce the number of queries which we need to find the information
     * about the messages in large conversation
     *
     * @var array
     */
    private static $usersHashmap;

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
                $user = self::extractUserName($message['user_id']);
                $message['user_name'] = $user;
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
     * @param int $userId
     * @return string
     */
    private static function extractUserName(int $userId): string
    {
        if (isset(self::$usersHashmap[$userId])) {
            return self::$usersHashmap[$userId];
        }
        $user = User::searchBy($userId)->name;
        return self::$usersHashmap[$userId] = $user;
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