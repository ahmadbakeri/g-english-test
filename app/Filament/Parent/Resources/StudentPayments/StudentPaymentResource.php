<?php

namespace App\Filament\Parent\Resources\StudentPayments;

use App\Filament\Parent\Resources\StudentPayments\Pages\ManageStudentPayments;
use App\Models\StudentPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentPaymentResource extends Resource
{
    protected static ?string $model = StudentPayment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string { return __('Receipts'); }
    public static function getPluralModelLabel(): string { return __('Payment Receipts'); }
    public static function getModelLabel(): string { return __('Receipt'); }

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
        $children = auth()->user()->children;

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($children) {
                return $query
                    ->with(['student'])
                    ->whereIn('student_id', $children->pluck('id'))
                    ->latest('year')
                    ->latest('month');
            })
            ->columns([
                Split::make([
                    TextColumn::make('student.name')
                        ->label('Nama Anak')
                        ->weight('bold')
                        ->description(fn (StudentPayment $record): string => $record->notes ?: 'Tagihan SPP')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('due_date')
                        ->label('Jatuh Tempo')
                        ->date('d M Y')
                        ->placeholder('-')
                        ->sortable(),
                    TextColumn::make('amount')
                        ->label('Nominal')
                        ->money('IDR', locale: 'id')
                        ->weight('bold')
                        ->alignEnd()
                        ->sortable(),
                    TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'paid' => 'Lunas',
                            'unpaid' => 'Belum Lunas',
                            'pending' => 'Menunggu',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'paid' => 'success',
                            'unpaid' => 'danger',
                            'pending' => 'warning',
                            default => 'gray',
                        }),
                    TextColumn::make('paid_at')
                        ->label('Tanggal Bayar')
                        ->date('d M Y')
                        ->placeholder('-')
                        ->sortable(),
                ])->from('md'),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Pilih Anak')
                    ->options($children->pluck('name', 'id')->toArray()),
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Lunas',
                        'unpaid' => 'Belum Lunas',
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('print')
                    ->label('Download Kwitansi PDF')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->visible(fn (StudentPayment $record) => $record->status === 'paid')
                    ->url(fn (StudentPayment $record) => route('report.student-payment', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStudentPayments::route('/'),
        ];
    }
}
