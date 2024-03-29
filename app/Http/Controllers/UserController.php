<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File as SystemFile;
use App\Events\RequestRecievedEvent;
use App\Notifications\FriendRequest;
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
            'password' => 'required|unique:users|max:255',
        ]);

        $user = new User;
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->email_verified_at = now(); // The email isn't actually verified since the site isn't going into live service, this is just demonstrative.
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
        if (Gate::allows('full-account') && !($user->guest)) {
            return view('users.show', ['user' => $user]);
        }else {
            session()->flash('message', 'Guest users do not have access to account page functionality.');
            session()->flash('alert-class', 'alert-warning');
            return redirect()->route('home');
        }
    }

    public function sendRequest(string $id) {
        $recipient =  User::find($id);
        $user = Auth::user();

        if($user->friends->contains($id)){
            session()->flash('message', "You're already friends with this user.");
            session()->flash('alert-class', 'alert-warning');
        }
        elseif($recipient->guest){
            session()->flash('message', "You cannot friend a guest user.");
            session()->flash('alert-class', 'alert-warning');
        }
        // Only sends a request if the recipient doesn't already have one from the same source.
        elseif(is_null($recipient->notifications()->firstWhere('data->sender_id', $user->id))){
            $recipient->notify(new FriendRequest($user));
            RequestRecievedEvent::dispatch($recipient->id, $user);
            session()->flash('message', 'Friend request sent!');
        }
        else{
            session()->flash('message', "You've already sent a friend request to this user.");
            session()->flash('alert-class', 'alert-warning');
        }
        return redirect()->route('users.show', ['id'=> $id]);
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

        if(Gate::allows('private', $id)){
            $user = User::findOrFail($id);

            //Deleting user files from storage when the account is deleted.
            SystemFile::deleteDirectory('storage/media/videos/'.$user->username);
            SystemFile::deleteDirectory('storage/media/audios/'.$user->username);
            SystemFile::deleteDirectory('storage/media/images/'.$user->username);

            // Due to the way the Laravel handles the creation/deletion of tables, and the fact
            // that the rooms table doesn't get cascade deleted when a file it is associated with gets
            // deleted, we need to delete the rooms belonging to the user manually when an account is 
            // deleted to prevent a constraint violation. Fortunately this is easy to do. 
            $user->rooms()->delete();
            $user->delete();

            session()->flash('message', 'Account deleted.');
            return redirect()->route('login.logout');
        }else{
            session()->flash('message', 'You do not have permission to delete this account');
            session()->flash('alert-class', 'alert-danger');
            return redirect()->route('home');
        }
    }
}
