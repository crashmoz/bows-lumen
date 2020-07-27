<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
// use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Model
{
    protected $table = 'account';

    public $incrementing = false;

    public $timestamps = false;

    protected $hidden = [
        'password',
    ];

    public function generateToken()
    {
        $this->api_token = Str::random(60);
        $this->save();
        return $this->api_token;
    }

    public function saveToken($token)
    {
        $this->api_token = $token;
        return $this->save();
    }
}