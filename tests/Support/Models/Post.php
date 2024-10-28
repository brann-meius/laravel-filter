<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'content',
        'created_at',
        'updated_at',
    ];
}