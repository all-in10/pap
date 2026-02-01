<div class="p-4">
    <h2 class="text-lg font-bold">Audit Log #{{ $record->id }}</h2>

    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-sm font-medium text-gray-500">Evento</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $record->event }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Usuário</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $record->user?->email ?? '-' }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">URL</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $record->url }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Criado em</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $record->created_at }}</dd>
        </div>
        <div class="col-span-1 sm:col-span-2">
            <dt class="text-sm font-medium text-gray-500">Old Values</dt>
            <dd class="mt-1 text-sm text-gray-900"><pre>{{ json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></dd>
        </div>
        <div class="col-span-1 sm:col-span-2">
            <dt class="text-sm font-medium text-gray-500">New Values</dt>
            <dd class="mt-1 text-sm text-gray-900"><pre>{{ json_encode($record->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></dd>
        </div>
    </dl>
</div>
