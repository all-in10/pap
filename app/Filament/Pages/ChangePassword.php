<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Change Password';
    protected static string $view = 'filament.pages.change-password';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Change Your Password')
                ->description('You must change your password before continuing.')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('New Password')
                        ->password()
                        ->required()
                        ->minLength(8)
                        ->confirmed()
                        ->rules(['required', 'min:8', 'confirmed']),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required(),
                ]),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getActions(): array
    {
        return [
            Actions\Action::make('changePassword')
                ->label('Change Password')
                ->color('primary')
                ->size('lg')
                ->action('submit'),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user) {
            $user->update([
                'password' => Hash::make($data['password']),
                'must_change_password' => false,
            ]);
        }

        \session()->flash('success', 'Password changed successfully!');
        redirect('/admin');
    }
}


