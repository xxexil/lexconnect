<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'phone', 'bio',
    ];

    /**
     * Returns a fully-resolved avatar URL.
     * Handles both legacy full URLs and new storage-relative paths.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) return null;
        if (str_starts_with($this->avatar, 'http')) return $this->avatar;
        return asset('storage/' . $this->avatar);
    }

    public function lawyerProfile() {
        return $this->hasOne(LawyerProfile::class);
    }

    public function consultationsAsClient() {
        return $this->hasMany(Consultation::class, 'client_id');
    }

    public function consultationsAsLawyer() {
        return $this->hasMany(Consultation::class, 'lawyer_id');
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function lawFirmProfile() {
        return $this->hasOne(LawFirmProfile::class);
    }

    public function isLawyer() {
        return $this->role === 'lawyer';
    }

    public function isLawFirm() {
        return $this->role === 'law_firm';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
