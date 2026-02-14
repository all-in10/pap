<?php

namespace App\Filament\Hooks;

use Filament\Forms\Form;

class FormProtectionHook
{
    /**
     * Registra o hook global para proteger formulários contra mudanças não salvas
     */
    public static function setup(): void
    {
        Form::configureUsing(function (Form $form) {
            $form->footerActionsAlignment('start');
        });
    }
}
