<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HoursbankResource\Pages;
use App\Filament\Resources\HoursbankResource\RelationManagers\WorklogsRelationManager;
use App\Models\Hoursbank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Services\Access;

class HoursbankResource extends Resource
{
    protected static ?string $model = Hoursbank::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Hours Bank';
    protected static ?string $pluralModelLabel = 'Hours Banks';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $modelLabel = 'Hours Bank';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'first_name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('total_hours')
                    ->label('Total Hours')
                    ->numeric()
                    ->disabled()
                    ->dehydrateStateUsing(fn($get) => $get('total_hours')), // apenas leitura
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(function ($query) {
            /** @var \App\Models\User|null $u */
            $u = Auth::user();
            if ($u && Access::isEmployeeRole($u)) {
                $employeeId = $u->employee?->id ?? null;
                if ($employeeId) {
                    $query->where('employee_id', $employeeId);
                }
            }

            return $query;
        })
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->formatStateUsing(fn($record) => round($record->employee->worklogs()->sum('extra_hours'), 2) . 'h')
                    ->sortable(),
            ])
            ->actions([
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
        return [
            WorklogsRelationManager::class, // relaciona os Worklogs do funcionário
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHoursbanks::route('/'),
            'create' => Pages\CreateHoursbank::route('/create'),
            'edit' => Pages\EditHoursbank::route('/{record}/edit'),
        ];
    }
}
