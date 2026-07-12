<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class ImageCropPositioner extends Field
{
    protected string $view = 'filament.forms.components.image-crop-positioner';

    protected string $imageField = 'photo';

    public function imageField(string $field): static
    {
        $this->imageField = $field;
        return $this;
    }

    public function getImageField(): string
    {
        return $this->imageField;
    }
}
