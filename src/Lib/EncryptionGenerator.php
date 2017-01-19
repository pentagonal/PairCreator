<?php
namespace Pentagonal\PairCreator\Lib;

use Pentagonal\SimpleEncryption\Encryption;

/**
 * Class EncryptionGenerator
 * License data always random generated.
 *
 * @package Pentagonal\PairCreator\Lib
 * @version     1.0.0
 * @author      pentagonal <org@pentagonal.org>
 */
class EncryptionGenerator
{
    const TOKEN_KEY_NAME = 'token';
    const DATA_KEY_NAME  = 'data';

    /**
     * Multiple token List
     *
     * @var array
     */
    protected $token_lists = [];

    /**
     * @var bool
     */
    protected $useCache = true;

    /**
     * @var array
     */
    protected $stored_data = [];

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $master_password;

    /**
     * EncryptionGenerator constructor.
     * set master password to decode data
     *
     * @param mixed $masterPassword
     */
    public function __construct($masterPassword = null)
    {
        $this->identifier = microtime();
        $this->setMasterPassword($masterPassword);
    }

    /**
     * Set Result Data never being array cached
     */
    public function setNoCacheRecord()
    {
        $this->useCache = false;
    }

    /**
     * Set Data to be cached to object property storage
     * this is recommended if use for small data decrypt
     * and not recommended if encrypted data store bunch of mega
     */
    public function setCacheRecord()
    {
        $this->useCache = true;
    }

    /**
     * Check if storage data
     *
     * @return bool
     */
    public function isCachedRecord()
    {
        return $this->useCache;
    }

    /**
     * Set List Of Password
     *
     * @param array $passwordList
     */
    public function setTokenList(array $passwordList)
    {
        $this->token_lists = [];
        foreach ($passwordList as $key => $value) {
            if (!is_numeric($value) && ! is_string($value)
                && !is_null($value) && !is_bool($value)
            ) {
                $isNotEmpty = (bool) $value;
                $value = @serialize($value);
                if ($isNotEmpty && $value == false) {
                    $value = gettype($value) . md5($key);
                }
            }

            $this->token_lists[$key] = $value;
        }
    }

    /**
     * Get list of Password
     *
     * @return array
     */
    public function getTokenList()
    {
        return $this->token_lists;
    }

    /**
     * @param mixed $key      key name for password
     * @param mixed $password you could use array, object, string boolean or any type
     *                        that can be to be serialized.
     */
    public function addToken($key, $password)
    {
        if (!is_numeric($password) && ! is_string($password)
            && !is_null($password) && !is_bool($password)
        ) {
            $isNotEmpty = (bool) $password;
            $password = @serialize($password);
            if ($isNotEmpty && $password == false) {
                $password = gettype($password) . md5($key);
            }
        }

        $this->token_lists[$key] = $password;
    }

    /**
     * Remove stored Password
     *
     * @param mixed $key
     */
    public function removeToken($key)
    {
        if (isset($this->token_lists[$key])) {
            unset($this->token_lists[$key]);
        }
    }

    /**
     * Set Master Password
     * cache into internal static
     * @see EncryptionGenerator::plainStoredPassword()
     *
     * @param mixed $password
     */
    public function setMasterPassword($password)
    {
        $this->plainStoredPassword($password);
        $this->master_password = $this->generate($password, false);
    }

    /**
     * Get Encrypted Master Password
     *
     * @return string
     */
    public function getEncryptedMasterPassword()
    {
        return $this->master_password;
    }

    /**
     * Internal Function store the password
     *      call without the arguments will be shown the current password stored
     *      Cache into internal static storage that prevent public known the stored
     *      password.
     *
     * @param  mixed $password
     * @return bool|null
     */
    protected function plainStoredPassword($password = null)
    {
        static $passwordStored = [];
        if (func_num_args() === 0) {
            if (isset($passwordStored[$this->identifier])) {
                return $passwordStored[$this->identifier];
            }

            return null;
        }

        $passwordStored[$this->identifier] = $password;
        return true;
    }


    /**
     * Create License Encrypted data, password is stored on
     * @see EncryptionGenerator::plainStoredPassword()
     *
     * @param mixed $data
     * @param bool|int  $split default split into 40 characters long
     *                         but if more than 10 line it will be try to
     *                         50, 60, 70 and at least 80
     *                         and must be more than 10 characters
     * @return string
     */
    public function generate($data, $split = true)
    {
        $stored_data = [
            static::TOKEN_KEY_NAME => $this->token_lists,
            static::DATA_KEY_NAME  => $data
        ];

        $data = Encryption::encrypt($stored_data, $this->plainStoredPassword());
        if ($this->isCachedRecord()) {
            $key = sha1($data);
            $this->stored_data[$key] = $stored_data;
        }
        if (is_numeric($split) && $split > 10) {
            $split = abs(round($split));
            if (strlen($data) > $split) {
                $data = implode("\n", str_split($data, $split));
            }
        } elseif ($split) {
            $length = strlen($data);
            $split  = 60;
            foreach ([40, 50, 60, 70, 80] as $v) {
                if (($length / $v) < 10) {
                    $split  = $v;
                    break;
                }
            }
            if (strlen($data) > $split) {
                $data = implode("\n", str_split($data, $split));
            } else {
                $data = implode("\n", str_split($data, $length/2));
            }
        }

        return $data;
    }

    /**
     * Decrypt data only without verify the token list data
     *
     * @param string $data
     * @return bool|mixed
     */
    public function decryptOnly($data)
    {
        if (!is_string($data)) {
            return false;
        }

        // trimming all whitespace
        $data = str_replace(["\r", "\n", "\t", " "], '', $data);
        $key = sha1($data);
        if (isset($this->stored_data[$key])) {
            if ($this->stored_data[$key] === false || $this->isCachedRecord()) {
                return $this->stored_data[$key];
            }
        }

        $this->stored_data[$key] = Encryption::decrypt($data, $this->plainStoredPassword());
        if (!is_array($this->stored_data[$key])
            || !isset($this->stored_data[$key][static::TOKEN_KEY_NAME])
            || !is_array($this->stored_data[$key][static::TOKEN_KEY_NAME])
            || !array_key_exists(static::DATA_KEY_NAME, $this->stored_data[$key])
        ) {
            $this->stored_data[$key] = false;
            return false;
        }

        $decrypted = $this->stored_data[$key];
        unset($this->stored_data[$key]);
        return $decrypted;
    }

    /**
     * Verify decrypted stored data and token list on stored data
     * must be match within one or more token list data set
     *
     * @param string $data
     * @return bool|mixed
     */
    public function decrypt($data)
    {
        $data = $this->decryptOnly($data);
        if (!is_array($data)) {
            return false;
        }

        if (empty($this->token_lists) || empty($data[self::TOKEN_KEY_NAME])) {
            return $data[self::DATA_KEY_NAME];
        }
        $token_list = @array_intersect($this->token_lists, $data[self::TOKEN_KEY_NAME]);
        if (empty($token_list)) {
            return false;
        }

        return $data[self::DATA_KEY_NAME];
    }

    /**
     * Verify decrypted stored data and must be match with all token
     *
     * @param string $data
     * @return bool|mixed
     */
    public function decryptMatch($data)
    {
        $data = $this->decryptOnly($data);
        if (!is_array($data)) {
            return false;
        }

        if ($this->token_lists !== $data[self::TOKEN_KEY_NAME]) {
            return false;
        }

        return $data[self::DATA_KEY_NAME];
    }

    /**
     * @return string with class name and current object hash
     */
    public function __toString()
    {
        return __CLASS__ .':'. \spl_object_hash($this);
    }
}
