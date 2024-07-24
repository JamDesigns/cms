<?php

namespace App\View\Components;

use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function render(): View
    {
        $totalPosts = 0;
        $posts = [];
        $categories = [];
        $roles = Role::get();

        $totalPosts = Post::where('status', 'published')
            ->where('created_at', '<=', Carbon::now())
            ->count();

        if ($totalPosts > 0) {
            $posts = Post::select('category_id', DB::raw('COUNT(category_id) as posts_count'))
                ->where('status', 'published')
                ->where('created_at', '<=', Carbon::now())
                ->groupBy('category_id');
            $categories = Category::joinSub($posts, 'posts_count', function (JoinClause $join) {
                $join->on('categories.id', '=', 'posts_count.category_id');
            })
                ->groupBy('categories.id')
                ->orderBy('posts_count', 'desc')
                ->limit(10)
                ->get();
        }

        return view('layouts.app', compact('categories', 'totalPosts', 'roles'));
    }
}
