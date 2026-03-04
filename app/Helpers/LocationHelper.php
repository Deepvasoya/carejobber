<?php

namespace App\Helpers;

use App\SiteSetting;

class LocationHelper
{
    /**
     * Get the active location levels from site settings.
     * Returns: 1 = City only, 2 = State > City, 3 = Country > State > City
     */
    public static function getLocationLevels(): int
    {
        $siteSetting = SiteSetting::first();
        return (int) ($siteSetting->location_levels ?? 3);
    }

    /**
     * Check if country field should be shown.
     */
    public static function showCountry(): bool
    {
        return self::getLocationLevels() === 3;
    }

    /**
     * Check if state field should be shown.
     */
    public static function showState(): bool
    {
        return in_array(self::getLocationLevels(), [2, 3]);
    }

    /**
     * Check if city field should be shown.
     */
    public static function showCity(): bool
    {
        return true; // City is always shown (all levels include city)
    }

    /**
     * Get location fields configuration for forms.
     */
    public static function getFormConfig(): array
    {
        $levels = self::getLocationLevels();
        
        return [
            'levels' => $levels,
            'show_country' => $levels === 3,
            'show_state' => in_array($levels, [2, 3]),
            'show_city' => true,
            'labels' => [
                'country' => __('Select Country'),
                'state' => __('Select Province/State'),
                'city' => __('Select City'),
            ],
        ];
    }
}
