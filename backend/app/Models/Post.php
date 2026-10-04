<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    // posts has both created_at and updated_at, so Laravel's default timestamps apply.

    protected $fillable = [
        'blog_id',
        'title',
        'content',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    // Post::withBlogTitle() adds a "blog_title" column, like the old API's JOIN on blogs.
    public function scopeWithBlogTitle(Builder $query): void
    {
        $query->addSelect([
            'blog_title' => Blog::select('title')->whereColumn('blogs.id', 'posts.blog_id'),
        ]);
    }
}
