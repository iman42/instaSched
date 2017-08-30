<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = "accounts";
    public function user(){
        return $this->belongsTo('App\User', 'user', 'id');
    }
    public function password(){
        return decrypt($this->encrypted_password);
    }
}
