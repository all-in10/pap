<?php

namespace App\Filament\Concerns;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

/**
 * Classe para gerenciar a filtragem de widgets baseado em visibilidade (roles)
 * Registra um hook no Filament para filtrar widgets que não devem ser visíveis
 */
class WidgetFiltering
{
    /**
     * Filtra widgets baseado no método canView() da trait WidgetVisibility
     * Retorna apenas os widgets que o usuário atual pode visualizar
     */
    public static function filterWidgets(array $widgets): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        return array_filter($widgets, function (string|Widget $widget) {
            if (is_string($widget)) {
                // Se é uma classe string, instancia e verifica
                if (!class_exists($widget)) {
                    return true;
                }

                // Verifica se a classe tem o método canView
                if (method_exists($widget, 'canView')) {
                    return $widget::canView();
                }

                return true;
            }

            // Se é uma instância, verifica se tem canView
            if (method_exists($widget, 'canView')) {
                return $widget::canView();
            }

            return true;
        });
    }
}
