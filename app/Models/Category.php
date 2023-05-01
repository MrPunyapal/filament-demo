<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =[
        'name',
        'order'
    ];

    protected static function booted()
    {
        static::creating(function ($screen) {
            $screen->order = 1 + self::max('order');
        });
    }

    public function posts()

    {
        return $this->hasMany(Post::class);
    }
}
