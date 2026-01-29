@php
    $changes = $getState();
@endphp

@if($changes && count($changes) > 0)
    <div class="space-y-4">
        @foreach($changes as $field => $change)
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 dark:text-white capitalize">
                        {{ str_replace('_', ' ', $field) }}
                    </h4>
                    <span class="inline-flex items-center rounded-full bg-blue-100 dark:bg-blue-900 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:text-blue-200">
                        Updated
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Old Value -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Old Value</p>
                        <div class="rounded bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-2">
                            <code class="text-sm text-red-700 dark:text-red-300 break-all">
                                @if(is_array($change) && isset($change['old']))
                                    {{ is_scalar($change['old']) ? $change['old'] : json_encode($change['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                @else
                                    {{ $change }}
                                @endif
                            </code>
                        </div>
                    </div>

                    <!-- New Value -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">New Value</p>
                        <div class="rounded bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-2">
                            <code class="text-sm text-green-700 dark:text-green-300 break-all">
                                @if(is_array($change) && isset($change['new']))
                                    {{ is_scalar($change['new']) ? $change['new'] : json_encode($change['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                @else
                                    {{ $change }}
                                @endif
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4 text-center">
        <p class="text-gray-500 dark:text-gray-400 text-sm">No changes recorded</p>
    </div>
@endif
