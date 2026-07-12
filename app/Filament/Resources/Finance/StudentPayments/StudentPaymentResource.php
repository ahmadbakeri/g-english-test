<?php

namespace App\Filament\Resources\Finance\StudentPayments;

use App\Filament\Resources\Finance\StudentPayments\Pages\ManageStudentPayments;
use App\Models\StudentPayment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentPaymentResource extends Resource
{
    public static function getModelLabel(): string
    {
        return __('Student Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Student Payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Payments');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    protected static ?string $model = StudentPayment::class;
    public static function getNavigationGroup(): ?string
    {
        return __('Accounting');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label(__('Student'))
                    ->relationship('student', 'name')
                    ->searchable()
                    ->required(),
                Select::make('month')
                    ->label(__('Month'))
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ])
                    ->required(),
                TextInput::make('year')
                    ->label(__('Year'))
                    ->numeric()
                    ->default(now()->year)
                    ->required(),
                TextInput::make('amount')
                    ->label(__('Amount (Rp)'))
                    ->numeric()
                    ->default(250000)
                    ->required(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        'unpaid' => 'Belum Lunas',
                        'paid' => 'Lunas'
                    ])
                    ->default('unpaid')
                    ->required(),
                DateTimePicker::make('paid_at')
                    ->label(__('Payment Date')),
                Textarea::make('notes')
                    ->label(__('Notes'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('month')
                    ->label(__('Month'))
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        default => (string) $state,
                    })
                    ->sortable(),
                TextColumn::make('year')
                    ->label(__('Year'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('Amount'))->translateLabel()
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum Lunas',
                        'paid' => 'Lunas',
                        default => $state,
                    }),
                TextColumn::make('paid_at')
                    ->label(__('Payment Date'))
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                ])->from('md'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(['unpaid' => 'Belum Lunas', 'paid' => 'Lunas']),
                \Filament\Tables\Filters\SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->options(\App\Models\Student::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                \Filament\Actions\Action::make('pay')
                    ->label(__('Set as Paid'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (StudentPayment $record) => $record->status === 'unpaid')
                    ->action(function (StudentPayment $record) {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                    }),
                \Filament\Actions\Action::make('print')
                    ->label(__('Print Receipt'))
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->visible(fn (StudentPayment $record) => $record->status === 'paid')
                    ->url(fn (StudentPayment $record) => route('report.student-payment', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStudentPayments::route('/'),
        ];
    }
}


