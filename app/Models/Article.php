<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 */
class Article extends Model
{
    protected $table = 'article';

    protected $attributes = [
        'is_completed' => false,
    ];
}
