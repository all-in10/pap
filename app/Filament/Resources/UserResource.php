<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\Access;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $navigationGroup = 'Gestão de Usuários';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),

            // email_verified_at is set automatically on create and is not editable via the form

            // Role select using the enum values
                Forms\Components\Select::make('role')
                ->label('Função')
                ->options([
                    UserRole::ROOT->value => 'Super Administrador',
                    UserRole::ADMIN->value => 'Administrador',
                    UserRole::HR->value => 'Recursos Humanos',
                    UserRole::EMPLOYEE->value => 'Funcionário',
                ])
                ->required()
                ->default(UserRole::EMPLOYEE->value),

            // Password: hash on save, optional on edit
                Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                ->dehydrated(fn($state) => filled($state))
                ->maxLength(255)
                ->helperText('Deixe em branco para manter a senha atual ao editar'),

            // Optional relation to Employee (if present)
            Forms\Components\Select::make('employee_id')
                ->label('Funcionário Relacionado')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->nullable(),

            // Must change password toggle
            Forms\Components\Toggle::make('must_change_password')
                ->label('Forçar alteração de senha')
                ->default(false),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Função')
                    ->formatStateUsing(function ($state) {
                        $map = [
                            UserRole::ROOT->value => 'Super Administrador',
                            UserRole::ADMIN->value => 'Administrador',
                            UserRole::HR->value => 'Recursos Humanos',
                            UserRole::EMPLOYEE->value => 'Funcionário',
                        ];

                        if ($state instanceof \BackedEnum) {
                            $key = $state->value;
                        } elseif (is_string($state) || is_int($state) || is_float($state)) {
                            $key = (string) $state;
                        } else {
                            $key = '';
                        }

                        $label = $map[$key] ?? $key;

                        $colorMap = [
                            UserRole::ROOT->value => ['label' => 'Super Administrador', 'color' => '#414833'],
                            UserRole::ADMIN->value => ['label' => 'Administrador', 'color' => '#7f4f24'],
                            UserRole::HR->value => ['label' => 'Recursos Humanos', 'color' => '#b6ad90'],
                            UserRole::EMPLOYEE->value => ['label' => 'Funcionário', 'color' => '#a4ac86'],
                        ];

                        $entry = $colorMap[$key] ?? ['label' => $label, 'color' => '#6b7280'];

                        return sprintf(
                            '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background: %s; color: #ffffff;">%s</span>',
                            $entry['color'],
                            e($entry['label'])
                        );
                    })
                    ->html()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('must_change_password')
                    ->label('Forçar alteração de senha')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-s-x-circle')
                    ->sortable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
