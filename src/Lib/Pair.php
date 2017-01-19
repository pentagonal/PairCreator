<?php
namespace Pentagonal\PairCreator\Lib;

/**
 * Class Pair
 * @package Pentagonal\PairCreator\Lib
 * @version     1.0.0
 * @author      pentagonal <org@pentagonal.org>
 */
class Pair
{
    const SPLIT_LENGTH = 60;
    const KEY_NAME   = 'key';
    const DATA_NAME  = 'data';
    const TOKEN_NAME = 'token';
    const SIGN_NAME  = 'sign';
    const KEY_SIGN_NAME  = 'key_sign';
    const TIME_NAME  = 'time';

    /**
     * @var EncryptionGenerator
     */
    protected $encryptionGenerator;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $keyPrefix = 'BEGIN KEY';

    /**
     * @var string
     */
    protected $dataPrefix = 'BEGIN DATA';

    /**
     * @var string
     */
    protected $passwordSign;

    /**
     * Pair constructor.
     *
     * @param mixed  $masterPassword
     * @param array  $tokenList
     */
    public function __construct(
        $masterPassword = null,
        array $tokenList = []
    ) {
        if (!class_exists(__NAMESPACE__ . '\\EncryptionGenerator')) {
            require_once __DIR__ . '/EncryptionGenerator.php';
        }

        $this->encryptionGenerator = new EncryptionGenerator($masterPassword);
        $this->encryptionGenerator->setTokenList($tokenList);
        $this->passwordSign = sha1($this->encryptionGenerator->getEncryptedMasterPassword());
    }

