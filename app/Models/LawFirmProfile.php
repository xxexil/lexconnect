<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawFirmProfile extends Model
{
    protected $fillable = [
        'user_id', 'firm_name', 'tagline', 'description', 'address', 'city',
        'website', 'phone', 'founded_year', 'firm_size', 'cut_percentage', 'specialties',
        'is_verified', 'logo', 'dti_sec_registration_doc', 'business_permit_doc',
        'valid_id_doc', 'ibp_id_doc', 'ibp_details', 'rating', 'reviews_count',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'cut_percentage' => 'decimal:2',
        'specialties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lawyers()
    {
        return $this->hasMany(LawyerProfile::class, 'law_firm_id');
    }

    public function applications()
    {
        return $this->hasMany(FirmApplication::class, 'law_firm_id');
    }

    public function getFirmSizeLabelAttribute(): string
    {
        return match($this->firm_size) {
            'solo'   => 'Solo Practice',
            'small'  => 'Small Firm (2–10)',
            'medium' => 'Mid-size Firm (11–50)',
            'large'  => 'Large Firm (50+)',
            default  => ucfirst($this->firm_size),
        };
    }
    public function getFormattedCutPercentageAttribute(): string
    {
        return rtrim(rtrim(number_format((float) $this->cut_percentage, 2, '.', ''), '0'), '.');
    }

    public static function documentColumns(): array
    {
        return [
            'dti_sec_registration' => 'dti_sec_registration_doc',
            'business_permit' => 'business_permit_doc',
            'valid_id' => 'valid_id_doc',
            'ibp_id' => 'ibp_id_doc',
        ];
    }

    public function documentPath(string $document): ?string
    {
        $column = static::documentColumns()[$document] ?? null;

        return $column ? $this->{$column} : null;
    }

    public function documentUrl(string $document): ?string
    {
        $path = $this->documentPath($document);

        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return route('documents.law-firms.document', [
            'lawFirmProfile' => $this,
            'document' => $document,
        ]);
    }
}
