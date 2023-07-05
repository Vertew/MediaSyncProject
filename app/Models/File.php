<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function rooms(): BelongsToMany{
        return $this->belongsToMany(Room::class);
    }
}
