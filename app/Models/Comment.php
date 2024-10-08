<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function post(): BelongsTo {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function parent(): belongsTo {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function children(): HasMany {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('children');
    }
}
