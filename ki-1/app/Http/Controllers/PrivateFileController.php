<?php

namespace App\Http\Controllers;

use App\Models\PrivateFile;

//return type View

use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\WhitespacePathNormalizer;


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
        //validate form for documents
        $validator = Validator::make($request->all(), [
            'private_file'     => 'required|mimes:pdf,doc,docx,xls,xlsx,mp4'
        ]);

        //try again for video
        if ($validator->fails()) {
            $validator = Validator::make($request->all(), [
                'private_file'     => 'required|mimetypes:video'
            ]);
        }

        //file not valid type, error messages uses user's id
        if ($validator->fails()) {
            return redirect()->route('privatefiles.index')->withErrors([Auth::user()->id => 'Invalid file format']);
        }

        //upload files to folders per user
        $PathNormalizerInstance = new WhitespacePathNormalizer;
        //store file as it's original file name (IMPLEMENT ENCRYPTION HERE)
        $file = $request->file('private_file');
        $file->storeAs($PathNormalizerInstance->normalizePath('private/privatefiles/' . (Auth::user()->username)), $file->getClientOriginalName());

        //create post
        PrivateFile::create([
            'user_id' => Auth::user()->id,
            'private_file'     => $file->getClientOriginalName(),
        ]);

        //redirect to index
        return redirect()->route('privatefiles.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
}
