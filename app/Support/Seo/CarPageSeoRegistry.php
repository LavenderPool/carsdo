<?php

namespace App\Support\Seo;

use InvalidArgumentException;

final class CarPageSeoRegistry
{
    /**
     * @var array<string, array{name: string, car_seo_prefix: string, placeholders_hint: string}>
     */
    private const PAGE_DEFINITIONS = [
        'car_index' => [
            'name' => 'Основная страница автомобиля',
            'car_seo_prefix' => 'seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {year}, {site_name}, {price}, {price_range}, {configurations_count}.',
        ],
        'car_equipment' => [
            'name' => 'Страница комплектаций автомобиля',
            'car_seo_prefix' => 'equipment_seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {group}, {configuration_price}, {site_name}.',
        ],
        'car_dealer' => [
            'name' => 'Страница дилеров автомобиля',
            'car_seo_prefix' => 'dealer_seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {city}, {site_name}, {price}, {price_range}, {current_year}.',
        ],
        'car_reviews' => [
            'name' => 'Страница отзывов автомобиля',
            'car_seo_prefix' => 'reviews_seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {reviews_count}, {site_name}.',
        ],
        'car_crash_test' => [
            'name' => 'Страница краш-теста автомобиля',
            'car_seo_prefix' => 'crash_test_seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {crash_test_year}, {crash_test_rating}, {site_name}.',
        ],
        'car_test_drive' => [
            'name' => 'Страница тест-драйва автомобиля',
            'car_seo_prefix' => 'test_drive_seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {test_drives_count}, {site_name}.',
        ],
        'car_photo' => [
            'name' => 'Страница фото автомобиля',
            'car_seo_prefix' => 'photo_seo',
            'placeholders_hint' => 'Плейсхолдеры: {brand}, {car}, {site_name}.',
        ],
    ];

    /**
     * @return array<string, array{name: string, car_seo_prefix: string, placeholders_hint: string}>
     */
    public static function definitions(): array
    {
        return self::PAGE_DEFINITIONS;
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::PAGE_DEFINITIONS);
    }

    public static function has(string $pageKey): bool
    {
        return array_key_exists($pageKey, self::PAGE_DEFINITIONS);
    }

    /**
     * @return array{name: string, car_seo_prefix: string, placeholders_hint: string}
     */
    public static function definition(string $pageKey): array
    {
        if (! self::has($pageKey)) {
            throw new InvalidArgumentException("Unknown car page seo key [{$pageKey}].");
        }

        return self::PAGE_DEFINITIONS[$pageKey];
    }

    public static function name(string $pageKey): string
    {
        return self::definition($pageKey)['name'];
    }

    public static function carSeoPrefix(string $pageKey): string
    {
        return self::definition($pageKey)['car_seo_prefix'];
    }

    public static function placeholdersHint(string $pageKey): string
    {
        return self::definition($pageKey)['placeholders_hint'];
    }
}
