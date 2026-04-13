<?php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LawyerProfile extends Model {
    protected $fillable = [
        "user_id","specialty","firm","hourly_rate","experience_years","location","bio","is_certified","availability_status","rating","reviews_count","government_id_doc","ibp_id_doc","law_firm_id",
        "gcash_number","gcash_qr"
    ];
    protected $casts = ["is_certified" => "boolean"];
    public function user() { return $this->belongsTo(User::class); }
    public function lawFirm() { return $this->belongsTo(LawFirmProfile::class, 'law_firm_id'); }
    public function firmApplications() { return $this->hasMany(FirmApplication::class, 'lawyer_id', 'user_id'); }
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

    public function isInConsultation(?Carbon $at = null): bool
    {
        if (!$this->user_id) {
            return false;
        }

        $at ??= now();

        return Consultation::where('lawyer_id', $this->user_id)
            ->where('status', 'upcoming')
            ->where('scheduled_at', '<=', $at)
            ->get(['scheduled_at', 'duration_minutes'])
            ->contains(function (Consultation $consultation) use ($at) {
                return $at->lt($consultation->scheduled_at->copy()->addMinutes($consultation->duration_minutes));
            });
    }

    public function hasActiveSession(?Carbon $at = null): bool
    {
        if (!$this->user_id) {
            return false;
        }

        $at ??= now();

        if (auth()->check() && (int) auth()->id() === (int) $this->user_id) {
            return true;
        }

        if (config('session.driver') !== 'database') {
            return ($this->availability_status ?? 'offline') !== 'offline';
        }

        $sessionTable = config('session.table', 'sessions');
        if (!Schema::hasTable($sessionTable)) {
            return ($this->availability_status ?? 'offline') !== 'offline';
        }

        $cutoff = $at->copy()->subMinutes((int) config('session.lifetime', 120))->timestamp;

        return DB::table($sessionTable)
            ->where('user_id', $this->user_id)
            ->where('last_activity', '>=', $cutoff)
            ->exists();
    }

    public function currentStatus(?Carbon $at = null): string
    {
        $at ??= now();

        if (auth()->check() && (int) auth()->id() === (int) $this->user_id) {
            return $this->isInConsultation($at) ? 'busy' : 'active';
        }

        if (!$this->hasActiveSession($at)) {
            return 'offline';
        }

        if ($this->isInConsultation($at)) {
            return 'busy';
        }

        return 'active';
    }

    public function currentStatusClass(?Carbon $at = null): string
    {
        $status = $this->currentStatus($at);

        return $status === 'active' ? 'available' : $status;
    }

    public function currentStatusLabel(?Carbon $at = null): string
    {
        return ucfirst($this->currentStatus($at));
    }
}
