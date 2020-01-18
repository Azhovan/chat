<?php

namespace Tests\Units;

use Chat\Encryptions\Encrypter;
use PHPUnit\Framework\TestCase;

class EncrypterTest extends TestCase
{
    /**
     * @test 
     */
    public function can_encrypt_message()
    {
        $encryptedMessage = (new Encrypter(''))->encrypt("plain text message");
        $this->assertNotNull($encryptedMessage);
    }

    /**
     * @test
     */
    public function can_decrypt_encrypted_message()
    {
        $message = "plain text message";

        $encrypter = new Encrypter('123232');
        $encrypted = $encrypter->encrypt($message);
        $decrypted = $encrypter->decrypt($encrypted);

        $this->assertSame($decrypted, $message);
    }
}