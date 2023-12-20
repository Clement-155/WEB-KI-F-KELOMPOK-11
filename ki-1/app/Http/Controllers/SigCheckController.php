<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\PublicKeyLoader;

class SigCheckController extends Controller
{
    public function index(): View
    {
        $users =  User::all()->where('id', '!=', Auth::user()->id);
        //render view with posts
        return view('signature.PdfSigCheckView', compact('users'));
    }
    public function pdfSigCheck(Request $request)
    {
        $request->validate([
            'owner' => 'required',
            'private_file' => 'required|mimes:pdf'
        ]);

        $public = User::all()->where('id','=', $request->owner)->first()->public;

        

        // Get the file from the request
        $file = $request->file('private_file');
        // Get file's data as string
        $ori_file = file_get_contents($file->getRealPath());

        $offset = strrpos($ori_file, "%%EOF") + 5;

        $signature_data = substr($ori_file, $offset, strlen($ori_file)-$offset);

        $digest = "";
        
        var_dump(openssl_public_decrypt($signature_data, $digest, $public));

        var_dump(error_get_last());


        $hashController = new Hash();
        $hashController->setHash("sha256");
        $new_digest = $hashController->hash($ori_file);

        if($digest == $new_digest)
        {
            dd("wot");
            $result = "SUCCESS : Signature is valid.";
        }
        else
        {
            dd("sed");
            $result = "WARNING : Signature invalid or error occured!!!";
        }

        return back()->with(['result' => $result]);
    }
}
