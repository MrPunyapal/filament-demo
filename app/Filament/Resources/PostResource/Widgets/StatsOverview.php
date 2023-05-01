<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Posts', Post::count()),
            Card::make('Total Published Today', Post::whereDate('published_at', today())->count()),
            Card::make('Total Published This Month', Post::whereMonth('published_at', today())->count())
            ->description(now()->format('F'))->descriptionIcon('heroicon-o-calendar')
            ->descriptionColor('success')
        ];
    }
}