    /**
     * Set Key Prefix for Encoded Key
     *
     * @param string $string
     * @throws \InvalidArgumentException
     */
    public function setKeyPrefix($string)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Key prefix must be as astring %s given",
                    gettype($string)
                )
            );
        }

        $this->keyPrefix = trim($string);
    }

    /**
     * Set Data Prefix for Encoded Data
     *
     * @param string $string
     * @throws \InvalidArgumentException
     */
    public function setDataPrefix($string)
    {
        if (!is_string($string)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Key prefix must be as astring %s given",
                    gettype($string)
                )
            );
        }

        $this->keyPrefix = trim($string);
    }

    /**
     * Clean The Prefix
     *
     * @param string $content
     * @return string
     */
    public function cleanPrefix($content)
    {
        if (!is_string($content)) {
            return false;
        }

        return trim(preg_replace('/^\-.+\n/', '', trim($content)));
    }

    /**
     * Clean for Crypt
     *
     * @param string $data
     * @return bool|string
     */
    public function cleanForCrypt($data)
    {
        if (!is_string($data)) {
            return false;
        }
        $data = $this->cleanPrefix($data);
        return str_replace(["\n", "\r", " ", "\t"], '', $data);
    }

    /**
     * Add Prefix Pair
     *
     * @param string $content
     * @param string $name
     * @return string
     */
    protected function addPrefix($content, $name)
    {
        $lengthName = strlen($name);
        if ($lengthName < 1) {
            return $content;
        }
        $content = $this->cleanPrefix($content);
        $contentArray = explode("\n", trim($content));
        $length  = strlen(trim(reset($contentArray)));
        if ($lengthName >= $length) {
            return $content;
        }
        $lengthReset  = round(($length - $lengthName) / 2)-1;
        $separator = str_repeat('-', $lengthReset);
        $separator = "{$separator} {$name} {$separator}";
        if (strlen($separator) > $length) {
            $separator = substr($separator, 0, -1);
        }
        $content   = "{$separator}\n{$content}\n";
        return $content;
    }

    /**
     * With Key
     *
     * @param string $key
     * @return Pair
     */
    public function withKey($key)
    {
        $object = clone $this;
        return $object->setKey($key);
    }

    /**
     * Set Public Key
     *
     * @param string $key
     * @return Pair
     * @throws \InvalidArgumentException
     */
    public function setKey($key)
    {
        $verify = $this->verifyKey($key);
        if (! is_array($verify)) {
            throw new \InvalidArgumentException(
                'Invalid key detected!'
            );
        }

        $this->key = $this->addPrefix($key, $this->keyPrefix);
        return $this;
    }

    /**
     * Get Public key, Generate public if not exists
     *
     * @return string
     */
    public function getKey()
    {
        if (!isset($this->key)) {
            return $this->generateKey();
        }

        return $this->key;
    }

    /**
     * Verify Private Key
     *
     * @param string $key
     * @return bool|array
     */
    public function verifyKey($key)
    {
        if (!is_string($key)) {
            return false;
        }
        $generator = new EncryptionGenerator();
        $key = $generator->decrypt($this->cleanForCrypt($key));
        if (is_array($key)
            && !empty($key[static::TIME_NAME])
            && is_int($key[static::TIME_NAME])
            && $key[static::TIME_NAME] < (@time()+(36*2600))
            && !empty($key[static::SIGN_NAME])
            && !empty($key[static::TOKEN_NAME])
        ) {
            $token = $this
                ->encryptionGenerator
                ->decrypt($key[static::TOKEN_NAME]);
            return is_array($token) ? $key : false;
        }

        return false;
    }

    /**
     * Check if is Hex String
     * @param string $hexString
     * @return bool
     */
    public function isHex($hexString)
    {
        return is_string($hexString)
            && !preg_match('/[^a-f0-9]+/i', $hexString);
    }

    /**
     * Decrypt Data
     *
     * @param string $data
     * @return array|bool
     */
    public function decryptData($data)
    {
        if (!is_string($data)) {
            return false;
        }

        $data = $this->cleanForCrypt($data);
        $data = $this->encryptionGenerator->decrypt($data);
        if (is_array($data)
            && !empty($data[static::SIGN_NAME])
            && !empty($data[static::KEY_SIGN_NAME])
            && $this->isHex($data[static::SIGN_NAME])
            && $this->isHex($data[static::KEY_SIGN_NAME])
            && array_key_exists(static::DATA_NAME, $data)
        ) {
            return $data;
        }

        return false;
    }

    /**
     * Verify Data
     *
     * @param string $key
     * @param string $data
     * @return bool|mixed  returning stored data
     */
    public function verify($key, $data)
    {
        $keyData = $this->verifyKey($key);
        if (!is_array($keyData)) {
            return false;
        }
        $data = $this->decryptData($data);
        if (!is_array($data)) {
            return false;
        }
        if ($data[static::SIGN_NAME] != $keyData[static::SIGN_NAME]
            || $data[static::KEY_SIGN_NAME] != sha1($this->cleanForCrypt($key))
        ) {
            return false;
        }

        return $data[static::DATA_NAME];
    }

    /**
     * @param mixed $data
     * @return string
     */
    protected function generateProtectedData($data)
    {
        $sign_pub  = sha1($this->cleanForCrypt($this->getKey()));
        return $this->addPrefix(
            $this
            ->encryptionGenerator
            ->generate(
                [
                    static::KEY_SIGN_NAME => $sign_pub,
                    static::SIGN_NAME => $this->passwordSign,
                    static::DATA_NAME => $data
                ],
                static::SPLIT_LENGTH
            ),
            $this->dataPrefix
        );
    }

    /**
     * Generate Data
     *
     * @param mixed $data
     * @return string[]
     */
    public function generateData($data)
    {
        return [
            static::KEY_NAME  => $this->getKey(),
            static::DATA_NAME => $this->generateProtectedData($data),
        ];
    }

    /**
     * Generate Public Key
     *
     * @return string
     */
    public function generateKey()
    {
        $generator = new EncryptionGenerator();
        // encrypt the token
        $token = $this
            ->encryptionGenerator
            ->generate($this->encryptionGenerator->getTokenList(), false);
        $this->key = $this->addPrefix(
            $generator->generate(
                [
                    static::TOKEN_NAME => $token,
                    static::TIME_NAME  => @time(),
                    static::SIGN_NAME  => $this->passwordSign
                ],
                static::SPLIT_LENGTH
            ),
            $this->keyPrefix
        );

        return $this->key;
    }
}
