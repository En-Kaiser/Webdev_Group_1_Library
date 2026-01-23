<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class author extends Model
{
    protected $table = 'authors';
    protected $primaryKey = 'author_id';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(book::class, 'books_joint_authors', 'author_id', 'book_id');
    }
}
