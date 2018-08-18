<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Predict extends Model
{
    protected $fillable = [
        'user_id', 'movie_name', 'movie_score'
    ];
}
