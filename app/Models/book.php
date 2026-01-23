<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class book extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'book_id';

    public function authors()
    {
        return $this->belongsToMany(
            author::class,
            'books_joint_authors',
            'book_id',
            'author_id'
        );
    }

    public function genres()
    {
        return $this->belongsToMany(
            genre::class,
            'books_joint_genres',
            'book_id',
            'genre_id'
        );
    }

    public function bookTypeAvail()
    {
        return $this->hasMany(book_type_avail::class, 'book_id', 'book_id');
    }
}
