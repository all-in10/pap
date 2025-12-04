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

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $pluralModelLabel = 'Employees';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $modelLabel = 'Employee';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Location')
                ->description('Select country, state and city')
                ->schema([
                    Forms\Components\Select::make('country_id')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Country')
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
                        ->label('State'),

                    Forms\Components\Select::make('city_id')
                        ->options(fn($get) => $get('state_id')
                            ? \App\Models\City::where('state_id', $get('state_id'))->pluck('name', 'id')
                            : [])
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('City'),

                    Forms\Components\Select::make('department_id')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Department'),

                    Forms\Components\Select::make('designation_id')
                        ->relationship('designation', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Designation'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Personal Data')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->label('First Name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')->label('Middle Name')->maxLength(255)->nullable(),
                    Forms\Components\TextInput::make('last_name')->label('Last Name')->required()->maxLength(255),
                    Forms\Components\Select::make('gender')
                        ->label('Gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'n/a' => 'N/A',
                        ])
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('nss')->label('NSS')->required()->maxLength(20),
                    Forms\Components\TextInput::make('nif')->label('NIF')->maxLength(20)->nullable(),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Phone')
                        ->maxLength(20)
                        ->nullable()
                        ->placeholder('+351 123 456 789'),
                    Forms\Components\Textarea::make('observations')->label('Notes')->rows(3)->nullable(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Address')
                ->schema([
                    Forms\Components\TextInput::make('address')->label('Address')->required()->maxLength(255),
                    Forms\Components\TextInput::make('zip_code')->label('Zip Code')->required()->maxLength(10),
                ])
                ->columns(2),

            Forms\Components\Section::make('Dates')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->label('Date of Birth')
                        ->required()
                        ->maxDate(now()->subYears(18))
                        ->native(false),
                    Forms\Components\DatePicker::make('date_hired')
                        ->label('Date Hired')
                        ->required()
                        ->maxDate(now())
                        ->native(false),
                ])
                ->columns(2),

            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label('First Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('last_name')->label('Last Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('department.name')->label('Department')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('designation.name')->label('Designation')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('date_hired')->label('Date Hired')->date()->sortable(),
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

    public static function infolists(): Infolist
    {
        return Infolist::make()->schema([
            Section::make('Personal Data')->schema([
                TextEntry::make('first_name')->label('First Name'),
                TextEntry::make('last_name')->label('Last Name'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('department.name')->label('Department'),
                TextEntry::make('designation.name')->label('Designation'),
                TextEntry::make('date_hired')->label('Date Hired'),
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
