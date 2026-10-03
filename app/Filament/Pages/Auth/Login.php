<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getTitle(): string|Htmlable
    {
        return 'Masuk - ERP Marel';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Sign in';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'PT Marel Sukses Pratama • Enterprise Resource Planning';
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->placeholder('nama@erpmsp.com');
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->placeholder('••••••••');
    }
}
