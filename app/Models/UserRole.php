<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserRole extends Authenticatable
{
    protected $table = 'users_roles';
    protected $guarded = [];
}
