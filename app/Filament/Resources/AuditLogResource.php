<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // make Intelephense aware of the User methods (isRoot)

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationLabel = 'Audit Logs';
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Admin';

    public static function shouldRegisterNavigation(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var User|null $user */
        $user = Auth::user();

        return $user?->isRoot() === true;
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('event')->label('Evento')->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Usuário')
                    ->formatStateUsing(function ($state, \App\Models\AuditLog $record) {
                        return $record->user?->email ?? '-';
                    }),
                Tables\Columns\TextColumn::make('auditable_type')->label('Tipo'),
                Tables\Columns\TextColumn::make('url')->label('URL')->limit(50)->toggleable(true),
                Tables\Columns\TextColumn::make('created_at')->label('Criado')->dateTime()->sortable(),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
