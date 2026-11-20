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

    /**
     * Champs de configuration générale.
     *
     * @var array<string, array{key: string, label: string, default: string, description: string}>
     */
    private const GENERAL_FIELDS = [
        'app_name' => [
            'key' => 'general.app_name',
            'label' => 'Nom de l’application',
            'default' => 'Job2You',
            'description' => 'Nom affiché dans l’interface et les emails.',
        ],
        'support_email' => [
            'key' => 'general.support_email',
            'label' => 'Email de support',
            'default' => '',
            'description' => 'Adresse de contact affichée aux utilisateurs.',
        ],
        'contact_phone' => [
            'key' => 'general.contact_phone',
            'label' => 'Téléphone de contact',
            'default' => '',
            'description' => 'Numéro affiché sur les pages publiques.',
        ],
    ];

    /**
     * Champs de configuration SMS.
     *
     * @var array<string, array{key: string, label: string, default: string, description: string}>
     */
    private const SMS_FIELDS = [
        'free_trial_limit' => [
            'key' => 'sms.free_trial_limit',
            'label' => 'SMS gratuits (essai)',
            'default' => '2',
            'description' => 'Nombre de SMS offerts aux candidats sans abonnement.',
        ],
        'sms_provider' => [
            'key' => 'sms.provider',
            'label' => 'Fournisseur SMS',
            'default' => 'tirex',
            'description' => 'Fournisseur utilisé pour l’envoi des alertes.',
        ],
    ];

    /**
     * Champs de configuration de la collecte.
     *
     * @var array<string, array{key: string, label: string, default: string, description: string}>
     */
    private const SCRAPING_FIELDS = [
        'max_offers_per_source' => [
            'key' => 'scraping.max_offers_per_source',
            'label' => 'Offres max par source',
            'default' => '100',
            'description' => 'Nombre maximum d’offres traitées par source et par exécution.',
        ],
        'request_timeout' => [
            'key' => 'scraping.request_timeout',
            'label' => 'Timeout des requêtes (s)',
            'default' => '30',
            'description' => 'Délai maximal d’attente des requêtes de collecte.',
        ],
    ];

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Affiche les paramètres de l'application.
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
            'generalFields' => $this->editableFields(self::GENERAL_FIELDS),
            'smsFields' => $this->editableFields(self::SMS_FIELDS),
            'scrapingFields' => $this->editableFields(self::SCRAPING_FIELDS),
        ]);
    }

    /**
     * Met à jour les paramètres de l'application.
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
            'app_name' => ['nullable', 'string', 'max:100'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'free_trial_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'sms_provider' => ['nullable', 'string', 'max:50'],
            'max_offers_per_source' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'request_timeout' => ['nullable', 'integer', 'min:1', 'max:300'],
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
        $this->store(self::THRESHOLD_KEY, (float) $validated['alert_threshold'], Setting::TYPE_DECIMAL, Setting::GROUP_MATCHING);
        $this->storeTextFields($request, self::GENERAL_FIELDS, Setting::GROUP_GENERAL);
        $this->storeIntegerFields($request, self::SMS_FIELDS, Setting::GROUP_SMS, ['free_trial_limit']);
        $this->storeTextFields($request, self::SMS_FIELDS, Setting::GROUP_SMS, ['sms_provider']);
        $this->storeIntegerFields($request, self::SCRAPING_FIELDS, Setting::GROUP_SCRAPING);

        $this->activityLogger->adminAction(
            'Modification paramètres application',
            'Général, matching, SMS et collecte mis à jour.',
        );

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Paramètres de l’application mis à jour avec succès.');
    }

    /**
     * Enregistre les pondérations validées.
     *
     * @param  array<string, string|float|int>  $weights
     */
    private function storeWeights(array $weights): void
    {
        foreach (self::WEIGHT_FIELDS as $field => $definition) {
            $this->store($definition['key'], (float) $weights[$field], Setting::TYPE_DECIMAL, Setting::GROUP_MATCHING);
        }
    }

    /**
     * Enregistre les champs texte présents dans la requête.
     *
     * @param  array<string, array{key: string, label: string, default: string, description: string}>  $definitions
     * @param  array<int, string>  $only
     */
    private function storeTextFields(Request $request, array $definitions, string $group, array $only = []): void
    {
        $fields = $only !== [] ? array_intersect_key($definitions, array_flip($only)) : $definitions;

        foreach ($fields as $name => $definition) {
            if (! $request->has($name)) {
                continue;
            }

            $this->store(
                $definition['key'],
                (string) $request->input($name, ''),
                Setting::TYPE_STRING,
                $group,
                $definition['label'],
                $definition['description'],
            );
        }
    }

    /**
     * Enregistre les champs entiers présents dans la requête.
     *
     * @param  array<string, array{key: string, label: string, default: string, description: string}>  $definitions
     * @param  array<int, string>  $only
     */
    private function storeIntegerFields(Request $request, array $definitions, string $group, array $only = []): void
    {
        $fields = $only !== [] ? array_intersect_key($definitions, array_flip($only)) : $definitions;

        foreach ($fields as $name => $definition) {
            if (! $request->has($name)) {
                continue;
            }

            $this->store(
                $definition['key'],
                (int) $request->input($name, 0),
                Setting::TYPE_INTEGER,
                $group,
                $definition['label'],
                $definition['description'],
            );
        }
    }

    /**
     * Enregistre une valeur en conservant les métadonnées du setting.
     */
    private function store(
        string $key,
        mixed $value,
        string $type,
        string $group,
        ?string $label = null,
        ?string $description = null,
    ): void {
        $setting = Setting::query()->where('key', $key)->first();

        Setting::set(
            $key,
            $value,
            $type,
            $label ?? $setting?->label,
            $description ?? $setting?->description,
            $group,
        );
    }

    /**
     * Construit les champs affichés dans le formulaire.
     *
     * @param  array<string, array{key: string, label: string, default: string, description: string}>  $definitions
     * @return array<int, array{name: string, label: string, value: string, description: string}>
     */
    private function editableFields(array $definitions): array
    {
        $settings = Setting::query()
            ->whereIn('key', array_column($definitions, 'key'))
            ->get()
            ->keyBy('key');

        return collect($definitions)
            ->map(fn (array $definition, string $name): array => [
                'name' => $name,
                'label' => $settings->get($definition['key'])?->label ?? $definition['label'],
                'value' => $settings->get($definition['key'])?->value ?? $definition['default'],
                'description' => $settings->get($definition['key'])?->description ?? $definition['description'],
            ])
            ->values()
            ->all();
    }
}
