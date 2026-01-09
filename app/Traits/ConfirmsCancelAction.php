<?php

namespace App\Traits;

use Filament\Actions\Action;
use Illuminate\Support\Js;

trait ConfirmsCancelAction
{
    protected function getCancelFormAction(): Action
    {
        $redirect = $this->previousUrl ?? static::getResource()::getUrl();

        $handler = sprintf(
            "if (confirm('Deseja realmente cancelar a edição? As alterações não salvas serão perdidas.')) { document.referrer ? window.history.back() : (window.location.href = %s); }",
            Js::from($redirect)
        );

        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler($handler)
            ->color('gray');
    }
}
