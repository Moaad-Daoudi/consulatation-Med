<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{

    protected $fillable = [
        'user_id',
        'specialisation',
        'biography',
        'gender',
        'phone_number',
        'photo_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
