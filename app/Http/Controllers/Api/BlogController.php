<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->get()->map(function($b) {
            return [
                'id' => $b->id,
                'title' => $b->title,
                'description' => $b->description,
                'image' => $b->image ? asset('storage/' . $b->image) : null,
                'link' => $b->link,
                'created_at' => $b->created_at,
            ];
        });

        return response()->json($blogs);
    }
}
