<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = ['account_id','type','amount','currency','reference_no','status','remarks'];

    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function billPayment(): HasOne { return $this->hasOne(BillPayment::class, 'transaction_id'); }
}
