<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    protected $table = 'beneficiaries';
    protected $fillable = ['user_id','nickname','bank_code','account_number','account_name'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
