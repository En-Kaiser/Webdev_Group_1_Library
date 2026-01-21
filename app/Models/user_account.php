<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class user_account extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'user_accounts';

    protected $primaryKey = 'user_id';

    protected $hidden = [
        'password'
    ];

    public function course()
    {
        return $this->belongsTo(course::class, 'course_id', 'course_id');
    }

    public function history()
    {
        return $this->hasMany(history::class, 'user_id', 'user_id');
    }

    public function bookmarks()
    {
        return $this->belongsToMany(book::class, 'bookmarks', 'user_id', 'book_id');
    }
}
