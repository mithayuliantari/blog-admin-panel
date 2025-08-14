<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
    ];

    // Accessor opsional: $blog->image_url
    public function getImageUrlAttribute()
    {
        if (! $this->image) return null;
        return asset('storage/' . $this->image);
    }
}
