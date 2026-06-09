<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    protected $fillable = ['client_id', 'title', 'price'];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
