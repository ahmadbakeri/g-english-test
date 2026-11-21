<?php

namespace App\Filament\Parent\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ChildAttendanceWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 0;
    protected static ?string $heading = 'Riwayat Presensi Anak';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $childUserIds = $user->children->pluck('user_id')->toArray();

        return $table
            ->query(
                \App\Models\Attendance::query()
                    ->whereIn('user_id', $childUserIds)
                    ->latest('date')
                    ->latest('time_in')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_in')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_out')
                    ->label('Jam Keluar')
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Present' => 'success',
                        'Late' => 'warning',
                        'Absent' => 'danger',
                        'Excused' => 'info',
                        default => 'gray',
                    }),
            ]);
    }
}
