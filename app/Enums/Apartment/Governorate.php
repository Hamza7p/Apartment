<?php

namespace App\Enums\Apartment;

enum Governorate: int
{
    case DAMASCUS = 1;
    case ALEPPO = 2;
    case HOMS = 3;
    case HAMA = 4;
    case LATAKIA = 5;
    case TARTUS = 6;
    case IDLIB = 7;
    case DEIR_EZ_ZOR = 8;
    case RAQQA = 9;
    case HASAKA = 10;
    case DARA = 11;
    case SUWAYDA = 12;
    case QUNEITRA = 13;

    public function label(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return match ($locale) {
            'ar' => match ($this) {
                self::DAMASCUS => 'دمشق',
                self::ALEPPO => 'حلب',
                self::HOMS => 'حمص',
                self::HAMA => 'حماة',
                self::LATAKIA => 'اللاذقية',
                self::TARTUS => 'طرطوس',
                self::IDLIB => 'إدلب',
                self::DEIR_EZ_ZOR => 'دير الزور',
                self::RAQQA => 'الرقة',
                self::HASAKA => 'الحسكة',
                self::DARA => 'درعا',
                self::SUWAYDA => 'السويداء',
                self::QUNEITRA => 'القنيطرة',
            },
            default => match ($this) {
                self::DAMASCUS => 'Damascus',
                self::ALEPPO => 'Aleppo',
                self::HOMS => 'Homs',
                self::HAMA => 'Hama',
                self::LATAKIA => 'Latakia',
                self::TARTUS => 'Tartus',
                self::IDLIB => 'Idlib',
                self::DEIR_EZ_ZOR => 'Deir ez-Zor',
                self::RAQQA => 'Raqqa',
                self::HASAKA => 'Hasakah',
                self::DARA => 'Daraa',
                self::SUWAYDA => 'As-Suwayda',
                self::QUNEITRA => 'Quneitra',
            },
        };
    }

    /**
     * Return all governorates as array for frontend (value + labels)
     */
    public static function allLabels(): array
    {
        return collect(self::cases())->map(fn ($g) => [
            'value' => $g->value,
            'en' => $g->label('en'),
            'ar' => $g->label('ar'),
        ])->toArray();
    }

    /**
     * Get enum instance by name (EN or AR)
     */
    public static function fromName(string $name): ?self
    {
        return collect(self::cases())
            ->first(fn ($g) => $g->label('en') === $name || $g->label('ar') === $name);
    }
}
