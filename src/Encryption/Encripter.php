<?php
declare(strict_types=1);

namespace Chat\Encryption;

use Exception;

class Encrypter implements EncrypterInterface
{
    /**
     * Encryption Algorithm
     *
     * @var string
     */
    private $cipher;

    /**
     * @var string $secret
     */
    private $secret;

    /**
     * Encrypter constructor.
     *
     * @param  string $secret
     * @param  string $cipher
     * @throws Exception
     */
    public function __construct(string $secret = '', string $cipher = 'AES-256-CBC')
    {
        $this->secret = $secret ?? $this->salt();
        $this->cipher = $cipher;
    }

    /**
     * Generate new salt to use in encryption
     *
     * @return string
     * @throws Exception
     */
    public function salt(): string
    {
        return random_bytes(16);
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $value): string
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

        // Encrypt the data by using AES 256 encryption in cbc mode
        $encrypted = openssl_encrypt(
            $value, $this->cipher, $this->secret, 0, $iv
        );
        if ($encrypted === false) {
            throw new \RuntimeException(
                sprintf(
                    "encryption failed. %s",
                    " can not encrypt data"
                )
            );
        }
        // include $iv with encrypted data
        // we will need it to verify mac
        return base64_encode($encrypted . '::' . $iv);
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $value): string
    {
        list($encrypted_payload, $iv) = explode('::', base64_decode($value), 2);
        // decrypt data by using extracted iv and using secret
        $decrypted = openssl_decrypt(
            $encrypted_payload, $this->cipher, $this->secret, 0, $iv
        );
        // if some thing is going wrong like invalid secret or invalid payload, exception thrown
        if ($decrypted === false) {
            throw new \RuntimeException("decryption failed.");
        }

        return $decrypted;
    }
}