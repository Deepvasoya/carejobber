<?php

namespace App\Helpers;

use App\SiteSetting;

class LocationHelper
{
    /**
     * Get the active location levels from site settings.
     * Returns: 1-4 (number of location fields to show)
     */
    public static function getLocationLevels(): int
    {
        $siteSetting = SiteSetting::first();
        
        // If location_multiple_fields is disabled, return 1 (city only)
        if (!($siteSetting->location_multiple_fields ?? true)) {
            return 1;
        }
        
        return (int) ($siteSetting->location_levels ?? 3);
    }

    /**
     * Check if location multiple fields is enabled.
     */
    public static function isMultipleFieldsEnabled(): bool
    {
        $siteSetting = SiteSetting::first();
        return (bool) ($siteSetting->location_multiple_fields ?? true);
    }

    /**
     * Check if country field should be shown (Level 3 or 4).
     */
    public static function showCountry(): bool
    {
        return in_array(self::getLocationLevels(), [3, 4]);
    }

    /**
     * Check if state field should be shown (Level 2, 3, or 4).
     */
    public static function showState(): bool
    {
        return in_array(self::getLocationLevels(), [2, 3, 4]);
    }

    /**
     * Check if city field should be shown (always true for levels 1-4).
     */
    public static function showCity(): bool
    {
        return true;
    }

    /**
     * Check if district field should be shown (Level 4 only).
     */
    public static function showDistrict(): bool
    {
        return self::getLocationLevels() === 4;
    }

    /**
     * Get custom label for location field.
     */
    public static function getFieldLabel(int $fieldNumber): string
    {
        $siteSetting = SiteSetting::first();
        $labelField = "location_field_{$fieldNumber}_label";
        
        $customLabel = $siteSetting->$labelField ?? null;
        
        // Return custom label if set, otherwise return default
        if (!empty($customLabel)) {
            return ucfirst($customLabel);
        }
        
        // Default labels
        $defaults = [
            1 => 'Country',
            2 => 'State/Province',
            3 => 'City',
            4 => 'District',
        ];
        
        return $defaults[$fieldNumber] ?? 'Location';
    }

    /**
     * Get location fields configuration for forms.
     */
    public static function getFormConfig(): array
    {
        $levels = self::getLocationLevels();
        
        return [
            'levels' => $levels,
            'multiple_fields_enabled' => self::isMultipleFieldsEnabled(),
            'show_country' => self::showCountry(),
            'show_state' => self::showState(),
            'show_city' => self::showCity(),
            'show_district' => self::showDistrict(),
            'labels' => [
                'country' => __('Select ' . self::getFieldLabel(1)),
                'state' => __('Select ' . self::getFieldLabel(2)),
                'city' => __('Select ' . self::getFieldLabel(3)),
                'district' => __('Select ' . self::getFieldLabel(4)),
            ],
        ];
    }
}
