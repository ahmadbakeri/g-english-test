<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class FinancialReport extends Page
{
    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'heroicon-o-currency-dollar';
    }

    public static function getNavigationLabel(): string
    {
        return __('Financial Report');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('Financial Report');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    protected string $view = 'filament.pages.financial-report';

    public $month;
    public $year;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function getIncomeProperty()
    {
        return \App\Models\StudentPayment::where('status', 'paid')
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->sum('amount');
    }

    public function getExpenseProperty()
    {
        return \App\Models\TutorSalary::where('status', 'paid')
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->sum('total_amount');
    }

    public function getProfitProperty()
    {
        return $this->income - $this->expense;
    }
}
