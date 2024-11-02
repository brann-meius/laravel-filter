<?php

declare(strict_types=1);

namespace Meius\LaravelFilter\Tests\Support\Models;

use Meius\LaravelFilter\Traits\HasFilterAlias;

class Comment extends Model
{
    use HasFilterAlias;

    protected string $filterAlias = 'c';

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
