<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $fillable = ['user_id', 'title', 'description', 'status', 'due_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}