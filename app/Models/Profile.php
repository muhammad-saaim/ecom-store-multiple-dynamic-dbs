<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Import User model
use App\Models\User;

class Profile extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $fillable = ['user_id', 'bio'];

    /**
     * Inverse of User → Profile (One-to-One)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
