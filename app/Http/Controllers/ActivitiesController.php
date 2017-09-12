<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \InstagramAPI\Instagram;
use \App\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ActivitiesController extends Controller
{
    public function __construct(){
       $this->middleware('auth');
    }

    private function getVideoDimensions( $filePath )
    {
        exec("ffprobe -v error -show_entries stream=width,height -of default=noprint_wrappers=1 '$filePath'",$O,$S);
        if(!empty($O))
        {
            $list = [
                    explode("=",$O[0])[1],
                    explode("=",$O[1])[1],
            ];

            return $list;
        }else
        {
            return false;
        }
    }
    private function getVideoDuration($filePath)
    {
        exec('ffmpeg -i'." '$filePath' 2>&1 | grep Duration | awk '{print $2}' | tr -d ,",$O,$S);
        if(!empty($O[0]))
        {
            $ar = explode(':', $O[0]);
            $seconds = $ar[2];
            $seconds += $ar[1] * 60;
            $seconds += $ar[0] * 60 * 60;
            return $seconds;
        }else
        {
            return false;
        }
    }

    public function index(){
        $tasks = DB::table('tasks')->where('user', '=', Auth::user()->id)->get()->sortBy('timestamp');
        foreach($tasks as $task){
            $task->is_video = (substr(Storage::getMimeType($task->filepath), 0, 5) == 'video');
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
        //    'utc_time.*' => 'nullable|integer',
            'caption.*' => 'nullable|between:0,2200',
        ]);
        if(!$request->file('file')){
            $request->session()->flash('status', 'No file uploaded.');
            return redirect('/activities/add/single')->withInput();
        }
        if(substr($request->file('file')->getMimeType(), 0, 5) != 'image' && substr($request->file('file')->getMimeType(), 0, 5) != 'video') {
            $request->session()->flash('status', 'File must be image or video.');
            return redirect('/activities/add/single')->withInput();
        }
        if(!$request->file('file')->isValid()){
            $request->session()->flash('status', 'Failed to upload file. Try again.');
            return redirect('/activities/add/single')->withInput();
        }
        if(substr($request->file('file')->getMimeType(), 0, 5) == 'image'){
            $filepath = $request->file('file')->path();
            $metadata = getimagesize($filepath);
            if($metadata[0] / $metadata[1] <= 0.8 || $metadata[0] / $metadata[1] >= 1.91){
                $request->session()->flash('status', 'File does not meet specifications, please use this link to edit file to have aspect ratio between 0.8 and 1.91 and resolution up to 1080x1350: <a href="https://ezgif.com/crop" target="_BLANK"> EZGif.com </a>');
                return redirect('/activities/add/single')->withInput();
            }
            if($metadata[0] > 1080 || $metadata[1] > 1350){
                $request->session()->flash('status', 'File does not meet specifications, please use this link to edit file to have aspect ratio between 0.8 and 1.91 and resolution up to 1080x1350: <a href="https://ezgif.com/crop" target="_BLANK"> EZGif.com </a>');
                return redirect('/activities/add/single')->withInput();
            }
        }
        if(substr($request->file('file')->getMimeType(), 0, 5) == 'video'){
            $filepath = $request->file('file')->path();
            $metadata = $this->getVideoDimensions($filepath);
            if($metadata[0] / $metadata[1] <= 0.8 || $metadata[0] / $metadata[1] >= 1.91){
                $request->session()->flash('status', 'File does not meet specifications, please use this link to edit file to have aspect ratio between 0.8 and 1.91 and resolution up to 1080x1350: <a href="https://ezgif.com/resize-video" target="_BLANK"> EZGif.com </a>');
                return redirect('/activities/add/single')->withInput();
            }
            if($metadata[0] > 1080 || $metadata[1] > 1350){
                $request->session()->flash('status', 'File does not meet specifications, please use this link to edit file to have aspect ratio between 0.8 and 1.91 and resolution up to 1080x1350: <a href="https://ezgif.com/resize-video" target="_BLANK"> EZGif.com </a>');
                return redirect('/activities/add/single')->withInput();
            }
            if($this->getVideoDuration($filepath) > 60){
                $request->session()->flash('status', 'File does not meet specifications, video longer than 60 seconds: <a href="https://ezgif.com/cut-video" target="_BLANK"> EZGif.com </a>');
                return redirect('/activities/add/single')->withInput();

        }
    }
        $filepath = $request->file('file')->storeAs('userfiles/'.Auth::user()->id, time().rand(0,999).'.'.$request->file('file')->extension());
        if($request->has('enable')){
            foreach($request->enable as $account_id){
                $caption = $request->caption[$account_id];
                $day = $request->day[$account_id];
                $time = $request->utc_time[$account_id];
                if(!is_numeric($time) || $time < time() - 3600){
                    $request->session()->flash('status', "Invalid time entered.");
                    return redirect('/activities/add/single')->withInput();
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
            }
        }
        return redirect('/activities');
    }
    public function delete($id){
        if(Auth::user()->id == DB::table('tasks')->where('id', '=', $id)->first()->user){
            $filepath = DB::table('tasks')->where('id', '=', $id)->first()->filepath;
            if(DB::table('tasks')->where('filepath', '=', $filepath)->count() <= 1){
                Storage::delete($filepath);
            }
            DB::table('tasks')->where('id', '=', $id)->delete();
        }
        return redirect('/activities');
    }
}
