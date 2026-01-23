<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class genre extends Model
{
    protected $table = 'genres';
    protected $primaryKey = 'genre_id';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(book::class, 'books_joint_genres', 'genre_id', 'book_id');
    }
}
