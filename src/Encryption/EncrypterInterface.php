<?php

namespace Chat\Encryption;

interface EncrypterInterface
{
        /**
         * Encrypt the given value.
         *
         * @param  string $value
         * @return string
         * @throws \Exception
         */
    public function encrypt(string $value): string;

        /**
         * Decrypt the given value.
         *
         * @param  string $payload
         * @return string
         * @throws \Exception
         */
    public function decrypt(string $payload): string;
}