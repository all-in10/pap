<?php

namespace App\Filament\Resources\HoursbankResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Models\Worklog;
use Carbon\Carbon;

class WorklogsRelationManager extends RelationManager
{
    protected static string $relationship = 'worklogs';
    protected static ?string $title = 'Registo de horas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('work_date')
                    ->label('Data')
                    ->required(),

                Forms\Components\TimePicker::make('start_time')
                    ->label('Início')
                    ->required()
                    ->displayFormat('h:i A')
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set, $get) =>
                        $set('hours_worked', $this->calculateHoursWorked($get('start_time'), $get('end_time')))
                    ),

                Forms\Components\TimePicker::make('end_time')
                    ->label('Fim')
                    ->required()
                    ->displayFormat('h:i A')
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set, $get) =>
                        $set('hours_worked', $this->calculateHoursWorked($get('start_time'), $get('end_time')))
                    ),

                Forms\Components\TextInput::make('hours_worked')
                    ->label('Hours Worked')
                    ->numeric()
                    ->required()
                    ->dehydrateStateUsing(fn($get) => $this->calculateHoursWorked($get('start_time'), $get('end_time'))),

                Forms\Components\Textarea::make('notes')
                    ->label('Observações')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('work_date')
                    ->label('Data')
                    ->date(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Início')
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Fim')
                    ->formatStateUsing(fn($state) => Carbon::createFromFormat('H:i:s', $state)->format('h:i A')),

                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours Worked')
                    ->formatStateUsing(fn($state) => round($state, 2) . 'h'),

                Tables\Columns\TextColumn::make('extra_hours')
                    ->label('Extras')
                    ->formatStateUsing(fn($record) => max(0, round($record->hours_worked - 8, 2)) . 'h')
                    ->color(fn($record) => $record->hours_worked > 8 ? 'success' : 'secondary'),
            ])
            ->defaultSort('work_date', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(function (): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        return $u !== null && $u->can('create', Worklog::class);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        if ($u === null) return false;
                        return $u->can('update', $record);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(function ($record): bool {
                        /** @var \App\Models\User|null $u */
                        $u = Auth::user();
                        if ($u === null) return false;
                        return $u->can('delete', $record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function calculateHoursWorked(?string $startTime, ?string $endTime): float
    {
        if ($startTime && $endTime) {
            $start = Carbon::createFromFormat('H:i', substr($startTime, 0, 5));
            $end = Carbon::createFromFormat('H:i', substr($endTime, 0, 5));

            return $start->floatDiffInHours($end);
        }

        return 0;
    }
}
