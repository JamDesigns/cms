<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Forms\Components\Select;
use LaraZeus\Quantity\Components\Quantity;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class BlogPostsChart extends ApexChartWidget
{
    protected static ?int $sort = 2;

    public ?string $legend = '';

    protected static ?string $chartId = 'blogPostsChart';

    public function getHeading(): string
    {
        return __('Posts');
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
        $status = 'published';
        $start = Carbon::parse($this->filterFormData['Year'] . "-01-01 00:00:00.0");
        $end = Carbon::parse($this->filterFormData['Year'] . "-12-31 23:59:59.999999");

        switch ($this->filterFormData['Status']) {
            case 'draft':
                $status = 'draft';
                $this->legend = __('Draft posts');
                break;
            case 'reviewing':
                $status = 'reviewing';
                $this->legend = __('Reviewing posts');
                break;
            case 'published':
                $status = 'published';
                $this->legend = __('Published posts');
                break;
            case 'rejected':
                $status = 'rejected';
                $this->legend = __('Rejected posts');
                break;
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
                Post::where('status', $status)
            )
                ->between(
                    start: $start,
                    end: $end,
                )
                ->perMonth()
                ->count();
        } else {
            $data = Trend::query(
                Post::where('user_id', '=', auth()->user()->id)
                    ->where('status', $status)
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
            Select::make('Status')
                ->translateLabel()
                ->options(([
                    __('In process') => [
                        'draft' => __('Draft'),
                        'reviewing' => __('Reviewing'),
                    ],
                    __('Reviewed') => [
                        'published' => __('Published'),
                        'rejected' => __('Rejected'),
                    ],
                ]))
                ->default('published')
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
