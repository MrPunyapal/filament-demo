<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =[
        'title',
        'slug',
        'content',
        'published_at',
        'seo_title',
        'seo_description',
        'image',
        'is_visible',
        'author_id',
        'category_id',
        'tags'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'tags' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
