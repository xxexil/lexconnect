<?php

namespace App\Services;

use App\Models\LawFirmProfile;
use App\Models\LawyerProfile;
use App\Models\PayMongoChildMerchant;
use Illuminate\Support\Arr;

class PayMongoChildMerchantService
{
    public function ensureForOwner(LawyerProfile|LawFirmProfile $owner): PayMongoChildMerchant
    {
        $merchant = $owner->paymongoChildMerchant()->firstOrCreate(
            ['provider' => 'paymongo'],
            [
                'merchant_type' => $owner instanceof LawyerProfile ? 'lawyer' : 'law_firm',
                'status' => PayMongoChildMerchant::STATUS_DRAFT,
                'onboarding_mode' => $this->defaultMode(),
                'metadata' => $this->buildMetadata($owner),
            ]
        );

        if (!$merchant->metadata) {
            $merchant->update(['metadata' => $this->buildMetadata($owner)]);
        }

        return $merchant->fresh();
    }

    public function platformsEnabled(): bool
    {
        return (bool) config('services.paymongo.child_merchants_enabled', false);
    }

    public function defaultMode(): string
    {
        return config('services.paymongo.child_merchants_mode', 'hosted');
    }

    public function supportMessage(): string
    {
        if ($this->platformsEnabled()) {
            return 'PayMongo Platforms is enabled in configuration. You can proceed with merchant onboarding once the API workflow is connected.';
        }

        return 'Ask PayMongo to enable Platforms / Child Accounts for your merchant before connecting this payout setup to live onboarding.';
    }

    protected function buildMetadata(LawyerProfile|LawFirmProfile $owner): array
    {
        if ($owner instanceof LawyerProfile) {
            return array_filter([
                'display_name' => $owner->user?->name,
                'email' => $owner->user?->email,
                'specialty' => $owner->specialty,
                'location' => $owner->location,
                'law_firm_id' => $owner->law_firm_id,
            ], fn ($value) => !is_null($value) && $value !== '');
        }

        return array_filter([
            'display_name' => $owner->firm_name,
            'email' => $owner->user?->email,
            'city' => $owner->city,
            'phone' => $owner->phone,
            'website' => $owner->website,
            'specialties' => Arr::wrap($owner->specialties),
        ], fn ($value) => !is_null($value) && $value !== '' && $value !== []);
    }
}
