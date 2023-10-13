<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }


    public function customLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|min:1',
            'password' => 'required|min:6',
        ]);

        //$credentials = $request->only('username', 'password');
        
        //If auth success
        // if (Auth::attempt($credentials)) {
            
        //     return redirect()->intended('welcome')
        //         ->withSuccess('Signed in');
        // }
        $user = User::where([
            'username' => $request->username,
            'password' => $request->password
        ])->first();

        if($user){
            Auth::login($user);
            return redirect("dashboard")->with('success', 'Logging in..');
        }
        //If auth failed
        return redirect("login")->with('failed', 'Login details are not valid');
    }



    public function registration()
    {
        return view('auth.registration');
    }


    public function customRegistration(Request $request)
    {
        $request->validate([
            'id-photo' => 'required|image|mimes:jpeg,jpg,png|max:10000',
            'username' => 'required|min:4|unique:App\Models\User,username',
            'password' => 'required|min:6',
            'fullname' => 'required',
            'gender' => 'required',
            'citizenship' => 'required',
            'religion' => 'required',
            'marital-status' => 'required',
        ]);

        $data = $request->all();
        //upload image
        $image = $request->file('id-photo');

        
        $image->storeAs('public/id-card', $image->hashName());
        $data['id-photo'] = $image;
        $check = $this->create($data);

        return redirect("login")->withSuccess('You have signed-up');
    }

    //Creates new row in database
    public function create(array $data)
    {

        return User::create([
            'id-photo' => $data['id-photo']->hashName(),
            'username' => $data['username'],
            'password' => $data['password'],
            'fullname' => $data['fullname'],
            'gender' => $data['gender'],
            'citizenship' => $data['citizenship'],
            'religion' => $data['religion'],
            'marital-status' => $data['marital-status'],
        ]);
    }


    public function dashboard()
    {
        if (Auth::check()) {
            return redirect('privatefiles')->with('success', 'Login Success');
        }

        return redirect("login")->with('failed', 'You are not allowed to access');
    }


    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
