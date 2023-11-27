<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Orbit\Concerns\Orbital;

class Post extends Model
{
    use Orbital;

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public static function schema(Blueprint $table): void
    {
        $table->string('title');
        $table->string('slug');
        $table->string('description');
        $table->string('categories');
        $table->timestamp('published_at');
        $table->text('excerpt');
    }

    public function getKeyName(): string
    {
        return 'slug';
    }

    public function getIncrementing(): bool
    {
        return false;
    }
}
