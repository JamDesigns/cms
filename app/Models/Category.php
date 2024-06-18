<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Category extends Model
{
    use HasFactory;
    // use HasSEO;

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function posts(): HasMany
    {
        return $this->hasMany((Post::class));
    }
}
