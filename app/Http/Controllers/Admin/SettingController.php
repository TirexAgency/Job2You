<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Pondérations éditables et leur clé de setting associée.
     *
     * @var array<string, array{key: string, default: float}>
     */
    private const WEIGHT_FIELDS = [
        'skills' => ['key' => 'matching.skills_weight', 'default' => 0.40],
        'job' => ['key' => 'matching.job_weight', 'default' => 0.25],
        'location' => ['key' => 'matching.location_weight', 'default' => 0.15],
        'experience' => ['key' => 'matching.experience_weight', 'default' => 0.10],
        'contract' => ['key' => 'matching.contract_weight', 'default' => 0.10],
    ];

    private const THRESHOLD_KEY = 'matching.alert_threshold';

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Affiche les paramètres de matching.
     */
    public function edit(): View
    {
        $settings = Setting::query()
            ->where('group', Setting::GROUP_MATCHING)
            ->whereIn('key', [...array_column(self::WEIGHT_FIELDS, 'key'), self::THRESHOLD_KEY])
            ->get()
            ->keyBy('key');

        $weightFields = collect(self::WEIGHT_FIELDS)
            ->map(fn (array $definition, string $field): array => [
                'field' => $field,
                'label' => $settings->get($definition['key'])?->label ?? ucfirst($field),
                'description' => $settings->get($definition['key'])?->description,
                'value' => $settings->get($definition['key'])?->value ?? (string) $definition['default'],
            ])
            ->values();

        $threshold = $settings->get(self::THRESHOLD_KEY);

        return view('admin.settings.edit', [
            'weightFields' => $weightFields,
            'threshold' => [
                'label' => $threshold?->label ?? 'Seuil d’alerte SMS',
                'description' => $threshold?->description,
                'value' => $threshold?->value ?? '0.70',
            ],
        ]);
    }

    /**
     * Met à jour les paramètres de matching.
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'weights' => ['required', 'array'],
            'weights.skills' => ['required', 'numeric', 'min:0', 'max:1'],
            'weights.job' => ['required', 'numeric', 'min:0', 'max:1'],
            'weights.location' => ['required', 'numeric', 'min:0', 'max:1'],
            'weights.experience' => ['required', 'numeric', 'min:0', 'max:1'],
            'weights.contract' => ['required', 'numeric', 'min:0', 'max:1'],
            'alert_threshold' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            $weights = $request->input('weights', []);

            if (! is_array($weights)) {
                return;
            }

            $sum = array_sum(array_map('floatval', $weights));

            if (abs($sum - 1.0) > 0.001) {
                $validator->errors()->add(
                    'weights',
                    'La somme des pondérations doit être égale à 1.0 (actuellement '.number_format($sum, 2, ',', ' ').').',
                );
            }
        });

        $validated = $validator->validate();

        $this->storeWeights($validated['weights']);
        $this->store(self::THRESHOLD_KEY, (float) $validated['alert_threshold']);

        $this->activityLogger->adminAction(
            'Modification paramètres matching',
            'Pondérations et seuil d’alerte SMS mis à jour.',
        );

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Paramètres de matching mis à jour avec succès.');
    }

    /**
     * Enregistre les pondérations validées.
     *
     * @param  array<string, string|float|int>  $weights
     */
    private function storeWeights(array $weights): void
    {
        foreach (self::WEIGHT_FIELDS as $field => $definition) {
            $this->store($definition['key'], (float) $weights[$field]);
        }
    }

    /**
     * Enregistre une valeur décimale en conservant les métadonnées du setting.
     */
    private function store(string $key, float $value): void
    {
        $setting = Setting::query()->where('key', $key)->first();

        Setting::set(
            $key,
            $value,
            Setting::TYPE_DECIMAL,
            $setting?->label,
            $setting?->description,
            Setting::GROUP_MATCHING,
        );
    }
}
