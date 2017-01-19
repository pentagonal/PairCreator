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

    $pair = new Lib\Pair($masterPassword);
    $data = [
        'key' => 'Content Of Key',
        'name' => 'Content Of Name'
    ];

    $dataGenerated = $pair->generateData($data);
    if (php_sapi_name() == 'cli') {
        echo "\n{$dataGenerated['key']}\n";
        echo "\n{$dataGenerated['data']}\n";
    } else {
        echo "<h2>KEY</h2>\n";
        echo "<pre>{$dataGenerated['key']}</pre>\n";
        echo "<h2>DATA</h2>\n";
        echo "<pre>{$dataGenerated['data']}</pre>\n";
    }

    $decryptedData = $pair->verify($dataGenerated['key'], $dataGenerated['data']);
    if (php_sapi_name() == 'cli') {
        echo "\n----------------------- Decrypted Data ---------------------\n";
        print_r($decryptedData);
    } else {
        $decryptedData =  print_r($decryptedData, true);
        echo "<h2>Decrypted Data</h2>\n";
        echo "<pre>$decryptedData</pre>\n";
    }
}