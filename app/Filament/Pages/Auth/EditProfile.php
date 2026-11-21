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
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 0.5rem; position: relative;">
                <button type="button" onclick="window.history.back()" style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 9999px; background-color: transparent; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; color: #6b7280; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.color=\'var(--primary-600)\'; this.style.borderColor=\'var(--primary-500)\'; this.style.boxShadow=\'0 4px 6px -1px rgba(0, 0, 0, 0.1)\';" onmouseout="this.style.color=\'#6b7280\'; this.style.borderColor=\'#e5e7eb\'; this.style.boxShadow=\'0 1px 2px 0 rgba(0, 0, 0, 0.05)\';">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <h1 style="font-size: 1.875rem; line-height: 2.25rem; font-weight: 800; letter-spacing: -0.025em; color: inherit; margin: 0; text-align: center;">
                    ' . parent::getHeading() . '
                </h1>
            </div>
        ');
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\ViewField::make('avatar_preview')
                    ->view('filament.components.profile-avatar')
                    ->columnSpanFull(),
                
                \Filament\Forms\Components\FileUpload::make('new_avatar')
                    ->label(__('Unggah Foto Baru'))
                    ->image()
                    ->disk('r2')
                    ->directory('avatars')
                    ->maxSize(2048)
                    ->columnSpanFull()
                    ->dehydrated(false),
                
                \Filament\Forms\Components\Hidden::make('delete_avatar')
                    ->default(false),
                
                \Filament\Forms\Components\ViewField::make('delete_avatar_btn')
                    ->view('filament.components.delete-avatar-btn')
                    ->columnSpanFull()
                    ->hidden(fn () => auth()->user()->avatar_url === null),

                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                \Filament\Forms\Components\TextInput::make('old_password')
                    ->password()
                    ->label(__('Password Lama'))
                    ->requiredWith('password')
                    ->revealable()
                    ->rule(function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            if (! \Illuminate\Support\Facades\Hash::check($value, auth()->user()->password)) {
                                $fail(__('Password lama yang Anda masukkan salah.'));
                            }
                        };
                    })
                    ->dehydrated(false),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();
        $oldAvatar = $user->avatar_url;
        
        $formState = $this->form->getState();
        $deleteAvatar = filter_var($data['delete_avatar'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $newAvatar = $formState['new_avatar'] ?? null;

        // Jika user mengunggah foto baru ATAU mengeklik hapus foto
        if ($newAvatar || $deleteAvatar) {
            // Hapus foto lama di R2
            if ($oldAvatar) {
                \Illuminate\Support\Facades\Storage::disk('r2')->delete($oldAvatar);
            }
            
            $data['avatar_url'] = $newAvatar ? $newAvatar : null;
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    public function backAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('back')
            ->label(__('filament-panels::auth/pages/edit-profile.actions.cancel.label'))
            ->url(filament()->getUrl())
            ->color('gray');
    }

    protected function afterSave(): void
    {
        $this->redirect(request()->header('Referer'), navigate: false);
    }
}
