<?php

namespace App\Filament\Parent\Resources\StudentPayments;

use App\Filament\Parent\Resources\StudentPayments\Pages\ManageStudentPayments;
use App\Models\StudentPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class StudentPaymentResource extends Resource
{
    protected static ?string $model = StudentPayment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Kwitansi';
    protected static ?string $pluralModelLabel = 'Kwitansi Pembayaran';
    protected static ?string $modelLabel = 'Kwitansi';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereIn('student_id', auth()->user()->children->pluck('id'))->latest('year')->latest('month');
            })
            ->columns([
                TextColumn::make('student.name')
                    ->label('Nama Anak')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')->label('Keterangan')->wrap(),
                TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y')->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('paid_at')
                    ->label('Tanggal Bayar')
                    ->date()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\Action::make('print')
                    ->label('Download PDF')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->visible(fn (StudentPayment $record) => $record->status === 'paid')
                    ->url(fn (StudentPayment $record) => route('report.student-payment', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStudentPayments::route('/'),
        ];
    }
}

