<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $posts = Post::where('category_id', '=', $category->id)
            ->where('status', '=', 'published')
            ->where('created_at', '<=', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->paginate(9);
        $countPosts = $posts->count();

        return view('categories.show', compact('category', 'posts', 'countPosts'));
    }
}
