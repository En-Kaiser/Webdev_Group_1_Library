<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    protected $table = 'history';
    public $timestamps = false;
    protected $primaryKey = 'history_id';

    protected $fillable = [
        'book_id',
        'user_id',
        'type',
        'status',
        'date_borrowed',
        'date_return',
    ];

    public function book()
    {
        return $this->belongsTo(book::class, 'book_id', 'book_id');
    }

    public function user()
    {
        return $this->belongsTo(user_account::class, 'user_id', 'user_id');
    }
}
