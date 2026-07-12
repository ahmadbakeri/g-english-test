<?php
namespace App\Filament\Resources\HR\AttendanceResource\Pages;
use App\Filament\Resources\HR\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    
    protected function getHeaderActions(): array 
    { 
        return [
            Actions\CreateAction::make()->label(__('Add Attendance'))
        ]; 
    }

    public function getTabs(): array
    {
        return [
            'semua' => \Filament\Schemas\Components\Tabs\Tab::make('Semua'),
            'siswa' => \Filament\Schemas\Components\Tabs\Tab::make('Siswa')
                ->modifyQueryUsing(fn ($query) => $query->where('role_type', 'student')),
            'tutor' => \Filament\Schemas\Components\Tabs\Tab::make('Tutor')
                ->modifyQueryUsing(fn ($query) => $query->where('role_type', 'tutor')),
            'karyawan' => \Filament\Schemas\Components\Tabs\Tab::make('Karyawan')
                ->modifyQueryUsing(fn ($query) => $query->where('role_type', 'employee')),
        ];
    }
}
