<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

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

        $encryptionKey = 'amogus'; // If hard to decrypt later change to static key

        // Encrypt sensitive data with the generated key
        // $data['username'] = $this->rc4Encrypt($data['username'], $encryptionKey);
        // $data['password'] = $this->rc4Encrypt($data['password'], $encryptionKey);
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
            $encoded = base64_encode($encrypted);
        }
        return $encoded;
    }
}
