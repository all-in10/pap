<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use App\Enums\UserRole;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use App\Services\Access;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    public function mount(): void
    {
        parent::mount();

        /** @var \App\Models\User|null $u */
        $u = Auth::user();
        if ($u && Access::isEmployeeRole($u)) {
            $employeeId = $u->employee?->id ?? null;
            if ($employeeId) {
                $this->redirect(EmployeeResource::getUrl('view', ['record' => $employeeId]));
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function (): bool {
                    /** @var \App\Models\User|null $u */
                    $u = Auth::user();
                    return $u !== null && $u->can('create', Employee::class);
                }),
        ];
    }

    // Filtering moved to the Resource::table() via modifyQueryUsing().
}
