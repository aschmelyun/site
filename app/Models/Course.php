<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Orbit\Concerns\Orbital;

class Course extends Model
{
    use Orbital;

    public static function schema(Blueprint $table): void
    {
        $table->string('title');
        $table->string('slug');
        $table->string('categories');
        $table->string('thumbnail');
        $table->string('link');
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