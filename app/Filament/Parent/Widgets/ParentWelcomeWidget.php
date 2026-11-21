<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\Widget;

class ParentWelcomeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -2;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.parent.widgets.parent-welcome-widget';
}
