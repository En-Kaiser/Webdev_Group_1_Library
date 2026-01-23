<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class books_joint_author extends Model
{
    protected $table = 'books_joint_authors';
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'author_id'
    ];

    public function book()
    {
        return $this->belongsTo(book::class, 'book_id', 'book_id');
    }

    public function author()
    {
        return $this->belongsTo(author::class, 'author_id', 'author_id');
    }
}
