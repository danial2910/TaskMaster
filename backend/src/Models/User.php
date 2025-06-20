<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $hidden = ['password'];
    protected $fillable = ['name', 'email', 'password'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}