<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeoffResource\Pages;
use App\Models\Timeoff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Services\Access;

class TimeoffResource extends Resource
{
    protected static ?string $model = Timeoff::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Solicitações de Licenças';
    protected static ?string $pluralModelLabel = 'Solicitações de Licenças';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $modelLabel = 'Solicitação de Licenças';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label('Funcionário')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Data de Início')
                ->native(false)
                ->required(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Data de Término')
                ->native(false)
                ->required(),

            Forms\Components\Select::make('type')
                ->label('Tipo')
                ->options([
                    'vacation'       => 'Férias',
                    'sick_leave'     => 'Licença Médica',
                    'personal_leave' => 'Licença Pessoal',
                    'other'          => 'Outro',
                ])
                ->native(false)
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending'  => 'Pendente',
                    'approved' => 'Aprovado',
                    'rejected' => 'Rejeitado',
                ])
                ->default('pending')
                ->required(),

            Forms\Components\Textarea::make('reason')
                ->label('Motivo')
                ->rows(3)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('employee.first_name')->label('Funcionário')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('start_date')->label('Data de Início')->date(),
            Tables\Columns\TextColumn::make('end_date')->label('Data de Término')->date(),
            Tables\Columns\TextColumn::make('type')
                ->label('Tipo')
                ->formatStateUsing(function ($state) {
                    $map = [
                        'vacation'       => ['label' => 'Férias', 'color' => '#c2c5aa'],
                        'sick_leave'     => ['label' => 'Licença Médica', 'color' => '#a68a64'],
                        'personal_leave' => ['label' => 'Licença Pessoal', 'color' => '#936639'],
                        'other'          => ['label' => 'Outro', 'color' => '#656d4a'],
                    ];

                    $entry = $map[$state] ?? ['label' => (string) $state, 'color' => '#414833'];

                    return sprintf(
                        '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background: %s; color: #ffffff;">%s</span>',
                        $entry['color'],
                        e($entry['label'])
                    );
                })
                ->html(),

            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->formatStateUsing(function ($state) {
                    $map = [
                        'pending' => ['label' => 'Pendente', 'color' => '#b6ad90'],
                        'approved' => ['label' => 'Aprovado', 'color' => '#c2c5aa'],
                        'rejected' => ['label' => 'Rejeitado', 'color' => '#a68a64'],
                    ];

                    $entry = $map[$state] ?? ['label' => (string) $state, 'color' => '#414833'];

                    return sprintf(
                        '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background: %s; color: #ffffff;">%s</span>',
                        $entry['color'],
                        e($entry['label'])
                    );
                })
                ->html(),

            Tables\Columns\TextColumn::make('reason')->label('Motivo')->limit(50),
        ])
                ->modifyQueryUsing(function ($query) {
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
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeoffs::route('/'),
            'create' => Pages\CreateTimeoff::route('/create'),
            'edit' => Pages\EditTimeoff::route('/{record}/edit'),
        ];
    }
}
