<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExportController extends Controller
{
    use AuthorizesRequests;
    /**
     * Exporta dados em formato CSV
     */
    public function exportCSV(string $model, array $ids = null): StreamedResponse
    {
        $this->authorize('export', auth()->user());

        // Suporte especializado para ActivityLog
        if ($model === 'ActivityLog') {
            $modelClass = 'Spatie\\Activitylog\\Models\\Activity';
        } else {
            $modelClass = 'App\\Models\\' . $model;
        }
        
        if (!class_exists($modelClass)) {
            abort(404, 'Modelo não encontrado');
        }

        $query = $modelClass::query();
        
        if ($ids && count($ids) > 0) {
            $query->whereIn('id', $ids);
        }

        $data = $query->get();

        $filename = strtolower($model) . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Headers
            if ($data->count() > 0) {
                fputcsv($file, array_keys($data[0]->toArray()));
                
                // Dados
                foreach ($data as $row) {
                    fputcsv($file, array_values($row->toArray()));
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporta dados em formato Excel
     */
    public function exportExcel(string $model, array $ids = null)
    {
        $this->authorize('export', auth()->user());

        // Suporte especializado para ActivityLog
        if ($model === 'ActivityLog') {
            $modelClass = 'Spatie\\Activitylog\\Models\\Activity';
        } else {
            $modelClass = 'App\\Models\\' . $model;
        }
        
        if (!class_exists($modelClass)) {
            abort(404, 'Modelo não encontrado');
        }

        $query = $modelClass::query();
        
        if ($ids && count($ids) > 0) {
            $query->whereIn('id', $ids);
        }

        $data = $query->get();
        $filename = strtolower($model) . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new class($data) implements FromArray, WithHeadings {
                private $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data->map(fn ($item) => $item->toArray())->toArray();
                }

                public function headings(): array
                {
                    if ($this->data->count() === 0) {
                        return [];
                    }
                    return array_keys($this->data[0]->toArray());
                }
            },
            $filename
        );
    }

    /**
     * Exporta dados em formato JSON
     */
    public function exportJSON(string $model, array $ids = null): StreamedResponse
    {
        $this->authorize('export', auth()->user());

        // Suporte especializado para ActivityLog
        if ($model === 'ActivityLog') {
            $modelClass = 'Spatie\\Activitylog\\Models\\Activity';
        } else {
            $modelClass = 'App\\Models\\' . $model;
        }
        
        if (!class_exists($modelClass)) {
            abort(404, 'Modelo não encontrado');
        }

        $query = $modelClass::query();
        
        if ($ids && count($ids) > 0) {
            $query->whereIn('id', $ids);
        }

        $data = $query->get();

        $json = [
            'exported_at' => now()->toIso8601String(),
            'model' => $model,
            'count' => $data->count(),
            'data' => $data->map(fn ($item) => $item->toArray())->toArray(),
        ];

        $filename = strtolower($model) . '_' . now()->format('Y-m-d_H-i-s') . '.json';

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(
            function () use ($json) {
                echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            },
            200,
            $headers
        );
    }
}
