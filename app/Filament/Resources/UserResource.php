<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilizadores';
    protected static ?string $modelLabel = 'Utilizador';
    protected static ?string $navigationGroup = 'Gerenciamento do Sistema';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->maxLength(255),

            

            Forms\Components\DateTimePicker::make('email_verified_at')->label('E-mail Verificado Em'),

            Forms\Components\TextInput::make('password')
                ->label('Senha')
                ->password()
                ->maxLength(255)
                ->helperText('Se vazio, será atribuída a senha padrão e o utilizador será forçado a alterá-la no primeiro acesso.')
                ->dehydrateStateUsing(fn($state) => $state ? \Illuminate\Support\Facades\Hash::make($state) : null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('E-mail Verificado Em')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('role')
                    ->label('Função')
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('forcePasswordChange')
                    ->label('Forçar troca de senha')
                    ->icon('heroicon-o-key')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Forçar troca de senha')
                    ->modalDescription('Irá definir a senha padrão e forçar o utilizador a alterá-la no primeiro acesso.')
                    ->action(function ($record) {
                        $default = env('DEFAULT_USER_PASSWORD', 'ChangeMe123!');

                        $record->password = \Illuminate\Support\Facades\Hash::make($default);
                        $record->must_change_password = true;
                        $record->password_changed_at = null;
                        $record->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Ação realizada')
                            ->success()
                            ->body('Senha padrão definida e usuário forçado a alterar no próximo login.')
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('forcePasswordChange')
                        ->label('Forçar troca de senha')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $default = env('DEFAULT_USER_PASSWORD', 'ChangeMe123!');

                            foreach ($records as $record) {
                                $record->password = \Illuminate\Support\Facades\Hash::make($default);
                                $record->must_change_password = true;
                                $record->password_changed_at = null;
                                $record->save();
                            }

                            \Filament\Notifications\Notification::make()
                                ->title(count($records) . ' utilizadores atualizados')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
