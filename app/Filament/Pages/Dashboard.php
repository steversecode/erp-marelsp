<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\Support\Enums\NavigationGroup;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'icon-dashboard';

    protected string $view = 'filament.pages.dashboard';

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return null;
    }

    public function getHeaderWidgets(): array
    {
        return [];
    }

    public function getVisibleHeaderWidgets(): array
    {
        return [];
    }

    public function getWidgets(): array
    {
        return [];
    }

    public function getVisibleWidgets(): array
    {
        return [];
    }

    public function getFooterWidgets(): array
    {
        return [];
    }

    public function getVisibleFooterWidgets(): array
    {
        return [];
    }

    public function getAppsProperty(): array
    {
        $navigation = filament()->getNavigation();
        $apps = [];

        foreach ($navigation as $group) {
            $label = $group->getLabel();
            $icon = $group->getIcon();
            $firstItem = $group->getItems()->first();
            $url = $firstItem?->getUrl();

            // Skip empty groups or dashboard group itself in the app launcher
            if (! $label || ! $url || ! $icon || strtolower($label) === 'dashboard') {
                continue;
            }

            $apps[] = [
                'label'    => $label,
                'icon'     => $icon,
                'url'      => $url,
                'isActive' => $group->isActive(),
            ];
        }

        return $apps;
    }
}
