<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \InstagramAPI\Instagram;
use \App\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivitiesController extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }
    public function index(){
        $tasks = DB::table('tasks')->where('user', '=', Auth::user()->id)->get()->sortBy('timestamp');
        foreach($tasks as $task){
            $task->account_name = Account::where('id', '=', $task->account)->first()->username;
        }
        return view('activities.index')
            ->with('tasks', $tasks);
    }
    public function create(){
        return view('activities.create')
            ->with('user', Auth::user());
    }
    public function store(Request $request){
        $this->validate($request, [
            'utc_time.*' => 'integer',
        ]);
        if(!$request->file('file')){
            $request->session()->flash('status', 'No file uploaded.');
            return redirect('/activities/add')->withInput();
        }
        if(substr($request->file('file')->getMimeType(), 0, 5) != 'image' && substr($request->file('file')->getMimeType(), 0, 5) != 'video') {
            $request->session()->flash('status', 'File must be image or video.');
            return redirect('/activities/add')->withInput();
        }
        if(!$request->file('file')->isValid()){
            $request->session()->flash('status', 'Failed to upload file. Try again.');
            return redirect('/activities/add')->withInput();
        }
        $filepath = $request->file('file')->storeAs('userfiles/'.Auth::user()->id, time().rand(0,999).'.'.$request->file('file')->extension());
        if($request->has('enable')){
            foreach($request->enable as $account_id){
                $caption = $request->caption[$account_id];
                $day = $request->day[$account_id];
                $time = $request->utc_time[$account_id];
                if($time < time() - 3600){
                    $request->session()->flash('status', "Invalid time entered.");
                    return redirect('/activities/add')->withInput();
                }
                $account = Account::where('id', '=', $account_id)->first();
                if(!$caption){
                    $caption = "";
                }
                DB::table('tasks')->insert([
                    'user' => Auth::user()->id,
                    'filepath' => $filepath,
                    'timestamp' => $time,
                    'account' => $account->id,
                    'caption' => $caption,
                    'error' => "",
                    "created_at" => \Carbon\Carbon::now(),
                    "updated_at" => \Carbon\Carbon::now(),
                ]);
                $error = false;
                if($error){
                    $string = 'Something went wrong with account: '.$account->username;
                    if($error->getMessage()){
                        $string .= ' | Error: '.$error->getMessage();
                    }
                    $request->session()->flash('status', $string);
                }
            }
        }
        return redirect('/activities');
    }
}
