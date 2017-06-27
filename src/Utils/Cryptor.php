<?php

namespace Phalconify\Utils;

/**
 * Description of Crypt.
 */
class Cryptor extends \Phalcon\Di\Injectable
{
    /**
     * Gets the cryptor to use.
     *
     * @staticvar \Phalcon\Crypt $cryptor
     *
     * @return \Phalcon\Crypt
     */
    protected function getCryptor()
    {
        static $cryptor;
        if (!isset($cryptor)) {
            $cryptor = new \Phalcon\Crypt();
        }

        return $cryptor;
    }

    /**
     * Gets the global encryption key to use.
     *
     * @staticvar string $key
     *
     * @return string
     */
    protected function getKey()
    {
        static $key;
        if (!isset($key)) {
            $config = $this->getDI()->getShared('phalconify-config');
            $key = $config->encryption->key;
        }

        return $key;
    }

    /**
     * Decorator of \Phalcon\Crypt::encrypt.
     */
    public function encrypt($text, $key = null)
    {
        return $this->getCryptor()->encrypt($text, $key ?: $this->getKey());
    }

    /**
     * Decorator of \Phalcon\Crypt::decrypt.
     */
    public function decrypt($text, $key = null): string
    {
        return $this->getCryptor()->decrypt($text, $key ?: $this->getKey());
    }

    /**
     * Decorator of \Phalcon\Crypt::decryptBase64.
     */
    public function decryptBase64($text, $key = null, $safe = false): string
    {
        return $this->getCryptor()->decryptBase64($text, $key ?: $this->getKey(), $safe);
    }

    /**
     * Decorator of \Phalcon\Crypt::encryptBase64.
     */
    public function encryptBase64($text, $key = null, $safe = false): string
    {
        return $this->getCryptor()->encryptBase64($text, $key ?: $this->getKey(), $safe);
    }
}
