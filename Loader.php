<?php
namespace Pentagonal\PairCreator {
    if (!class_exists(__NAMESPACE__ .'\\Lib\\EncryptionGenerator')) {
        require_once __DIR__ . '/src/Lib/EncryptionGenerator.php';
    }
    if (!class_exists(__NAMESPACE__ .'\\Lib\\Pair')) {
        require_once __DIR__ . '/src/Lib/Pair.php';
    }
}
