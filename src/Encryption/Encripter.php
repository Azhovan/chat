<?php
declare(strict_types=1);

namespace Chat\Encryption;

use Exception;

class Encrypter implements EncrypterInterface
{
    /**
     * The salt.
     *
     * @var string
     */
    protected $salt;

    /**
     * The encryption algorithm.
     *
     * @var string
     */
    protected $cipher;

    /**
     * Encrypter constructor.
     *
     * @param string $salt
     * @throws Exception
     */
    public function __construct(string $salt)
    {
        $this->cipher = 'AES-256-CBC';
        $this->salt = $salt ?? $this->salt();
    }


    /**
     * Create a new salt.
     *
     * @return string
     * @throws Exception
     */
    public static function salt()
    {
        return random_bytes(32);
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $value): string
    {
        $iv = random_bytes(
            openssl_cipher_iv_length($this->cipher)
        );

        // encrypt the value using openssl
        $value = \openssl_encrypt(serialize($value), $this->cipher, $this->salt, 0, $iv);

        if ($value === false) {
            throw new \RuntimeException(sprintf(
                "encryption failed. input: %s reason: %s",
                $value, "openssl encryption failed."
            ));
        }

        // this hash mac will be used to make sure we are decrypting the message
        // which is encrypted with the previous key
        $mac = $this->hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf("Encryption failed. input: %s reason: %s",
                $value, "can not encode message into json."
            ));
        }

        return base64_encode($json);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param string $iv
     * @param mixed $value
     * @return string
     */
    protected function hash(string $iv, string $value): string
    {
        return hash_hmac('sha256', $iv . $value, $this->salt);
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $payload): string
    {
        $payload = $this->getVerifiedPayload($payload);

        $iv = base64_decode($payload['iv']);

        // decrypt the payload.
        $decrypted = \openssl_decrypt(
            $payload['value'], $this->cipher, $this->salt, 0, $iv
        );

        if ($decrypted === false) {
            throw new \RuntimeException(sprintf("Decryption failed. input: %s, %s",
                $payload, "openssl decrypt failed."
            ));
        }

        return unserialize($decrypted);
    }

    /**
     * Extract encrypted payload
     *
     * @param string $payload
     * @return array
     * @throws Exception
     */
    protected function getVerifiedPayload(string $payload): array
    {
        $payload = json_decode(base64_decode($payload), true);

        if (!$this->verifyPayload($payload)) {
            throw new \RuntimeException(sprintf("Decryption failed. %s",
                'The payload is invalid.'
            ));
        }

        if (!$this->verifyMac($payload)) {
            throw new \RuntimeException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify the MAC
     *
     * @param array $payload
     * @return bool
     * @throws Exception
     */
    protected function verifyMac(array $payload)
    {
        $bytes = random_bytes(16);

        $computedMac = hash_hmac(
            'sha256',
            $this->hash($payload['iv'], $payload['value']),
            $bytes, true
        );

        return hash_equals(
            hash_hmac('sha256', $payload['mac'], $bytes, true),
            $computedMac
        );
    }

    /**
     * Verify the encryption payload.
     *
     * @param array $payload
     * @return bool
     */
    protected function verifyPayload(array $payload): bool
    {
        return isset($payload['iv'], $payload['value'], $payload['mac']) &&
            strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
    }


}