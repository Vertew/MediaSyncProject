<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Profile;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|unique:users|max:30',
            'email' => 'required|unique:users|email',
            'password' => 'required|max:255',
        ]);

        $user = new User;
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->email_verified_at = now(); // The email isn't actually verified currently, this is just demonstrative.
        $user->password = $validatedData['password'];
        $user->guest = false;
        $user->remember_token = Str::random(10);
        $user->save();

        $profile = new Profile; // Profiles are intrinsically linked to users, so when a user is created, an empty profile is also created.
        $profile->user_id = $user->id;
        $profile->save();

        // If the user is creating a new account while logged in as a guest, the guest account is deleted.
        if (Auth::check()) {
            if(Auth::user()->guest){
                UserController::destroy(Auth::id());
            }
        }

        session()->flash('message', 'New account created!');

        // Login the user upon account creation
        if (Auth::attempt($validatedData)) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }
    }

    // Creation of a temporary guest account with no email, password or profile.
    public function storeGuest(Request $request){


        // Making sure people can't keep creating loads of new guest accounts and filling up the database.
        if (Auth::check()) {
            session()->flash('message', "You're already logged in.");
            session()->flash('alert-class', 'alert-warning');
            return redirect()->route('home');
        }else{
            $user = new User;
            $user->username = "Guest-" . Str::random(6);
            $user->guest = true;
            $user->remember_token = Str::random(10);
            $user->save();

            session()->flash('message', 'Logged in as a guest. This account is only temporary, for the full experience register your own full account!');

            // There's no need for credential validation on a guest account so we log the guest in via their ID.
            if (Auth::loginUsingId($user->id)) {
                $request->session()->regenerate();
                return redirect()->route('home');
            }
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        // Guests can't look at user account pages
        if (Gate::allows('full-account')) {
            return view('users.show', ['user' => $user]);
        }else{
            session()->flash('message', 'Guest users do not have access to the account page.');
            session()->flash('alert-class', 'alert-warning');
            return redirect()->route('home');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $user = User::findOrFail($id);

        //Deleting user files from storage when the account is deleted.
        foreach($user->files as $file){
            SystemFile::delete('storage/media/'.$file->type.'s/'.$file->title);
        }

        $user->delete();

        session()->flash('message', 'User was deleted.');
        return redirect()->route('login.logout');

    }
}
