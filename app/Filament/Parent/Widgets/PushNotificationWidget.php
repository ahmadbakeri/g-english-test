<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\Widget;

class PushNotificationWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.parent.widgets.push-notification-widget';
    protected int | string | array $columnSpan = 'full';
}
