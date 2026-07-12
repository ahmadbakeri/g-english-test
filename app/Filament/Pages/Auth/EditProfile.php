<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.history.back()" class="custom-back-btn" style="margin-bottom: 0;">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </button>
                <span>' . parent::getHeading() . '</span>
            </div>
        ');
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->label(__('Profile Picture'))
                    ->avatar()
                    ->alignCenter()
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'flex justify-center text-center hide-edit-btn'])
                    ->circleCropper()
                    ->imageCropAspectRatio('1:1')
                    ->automaticallyOpenImageEditorForAspectRatio()
                    ->directory('avatars')
                    ->maxSize(2048),
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    public function backAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('back')
            ->label(__('filament-panels::auth/pages/edit-profile.actions.cancel.label'))
            ->url(filament()->getUrl())
            ->color('gray');
    }
}
