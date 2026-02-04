<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContractPdfController extends Controller
{
    public function download(Contract $contract)
    {
        $pdf = Pdf::loadView('contracts.pdf', compact('contract'))->setPaper('a4');

        return $pdf->download('contract-' . $contract->id . '.pdf');
    }
}
