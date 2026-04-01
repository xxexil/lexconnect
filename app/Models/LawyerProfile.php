<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LawyerProfile extends Model {
    protected $fillable = [
        "user_id","specialty","firm","hourly_rate","experience_years","location","bio","is_certified","availability_status","rating","reviews_count","government_id_doc","ibp_id_doc","law_firm_id",
        "gcash_number","gcash_qr"
    ];
    protected $casts = ["is_certified" => "boolean"];
    public function user() { return $this->belongsTo(User::class); }
    public function lawFirm() { return $this->belongsTo(LawFirmProfile::class, 'law_firm_id'); }
    public function firmApplications() { return $this->hasMany(FirmApplication::class, 'lawyer_id', 'user_id'); }
    public function paymongoChildMerchant() { return $this->morphOne(PayMongoChildMerchant::class, 'owner'); }
    public function nextConsultation() {
        return $this->hasOne(Consultation::class, 'lawyer_id', 'user_id')
            ->where('status', 'upcoming')
            ->orderBy('scheduled_at');
    }

    public function upcomingConsultations() {
        return $this->hasMany(Consultation::class, 'lawyer_id', 'user_id')
            ->whereIn('status', ['upcoming', 'pending'])
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDays(14))
            ->whereHas('payment', function($q) {
                $q->where('status', 'downpayment_paid');
            })
            ->orderBy('scheduled_at');
    }
}
