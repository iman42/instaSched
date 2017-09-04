<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \InstagramAPI\Instagram;
use \App\Account;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('manage')
            ->with('user', Auth::user());
    }

    public function deleteAccount($id){
        $account = Account::where('id', '=', $id)->first();
        if(Auth::user()->id != $account->user){
            return redirect('/');
        }
        $account->delete();
        return redirect('/manage');
    }

    public function addAccount(Request $request){
        $username = $request->username;
        $password = $request->password;
        $debug = false;
        $truncatedDebug = false;
        $ig = new Instagram($debug, $truncatedDebug);
        $success = false;
        try {
            foreach(Account::all() as $ac){
                if(Auth::user()->id == $ac->user && $username == $ac->username){
                    $request->session()->flash('status', "You've already added this account.");
                    return redirect('/manage');
                }
            }
            $ig->setUser($username, $password);
            $loginResponse = $ig->login();
            if(is_null($loginResponse)){
                $request->session()->flash('status', "Error: We fucked up. This shouldn't have happened. Please try again.");
            }
            else{
                $success = true;
            }
            $ig->logout();
        } catch (\Exception $e) {
            $success = false;
            if(!$e->getMessage()){
                $request->session()->flash('status', 'Something went wrong. Please make sure two factor authentication is disabled.');
            }
            elseif($e->getMessage() == 'InstagramAPI\Response\LoginResponse: checkpoint_required.'){
                $request->session()->flash('status', "Error: Log in to this instagram account on your device, if problem persist contact us.");
            }
            else{
                $request->session()->flash('status', 'Error: Something went wrong: '.$e->getMessage()."\n");
            }
        }
        if($success){
            $encrypted_password = encrypt($password);
            $account = new Account;
            $account->username = $username;
            $account->encrypted_password = $encrypted_password;
            $account->user = Auth::user()->id;
            $account->save();
        }
        return redirect('/manage');
    }
}
