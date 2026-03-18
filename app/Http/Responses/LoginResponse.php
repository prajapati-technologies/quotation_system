<?php

namespace App\Http\Responses;

use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (auth()->check() && auth()->user()->role === 'sales') {
            return redirect()->to(QuotationResource::getUrl('index'));
        }

        return redirect()->intended(
            config('filament.home_url') ?? filament()->getUrl()
        );
    }
}
