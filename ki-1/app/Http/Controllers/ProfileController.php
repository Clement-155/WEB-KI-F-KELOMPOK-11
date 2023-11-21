<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CustomAuthController;

use App\Models\User;
use Illuminate\Http\Request;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use phpseclib3\Crypt\RC4;

class ProfileController extends Controller
{
    public function index(): View
    {
        //get user
        $user = User::where('username', '=', Auth::user()->username)->first();

        $rc4Controller = new CustomAuthController();

        $userProfile = array(
            "id-card" => $rc4Controller->rc4Decrypt($user->getAttribute('id-photo'), 'amogus'),
            "fullname" => $rc4Controller->rc4Decrypt($user->fullname, 'amogus'),
            "gender" => $rc4Controller->rc4Decrypt($user->gender, 'amogus'),
            "citizenship" => $rc4Controller->rc4Decrypt($user->citizenship, 'amogus'),
            "religion" => $rc4Controller->rc4Decrypt($user->religion, 'amogus'),
            "marital" => $rc4Controller->rc4Decrypt($user->getAttribute('marital-status'), 'amogus'),
        );

        //render view with posts
        return view('profile', compact('userProfile'));
    }
}
