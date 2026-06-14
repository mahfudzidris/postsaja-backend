<?php

namespace App\Filament\Auth;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse implements \Filament\Auth\Http\Responses\Contracts\LogoutResponse
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->to('https://postsaja.com');
    }
}
