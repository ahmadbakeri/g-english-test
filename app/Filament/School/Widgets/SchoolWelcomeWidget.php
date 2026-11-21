<?php

namespace App\Filament\School\Widgets;

use Filament\Widgets\Widget;

class SchoolWelcomeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -2;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.school.widgets.school-welcome-widget';
}
