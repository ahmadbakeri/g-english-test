<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Resources\Finance\TutorSalaryResource\Pages;
use App\Models\TutorSalary;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TutorSalaryResource extends Resource
{
    protected static ?string $model = TutorSalary::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    public static function getNavigationLabel(): string { return __('Tutor Salaries'); }
    public static function getModelLabel(): string { return __('Salary'); }
    public static function getPluralModelLabel(): string { return __('Salary'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                \Filament\Schemas\Components\Section::make(__('Salary Data'))
                    ->description('Rekapitulasi jam pelajaran (JP) tervalidasi dan perhitungan honor bulanan pengajar.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\Select::make('tutor_id')
                            ->label(__('Tutor'))
                            ->translateLabel()
                            ->options(\App\Models\Tutor::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('month')
                            ->label(__('Month'))
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->required()
                            ->default(now()->month),
                        Forms\Components\Select::make('year')
                            ->label(__('Year'))
                            ->options(array_combine(range(now()->year - 2, now()->year + 2), range(now()->year - 2, now()->year + 2)))
                            ->required()
                            ->default(now()->year),
                        Forms\Components\TextInput::make('total_jp')
                            ->label(__('Total JP'))
                            ->numeric()
                            ->suffix('JP')
                            ->required()
                            ->default(0)
                            ->helperText('Diisi secara manual atau dihitung otomatis dari jurnal tervalidasi.'),
                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total Salary (Rp)'))
                            ->prefix('Rp')
                            ->numeric()
                            ->required()
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Dibayar',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\DatePicker::make('paid_at')
                            ->label(__('Paid Date'))
                            ->nullable(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
        ->recordAction('edit')
        ->columns([
                Tables\Columns\TextColumn::make('tutor.name')
                    ->label(__('Tutor'))
                    ->translateLabel()
                    ->weight('bold')
                    ->description(fn (TutorSalary $record): string => 'Periode: ' . ($months[$record->month] ?? $record->month) . ' ' . $record->year)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_jp')
                    ->label(__('Total JP'))
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->suffix(' JP'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total Salary'))
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) ($state ?? 0), 0, ',', '.'))
                    ->weight('bold')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'paid' ? 'Dibayar' : 'Pending')
                    ->description(fn (TutorSalary $record): ?string => $record->paid_at ? 'Dibayar ' . $record->paid_at->format('d/m/Y') : null)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('month')
                    ->label(__('Month'))
                    ->formatStateUsing(fn (int $state): string => $months[$state] ?? (string) $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('year')
                    ->label(__('Year'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label(__('Paid Date'))
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'paid' => 'Dibayar']),
            Tables\Filters\SelectFilter::make('tutor_id')->label(__('Tutor'))->translateLabel()->options(\App\Models\Tutor::pluck('name', 'id')),
        ])
        ->actions([
            \Filament\Actions\EditAction::make()
                ->button()
                ->size('sm'),
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\Action::make('calculate')
                    ->label(__('Calculate Automatically'))
                    ->icon('heroicon-o-calculator')
                    ->color('info')
                    ->action(function (\App\Models\TutorSalary $record) {
                        $currentMonthDate = \Carbon\Carbon::create($record->year, $record->month, 1);
                        $startDate = $currentMonthDate->copy()->subMonth()->day(26)->startOfDay();
                        $endDate = $currentMonthDate->copy()->day(25)->endOfDay();

                        $totalJp = \App\Models\Journal::where('tutor_id', $record->tutor_id)
                            ->whereBetween('date', [$startDate, $endDate])
                            ->where('status', 'validated')
                            ->sum('jp_count');

                        $ratePerJp = 25000;
                        if ($record->tutor && $record->tutor->grade === 'A') {
                            $ratePerJp = 50000;
                        }
                        if ($record->tutor && $record->tutor->grade === 'B') {
                            $ratePerJp = 35000;
                        }
                        if ($record->tutor && $record->tutor->grade === 'C') {
                            $ratePerJp = 25000;
                        }

                        $record->update([
                            'total_jp' => $totalJp,
                            'total_amount' => $totalJp * $ratePerJp,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('Successfully calculated automatically!'))
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('print')
                    ->label(__('Print Payslip'))
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (\App\Models\TutorSalary $record) => route('report.tutor-salary', $record->id))
                    ->openUrlInNewTab(),
                \Filament\Actions\EditAction::make(),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Aksi'),
        ])
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['tutor']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTutorSalaries::route('/'),
        ];
    }
}
