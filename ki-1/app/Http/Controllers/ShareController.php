<?php

namespace App\Http\Controllers;

use ErrorException;
use Illuminate\Http\Request;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Random;


use App\Models\PrivateFile;
use App\Models\User;
//return type View
use phpseclib3\Crypt\RC4;
use phpseclib3\Crypt\RSA;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\WhitespacePathNormalizer;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CustomAuthController;
use Exception;
class ShareController extends Controller
{
    public function index(): View
    {
        //get posts
        $files = PrivateFile::all()->where('user_id', '=', Auth::user()->id); //FIX : Can use eliquent to get files instead
        $users =  User::all()->where('id', '!=', Auth::user()->id);
        //render view with posts
        return view('sharefiles.dashboard', compact('files', 'users'));
    }
    public function store(Request $request): RedirectResponse
    {
        // Validate form for documents

        // Get the file from the request
        $file = $request->sharefile;
        $user = $request->user;
        $public = User::all()->where('id','=', $user)->first()->public;
        
        $public = PublicKeyLoader::load($public, $password = false);

        $filekey = PrivateFile::all()->where('id','=', $file)->first()->key;
        $controller = new CustomAuthController;

        $encrypted = $controller->rsaencrypt($filekey,$public);
        $encrypted = base64_encode($encrypted);
        session(['encrypted' => $encrypted, 'file_id' => $file]);
        // Redirect to index
        return redirect()->route('share.index')->with(['success' => 'key untuk enkripsi dibuat!']);
    }

    // public function downloadKey($key){
    //     return response()->streamDownload(function () use ($key) {
    //         echo $key;
    //     }, "key.txt");
    // }

    public function download_index(): View
    {
        return view('sharefiles.SharedFileView');
    }
    
    /**
     * PROBLEM : Catch error for :
     * 1a. File do exists, or/and
     * 1b. Right user
     * 2. Wrong key
     * ERROR : phpseclib3 \ Exception \ BadDecryptionException
     * 
     * PROBLEM : Catch error for :
     * 0. Wrong user
     * 1a. File do exists, or/and
     * 1b. Right user
     * 2. Right key
     * ERROR : RunTimeException, Decryption Error
     */
    public function download_shared(Request $request)
    {
        try{
        $key = $request->file('file_key');
        $key = file_get_contents($key->getRealPath());
        $key = base64_decode($key);
        $path = PrivateFile::all()->where('id','=', $request->file_id)->first()->private_file;
        $owner_name = $request->owner_name;

        $fileData = file_get_contents(storage_path("app/private/privatefiles/" . $owner_name . '/' . $path ));  
        $private = User::all()->where('id','=', Auth::user()->id)->first()->private;
        $private = PublicKeyLoader::load($private, $password = false);
        $key = $private->decrypt($key);

        //dd($fileData);

        $controller = new CustomAuthController();
        $encryptType = substr($path, 0, 4);
        switch ($encryptType) { 
            case "aes_":
                $fileData = $controller->aes256cbcDecrypt($fileData, $key);
                break;
            case "des_":
                $fileData = $controller->desDecrypt($fileData, $key);
                break;
            case "rc4_":
                $fileData = $controller->rc4Decrypt($fileData, $key);
                break;
            }

        return response()->streamDownload(function () use ($fileData) {
            echo $fileData;
        }, "decrypted_" . $path);
    }  catch (ErrorException $e) {
        abort(404); //or whatever you want do here
    }
    }
}
