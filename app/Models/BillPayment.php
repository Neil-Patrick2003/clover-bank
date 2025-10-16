<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillPayment extends Model
{

    protected $table = 'bill_payments';
    protected $fillable = ['account_id','biller_id','amount','reference_no','status','transaction_id'];

    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function biller(): BelongsTo { return $this->belongsTo(Bill::class, 'biller_id'); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
}
