<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;


use App\Models\PrivateFile;
use App\Models\User;
//return type View
use phpseclib3\Crypt\RC4;
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
        $users =  User::all();
        //render view with posts
        return view('sharefiles.dashboard', compact('files', 'users'));
    }
    public function store(Request $request): RedirectResponse
    {
        // Validate form for documents

        // Get the file from the request
        $file = $request->sharefile;
        $user = $request->user;
        $public = User::where('username','=', $user)->first()->public;
        $filekey = PrivateFile::where('private_file','=', $file)->first()->key;
        $controller = new CustomAuthController;
        $encrypted = $controller->rsaencrypt($filekey,$public);
        session(['encrypted' => $encrypted]);
        // Redirect to index
        return redirect()->route('share.index')->with(['success' => 'key untuk enkripsi dibuat!']);
    }

}
