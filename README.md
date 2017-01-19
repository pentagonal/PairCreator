## PAIR CREATOR

[![Build Status](https://travis-ci.org/pentagonal/PairCreator.svg?branch=master)](https://travis-ci.org/pentagonal/PairCreator)

Pair the given encrypted key with encrypted data.

```bash
composer require pentagonal/pair-creator
```

### Usage

```php
<?php
use \Pentagonal\PairCreator\Lib\Pair;

/**
 * @param mixed $masterPassword 
 */
$masterPassword = 'Strong Password';

/**
 * Instantiate Pair 
 */
$pair = new Pair($masterPassword);


/**
 * @param mixed $dataToBeEncrypted any data type to encrypted 
 */
$dataToBeEncrypted = [
    'My Data' => 'this data'  
];

/**
 * Encrypt The Data
 *
 * @param array $encryptedDataAndKey
 *              [
 *                  Pair::KEY_NAME => (string key)
 *                  Pair::DATA_NAME => (string data)
 *              ]
 */
$encryptedDataAndKey = $pair->generateData($dataToBeEncrypted);

$encryptedKey  = $encryptedDataAndKey[Pair::KEY_NAME];
$encryptedData = $encryptedDataAndKey[Pair::DATA_NAME];

/**
 * Decrypt The Data
 * @param mixed $decrypted
 *          followed the data saved 
 */
$decrypted = $pair->verify($encryptedKey, $encryptedData);

```


### Example Encrypted Key


```text
------------------------- BEGIN KEY ------------------------
eyY2E4NDFjNTc5MTk5N2VkZDdkZTUzOGQ5MGVhY2MwNmJlYzc1YjFmMS1vQ2
I2JtaWNybyI6MTQ4NDgxNDY1OS42MTksIm1ldGhvZCI6IkFFUy0yNTYtQ0JD
IiwiaXYiOiI0RmRVRUdIaDRIV25jV0xzcnBVOW5RIiwidmFsdWUiOiJnd0Ur
aStLdytLaEJVM29ldGdFbjVlKzBRVkNueEZ5ODl6RFBPazdQU3J2RW10Qmtm
dnZaMVhvcHZTaDZCTlVwcEl3RlFoNHVDeWcwWjhrVmR2VVZLaGpldEVXVCtl
bWtcL0Z0NTV2c0Y5UnkrOWd0V3VTMTZ0cWlvTE5HalVLWnhjaldjTFBzK0Fn
TURxWDIyNW1qVXVJRWc0SDZ4SWFrMVNQa3p5YkZadzdEUCsrTTVReEtCdUdL
dWc0RDJjREtmSzNGQTg3cEJIaGpjQWNRUG05ZXNTNUxac3J2QjgrM1JFNGxX
dEpNNTdTdWloZHpLWWFLNTBPZjFjRitlRmFWSHhkTEZvZExDVG5pOVlSRFdN
YmozZEUzS1Z6T1l2QzVyQVJ2ek9iK0FJVWZLaFViSFIzKyt6XC91Z3l4bzB0
NndVOWdiNlNPaFVxNm1qY1Njc3VNeERCcHpqcWV4UGgxbkR2SEpTZjQ2czl2
cDR1VGNXYkJMcHd2NmJpNHRSVTlqMnpES2pONEhZbURrNWxBQ0pRQndFUm11
elAwYzlXM0h5Wng0c1lKRTdzWmZ4RjhrbWJldlwvTXZTWkp3d2JDZnpCMDRR
dEdFd3ZaWWxta01ZcXkwejNkMkR6SGp2ZmprTGpHaU4yQmpPVjNTNmtcL1hE
bDQ0WlB5anFUM0FNSCtyK043NXJEbmZhQzJ4N3NtY1dxaU5oZWpORVM1SXR5
VldpYW1xWFljR0Y5K3QyVlBXQzhEQ1Q3dms3Y2JOZkRwYmVnVFV3SHZoNTR4
dWxuZ1FDb1dKSVJcL21MR3hnRDhNRzJCdFg2NDUwdTUxMnpcL1U5UDJ3dkJ1
ZUxiY3A2Y1Z2cjhlRCtzRTRGN1BselhoR0pXZHVicUcwK3hhRE15RnhmbjB3
ZXlYOHRMa0ZZN1JQWTRPUGlmTDdPcTRzT1wvOHl4eEMifVsiYWU0ZDcwNjk5
MWU2YzczZWRiYjEwZGZhZDI3NGRkZjNlMDc3ZDMyMyJd

```

### Example Encrypted Data


```text
------------------------ BEGIN DATA ------------------------
eyJtaWNybyI6MTQ4NDgxNDY1OS42MTkyLCJtZXRob2QZDM5YTc3ODc3MjIxN
jZkMWRjMThkMTg3MDJhOTU0YmQyNDVkZjVhZi1vQ2I43iOiJBRVMtMjU2LUN
CQyIsIml2IjoiN3dSeGxuUnlhcmY0SG4xMUgta1cwZyIsInZhbHVlIjoiSUF
yRzdSa3R3ZnhoUWtOcTdDbUhFYzQzWko5dXRCeWltaXZobXY0YlV0c0p0Wmd
YSXVDXC81REpHcSsxMWtcL0hYSU1MVTBIMytJTVNTRldqUXRFN1FoZHBKM0Z
weEwzS2Fyc3gyM2x2dUhnelBtd3ZmVm90emF6dU4xOE1CK0xsQWM1NDlkQmk
4VytVbzZ6OGgyemV3U2NiZVpzbHk4NjdKKzI2UGNLczlsdlJGak40XC96TDF
LNUxYZ0k1VEl0cmw5U2VqVDlxRktNcWRmSHpqVXJhM3pPSGtORlF3WHFqZlF
RMVFtYVMxK3lMbVwvQTNhbVNIZkxNY0ZDMlI3OHRcL2FVNE1jVjlvaWFPSFh
xRVBHTm9lemhRbk9pcVRvYWxVeXBQZ2RNRmlYbWh5TWNGcUJPSjQ3ZWVhS2F
rZFRBcFZqbWl1c0cwUnZZOEdCV1NRTzBBZUhEZjlHcGdlQUFcL2dyQ1dFS29
IV3VtOVJJPSJ9WyIzOTNiMDYwNjJhZDI1NjQ1ZmZjMDU4ZDhhYzlkMjE3NmQ
0NGFiYjBhIl0

```

### License

MIT License [https://opensource.org/licenses/MIT](https://opensource.org/licenses/MIT)
