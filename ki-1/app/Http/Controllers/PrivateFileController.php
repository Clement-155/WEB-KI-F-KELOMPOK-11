<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Random;


use App\Models\PrivateFile;

//return type View
use phpseclib3\Crypt\RC4;
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
use Exception;

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

        // Generate a random encryption rckey
        $rckey = 'amogus';
        $aeskey = 'abcdefghijklmnopqrstuvwxyz123456';
        $deskey = "12345678";

        $rc4 = new RC4();
        // Encrypt the file data
        $controller = new CustomAuthController();
        $rc4->setKey($rckey);

        //RC4
        $start = hrtime(true);
        $rc4Data = $rc4->encrypt(file_get_contents($file->getRealPath()));
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("rc4Eecrypt : Code block was running for $eta milliseconds");

        $aesData = $controller->aes256cbcEncrypt(file_get_contents($file->getRealPath()), $aeskey);
        $desData = $controller->desEncrypt(file_get_contents($file->getRealPath()), $deskey);

        // Determine the file extension
        $fileExtension = $file->getClientOriginalExtension();

        // Generate a unique file name for the encrypted file
        $originalFileName = $file->getClientOriginalName();
        $rc4FileName = 'rc4_' . time() . '.' . $fileExtension;
        $aesFileName = 'aes_' . time() . '.' . $fileExtension;
        $desFileName = 'des_' . time() . '.' . $fileExtension;

        // Store the encrypted file
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $rc4FileName, $rc4Data);
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $aesFileName, $aesData);
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $desFileName, $desData);

        // Create a record in the database
        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $rc4FileName,
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

    public function download($path) //Currently for rc4
    {
        try {
            // Get the encrypted file
            $encryptedFile = Storage::get('private/privatefiles/' . Auth::user()->username . '/' . $path);

            // Create a new Algorithm object
            $rc4 = new RC4();
            $controller = new CustomAuthController();

            // Set the encryption rckey
            $rc4->setKey('amogus');
            $aeskey = 'abcdefghijklmnopqrstuvwxyz123456';
            $deskey = "12345678";

            // Decrypt the file data
            if (strpos($path, 'rc4_') === 0) {
                $start = hrtime(true);

                // Decrypt the file data using RC4
                $decryptedData = $rc4->decrypt($encryptedFile);

                $end = hrtime(true);
                $eta = $end - $start;
                $eta /= 1e+6;
                Log::channel('encrypt_log')->info("rc4Decrypt : Code block was running for $eta milliseconds");
            }
 
             elseif (strpos($path, 'aes_') === 0) {
                // Decrypt the file data using AES
                $decryptedData = $controller->aes256cbcDecrypt($encryptedFile, $aeskey);
            } elseif (strpos($path, 'des_') === 0) {
                // Decrypt the file data using DES
                $decryptedData = $controller->desDecrypt($encryptedFile, $deskey);
            }

            // Create a response
            $response = response($decryptedData);

            // Set the appropriate headers
            $response->header('Content-Type', 'application/octet-stream');
            $response->header('Content-Disposition', 'attachment; filename="' . $path . '"');

            return $response;
        } catch (FileNotFoundException $e) {
            abort(404); //or whatever you want do here
        }
    }

    // public function download($path)
    // {
    //     try{
    //         $result = response()->download(storage_path("app/private/privatefiles/" . Auth::user()->username . '/' . $path ));
    //     }  catch (FileNotFoundException $e) {
    //         abort(404); //or whatever you want do here
    //     }
    //     return $result;
    // }
}
