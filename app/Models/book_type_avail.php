<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class book_type_avail extends Model
{
    protected $table = 'book_type_avail';
    public $timestamps = false;
    protected $fillable = ['book_id', 'type', 'availability'];

    public function book()
    {
        return $this->belongsTo(book::class, 'book_id', 'book_id');
    }
}
