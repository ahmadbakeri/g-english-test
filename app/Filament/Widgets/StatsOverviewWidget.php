<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Evaluation\TestScheduleRequestResource;
use App\Filament\Resources\Finance\StudentPayments\StudentPaymentResource;
use App\Filament\Resources\HR\ExternalStudentResource;
use App\Filament\Resources\HR\StudentResource;
use App\Models\CbtExam;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\TestScheduleRequest;
use App\Models\TestSession;
use App\Models\Tutor;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected int | array | null $columns = [
        'default' => 1,
        'sm' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $internalActive = Student::query()
            ->where('is_active', true)
            ->where(fn (Builder $q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->count();
        $internalTotal = Student::query()
            ->where(fn (Builder $q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->count();

        $externalCount = Student::where('student_type', 'external')->count();
        $schoolCount = User::where('role', 'school')->count();

        $pendingRequests = TestScheduleRequest::where('status', 'pending')->count();
        $activeSessions = TestSession::where('is_active', true)->count();
        $activeExams = CbtExam::where('is_active', true)->count();

        $now = now();
        $currentMonthIncome = (int) StudentPayment::query()
            ->where('status', 'paid')
            ->where(function (Builder $q) use ($now) {
                $q->where(fn (Builder $sub) => $sub->where('month', $now->month)->where('year', $now->year))
                    ->orWhere(fn (Builder $sub) => $sub->whereNull('month')->whereMonth('paid_at', $now->month)->whereYear('paid_at', $now->year));
            })
            ->sum('amount');

        $totalTutors = Tutor::where('is_active', true)->count();
        $totalStaff = User::whereIn('role', ['admin', 'tutor', 'kemenag', 'kemendikdasmen'])->count();

        return [
            Stat::make(__('Internal Students'), number_format($internalActive, 0, ',', '.'))
                ->description(__('Active of :total registered', ['total' => number_format($internalTotal, 0, ',', '.')]))
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->url(StudentResource::getUrl('index'))
                ->extraAttributes(['class' => 'stat-card stat-emerald']),

            Stat::make(__('External Students'), number_format($externalCount, 0, ',', '.'))
                ->description(__(':count partner schools', ['count' => number_format($schoolCount, 0, ',', '.')]))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->icon('heroicon-o-trophy')
                ->color('warning')
                ->url(ExternalStudentResource::getUrl('index'))
                ->extraAttributes(['class' => 'stat-card stat-amber']),

            Stat::make(
                __('Test Sessions & Schedule'),
                $pendingRequests > 0
                    ? $pendingRequests . ' ' . __('Pending')
                    : $activeSessions . ' ' . __('Active')
            )
                ->description("{$activeSessions} Sesi • {$activeExams} CBT")
                ->descriptionIcon('heroicon-m-server-stack')
                ->icon('heroicon-o-calendar-date-range')
                ->color($pendingRequests > 0 ? 'warning' : 'info')
                ->url(TestScheduleRequestResource::getUrl('index'))
                ->extraAttributes(['class' => 'stat-card stat-indigo']),

            Stat::make(__('This Month Income'), 'Rp ' . number_format($currentMonthIncome, 0, ',', '.'))
                ->description("{$totalTutors} Tutor • {$totalStaff} Staf")
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->url(StudentPaymentResource::getUrl('index'))
                ->extraAttributes(['class' => 'stat-card stat-sky']),
        ];
    }
}
