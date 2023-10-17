<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        //get posts
        $user = User::where('username', '=', Auth::user()->username)->first(); //FIX : Can use eliquent to get files instead
        //Profile didecrypt dulu
        
        $userProfile = array(
            "id-card" => $user->getAttribute('id-photo'),
            "fullname" => $user->fullname,
            "gender" => $user->gender,
            "citizenship" => $user->citizenship,
            "religion" => $user->religion,
            "marital" => $user->getAttribute('marital-status'),
        );

        //render view with posts
        return view('profile', compact('userProfile'));
    }
}
