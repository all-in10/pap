<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Models\Designation;
use Illuminate\Support\Facades\Auth;
use App\Services\Access;
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
    protected static ?string $navigationLabel = 'Contratos';
    protected static ?string $pluralModelLabel = 'Contratos';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $modelLabel = 'Contrato';

    /**
     * Define o formulário para criação/edição de contratos
     * Inclui seleção de funcionário, cargo, tipo, salário, datas
     * Fluxo: campos reativos atualizam salário baseado no cargo
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label('Funcionário')
                ->relationship('employee', 'first_name')
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText('Será criado automaticamente se estiver vazio.'),

            Forms\Components\Select::make('designation_id')
                ->label('Cargo')
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
                ->label('Tipo de Contrato')
                ->relationship('contractType', 'name')
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive(),

            Forms\Components\TextInput::make('salary')
                ->label('Salário')
                ->numeric()
                ->required()
                ->minValue(0.01)
                ->step(0.01)
                ->regex('/^\d+(\.\d{1,2})?$/', 'Formato de valor inválido')
                ->rules(['required', 'numeric', 'min:0.01']),

            Forms\Components\DatePicker::make('start_date')
                ->label('Data de Início')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now())
                ->maxDate(now()->addMonths(6))
                ->reactive(),

            Forms\Components\DatePicker::make('end_date')
                ->label('Data de Término')
                ->nullable()
                ->helperText('Data de fim do contrato (se aplicável).')
                ->afterOrEqual('start_date')
                ->reactive(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active'     => 'Ativo',
                    'terminated' => 'Rescindido',
                    'suspended'  => 'Suspenso',
                ])
                ->default('active')
                ->required(),

            Forms\Components\DatePicker::make('date_hired')
                ->label('Data de Admissão')
                ->required()
                ->default(fn($get) => $get('employee.date_hired') ?? now()),
        ]);
    }

    /**
     * Define a tabela de listagem de contratos
     * Modifica query para filtrar apenas contratos do próprio funcionário se usuário for EMPLOYEE
     * Fluxo: query modificada -> colunas com formatação HTML para status -> ações incluindo geração de PDF
     */
    public static function table(Table $table): Table
    {
        return $table
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
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->sortable()
                    ->searchable(['employees.first_name', 'employees.last_name']),

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
                            'active' => ['label' => 'Ativo', 'color' => '#a4ac86'],
                            'terminated' => ['label' => 'Rescindido', 'color' => '#7f4f24'],
                            'suspended' => ['label' => 'Suspenso', 'color' => '#b6ad90'],
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
