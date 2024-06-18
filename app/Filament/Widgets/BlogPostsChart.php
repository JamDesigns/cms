<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use LaraZeus\Quantity\Components\Quantity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BlogPostsChart extends ApexChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $chartId = 'blogPostsChart';

    public function getHeading(): string
    {
        return __('Posts');
    }

    public function getSubheading(): string
    {
        return __('Year') . ': ' . $this->filterFormData['Year'];
    }

    protected function getOptions(): array
    {
        $userRoles = auth()->user()->roles;
        $isAdmin = true;
        $isEditor = true;
        $start = Carbon::parse($this->filterFormData['Year'] . "-01-01 00:00:00.0");
        $end = Carbon::parse($this->filterFormData['Year'] . "-12-31 23:59:59.999999");
        $legend = __('Anual posts');

        foreach ($userRoles as $role) {

            if (($role->name !== 'Super Admin' && $role->name !== 'Admin')) {
                $isAdmin = false;
            }

            if ($role->name !== 'Editor') {
                $isEditor = false;
            }
        }

        if ($isAdmin || $isEditor) {
            $data = Trend::model(Post::class)
                ->between(
                    start: $start,
                    end: $end,
                )
                ->perMonth()
                ->count();
        } else {
            $data = Trend::query(
                Post::where('user_id', '=', auth()->user()->id)
            )
                ->between(
                    start: $start,
                    end: $end,
                )
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
                    'type' => 'bar',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
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
