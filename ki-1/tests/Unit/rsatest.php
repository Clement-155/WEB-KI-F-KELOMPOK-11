<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\CustomAuthController; 
class rsatest extends TestCase
{
    public function testEncryptAndDecrypt()
    {
        $keys = rsakeygen();

        $originalData = 'Hello, RSA!';
        $publickey = $keys['publicKey'];
        $privatekey = $keys['privatekey'];
        $encryptedData = rsaencrypt($originalData,$publickey );
        $decryptedData = rsadecrypt($encryptedData, $privatekey);

        $this->assertEquals($originalData, $decryptedData);
    }
}
