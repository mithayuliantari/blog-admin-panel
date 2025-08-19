<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        Log::info('Filament access check:', [
            'user_email' => $this->email ?? 'guest',
            'user_role' => $this->role ?? 'null',
            'panel_id' => $panel->getId()
        ]);

        return $this->role === 'admin' && $panel->getId() === 'admin';
    }

    // Helper method untuk check admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
