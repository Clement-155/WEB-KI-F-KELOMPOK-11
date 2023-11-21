<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\CustomAuthController;
class RSATest extends TestCase
{
    public function testEncryptAndDecrypt()
    {   $controller = new CustomAuthController();
        $keys = $controller->rsakeygen();

        $originalData = 'Hello, RSA!';
        $publickey = $keys['publickey'];
        $privatekey = $keys['privatekey'];
        $encryptedData = $controller->rsaencrypt($originalData,$publickey );
        $decryptedData = $controller->rsadecrypt($encryptedData, $privatekey);

        $this->assertEquals($originalData, $decryptedData);
    }
}
