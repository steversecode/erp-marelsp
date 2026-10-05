<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    public ?array $data = [
        'email'    => '',
        'password' => '',
        'remember' => true,
    ];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill([
            'email'    => '',
            'password' => '',
            'remember' => true,
        ]);
    }

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
            ->placeholder('Masukkan kata sandi');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $this->validate([
            'data.email'    => ['required', 'string', 'email'],
            'data.password' => ['required', 'string'],
        ], [
            'data.email.required'    => 'Alamat email wajib diisi.',
            'data.email.email'       => 'Format email tidak valid.',
            'data.password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials = [
            'email'    => $this->data['email'] ?? '',
            'password' => $this->data['password'] ?? '',
        ];

        $remember = (bool) ($this->data['remember'] ?? false);

        if (! Filament::auth()->attempt($credentials, $remember)) {
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function getCachedFormActions(): array
    {
        return [];
    }

    public function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function getRenderHookScopes(): array
    {
        return [];
    }
}
