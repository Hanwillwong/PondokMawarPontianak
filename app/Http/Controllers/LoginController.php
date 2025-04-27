<?php

namespace App\Http\Controllers;

use App\Models\login;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.login', [
            "tittle" => "Login"
       ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',  // input bisa email atau phone number
            'password' => 'required|min:8',
        ]);
    
        $login = $request->input('login');
        $password = $request->input('password');
    
        // Mengecek apakah input berupa email atau nomor telepon
        $user = null;
    
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Jika email, cari user berdasarkan email
            $user = User::where('email', $login)->first();
        } elseif (preg_match('/^\d{10,15}$/', $login)) {
            // Jika nomor telepon, cari user berdasarkan nomor telepon
            $user = User::where('phone_number', $login)->first();
        }
    
        // Jika user ditemukan, lanjutkan otentikasi
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return redirect()->intended('/dashboard');
        } else {
            return back()->with('loginError', 'Invalid credentials');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(login $login)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(login $login)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, login $login)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(login $login)
    {
        //
    }
}
