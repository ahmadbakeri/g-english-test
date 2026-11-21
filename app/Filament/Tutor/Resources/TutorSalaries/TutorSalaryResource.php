<?php

namespace App\Filament\Tutor\Resources\TutorSalaries;

use App\Filament\Tutor\Resources\TutorSalaries\Pages\ManageTutorSalaries;
use App\Models\TutorSalary;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TutorSalaryResource extends Resource
{
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Accounting');
    }

    protected static ?string $model = TutorSalary::class;

    public static function getNavigationLabel(): string { return __('Salary Slip'); }
    public static function getPluralModelLabel(): string { return __('Salary Slip'); }
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['tutor'])->where('tutor_id', auth()->user()->tutor->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Rincian Slip Honor')
                    ->description('Informasi periode bulan, total jam pelajaran (JP), dan nominal honor.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Select::make('tutor_id')
                            ->relationship('tutor', 'name')
                            ->required(),
                        TextInput::make('month')
                            ->required()
                            ->numeric(),
                        TextInput::make('year')
                            ->required()
                            ->numeric(),
                        TextInput::make('total_jp')
                            ->required()
                            ->numeric()
                            ->suffix('JP')
                            ->default(0),
                        TextInput::make('total_amount')
                            ->required()
                            ->prefix('Rp')
                            ->numeric()
                            ->default(0),
                        TextInput::make('status')
                            ->required()
                            ->default('pending'),
                        DatePicker::make('paid_at'),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tutor.name')
                    ->label(__('Tutor')),
                TextEntry::make('month')
                    ->numeric(),
                TextEntry::make('year')
                    ->numeric(),
                TextEntry::make('total_jp')
                    ->numeric(),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('paid_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    TextColumn::make('tutor.name')
                        ->label(__('Tutor'))
                        ->weight('bold')
                        ->description(fn (TutorSalary $record): string => 'Periode: ' . ($months[$record->month] ?? $record->month) . ' ' . $record->year)
                        ->searchable(),
                    TextColumn::make('month')
                        ->label(__('Month'))
                        ->formatStateUsing(fn (int $state): string => $months[$state] ?? (string) $state)
                        ->sortable(),
                    TextColumn::make('year')
                        ->label(__('Year'))
                        ->numeric()
                        ->sortable(),
                    TextColumn::make('total_jp')
                        ->label(__('Total JP'))
                        ->numeric()
                        ->badge()
                        ->color('info')
                        ->suffix(' JP')
                        ->sortable(),
                    TextColumn::make('total_amount')
                        ->label(__('Total Salary'))
                        ->money('IDR', locale: 'id')
                        ->weight('bold')
                        ->alignEnd()
                        ->sortable(),
                    TextColumn::make('status')
                        ->label(__('Status'))
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => $state === 'paid' ? 'Dibayar' : 'Pending')
                        ->color(fn (string $state): string => match ($state) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            default => 'gray',
                        })
                        ->searchable(),
                    TextColumn::make('paid_at')
                        ->label(__('Paid Date'))
                        ->date('d M Y')
                        ->placeholder('-')
                        ->sortable(),
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    \Filament\Actions\Action::make('print')
                        ->label(__('Print Payslip'))
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn (TutorSalary $record) => route('report.tutor-salary', $record->id))
                        ->openUrlInNewTab(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('Aksi'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTutorSalaries::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
