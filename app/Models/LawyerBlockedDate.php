<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LawyerBlockedDate extends Model {
    protected $table = 'lawyer_blocked_dates';
    protected $fillable = ['lawyer_id', 'blocked_date', 'reason'];
    protected $casts = ['blocked_date' => 'date'];

    public function lawyer() {
        return $this->belongsTo(User::class, 'lawyer_id');
    }
}
