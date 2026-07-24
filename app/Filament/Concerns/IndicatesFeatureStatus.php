<?php

namespace App\Filament\Concerns;

/**
 * Marks a resource's sidebar item with a "Nonaktif" badge when the public
 * feature it belongs to is disabled in settings. When the feature is enabled,
 * the resource's own badge (if any) is used instead.
 *
 * Implementing resources must declare the feature key:
 *   protected static string $feature = 'donasi';
 *
 * To keep an existing badge while enabled, override the *WhenEnabled methods.
 */
trait IndicatesFeatureStatus
{
    public static function getNavigationBadge(): ?string
    {
        if (! feature_enabled(static::$feature)) {
            return 'Nonaktif';
        }

        return static::getNavigationBadgeWhenEnabled();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        if (! feature_enabled(static::$feature)) {
            return 'gray';
        }

        return static::getNavigationBadgeColorWhenEnabled();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        if (! feature_enabled(static::$feature)) {
            return 'Fitur ini dinonaktifkan di Pengaturan › Fitur Website';
        }

        return null;
    }

    protected static function getNavigationBadgeWhenEnabled(): ?string
    {
        return null;
    }

    protected static function getNavigationBadgeColorWhenEnabled(): string|array|null
    {
        return null;
    }
}
