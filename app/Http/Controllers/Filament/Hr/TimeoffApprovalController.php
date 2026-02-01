<?php

declare(strict_types=1);

namespace App\Http\Controllers\Filament\Hr;

use App\Http\Controllers\Controller;
use App\Models\Timeoff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class TimeoffApprovalController extends Controller
{
    public function approve(Request $request, Timeoff $timeoff)
    {
        if (!Gate::allows('manage-timeoffs')) {
            abort(403);
        }

        $timeoff->status = 'approved';
        $timeoff->approved_by = Auth::id();
        $timeoff->save();

        return redirect()->back();
    }

    public function reject(Request $request, Timeoff $timeoff)
    {
        if (!Gate::allows('manage-timeoffs')) {
            abort(403);
        }

        $timeoff->status = 'rejected';
        $timeoff->approved_by = Auth::id();
        $timeoff->save();

        return redirect()->back();
    }
}
