<?php

namespace App\Filament\Resources\Finance\StudentPayments;

use App\Filament\Resources\Finance\StudentPayments\Pages\ManageStudentPayments;
use App\Models\StudentPayment;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                \Filament\Schemas\Components\Section::make('Detail Tagihan & Pembayaran SPP')
                    ->description('Informasi tagihan siswa, batas waktu pembayaran, nominal Rupiah, dan status pelunasan.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('due_date')
                            ->label(__('Due Date')),
                        TextInput::make('amount')
                            ->label(__('Amount (Rp)'))
                            ->prefix('Rp')
                            ->numeric()
                            ->default(250000)
                            ->required(),
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'unpaid' => 'Belum Lunas',
                                'paid' => 'Lunas',
                            ])
                            ->default('unpaid')
                            ->required(),
                        DateTimePicker::make('paid_at')
                            ->label(__('Payment Date'))
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    TextColumn::make('student.name')
                        ->label(__('Student'))
                        ->weight('bold')
                        ->description(fn (StudentPayment $record): string => $record->notes ?: 'Tagihan SPP')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('notes')
                        ->label(__('Description'))
                        ->placeholder('-')
                        ->wrap(),
                    TextColumn::make('due_date')
                        ->label(__('Due Date'))
                        ->date('d M Y')
                        ->sortable(),
                    TextColumn::make('amount')
                        ->label(__('Amount'))
                        ->translateLabel()
                        ->money('IDR', locale: 'id')
                        ->weight('bold')
                        ->alignEnd()
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
                        ->placeholder('-')
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
                ActionGroup::make([
                    EditAction::make(),
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
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['student']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStudentPayments::route('/'),
        ];
    }
}
