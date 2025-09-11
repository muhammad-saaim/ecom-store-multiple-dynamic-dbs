<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = ['name', 'email'];

    // OPTIONAL: Define table if needed (default is 'users')
    // protected $table = 'users';

    /**
     * One-to-One relationship: User → Profile
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * One-to-Many relationship: User → Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Optional: Dynamic database connection
     * You can set the connection dynamically in code if needed
     * Example:
     * Config::set('database.default', 'slave'); // for reads
     */
    // protected $connection = 'mysql'; // default is master
}
