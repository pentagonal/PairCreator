<?php
namespace Pentagonal\PairCreator {

    if (!file_exists(dirname(__DIR__) .'/vendor/autoload.php')) {
        return;
    }

    require_once dirname(__DIR__) .'/vendor/autoload.php';

    /**
     * Master Password
     */
    $masterPassword = <<<EOF
    Whatever you want to set master password! Even though it will be:
    string, array, boolean,
    null integer etc.
EOF;

    $encryptionGenerator = new Lib\EncryptionGenerator($masterPassword);
    $data = [
        'user' => 'username',
        'expire' => @strtotime('+1 Year'),
        'info' => [
            'vip' => true,
            'product' => 'theme',
            'quantity' => 20,
            'package' => 'paket hemat'
        ]
    ];

    /* -------------------------------------------
                        GENERATE
     ------------------------------------------- */

    /**
     * @param mixed    $data
     * @param bool|int $split
     */
    $split = true;
    $license = $encryptionGenerator->generate($data, $split);
    if (php_sapi_name() == 'cli') {
        echo "\n------------------------------ License ------------------------------\n";
        echo "\n$license\n";
    } else {
        echo "<h2>License</h2>\n";
        echo "<pre>$license</pre>\n";
    }


    /* -------------------------------------------
                        DECRYPTED
     ------------------------------------------- */

    $encryptionGenerator = new Lib\EncryptionGenerator($masterPassword);
    $decryptedData = $encryptionGenerator->decrypt($license);
    // make printable
    $decryptedData = print_r($decryptedData, true);
    if (php_sapi_name() == 'cli') {
        echo "\n-------------------------- Decrypted Data -------------------------\n";
        echo "\n$decryptedData\n";
    } else {
        echo "<h2>Decrypted Data</h2>\n";
        echo "<pre>$decryptedData</pre>\n";
    }
}