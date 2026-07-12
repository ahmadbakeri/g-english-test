<?php

namespace App\Filament\Tutor\Widgets;

use Filament\Widgets\Widget;

class TutorWelcomeWidget extends Widget
{
    protected string $view = 'filament.tutor.widgets.tutor-welcome-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;
}
