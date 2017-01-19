<?php
namespace Pentagonal\PairCreator\Tests\PhpUnit;

use Pentagonal\PairCreator\Lib\Pair;

/**
 * Class TestPair
 * @package Pentagonal\PairCreator\Tests\PhpUnit
 */
class TestPair extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pair
     */
    protected $pair;
    /**
     * @var string
     */
    protected $password = 'Password';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $encryptedData;

    /**
     * @var array
     */
    protected $data = [
        'data' => 'Data',
        'data_2' => 'Data 2'
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->pair = new Pair($this->password);
    }

    /**
     * Test Assert Empty
     */
    public function testAssertEmpty()
    {
        $this->assertEmpty($this->key);
        $this->assertEmpty($this->encryptedData);
    }

    /**
     * Test Compare
     */
    public function testAssertCreateCompare()
    {
        // assert $pair property instance of Pair::class
        $this->assertInstanceOf('\\Pentagonal\\PairCreator\\Lib\\Pair', $this->pair);

        $this->key = $this->pair->generateKey();
        $this->encryptedData = $this->pair->generateData($this->data);

        $this->assertEquals($this->key, $this->pair->getKey());
        $this->assertNotEquals($this->key, $this->encryptedData);
        $this->assertNotEmpty($this->key);
        $this->assertNotEmpty($this->encryptedData);
    }

    /**
     * Test Key Stored
     */
    public function testCheckKey()
    {
        $this->key = $this->pair->generateKey();
        $this->encryptedData = $this->pair->generateData($this->data);

        $keyVerify = $this->pair->verifyKey($this->key);

        $this->assertNotEmpty($keyVerify);

        $this->assertArrayHasKey(Pair::SIGN_NAME, $keyVerify);
        $this->assertArrayHasKey(Pair::TOKEN_NAME, $keyVerify);
        $this->assertArrayHasKey(Pair::TIME_NAME, $keyVerify);

        $this->assertCount(3, $keyVerify);
        $this->assertCount(2, $this->encryptedData);
    }

    /**
     * Test Check Encrypted & Decrypted Data
     */
    public function testCheckData()
    {
        $this->key = $this->pair->generateKey();
        $this->encryptedData = $this->pair->generateData($this->data);

        $this->assertEquals($this->key, $this->encryptedData[Pair::KEY_NAME]);
        $this->assertNotEquals($this->encryptedData[Pair::DATA_NAME], $this->data);
        // verify
        $this->assertEquals(
            $this->pair->verify($this->key, $this->encryptedData[Pair::DATA_NAME]),
            $this->data
        );
    }

}
