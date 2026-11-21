<?php

namespace App\Filament\Resources\Operations\Admissions;

use App\Filament\Resources\Operations\Admissions\Pages\ManageAdmissions;
use App\Models\Admission;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmissionResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return __('Admissions');
    }

    public static function getModelLabel(): string
    {
        return __('Admission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Admissions');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    protected static ?string $model = Admission::class;
    public static function getNavigationGroup(): ?string
    {
        return __('Administration');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make(__('Student Data'))->schema([
                    TextInput::make('registration_number')
                        ->label(__('Registration No.'))
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('student_type')
                        ->label(__('Student Type'))
                        ->options([
                            'internal' => __('Internal'),
                            'external' => __('External'),
                        ])
                        ->default('internal')
                        ->required(),
                    TextInput::make('name')
                        ->label(__('Full Name'))
                        ->required(),
                    TextInput::make('birth_place')
                        ->label(__('Place of Birth')),
                    DatePicker::make('birth_date')
                        ->label(__('Date of Birth')),
                    Select::make('gender')
                        ->label(__('Gender'))
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ]),
                    TextInput::make('religion')
                        ->label(__('Religion')),
                    TextInput::make('email')
                        ->label(__('Email'))->translateLabel()
                        ->email(),
                    TextInput::make('phone')
                        ->label(__('Phone / WhatsApp No.'))
                        ->tel(),
                    TextInput::make('social_media')
                        ->label(__('Social Media Account')),
                    Textarea::make('address')
                        ->label(__('Address'))
                        ->columnSpanFull(),
                    Select::make('education_level')
                        ->label(__('Education Level'))
                        ->options([
                            'SD' => 'SD',
                            'SMP' => 'SMP',
                            'SMA' => 'SMA',
                            'Umum' => 'Umum',
                        ]),
                    TextInput::make('course_level')
                        ->label(__('Course Level')),
                    TextInput::make('class_name')
                        ->label(__('Class (Kelas)')),
                    TextInput::make('school_name')
                        ->label(__('School Name')),
                ])->columns(2),
                \Filament\Schemas\Components\Section::make(__('Parent Data'))->schema([
                    \Filament\Schemas\Components\Fieldset::make(__('Mother\'s Data'))->schema([
                        TextInput::make('mother_name')
                            ->label(__('Mother\'s Name')),
                        TextInput::make('mother_phone')
                            ->label(__('Mother\'s Phone'))
                            ->tel(),
                        TextInput::make('mother_email')
                            ->label(__('Mother\'s Email'))
                            ->email(),
                        TextInput::make('mother_job')
                            ->label(__('Mother\'s Occupation')),
                    ])->columns(2),
                    \Filament\Schemas\Components\Fieldset::make(__('Father\'s Data'))->schema([
                        TextInput::make('father_name')
                            ->label(__('Father\'s Name')),
                        TextInput::make('father_phone')
                            ->label(__('Father\'s Phone'))
                            ->tel(),
                        TextInput::make('father_email')
                            ->label(__('Father\'s Email'))
                            ->email(),
                        TextInput::make('father_job')
                            ->label(__('Father\'s Occupation')),
                    ])->columns(2),
                ]),
                \Filament\Schemas\Components\Section::make(__('Financial & Admission Status'))
                    ->description(__('Tutoring Fee (60x meetings, 3x/week). Includes Uniform and Books.'))
                    ->schema([
                    \Filament\Forms\Components\TextInput::make('tuition_fee')
                        ->label(__('Tuition Fee'))
                        ->numeric()
                        ->default(4000000)
                        ->disabled()
                        ->dehydrated(),
                    \Filament\Forms\Components\TextInput::make('registration_fee')
                        ->label(__('Registration Fee'))
                        ->numeric()
                        ->default(100000)
                        ->disabled()
                        ->dehydrated(),
                    \Filament\Forms\Components\Toggle::make('has_discount')
                        ->label(__('Apply 25% Discount'))
                        ->live()
                        ->afterStateUpdated(function ($set, $state, $get) {
                            $tuition = 4000000;
                            if ($state) {
                                $tuition = 3000000;
                            }
                            $set('tuition_fee', $tuition);
                            $set('total_fee', $tuition + 100000);
                        }),
                    \Filament\Forms\Components\TextInput::make('total_fee')
                        ->label(__('Total Fee'))
                        ->numeric()
                        ->default(4100000)
                        ->disabled()
                        ->dehydrated(),
                    \Filament\Forms\Components\Select::make('payment_method')
                        ->label(__('Payment Method'))
                        ->options([
                            'full' => __('Full Payment'),
                            'installment' => __('Installment (4x)'),
                        ])
                        ->required()
                        ->live(),
                    \Filament\Forms\Components\TextInput::make('amount_paid')
                        ->label(__('Amount Paid (DP / Full)'))
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->helperText(fn ($get) => $get('payment_method') === 'installment' ? __('Minimum DP is Rp 600.000') : __('Enter the full amount'))
                        ->rule(function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                if ($get('payment_method') === 'installment' && $value < 600000) {
                                    $fail(__('For installments, the minimum payment is Rp 600.000 (DP + Registration).'));
                                }
                                if ($get('payment_method') === 'full' && $value < $get('total_fee')) {
                                    $fail(__('For full payment, the amount must equal the Total Fee.'));
                                }
                            };
                        }),
                    \Filament\Forms\Components\Select::make('status')
                        ->label(__('Status'))
                        ->options([
                            'pending' => __('Pending'),
                            'approved' => __('Approved'),
                            'rejected' => __('Rejected'),
                        ])
                        ->required()
                        ->default('pending'),
                    Textarea::make('notes')
                        ->label(__('Internal Notes'))
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('registration_number')
                    ->label(__('Registration No.'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('Name'))->translateLabel()
                    ->searchable(),
                TextColumn::make('student_type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'external' ? 'warning' : 'info')
                    ->formatStateUsing(fn (?string $state): string => $state === 'external' ? __('External') : __('Internal')),
                TextColumn::make('phone')
                    ->label(__('WhatsApp No.'))
                    ->searchable(),
                TextColumn::make('previous_school')
                    ->label(__('Previous School'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label(__('Registration Date'))
                    ->dateTime('d M Y')
                    ->sortable(),
                ])->from('md'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('student_type')
                    ->label(__('Student Type'))
                    ->options([
                        'internal' => __('Internal'),
                        'external' => __('External'),
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                    ]),
            ])
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make('table')->fromTable(),
                ]),
            ])
            ->recordActions([
                EditAction::make()->label(__('Edit')),
                DeleteAction::make()->label(__('Delete')),
                \Filament\Actions\Action::make('approve')
                    ->label(__('Accept & Create Student'))
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Admission $record) => $record->status === 'pending' && $record->amount_paid >= 600000)
                    ->action(function (Admission $record) {
                        $record->update(['status' => 'approved']);
                        
                        // Generate sequential NIS (e.g., GES481103 -> GES481104)
                        $lastStudent = \App\Models\Student::where('nis', 'like', 'GES%')
                            ->orderBy('nis', 'desc')
                            ->first();

                        if ($lastStudent && preg_match('/^GES(\d+)$/', $lastStudent->nis, $matches)) {
                            $nextNumber = intval($matches[1]) + 1;
                            $newNis = 'GES' . $nextNumber;
                        } else {
                            $newNis = 'GES481101'; // Default starting point if none exists
                        }

                        // Buat data siswa
                        \App\Models\Student::create([
                            'name' => $record->name,
                            'nis' => $newNis,
                            'student_type' => $record->student_type ?? 'internal',
                            'gender' => $record->gender ?? 'L',
                            'birth_date' => $record->birth_date,
                            'address' => $record->address,
                            'phone' => $record->phone,
                            'mother_name' => $record->mother_name,
                            'mother_phone' => $record->mother_phone,
                            'mother_email' => $record->mother_email,
                            'mother_job' => $record->mother_job,
                            'father_name' => $record->father_name,
                            'father_phone' => $record->father_phone,
                            'father_email' => $record->father_email,
                            'father_job' => $record->father_job,
                            'birth_place' => $record->birth_place,
                            'religion' => $record->religion,
                            'social_media' => $record->social_media,
                            'education_level' => $record->education_level,
                            'course_level' => $record->course_level,
                            'class_name' => $record->class_name,
                            'school_name' => $record->school_name,
                            'is_active' => true,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title(__('Student successfully admitted and student data created.'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('Delete')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAdmissions::route('/'),
        ];
    }
}


