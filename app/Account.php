<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use \InstagramAPI\Instagram;

class Account extends Model
{
    protected $table = "accounts";
    public function user(){
        return $this->belongsTo('App\User', 'user', 'id');
    }
    public function password(){
        return decrypt($this->encrypted_password);
    }
    public function uploadFile($filepath, $caption, $delete = false){
        $username = $this->username;
        $password = $this->password();
        try{
            $ig = new Instagram();
            $ig->setUser($username, $password);
            $ig->login();
        }catch(\Exception $e){
            return $e;
        }
        $storagePath = Storage::getDriver()->getAdapter()->getPathPrefix();
        if(substr(Storage::getMimeType($filepath), 0, 5) == 'image') {
            try{
                $ig->uploadTimelinePhoto($storagePath.'/'.$filepath, ['caption' => $caption]);
                $ig->logout();
            }catch (\Exception $e) {
                try{
                    $ig->logout();
                }
                catch(\Exception $f){
                }
                return $e;
            }
        }
        elseif(substr(Storage::getMimeType($filepath), 0, 5) == 'video'){
            try{
                $ig->uploadTimelineVideo($storagePath.'/'.$filepath, ['caption' => $caption]);
                $ig->logout();
            }catch (\Exception $e) {
                try{
                    $ig->logout();
                }
                catch(\Exception $f){
                }
                return $e;
            }
        }
        else{
            return true;
        }
        if ($delete){
            Storage::delete($filepath);
        }
        return false;
    }
}
