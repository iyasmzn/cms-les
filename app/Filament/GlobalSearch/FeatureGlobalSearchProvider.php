<?php

namespace App\Filament\GlobalSearch;

use Filament\Facades\Filament;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\DefaultGlobalSearchProvider;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * Extends Filament's default global search so that the "Fitur" (halaman & resource
 * panel) juga muncul di kotak pencarian. Mengklik hasil membuka halaman fitur —
 * untuk resource diarahkan ke halaman daftar, bukan halaman edit record.
 */
class FeatureGlobalSearchProvider extends DefaultGlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $recordResults = parent::getResults($query);

        $featureResults = $this->getFeatureResults($query);

        $builder = GlobalSearchResults::make();

        if ($featureResults !== []) {
            $builder->category('Fitur', $featureResults);
        }

        $recordResults?->getCategories()->each(
            fn ($results, string $category) => $builder->category($category, $results),
        );

        return $builder;
    }

    /**
     * @return array<GlobalSearchResult>
     */
    protected function getFeatureResults(string $query): array
    {
        $results = [];

        /** @var array<class-string> $components */
        $components = [
            ...Filament::getPages(),
            ...Filament::getResources(),
        ];

        foreach ($components as $component) {
            if (! $component::shouldRegisterNavigation()) {
                continue;
            }

            if (! $component::canAccess()) {
                continue;
            }

            $label = $component::getNavigationLabel();
            $group = $this->normalizeGroup($component::getNavigationGroup());

            if (! $this->matches($query, $label, $group)) {
                continue;
            }

            try {
                $url = $component::getUrl();
            } catch (\Throwable) {
                continue;
            }

            $results[] = new GlobalSearchResult(
                title: $label,
                url: $url,
                details: $group !== null ? ['Grup' => $group] : [],
            );
        }

        return $results;
    }

    protected function matches(string $query, string $label, ?string $group): bool
    {
        $haystack = Str::lower(trim($label.' '.($group ?? '')));

        return str_contains($haystack, Str::lower(trim($query)));
    }

    protected function normalizeGroup(string|UnitEnum|null $group): ?string
    {
        if ($group instanceof UnitEnum) {
            return $group->name;
        }

        return $group;
    }
}
