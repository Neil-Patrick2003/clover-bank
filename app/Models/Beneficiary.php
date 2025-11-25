<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    protected $table = 'beneficiaries';
    protected $fillable = ['user_id','name','bank','account_number','currency'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
