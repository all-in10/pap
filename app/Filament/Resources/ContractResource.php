<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Models\Designation;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $pluralModelLabel = 'Contracts';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $modelLabel = 'Contract';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label('Employee')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Will be created automatically if empty.'),

            Forms\Components\Select::make('designation_id')
                ->label('Designation')
                ->relationship('designation', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $designation = Designation::find($state);
                        $set('salary', $designation?->base_salary ?? 0);
                    }
                }),

            Forms\Components\Select::make('contract_type_id')
                ->label('Contract Type')
                ->relationship('contractType', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive(),

            Forms\Components\TextInput::make('salary')
                ->label('Salary')
                ->numeric()
                ->required(),

            Forms\Components\DatePicker::make('start_date')
                ->label('Start Date')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now()),

            Forms\Components\DatePicker::make('end_date')
                ->label('End Date')
                ->nullable()
                ->helperText('Data de fim do contrato (se aplicável).'),  

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active'     => 'Active',
                    'terminated' => 'Terminated',
                    'suspended'  => 'Suspended',
                ])
                ->default('active')
                ->required(),

            Forms\Components\DatePicker::make('date_hired')
                ->label('Date Hired')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('designation.name')
                    ->label('Cargo / Designação')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('contractType.name')
                    ->label('Tipo de Contrato')
                    ->sortable(),

                Tables\Columns\TextColumn::make('salary')
                    ->label('Salário')
                    ->money('EUR', true),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Início')
                    ->date(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fim')
                    ->date()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($state) {
                        $map = [
                            'active' => ['label' => 'Active', 'color' => '#a4ac86'],
                            'terminated' => ['label' => 'Terminated', 'color' => '#7f4f24'],
                            'suspended' => ['label' => 'Suspended', 'color' => '#b6ad90'],
                        ];

                        $entry = $map[$state] ?? ['label' => (string) $state, 'color' => '#414833'];

                        return sprintf(
                            '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background: %s; color: #ffffff;">%s</span>',
                            $entry['color'],
                            e($entry['label'])
                        );
                    })
                    ->html(),
            ])
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

                Action::make('generate_pdf')
                    ->label('Gerar PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(function (Contract $record) {
                        $pdf = Pdf::loadView('pdf.contract', ['contract' => $record])
                            ->setOptions(['isHtml5ParserEnabled' => true]);
                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            "Contrato-{$record->id}.pdf"
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit'   => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
