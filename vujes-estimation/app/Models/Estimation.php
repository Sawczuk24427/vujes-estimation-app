<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimation extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'title', 'type', 'hours', 'hourly-rate', 'price'];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
