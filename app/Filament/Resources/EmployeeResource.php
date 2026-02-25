<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Funcionários';
    protected static ?string $pluralModelLabel = 'Funcionários';
    protected static ?string $modelLabel = 'Funcionário';
    protected static ?string $navigationGroup = 'Funcionários';
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Dados do Funcionário')
                ->tabs([
                    Tab::make('Informações Básicas')
                        ->schema([
                            Forms\Components\TextInput::make('first_name')
                                ->label('Primeiro Nome')
                                ->required()
                                ->maxLength(40),
                            Forms\Components\TextInput::make('middle_name')
                                ->label('Nome do Meio')
                                ->maxLength(40),
                            Forms\Components\TextInput::make('last_name')
                                ->label('Último Nome')
                                ->required()
                                ->maxLength(40),
                            Forms\Components\Select::make('gender')
                                ->options([
                                    'male' => 'Masculino',
                                    'female' => 'Feminino',
                                    'n/a' => 'N/A',
                                ])
                                ->required()
                                ->label('Gênero')
                                ->native(false),
                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email('rfc')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->rules([new \App\Rules\ValidEmailDomain()]),
                            Forms\Components\TextInput::make('phone_number')
                                ->label('Telefone')
                                ->maxLength(13)
                                ->required(),
                        ])
                        ->columns(2),

                    Tab::make('Identificação')
                        ->schema([
                            Forms\Components\TextInput::make('nss')
                                ->label('NSS')
                                ->required()
                                ->maxLength(9),
                            Forms\Components\TextInput::make('nif')
                                ->label('NIF')
                                ->required()
                                ->maxLength(9),
                            Forms\Components\DatePicker::make('date_of_birth')
                                ->label('Data de Nascimento')
                                ->required()
                                ->maxDate(now()->subYears(18))
                                ->native(false),
                            Forms\Components\Textarea::make('observations')
                                ->label('Observações')
                                ->rows(3)
                                ->columnSpan(2),
                        ])
                        ->columns(2),

                    Tab::make('Localização')
                        ->schema([
                            Forms\Components\Select::make('country_id')
                                ->relationship('country', 'name')
                                ->searchable()
                                ->required()
                                ->preload()
                                ->label('País'),

                            Forms\Components\Select::make('state_id')
                                ->options(
                                    fn($get) =>
                                    $get('country_id')
                                        ? \App\Models\State::where('country_id', $get('country_id'))->pluck('name', 'id')
                                        : []
                                )
                                ->searchable()
                                ->required()
                                ->preload()
                                ->label('Estado'),

                            Forms\Components\Select::make('city_id')
                                ->options(
                                    fn($get) =>
                                    $get('state_id')
                                        ? \App\Models\City::where('state_id', $get('state_id'))->pluck('name', 'id')
                                        : []
                                )
                                ->searchable()
                                ->required()
                                ->preload()
                                ->label('Cidade'),

                            Forms\Components\Select::make('department_id')
                                ->relationship('department', 'name')
                                ->searchable()
                                ->required()
                                ->label('Departamento'),

                            Forms\Components\Select::make('designation_id')
                                ->relationship('designation', 'name')
                                ->searchable()
                                ->nullable()
                                ->preload()
                                ->label('Cargo / Designação'),
                        ])
                        ->columns(2),

                    Tab::make('Endereço')
                        ->schema([
                            Forms\Components\TextInput::make('address')
                                ->label('Endereço')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('zip_code')
                                ->label('Código Postal')
                                ->required()
                                ->maxLength(10),
                        ])
                        ->columns(2),

                    Tab::make('Contratação')
                        ->schema([
                            Forms\Components\DatePicker::make('date_hired')
                                ->label('Data de Contratação')
                                ->required()
                                ->maxDate(now()->subYear(18))
                                ->native(false),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Ativo')
                                ->default(true),
                        ])
                        ->columns(2),
                ])
                ->columnSpan('full'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label('Primeiro Nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('last_name')->label('Último Nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('department.name')->label('Departamento')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('date_hired')->label('Data de Contratação')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->authorize(fn() => auth()->user()?->role === 'admin')
                    ->requiresConfirmation(),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->authorize(fn() => auth()->user()?->role === 'admin')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make()
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->authorize(fn() => auth()->user()?->role === 'admin'),
                Tables\Actions\ForceDeleteBulkAction::make()
                    ->visible(fn() => auth()->user()?->role === 'admin')
                    ->authorize(fn() => auth()->user()?->role === 'admin'),
            ]);
    }

    public static function infolists(): Infolist
    {
        return Infolist::make()->schema([
            Section::make('Dados Pessoais')->schema([
                TextEntry::make('first_name')->label('Primeiro Nome'),
                TextEntry::make('last_name')->label('Último Nome'),
                TextEntry::make('email')->label('E-mail'),
                TextEntry::make('department.name')->label('Departamento'),
                TextEntry::make('date_hired')->label('Data de Contratação'),
            ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // Aqui você pode adicionar ContractsRelationManager::class quando criar a relação
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            //'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
