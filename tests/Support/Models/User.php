<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Models;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'created_at',
        'updated_at',
    ];
}
