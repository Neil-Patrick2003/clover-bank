<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerApplication extends Model
{
    protected $table = 'customer_applications';
    protected $fillable = [
        'user_id','product_type','channel','status','assigned_admin_id','remarks','submitted_at','decided_at'
    ];
    protected $casts = ['submitted_at' => 'datetime', 'decided_at' => 'datetime'];

    public function applicant(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function assignedAdmin(): BelongsTo { return $this->belongsTo(User::class, 'assigned_admin_id'); }
    public function accountRequest(): HasOne { return $this->hasOne(ApplicationAccount::class, 'application_id'); }
    public function documents(): HasMany { return $this->hasMany(ApplicationDocument::class, 'application_id'); }

    public function requestedAccounts() {
        return $this->hasMany(ApplicationAccount::class, 'application_id');
    }
}
