<?php
declare(strict_types=1);

namespace App\Filament\Resources\WorklogResource\Pages;

use App\Filament\Resources\WorklogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorklog extends CreateRecord
{
    protected static string $resource = WorklogResource::class;
}
