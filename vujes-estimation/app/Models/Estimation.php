<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    protected $fillable = ['project_id', 'title', 'type', 'hours', 'hourly-rate', 'price'];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
