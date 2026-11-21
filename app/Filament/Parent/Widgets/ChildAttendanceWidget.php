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
        $childStudentIds = $user->children->pluck('id')->toArray();
        $childUserIds = $user->children->pluck('user_id')->filter()->toArray();

        return $table
            ->query(
                \App\Models\Attendance::query()
                    ->with(['student', 'user'])
                    ->where(function ($q) use ($childStudentIds, $childUserIds) {
                        $q->whereIn('student_id', $childStudentIds);
                        if (!empty($childUserIds)) {
                            $q->orWhereIn('user_id', $childUserIds);
                        }
                    })
                    ->latest('date')
                    ->latest('time_in')
            )
            ->columns([
                Tables\Columns\TextColumn::make('student_name')
                    ->label('Nama Siswa')
                    ->getStateUsing(fn (\App\Models\Attendance $record) => $record->student?->name ?? $record->user?->name ?? '-'),
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
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'present' => 'success',
                        'late' => 'warning',
                        'absent' => 'danger',
                        'excused' => 'info',
                        default => 'gray',
                    }),
            ]);
    }
}
