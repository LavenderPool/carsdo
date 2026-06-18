<?php

namespace App\Support\Seo;

final class AdminSeoFields
{
    /**
     * @var list<string>
     */
    public const BASE_KEYS = [
        'title',
        'description',
        'h1',
        'og_image',
        'canonical_url',
        'robots',
    ];

    /**
     * @var list<string>
     */
    public const SITE_PAGE_PREFIXES = [
        'home',
        'new_cars',
        'electric_cars',
        'crash_tests',
        'test_drives',
        'cars_photo',
    ];

    /**
     * @var list<string>
     */
    public const CAR_PAGE_PREFIXES = [
        'seo',
        'equipment_seo',
        'reviews_seo',
        'crash_test_seo',
        'test_drive_seo',
    ];

    /**
     * @return list<string>
     */
    public static function fieldsForPrefix(string $prefix): array
    {
        return array_map(
            static fn (string $key): string => "{$prefix}_{$key}",
            self::BASE_KEYS,
        );
    }

    /**
     * @return list<string>
     */
    public static function brandFields(): array
    {
        return self::fieldsForPrefix('seo');
    }

    /**
     * @return list<string>
     */
    public static function carFields(): array
    {
        $fields = [];

        foreach (self::CAR_PAGE_PREFIXES as $prefix) {
            array_push($fields, ...self::fieldsForPrefix($prefix));
        }

        return $fields;
    }

    /**
     * @return list<string>
     */
    public static function settingFields(): array
    {
        $fields = [
            'seo_title_suffix',
            'seo_default_description',
            'seo_default_robots',
            'seo_default_og_image',
        ];

        foreach (self::SITE_PAGE_PREFIXES as $prefix) {
            array_push($fields, ...self::fieldsForPrefix("{$prefix}_seo"));
        }

        return $fields;
    }
}
