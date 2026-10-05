<x-app-layout>
    <div class="container py-4" style="max-width: 1200px;">
        <h1 class="h3 fw-bold mb-4">Logs & Supervision</h1>
        <div class="bg-white border rounded-3 p-4">
            <p class="text-secondary mb-2">Dernières entrées du journal d'application (10 dernières) :</p>
            <pre class="bg-light border rounded p-3 small" style="max-height: 400px; overflow:auto;">{{ $logContent ?? trim(implode("\n", array_slice(file_exists(storage_path('logs/laravel.log')) ? file(storage_path('logs/laravel.log')) : [], -10))) }}</pre>
        </div>
    </div>
</x-app-layout>
