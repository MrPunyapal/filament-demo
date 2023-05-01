<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestComments extends BaseWidget
{
    protected array|string|int $columnSpan = 'full';
    
    protected function getTableQuery(): Builder
    {
        return Comment::query()->latest();
    }



    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('comment')->limit(50)->searchable()->sortable(),
        ];
    }
}
