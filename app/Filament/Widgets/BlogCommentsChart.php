<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Forms\Components\Select;
use Illuminate\Database\Query\JoinClause;
use LaraZeus\Quantity\Components\Quantity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BlogCommentsChart extends ApexChartWidget
{
    protected static ?int $sort = 3;

    public ?string $legend = '';

    protected static ?string $chartId = 'blogCommentsChart';

    public function getHeading(): string
    {
        return __('Comments');
    }

    public function getSubheading(): string
    {
        return __('Year') . ': ' . $this->filterFormData['Year'];
    }

    public function getFooter(): string
    {
        return $this->legend;
    }

    protected function getOptions(): array
    {
        $userRoles = auth()->user()->roles;
        $isAdmin = true;
        $isEditor = true;
        $start = Carbon::parse($this->filterFormData['Year'] . "-01-01 00:00:00.0");
        $end = Carbon::parse($this->filterFormData['Year'] . "-12-31 23:59:59.999999");

        if ($this->filterFormData['Status'] === 'approved') {
            $isApproved = true;
            $this->legend = __('Approved comments');
        } else {
            $isApproved = false;
            $this->legend = __('Rejected comments');
        }

        foreach ($userRoles as $role) {

            if (($role->name !== 'Super Admin' && $role->name !== 'Admin')) {
                $isAdmin = false;
            }

            if ($role->name !== 'Editor') {
                $isEditor = false;
            }
        }

        if ($isAdmin || $isEditor) {
            $data = Trend::query(
                Comment::where('status', '=', $isApproved)
            )
                ->between(
                    start: $start,
                    end: $end,
                )->perMonth()
                ->count();
        } else {
            $data = Trend::query(
                Comment::join('posts', function (JoinClause $join) {
                    $join->on('comments.post_id', '=', 'posts.id')
                        ->where('posts.user_id', '=', auth()->user()->id);
                })
                    ->where('comments.status', '=', $isApproved)
            )
                ->between(
                    start: $start,
                    end: $end,
                )
                ->dateColumn('comments.created_at')
                ->perMonth()
                ->count();
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'BlogCommentsChart',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->isoFormat('MMM')),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('Status')
                ->translateLabel()
                ->options(([
                    'approved' => __('approved'),
                    'rejected' => __('rejected'),
                ]))
                ->default('approved')
                ->native(false),
            Quantity::make('Year')
                ->translateLabel()
                ->numeric()
                ->prefix('1900')
                ->suffix(Carbon::now()->isoFormat('YYYY'))
                ->default(Carbon::now()->isoFormat('YYYY'))
                ->maxValue(Carbon::now()->isoFormat('YYYY'))
                ->minValue(1900),
        ];
    }
}
