<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function files(): BelongsToMany{
        return $this->belongsToMany(File::class)->withTimestamps()->withPivot('votes');
    }

    public function banned_users(): BelongsToMany{
        return $this->belongsToMany(User::class);
    }

    public function file(): BelongsTo{
        return $this->belongsTo(File::class);
    }
}
