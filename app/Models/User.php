<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    // this will auto load realation with eager load
    protected $with = ['media'];

    public function registerMediaConversions(?Media $media = null): void
    {
        // For a profile picture, we only need one version.
        // We'll name it 'avatar' for clarity.
        $this->addMediaConversion('webp')
            ->format('webp') // Convert to a modern, efficient format
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->nonQueued(); // Process immediately, good for profile pics
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'google2fa_secret',
        'google2fa_enabled',
        'two_factor_recovery_codes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google2fa_enabled' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->google2fa_enabled && $this->google2fa_secret !== null;
    }

    public function getRecoveryCodes(): array
    {
        if (!$this->two_factor_recovery_codes) {
            return [];
        }
        return json_decode($this->two_factor_recovery_codes, true) ?? [];
    }

    public function setRecoveryCodes(array $codes): void
    {
        $this->two_factor_recovery_codes = json_encode($codes);
        $this->save();
    }

    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
        }
        $this->setRecoveryCodes($codes);
        return $codes;
    }

    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->getRecoveryCodes();
        $key = array_search($code, $codes);
        if ($key !== false) {
            unset($codes[$key]);
            $this->setRecoveryCodes(array_values($codes));
            return true;
        }
        return false;
    }

    public function adminlte_profile_url()
    {
        return route('profile');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
