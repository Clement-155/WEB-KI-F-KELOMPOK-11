<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        // Get the file from the request
        $file = $request->file('private_file');
        // Get file's data as string
        $ori_file = file_get_contents($file->getRealPath());

        $offset = strrpos($ori_file, "%%EOF") + 5;

        $signature_data = substr($ori_file, $offset, strlen($ori_file)-$offset);



        return back()->with(['result' => "DEBUG"]);
    }
}
