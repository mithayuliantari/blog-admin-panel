<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $perPage   = (int)($request->input('per_page', 9));
        $category  = $request->input('category'); // slug
        $tag       = $request->input('tag');      // slug
        $q         = $request->input('q');        // search in title/excerpt

        $query = Blog::with(['category','tags'])->latest();

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%");
            });
        }

        if ($category) {
            $query->whereHas('category', fn($cat) => $cat->where('slug',$category));
        }

        if ($tag) {
            $query->whereHas('tags', fn($t) => $t->where('slug',$tag));
        }

        $paginator = $query->paginate($perPage);

        $data = $paginator->getCollection()->map(fn($b) => $this->mapBlogList($b));

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    public function show($slug)
    {
        $b = Blog::with(['category','tags'])->where('slug',$slug)->firstOrFail();

        return response()->json([
            'id'         => $b->id,
            'title'      => $b->title,
            'slug'       => $b->slug,
            'excerpt'    => $b->excerpt,
            'content'    => $b->content, // HTML dari RichEditor
            'image'      => $b->image_url,
            'category'   => $b->category?->only(['name','slug']),
            'tags'       => $b->tags->map->only(['name','slug']),
            'created_at' => $b->created_at,
        ]);
    }

    public function categories()
    {
        return Category::query()->orderBy('name')->get(['name','slug']);
    }

    public function tags()
    {
        return Tag::query()->orderBy('name')->get(['name','slug']);
    }

    private function mapBlogList(Blog $b): array
    {
        return [
            'id'       => $b->id,
            'title'    => $b->title,
            'slug'     => $b->slug,
            'excerpt'  => $b->excerpt ?? str($b->description)->limit(150),
            'image'    => $b->image_url,
            'category' => $b->category?->only(['name','slug']),
            'tags'     => $b->tags->map->only(['name','slug']),
            'created_at' => $b->created_at,
        ];
    }
}
