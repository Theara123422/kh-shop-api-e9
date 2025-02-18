<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const FIRST_NAME = 'first_name';
    const LAST_NAME  = 'last_name';
    const GENDER     = 'gender';
    const EMAIL      = 'email';
    const PHONE_NUMBER = 'phone_number';
    const COUNTRY    = 'country';
    const CITY       = 'city';
    const PASSWORD   = 'password';
    const PROFILE    = 'profile';
    const REMEMBER_TOKEN = 'remeber_token';
    const EMAIL_VERIFIED_AT = 'email_verified_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::FIRST_NAME,
        self::LAST_NAME,
        self::GENDER,
        self::EMAIL, 
        self::PHONE_NUMBER,
        self::COUNTRY,
        self::CITY,
        self::PASSWORD,
        self::PROFILE
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::EMAIL_VERIFIED_AT => 'datetime',
        self::PASSWORD => 'hashed'
    ];
}
