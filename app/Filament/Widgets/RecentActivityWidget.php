<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ActivityLog;

class RecentActivityWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function getHeading(): string
    {
        return __('Today\'s Access Activity');
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Recent Activity'))
            ->modelLabel(__('activity log'))
            ->pluralModelLabel(__('activity logs'))
            ->query(
                ActivityLog::query()->with('user')
                    ->whereDate('created_at', today())
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('No'))
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.role')
                    ->label(__('Role'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'tutor' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('page')
                    ->label(__('Page Opened'))
                    ->limit(30),
                Tables\Columns\TextColumn::make('ip')
                    ->label(__('IP Address')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Time'))
                    ->dateTime('H:i:s')
                    ->sortable(),
                ])->from('md'),
            ])
            ->paginated(false);
    }
}
