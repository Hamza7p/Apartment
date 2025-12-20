<?php

namespace App\Enums\Apartment;

use App\Enums\Base\EnumToArray;

enum Governorate: int
{
    use EnumToArray;

    case DAMASCUS        = 1;
    case RIF_DIMASHQ     = 2;
    case ALEPPO          = 3;
    case HOMS            = 4;
    case HAMA            = 5;
    case LATTAKIA        = 6;
    case TARTUS          = 7;
    case IDLIB           = 8;
    case DEIR_EZ_ZOR     = 9;
    case RAQQA           = 10;
    case HASAKAH         = 11;
    case DARA            = 12;
    case AS_SUWAYDA      = 13;
    case QUNEITRA        = 14;

    /**
     * Return the human-readable label for EN or AR
     */
    public function label(string $lang = 'en'): string
    {
        return match ($this) {
            self::DAMASCUS    => $lang === 'ar' ? 'دمشق' : 'Damascus',
            self::RIF_DIMASHQ => $lang === 'ar' ? 'ريف دمشق' : 'Rif Dimashq',
            self::ALEPPO      => $lang === 'ar' ? 'حلب' : 'Aleppo',
            self::HOMS        => $lang === 'ar' ? 'حمص' : 'Homs',
            self::HAMA        => $lang === 'ar' ? 'حماة' : 'Hama',
            self::LATTAKIA    => $lang === 'ar' ? 'اللاذقية' : 'Latakia',
            self::TARTUS      => $lang === 'ar' ? 'طرطوس' : 'Tartus',
            self::IDLIB       => $lang === 'ar' ? 'إدلب' : 'Idlib',
            self::DEIR_EZ_ZOR => $lang === 'ar' ? 'دير الزور' : 'Deir ez-Zor',
            self::RAQQA       => $lang === 'ar' ? 'الرقة' : 'Raqqa',
            self::HASAKAH     => $lang === 'ar' ? 'الحسكة' : 'Hasakah',
            self::DARA        => $lang === 'ar' ? 'درعا' : 'Daraa',
            self::AS_SUWAYDA  => $lang === 'ar' ? 'السويداء' : 'As-Suwayda',
            self::QUNEITRA    => $lang === 'ar' ? 'القنيطرة' : 'Quneitra',
        };
    }

    /**
     * Return all governorates as array for frontend (value + labels)
     */
    public static function allLabels(): array
    {
        return collect(self::cases())->map(fn($g) => [
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
            ->first(fn($g) => $g->label('en') === $name || $g->label('ar') === $name);
    }
}
