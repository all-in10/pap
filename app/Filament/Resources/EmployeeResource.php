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

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Funcionários';
    protected static ?string $pluralModelLabel = 'Funcionários';
    protected static ?string $modelLabel = 'Funcionário';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Localização')
                ->description('Selecione país, estado e cidade')
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
                        ->required(),

                    Forms\Components\Select::make('designation_id')
                        ->relationship('designation', 'name')
                        ->searchable()
                        ->nullable()
                        ->preload()
                        ->label('Cargo / Designação'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Dados Pessoais')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->label('Primeiro Nome')->required()->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')->label('Nome do Meio')->maxLength(255),
                    Forms\Components\TextInput::make('last_name')->label('Último Nome')->required()->maxLength(255),
                    Forms\Components\Select::make('gender')
                        ->options([
                            'male' => 'Masculino',
                            'female' => 'Feminino',
                            'n/a' => 'N/A',
                        ])
                        ->required()
                        ->label('Gênero')
                        ->native(false),
                    Forms\Components\TextInput::make('email')->label('E-mail')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('nss')->label('NSS')->required()->maxLength(20),
                    Forms\Components\TextInput::make('nif')->label('NIF')->maxLength(20),
                    Forms\Components\TextInput::make('phone_number')->label('Telefone')->maxLength(20),
                    Forms\Components\Textarea::make('observations')->label('Observações')->rows(3),
                ])
                ->columns(2),

            Forms\Components\Section::make('Endereço')
                ->schema([
                    Forms\Components\TextInput::make('address')->label('Endereço')->required()->maxLength(255),
                    Forms\Components\TextInput::make('zip_code')->label('Código Postal')->required()->maxLength(10),
                ])
                ->columns(2),

            Forms\Components\Section::make('Datas')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->label('Data de Nascimento')
                        ->required()
                        ->maxDate(now()->subYears(18))
                        ->native(false),
                    Forms\Components\DatePicker::make('date_hired')
                        ->label('Data de Contratação')
                        ->required()
                        ->maxDate(now())
                        ->native(false),
                ])
                ->columns(2),

            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                ])
                ->columns(1),
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
