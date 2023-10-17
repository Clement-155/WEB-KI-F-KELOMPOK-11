<?php
require 'vendor/autoload.php';

use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\RC4;

$plaintext = "This is a secret message.";
$encryptionKey = "secret88";

$rc4 = new RC4();
$rc4->setKey($encryptionKey);

$encryptedData = $rc4->encrypt($plaintext);
$decryptedData = $rc4->decrypt($encryptedData);

echo "Plaintext: $plaintext\n";
echo "Encrypted Data (Base64): " . base64_encode($encryptedData) . "\n";
echo "Decrypted Text: $decryptedData\n";

$plaintext = "This is a secret message.";
$encryptionKey = "secret88";
$iv = "12345678";

$des = new DES(DES::MODE_CBC);
$des->setKey($encryptionKey);
$des->setIV($iv);

$blockSize = $des->getBlockLength() / 8;
$encryptedData = '';
for ($i = 0; $i < strlen($plaintext); $i += $blockSize) {
    $block = substr($plaintext, $i, $blockSize);
    $encryptedData .= $des->encryptBlock($block);
}

// reset IV
$des->setIV($iv);
$decryptedData = '';
for ($i = 0; $i < strlen($encryptedData); $i += $blockSize) {
    $block = substr($encryptedData, $i, $blockSize);
    $decryptedData .= $des->decryptBlock($block);
}

echo "Plaintext: $plaintext\n";
echo "Encrypted Data (Base64): " . base64_encode($encryptedData) . "\n";
echo "Decrypted Text: $decryptedData\n";
