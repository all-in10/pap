<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    /**
     * Ensure the page header (title) is shown in Portuguese as well.
     */
    protected static ?string $title = 'Configurações';

    protected static ?string $navigationLabel = 'Configurações';
    protected static string $view = 'filament.pages.settings';
    protected static bool $shouldRegisterNavigation = false;
}
