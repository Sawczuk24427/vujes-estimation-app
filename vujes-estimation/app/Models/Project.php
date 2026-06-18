<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Client $client
 */
class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'client_id', 'status'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
