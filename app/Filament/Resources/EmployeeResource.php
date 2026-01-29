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
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Services\Access;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Funcionários';
    protected static ?string $pluralModelLabel = 'Funcionários';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $modelLabel = 'Funcionário';

    /**
     * Define o formulário para criação/edição de funcionários
     * Inclui seções para localização, dados pessoais, endereço, datas e status
     * Fluxo: campos reativos para país/estado/cidade, validações de email único, etc.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Localização')
                ->description('Selecione país, estado e cidade')
                ->schema([
                    Forms\Components\Select::make('country_id')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('País')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $country = \App\Models\Country::find($state);
                                if ($country) {
                                    $set('phone_number', '+' . $country->phonecode);
                                }
                            }
                        }),

                    Forms\Components\Select::make('state_id')
                        ->options(fn($get) => $get('country_id')
                            ? \App\Models\State::where('country_id', $get('country_id'))->pluck('name', 'id')
                            : [])
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Estado'),

                    Forms\Components\Select::make('city_id')
                        ->options(fn($get) => $get('state_id')
                            ? \App\Models\City::where('state_id', $get('state_id'))->pluck('name', 'id')
                            : [])
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Cidade'),

                    Forms\Components\Select::make('department_id')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Departamento'),

                    Forms\Components\Select::make('designation_id')
                        ->relationship('designation', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Cargo'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Dados Pessoais')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->label('Nome')->required()->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')->label('Nome do Meio')->maxLength(255)->nullable(),
                    Forms\Components\TextInput::make('last_name')->label('Sobrenome')->required()->maxLength(255),
                    Forms\Components\Select::make('gender')
                        ->label('Gênero')
                        ->options([
                            'male' => 'Masculino',
                            'female' => 'Feminino',
                            'n/a' => 'N/D',
                        ])
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('email')->label('E-mail')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('nss')->label('NSS')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true)
                        ->regex('/^[0-9]{9}$/', 'NSS deve conter 9 dígitos')
                        ->placeholder('123456789'),

                    Forms\Components\TextInput::make('nif')->label('NIF')
                        ->maxLength(20)
                        ->nullable()
                        ->unique(ignoreRecord: true)
                        ->regex('/^[0-9]{9}$/', 'NIF deve conter 9 dígitos')
                        ->placeholder('123456789'),

                    Forms\Components\TextInput::make('phone_number')
                        ->label('Telefone')
                        ->maxLength(20)
                        ->nullable()
                        ->placeholder('+351 123 456 789')
                        ->regex('/^\+?[0-9\s\-\(\)]+$/', 'Telefone inválido'),
                    Forms\Components\Textarea::make('observations')->label('Observações')->rows(3)->nullable(),
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
                        ->label('Data de Admissão')
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

    /**
     * Define a tabela de listagem de funcionários
     * Modifica a query para filtrar apenas o próprio funcionário se o usuário for EMPLOYEE
     * Fluxo: query modificada -> colunas exibidas -> ações visíveis baseadas em permissões
     */
    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function ($query) {
            /** @var \App\Models\User|null $u */
            $u = Auth::user();
            if ($u && Access::isEmployeeRole($u)) {
                $employeeId = $u->employee?->id ?? null;
                if ($employeeId) {
                    $query->where('id', $employeeId);
                }
            }

            return $query;
        })
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label('Nome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('last_name')->label('Sobrenome')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('department.name')->label('Departamento')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('designation.name')->label('Cargo')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('date_hired')->label('Data de Admissão')->date()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u !== null && $u->can('view', $record);
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u !== null && $u->can('update', $record);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u !== null && $u->can('delete', $record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Define o infolist para visualização detalhada do funcionário
     * Exibe dados pessoais em seções organizadas
     */
    public static function infolists(): Infolist
    {
        return Infolist::make()->schema([
            Section::make('Dados Pessoais')->schema([
                TextEntry::make('first_name')->label('Nome'),
                TextEntry::make('last_name')->label('Sobrenome'),
                TextEntry::make('email')->label('E-mail'),
                TextEntry::make('department.name')->label('Departamento'),
                TextEntry::make('designation.name')->label('Cargo'),
                TextEntry::make('date_hired')->label('Data de Admissão'),
            ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // You can add ContractsRelationManager::class here
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
