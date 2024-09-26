<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'id',
        'user_id',
        'post_id',
        'content',
        'created_at',
        'updated_at',
    ];
}