<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Query\JoinClause;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userRoles = auth()->user()->roles;
        $isAdmin = true;
        $isEditor = true;

        foreach ($userRoles as $role) {

            if (($role->name !== 'Super Admin' && $role->name !== 'Admin')) {
                $isAdmin = false;
            }

            if ($role->name !== 'Editor') {
                $isEditor = false;
            }
        }

        if ($isAdmin || $isEditor) {
            $totalPosts = Post::count();
            $totalDraftPosts = Post::where('status', 'draft')->count();
            $lastPost = Post::where('created_at', '<=', Carbon::now())->orderBy('created_at', 'desc')->first();
            $lastComment = Comment::with('post')->orderBy('created_at', 'desc')->first();
        } else {
            $totalPosts = Post::where('user_id', auth()->id())->count();
            $totalDraftPosts = Post::where('user_id', auth()->id())->where('status', 'draft')->count();
            $lastPost = Post::where('user_id', auth()->id())->where('created_at', '<=', Carbon::now())->orderBy('created_at', 'desc')->first();
            $lastComment = Comment::with('user')->join('posts', function (JoinClause $join) {
                $join->on('comments.post_id', '=', 'posts.id')
                    ->where('posts.user_id', '=', auth()->user()->id);
            })
                ->select('comments.*')
                ->where('comments.status', '=', true)
                ->orderBy('comments.created_at', 'desc')
                ->first();
        }

        if ($totalPosts === 0) {
            $percentageDraftPost = 0;
        } else {
            $percentageDraftPost = round($totalDraftPosts * 100 / $totalPosts, 2);
        }

        return [
            Stat::make(__('Posts'), $totalPosts)
                ->description(__('Total posts')),
            Stat::make(__('Last post'), (isset($lastPost->title) ? $lastPost->title : '---'))
                ->color(isset($lastPost->title) && $lastPost->status === 'published' ? 'success' : 'warning')
                ->description((isset($lastPost->created_at) ? '(' . __(ucfirst($lastPost->status)) . ') ' . __('Created at') . ' ' . Carbon::parse($lastPost->created_at)->isoFormat('dddd, D MMMM YYYY') . ' ' . __('by') . ' ' . $lastPost->user->name : '')),
            Stat::make(__('Draft posts'), $totalDraftPosts)
                ->color($percentageDraftPost > 0 ? 'warning' : 'success')
                ->description($percentageDraftPost . __('% of total') . ' ' . __('of') . ' ' . __('posts')),
            Stat::make(__('Comments'), Comment::count())
                ->description(__('Total comments on all posts')),
            Stat::make(__('Last comment made'), (isset($lastComment->post->title) ? $lastComment->post->title : '---'))
                ->description((isset($lastComment->created_at) ? '(' . __('Created the') . ' ' . Carbon::parse($lastComment->created_at)->isoFormat('dddd, D MMMM YYYY') . ' ' . __('by') . ' ' . $lastComment->user->name . ')' : '')),
            Stat::make(__('Categories'), Category::count())
                ->description(__('Total categories')),
        ];
    }
}
