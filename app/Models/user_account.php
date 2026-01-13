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

    protected $hidden = [
        'password'
    ];
}
