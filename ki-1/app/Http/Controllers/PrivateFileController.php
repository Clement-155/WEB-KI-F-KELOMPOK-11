<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Str;
use phpseclib3\Crypt\RC4;

include('..\vendor\autoload.php'); //adjust to your directory

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

        // Try again for video
        // if ($validator->fails()) {
        //     $validator = Validator::make($request->all(), [
        //         'private_file' => 'required|mimetypes:video'
        //     ]);
        // }

        // File not a valid type, error messages use user's id
        if ($validator->fails()) {
            return redirect()->route('privatefiles.index')->withErrors([Auth::user()->id => 'Invalid file format']);
            return redirect()->route('privatefiles.index')->withErrors([Auth::user()->id => 'Invalid file format']);
        }

        // Get the file from the request
        // Get the file from the request
        $file = $request->file('private_file');

        // Generate a random encryption key
        $key = 'amogus';

        // Create a new RC4 object
        $rc4 = new RC4();

        // Set the encryption key
        $rc4->setKey($key);

        // Encrypt the file data
        $encryptedData = $rc4->encrypt(file_get_contents($file->getRealPath()));

        // Determine the file extension
        $fileExtension = $file->getClientOriginalExtension();

        // Generate a unique file name for the encrypted file
        $originalFileName = $file->getClientOriginalName();
        $encryptedFileName = 'rc4_' . Str::slug($originalFileName) .  time() . '.' . $fileExtension;

        // Store the encrypted file
        Storage::put('private/privatefiles/' . Auth::user()->username . '/' . $encryptedFileName, $encryptedData);

        // Create a record in the database
        // Create a record in the database
        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file' => $encryptedFileName,
            'private_file' => $encryptedFileName,
        ]);

        // Redirect to index
        // Redirect to index
        return redirect()->route('privatefiles.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function download($path)
    {
        try {
            // Get the encrypted file
            $encryptedFile = Storage::get('private/privatefiles/' . Auth::user()->username . '/' . $path);

            // Create a new RC4 object
            $rc4 = new RC4();

            // Set the encryption key
            $rc4->setKey('amogus');

            // Decrypt the file data
            $decryptedData = $rc4->decrypt($encryptedFile);

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
}
