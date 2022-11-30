<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Users extends Model
{
    protected $table = 'users';
    public $incrementing = false;
    public $guarded = [];

    public static function NewUser($username, $password, $email)
    {
        return self::create([
            'id' => uuid(),
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email
        ]);
    }
}
