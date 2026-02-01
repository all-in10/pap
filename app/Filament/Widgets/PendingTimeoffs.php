<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Timeoff;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class PendingTimeoffs extends Widget
{
    use WithPagination;

    protected static string $view = 'filament.widgets.pending-timeoffs';

    // Use Tailwind pagination theme to match Filament
    protected string $paginationTheme = 'tailwind';

    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->isHr() || $user->isRoot();
    }

    public function getPending(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Timeoff::with(['employee.user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(6);
    }

    // If a page is changed due to a non-livewire action, reset to previous page when needed
    public function updated(): void
    {
        // No-op placeholder: WithPagination handles page state; keep for future resets
    }
}
