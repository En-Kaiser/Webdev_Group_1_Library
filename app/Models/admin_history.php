<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class admin_history extends Model
{
    protected $table = 'admin_history';
    protected $fillable = ['admin_id', 'book_id', 'user_id', 'description', 'change_created'];
    public $timestamps = false;
}
