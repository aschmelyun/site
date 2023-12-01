<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Orbit\Concerns\Orbital;

class Lesson extends Model
{
    use Orbital;

    public static function schema(Blueprint $table): void
    {
        $table->string('title');
        $table->string('slug');
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