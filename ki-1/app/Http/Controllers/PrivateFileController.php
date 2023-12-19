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
use ErrorException;
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




        // File not a valid type, error messages use user's id
        if ($validator->fails()) {
            return redirect()->route('privatefiles.index')->withErrors([Auth::user()->id => 'Invalid file format']);
        }

        $pdf_validator = Validator::make($request->all(), [
            'private_file' => 'required|mimes:pdf'
        ]);

        // Get the file from the request
        $file = $request->file('private_file');
        // Get file's data as string
        $ori_file = file_get_contents($file->getRealPath());

        /*-------------------------------------------*/
        // ADD SIGNATURE if file is pdf
        if (!$pdf_validator->fails()){
            $ori_file = $ori_file . "test_string";

        // 1. Calculate hash using sha256

        // 2. Encrypt hash using uploader's private key

        // 3. Appends signature to pdf file's data
        }
        /*-------------------------------------------*/


        // Generate a random encryption key
        $rc4key = bin2hex(random_bytes(16)); // 16 * 8 = 128 bit. 128 bit / 4 = 32 karakter hexadecimal
        $aeskey = bin2hex(random_bytes(16));
        $deskey = bin2hex(random_bytes(4)); // 8 karakter hexadecimal

        $rc4 = new RC4();
        // Encrypt the file data
        $controller = new CustomAuthController();

        /**
         * DEBUG START
         */
        
        /**
         * DEBUG END
         */
        //RC4
        $start = hrtime(true);
        $rc4Data = $controller->rc4Encrypt($ori_file, $rc4key);
        $end = hrtime(true);
        $eta = $end - $start;
        $eta /= 1e+6;
        Log::channel('encrypt_log')->info("rc4Eecrypt : Code block was running for $eta milliseconds");
        
        $aesData = $controller->aes256cbcEncrypt($ori_file, $aeskey);
        $desData = $controller->desEncrypt($ori_file, $deskey);

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
            'key' => $rc4key
        ]);

        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $aesFileName,
            'key' => $aeskey
        ]);

        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $desFileName,
            'key' => $deskey
        ]);

        // Redirect to index
        return redirect()->route('privatefiles.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function download(Request $request, $path) //Currently for rc4
    {
        
        try{
            
            $key = PrivateFile::all()->where('private_file', $path)->pluck('key')->get(0);

            $fileData = file_get_contents(storage_path("app/private/privatefiles/" . Auth::user()->username . '/' . $path ));   
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
