<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    // protected $fillable = [
    //     'full_name', 'email', 'phone',
    // ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
