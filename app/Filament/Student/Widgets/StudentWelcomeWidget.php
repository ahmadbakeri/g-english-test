<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\Widget;

class StudentWelcomeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.student.widgets.student-welcome-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = -1;
}
