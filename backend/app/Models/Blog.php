<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    // The existing blogs table has created_at but no updated_at.
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Blog::withUserName() adds a "user_name" column, like the old API's JOIN on users.
    public function scopeWithUserName(Builder $query): void
    {
        $query->addSelect([
            'user_name' => User::select('name')->whereColumn('users.id', 'blogs.user_id'),
        ]);
    }
}
