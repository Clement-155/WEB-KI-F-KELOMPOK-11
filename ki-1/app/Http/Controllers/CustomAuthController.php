<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\DSA;
use phpseclib3\Crypt\Random;
use phpseclib3\Crypt\RSA;
use Illuminate\Http\Request;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use phpseclib3\Crypt\Hash;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }


    public function customLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|min:1',
            'password' => 'required|min:6',
        ]);

        //$credentials = $request->only('username', 'password');

        //If auth success
        // if (Auth::attempt($credentials)) {

        //     return redirect()->intended('welcome')
        //         ->withSuccess('Signed in');
        // }
        $user = User::where([
            'username' => $request->username,
            'password' => $request->password
        ])->first();

        if ($user) {
            Auth::login($user);
            return redirect("dashboard")->with('success', 'Logging in..');
        }
        //If auth failed
        return redirect("login")->with('failed', 'Login details are not valid');
    }



    public function registration()
    {
        return view('auth.registration');
    }


    public function customRegistration(Request $request)
    {
        $request->validate([
            'id-photo' => 'required|image|mimes:jpeg,jpg,png|max:10000',
            'username' => 'required|min:4|unique:App\Models\User,username',
            'password' => 'required|min:6',
            'fullname' => 'required',
            'gender' => 'required',
            'citizenship' => 'required',
            'religion' => 'required',
            'marital-status' => 'required',
        ]);

        $data = $request->all();

        //Generate RSA keys
        $rsaKeys = $this->rsakeygen();
        $data['private'] = $rsaKeys['privatekey'];
        $data['public'] = $rsaKeys['publickey'];

        $encryptionKey = 'amogus'; // If hard to decrypt later change to static key

        $data['fullname'] = $this->rc4Encrypt($data['fullname'], $encryptionKey);
        $data['gender'] = $this->rc4Encrypt($data['gender'], $encryptionKey);
        $data['citizenship'] = $this->rc4Encrypt($data['citizenship'], $encryptionKey);
        $data['religion'] = $this->rc4Encrypt($data['religion'], $encryptionKey);
        $data['marital-status'] = $this->rc4Encrypt($data['marital-status'], $encryptionKey);

        //upload image
        $image = $request->file('id-photo');
        $fileName = $image->hashName();
        $image->storeAs('public/id-card', $fileName);
        $data['id-photo'] = $this->encryptImage(storage_path('app/public/id-card/' . $fileName), $encryptionKey);

        $check = $this->create($data);

        return redirect("login")->withSuccess('You have signed up');
    }

    //Creates new row in database
    public function create(array $data)
    {
        return User::create([
            'id-photo' => $data['id-photo'],
            'username' => $data['username'],
            'password' => $data['password'],
            'fullname' => $data['fullname'],
            'gender' => $data['gender'],
            'citizenship' => $data['citizenship'],
            'religion' => $data['religion'],
            'marital-status' => $data['marital-status'],
            'private' => $data['private'],
            'public' => $data['public'],
        ]);
    }


    public function dashboard()
    {
        if (Auth::check()) {

            return redirect('privatefiles')->with('success', 'Login Success');
        }

        return redirect("login")->with('failed', 'You are not allowed to access');
    }


    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    private function encryptImage($imagePath, $encryptionKey)
    {
        $imageData = File::get($imagePath);
        $encryptedImagePath = 'public/id-card/encrypted_' . basename($imagePath);

        // Encrypt the image data using RC4
        $encryptedData = $this->rc4Encrypt($imageData, $encryptionKey);

        // Write the encrypted image data to the file
        File::put(storage_path('app/' . $encryptedImagePath), $encryptedData);

        return $encryptedImagePath;
    }

    function rc4Encrypt($data, $key)
    {
        $start = hrtime(true);
        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        $n = strlen($key);
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % $n])) % 256;
            $temp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $temp;
        }
        $i = 0;
        $j = 0;
        $encrypted = '';
        $dataLength = strlen($data);
        for ($k = 0; $k < $dataLength; $k++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $temp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $temp;
            $encrypted .= $data[$k] ^ chr($s[($s[$i] + $s[$j]) % 256]); 
        }
        $encoded = base64_encode($encrypted);
        $functionName = __FUNCTION__;
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("$functionName : Code block was running for $eta milliseconds");
        return $encoded;
    }

    function rc4Decrypt($data, $key)
    {
        $start = hrtime(true);
        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        $n = strlen($key);
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % $n])) % 256;
            $temp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $temp;
        }
        $i = 0;
        $j = 0;
        $decrypted = '';
        $data = base64_decode($data);  // Decode the data from base64 to binary
        $dataLength = strlen($data);
        for ($k = 0; $k < $dataLength; $k++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $temp = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $temp;
            $decrypted .= $data[$k] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }
        $functionName = __FUNCTION__;
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("$functionName : Code block was running for $eta milliseconds");
        return $decrypted;
    }

    function aes256cbcEncrypt($data, $key)
    {
        $start = hrtime(true);
        $cipher = new AES('cbc');
        $iv = Random::string(16);
        $cipher->setIV($iv);
        $cipher->setKey($key);

        $ciphertext = $iv . $cipher->encrypt($data);
        $functionName = __FUNCTION__;
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("$functionName : Code block was running for $eta milliseconds");
        return $ciphertext;
    }

    function aes256cbcDecrypt($data, $key)
    {
        $start = hrtime(true);
        $cipher = new AES('cbc');
        $iv = substr($data, 0, 16);
        $cipher->setIV($iv);
        $cipher->setKey($key);

        $plaintext = $cipher->decrypt(substr($data, 16, strlen($data) - 16));
        $functionName = __FUNCTION__;
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("$functionName : Code block was running for $eta milliseconds");
        return $plaintext;
    }

    function desEncrypt($data, $key)
    {
        $start = hrtime(true);
        $cipher = new DES('cbc');
        $iv = Random::string(8);
        $cipher->setIV($iv);
        $cipher->setKey($key);

        $ciphertext = $iv . $cipher->encrypt($data);
        $functionName = __FUNCTION__;
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("$functionName : Code block was running for $eta milliseconds");
        return $ciphertext;
    }

    function desDecrypt($data, $key)
    {
        $start = hrtime(true);
        $cipher = new DES('cbc');
        $iv = substr($data, 0, 8);
        $cipher->setIV($iv);
        $cipher->setKey($key);

        $plaintext = $cipher->decrypt(substr($data, 8, strlen($data) - 8));
        $functionName = __FUNCTION__;
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("$functionName : Code block was running for $eta milliseconds");
        return $plaintext;
    }
    function rsakeygen()
    {
        $rsa = RSA::createKey();
        $public = $rsa->getPublicKey();
        return [
            'publickey' => $public,
            'privatekey' => $rsa,
        ];
    }
    function rsadecrypt($data, $key)
    {

        return $key->decrypt($data);
    }
    function rsaencrypt($data, $key)
    {
        
        $encryptedData = $key->encrypt($data);

        return $encryptedData;
    }

}
