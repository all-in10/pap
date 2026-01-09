<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationLogResource\Pages;
use App\Models\NotificationLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;
use App\Services\Access;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    // Hide this resource from the sidebar — notifications are shown in the Settings page only
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Histórico de Notificações';
    protected static ?string $navigationGroup = 'Gestão do Sistema';
    protected static ?string $pluralModelLabel = 'Histórico de Notificações';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Título')->sortable(),
                Tables\Columns\TextColumn::make('body')->label('Mensagem')->limit(100),
                Tables\Columns\TextColumn::make('created_by')->label('Criado por')->formatStateUsing(fn($state) => $state ?? '-'),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->dateTime()->sortable(),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationLogs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        // Only allow admins/root to view notification history
        return Access::isAdmin() || Access::isRoot();
    }
}
