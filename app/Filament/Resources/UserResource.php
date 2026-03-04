<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilizadores';
    protected static ?string $pluralModelLabel = 'Utilizadores';
    protected static ?string $modelLabel = 'Utilizador';
    protected static ?string $navigationGroup = 'Gerenciamento do Sistema';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Dados do Utilizador')
                ->tabs([
                    Tab::make('Informações Básicas')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nome')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email('rfc')
                                ->required()
                                ->maxLength(255)
                                ->rules([new \App\Rules\ValidEmailDomain()]),
                            
                            Forms\Components\Select::make('role')
                                ->label('Função')
                                ->options([
                                    'admin' => 'Administrador',
                                    'hr' => 'Recursos Humanos',
                                    'employee' => 'Funcionário',
                                ])
                                ->required()
                                ->columns(2),
                        ]),
                        
                    Tab::make('Segurança')
                        ->schema([
                            Forms\Components\TextInput::make('password')
                                ->label('Senha')
                                ->password()
                                ->maxLength(255)
                                ->minLength(8)
                                ->helperText('Se vazio, será atribuída a senha padrão e o utilizador será forçado a alterá-la no primeiro acesso.')
                                ->dehydrateStateUsing(function ($state) {
                                    return $state ?: null;
                                }),

                            Forms\Components\Toggle::make('must_change_password')
                                ->label('Forçar troca de senha no próximo acesso')
                                ->helperText('Quando ativado, o utilizador será obrigado a alterar a senha no próximo login.'),
                        ])
                        ->columns(2),
                ])
                ->columnSpan('full'),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Informações Básicas')
                ->schema([
                    TextEntry::make('name')
                        ->label('Nome'),

                    TextEntry::make('email')
                        ->label('E-mail'),

                    TextEntry::make('role')
                        ->label('Função')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'admin'    => 'Administrador',
                            'hr'       => 'Recursos Humanos',
                            'employee' => 'Funcionário',
                            default    => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'admin'    => 'danger',
                            'hr'       => 'warning',
                            'employee' => 'success',
                            default    => 'gray',
                        }),

                    TextEntry::make('email_verified_at')
                        ->label('E-mail Verificado Em')
                        ->badge()
                        ->formatStateUsing(fn ($state): string => $state
                            ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i')
                            : 'Não verificado'
                        )
                        ->color(fn ($state): string => $state ? 'success' : 'danger'),
                ])
                ->columns(2),

            Section::make('Segurança')
                ->schema([
                    IconEntry::make('must_change_password')
                        ->label('Forçar troca de senha')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')
                        ->falseIcon('heroicon-o-x-circle')
                        ->trueColor('warning')
                        ->falseColor('success'),
                ])
                ->columns(2),

            Section::make('Datas')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Criado em')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('updated_at')
                        ->label('Atualizado em')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(2),
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

                Tables\Columns\IconColumn::make('must_change_password')
                    ->label('Forçar troca de senha')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
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