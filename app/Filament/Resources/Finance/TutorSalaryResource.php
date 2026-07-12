<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Resources\Finance\TutorSalaryResource\Pages;
use App\Models\TutorSalary;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
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
        return __('Accounting');
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
            \Filament\Schemas\Components\Section::make(__('Salary Data'))->schema([
                Forms\Components\Select::make('tutor_id')->label(__('Tutor'))->translateLabel()
                    ->options(\App\Models\Tutor::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()->required(),
                Forms\Components\Select::make('month')->label(__('Month'))
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ])->required()->default(now()->month),
                Forms\Components\Select::make('year')->label(__('Year'))
                    ->options(array_combine(range(now()->year - 2, now()->year + 2), range(now()->year - 2, now()->year + 2)))
                    ->required()->default(now()->year),
                Forms\Components\TextInput::make('total_jp')->label(__('Total JP'))->numeric()->required()->default(0)
                    ->helperText('Diisi secara manual atau akan otomatis dihitung saat disimpan (opsional)'),
                Forms\Components\TextInput::make('total_amount')->label(__('Total Salary (Rp)'))->numeric()->required()->default(0),
                Forms\Components\Select::make('status')->label(__('Status'))
                    ->options([
                        'pending' => '⏳ Pending',
                        'paid' => '✅ Dibayar',
                    ])->default('pending')->required(),
                Forms\Components\DatePicker::make('paid_at')->label(__('Paid Date'))->nullable(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
            Tables\Columns\TextColumn::make('tutor.name')->label(__('Tutor'))->translateLabel()->searchable()->sortable(),
            Tables\Columns\TextColumn::make('month')->label(__('Month'))
                ->formatStateUsing(fn (int $state): string => [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ][$state] ?? (string)$state),
            Tables\Columns\TextColumn::make('year')->label(__('Year'))->sortable(),
            Tables\Columns\TextColumn::make('total_jp')->label(__('Total JP'))->numeric(),
            Tables\Columns\TextColumn::make('total_amount')->label(__('Total Salary'))->money('IDR', locale: 'id'),
            Tables\Columns\TextColumn::make('status')->label(__('Status'))->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'paid' => 'success',
                }),
            Tables\Columns\TextColumn::make('paid_at')->label(__('Paid Date'))->date('d/m/Y')->toggleable(),
            ])->from('md'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'paid' => 'Dibayar']),
            Tables\Filters\SelectFilter::make('tutor_id')->label(__('Tutor'))->translateLabel()->options(\App\Models\Tutor::pluck('name', 'id')),
        ])
        ->actions([
            \Filament\Actions\Action::make('calculate')
                ->label(__('Calculate Automatically'))
                ->icon('heroicon-o-calculator')
                ->color('info')
                ->action(function (\App\Models\TutorSalary $record) {
                    // Create start and end dates based on the selected month/year.
                    // The period is from the 26th of the previous month to the 25th of the selected month.
                    $currentMonthDate = \Carbon\Carbon::create($record->year, $record->month, 1);
                    
                    $startDate = $currentMonthDate->copy()->subMonth()->day(26)->startOfDay();
                    $endDate = $currentMonthDate->copy()->day(25)->endOfDay();

                    // Calculate based on validated journals for this tutor within the period
                    $totalJp = \App\Models\Journal::where('tutor_id', $record->tutor_id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'validated')
                        ->sum('jp_count');
                    
                    $ratePerJp = 25000; // Default C grade
                    if ($record->tutor && $record->tutor->grade === 'A') $ratePerJp = 50000;
                    if ($record->tutor && $record->tutor->grade === 'B') $ratePerJp = 35000;
                    if ($record->tutor && $record->tutor->grade === 'C') $ratePerJp = 25000;
                    
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
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTutorSalaries::route('/'),
        ];
    }
}



