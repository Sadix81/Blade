<?php

namespace Modules\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Otp\Database\Factories\OtpFactory;

class Otp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'otp',
        'expire_time',
        'user_id',
    ];

    // protected static function newFactory(): OtpFactory
    // {
    //     // return OtpFactory::new();
    // }
}
