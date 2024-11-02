<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Models;

use Meius\LaravelFilter\Traits\HasFilterAlias;

class Post extends Model
{
    use HasFilterAlias;

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
