<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Trait para exportar dados em múltiplos formatos
 * CSV, Excel (.xlsx) e JSON
 */
trait ExportableTrait
{
    /**
     * Exporta coleção em formato CSV
     */
    public static function exportToCSV(Collection $data, string $filename = 'export'): string
    {
        $csv = fopen('php://output', 'w');
        $headersSet = false;

        foreach ($data as $row) {
            $row = $row instanceof \Illuminate\Database\Eloquent\Model ? $row->toArray() : $row;
            
            if (!$headersSet) {
                fputcsv($csv, array_keys($row));
                $headersSet = true;
            }
            
            fputcsv($csv, array_values($row));
        }

        fclose($csv);
        return $filename . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
    }

    /**
     * Exporta coleção em formato Excel
     */
    public static function exportToExcel(Collection $data, string $filename = 'export')
    {
        $array = [];
        $headers = [];

        foreach ($data as $row) {
            $row = $row instanceof \Illuminate\Database\Eloquent\Model ? $row->toArray() : $row;
            
            if (empty($headers)) {
                $headers = array_keys($row);
            }
            
            $array[] = $row;
        }

        $filename = $filename . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($array, $headers) implements FromArray, WithHeadings {
            private $data;
            private $headers;

            public function __construct($data, $headers)
            {
                $this->data = $data;
                $this->headers = $headers;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headers;
            }
        }, $filename);
    }

    /**
     * Exporta coleção em formato JSON
     */
    public static function exportToJSON(Collection $data, string $filename = 'export'): string
    {
        $json = json_encode([
            'exported_at' => now()->toIso8601String(),
            'count' => $data->count(),
            'data' => $data->map(function ($item) {
                return $item instanceof \Illuminate\Database\Eloquent\Model ? $item->toArray() : $item;
            })->all(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $filename = $filename . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        return base64_encode($json); // Retorna encoded para download no controller
    }

    /**
     * Retorna nomes customizados de colunas para exportação
     * Pode ser sobrescrito nos modelos para customização
     */
    public static function getExportHeaders(): array
    {
        return [];
    }

    /**
     * Formata dados para exportação com relacionamentos
     */
    public static function formatForExport(Collection $data, array $with = []): Collection
    {
        return $data->map(function ($item) use ($with) {
            $arr = $item->toArray();
            
            // Inclui relacionamentos se especificados
            foreach ($with as $relation) {
                if ($item->relationLoaded($relation)) {
                    $arr[$relation] = $item->{$relation}?->toArray();
                }
            }
            
            return $arr;
        });
    }
}
