<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * Modèle pour les paramètres de configuration de l'application.
 * Permet de stocker des valeurs configurables (poids matching, seuils, etc.)
 * avec cache pour performance.
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
        'description',
        'group',
        'editable',
    ];

    protected $casts = [
        'editable' => 'boolean',
    ];

    // Types supportés
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_JSON = 'json';

    // Groupes
    public const GROUP_MATCHING = 'matching';
    public const GROUP_SMS = 'sms';
    public const GROUP_SCRAPING = 'scraping';
    public const GROUP_GENERAL = 'general';

    /**
     * Récupère une valeur de setting avec cache.
     * Retourne la valeur par défaut si non trouvée.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", fn () => self::getRaw($key, $default));
    }

    /**
     * Récupère la valeur brute sans cache.
     */
    public static function getRaw(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Définit une valeur de setting (crée ou met à jour).
     * Invalide le cache.
     */
    public static function set(string $key, mixed $value, string $type = self::TYPE_STRING, ?string $label = null, ?string $description = null, ?string $group = null): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => self::encodeValue($value, $type),
                'type' => $type,
                'label' => $label ?? ucfirst(str_replace(['.', '_'], ' ', $key)),
                'description' => $description,
                'group' => $group ?? self::GROUP_GENERAL,
            ]
        );

        Cache::forget("setting:{$key}");

        return $setting;
    }

    /**
     * Convertit la valeur stockée selon son type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($type) {
            self::TYPE_INTEGER => (int) $value,
            self::TYPE_DECIMAL => (float) $value,
            self::TYPE_BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_JSON => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Encode la valeur pour stockage.
     */
    protected static function encodeValue(mixed $value, string $type): string
    {
        return match ($type) {
            self::TYPE_JSON => json_encode($value),
            self::TYPE_BOOLEAN => $value ? '1' : '0',
            default => (string) $value,
        };
    }

    /**
     * Invalide le cache pour une clé.
     */
    public static function forget(string $key): void
    {
        Cache::forget("setting:{$key}");
    }

    /**
     * Récupère tous les settings d'un groupe.
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->orderBy('label')
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => self::castValue($s->value, $s->type)])
            ->toArray();
    }

    /**
     * Scope pour les settings editables.
     */
    public function scopeEditable($query)
    {
        return $query->where('editable', true);
    }

    /**
     * Scope par groupe.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}