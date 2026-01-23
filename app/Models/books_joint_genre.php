<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class books_joint_genre extends Model
{
    protected $table = 'books_joint_genres';
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'genre_id'
    ];

    public function book()
    {
        return $this->belongsTo(book::class, 'book_id', 'book_id');
    }

    public function genre()
    {
        return $this->belongsTo(genre::class, 'genre_id', 'genre_id');
    }
}
