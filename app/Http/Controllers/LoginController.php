<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class LoginController extends Controller
{


    /**
     * Display the specified resource.
     */
    public function show(): View
    {
        return view('login.show');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->route('home');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $id = Auth::id();
        $user = User::findOrFail($id);

        // Guest user accounts are deleted upon logging out.
        if($user->guest){
            foreach($user->files as $file){
                SystemFile::delete('storage/media/'.$file->type.'s/'.$file->title);
            }
            $user->delete();
        }

        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }
}
