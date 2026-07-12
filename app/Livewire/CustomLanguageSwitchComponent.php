<?php

declare(strict_types=1);

namespace App\Livewire;

use BezhanSalleh\LanguageSwitch\Http\Livewire\LanguageSwitchComponent;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

class CustomLanguageSwitchComponent extends LanguageSwitchComponent
{
    public function changeLocale(string $locale): void
    {
        LanguageSwitch::switchLocale(locale: $locale);

        // Dispatch a browser event to trigger the custom frontend animation
        $this->dispatch('language-changed', locale: $locale);
    }
}
