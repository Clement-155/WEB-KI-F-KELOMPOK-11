<?php

namespace App\Http\Controllers;

use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;


use App\Models\PrivateFile;

//return type View
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\WhitespacePathNormalizer;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\CustomAuthController;


class PrivateFileController extends Controller
{
    /**
     * index
     *
     * @return View
     */
    public function index(): View
    {
        //get posts
        $privateFiles = PrivateFile::latest()->where('user_id', '=', Auth::user()->id)->paginate(5); //FIX : Can use eliquent to get files instead
        //render view with posts
        return view('privatefiles.dashboard', compact('privateFiles'));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate form for documents
        $validator = Validator::make($request->all(), [
            'private_file' => 'required|mimes:pdf,doc,docx,xls,xlsx,mp4'
        ]);

        // Try again for video
        // if ($validator->fails()) {
        //     $validator = Validator::make($request->all(), [
        //         'private_file' => 'required|mimetypes:video'
        //     ]);
        // }

        // File not a valid type, error messages use user's id
        if ($validator->fails()) {
            return redirect()->route('privatefiles.index')->withErrors([Auth::user()->id => 'Invalid file format']);
        }

        // Get the file from the request
        $file = $request->file('private_file');

        // Generate a random encryption key
        $key = 'amogus';
        $aeskey = 'abcdefghijklmnopqrstuvwxyz123456';
        $deskey = "12345678";

        // Encrypt the file data
        $controller = new CustomAuthController();
        $encryptedData = $controller->rc4Encrypt(file_get_contents($file->getRealPath()), $key);
        $aesData = $controller->aes256cbcEncrypt(file_get_contents($file->getRealPath()), $aeskey);
        $desData = $controller->desEncrypt(file_get_contents($file->getRealPath()), $deskey);

        // Determine the file extension
        $fileExtension = $file->getClientOriginalExtension();

        // Generate a unique file name for the encrypted file
        $encryptedFileName = 'encrypted_' . time() . '.' . $fileExtension;
        $aesFileName = 'aes_' . time() . '.' . $fileExtension;
        $desFileName = 'des_' . time() . '.' . $fileExtension;

        // Store the encrypted file
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $encryptedFileName, $encryptedData);
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $aesFileName, $aesData);
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $desFileName, $desData);

        // Create a record in the database
        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $encryptedFileName,
        ]);

        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $aesFileName,
        ]);

        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $desFileName,
        ]);

        // Redirect to index
        return redirect()->route('privatefiles.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function download($path)
    {
        try{
            $result = response()->download(storage_path("app/private/privatefiles/" . Auth::user()->username . '/' . $path ));
        }  catch (FileNotFoundException $e) {
            abort(404); //or whatever you want do here
        }
        return $result;
    }
}
