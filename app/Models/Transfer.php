<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $table = 'transfers';
    protected $fillable = ['from_account_id','to_account_id','amount','currency','trx_out_id','trx_in_id'];

    public function fromAccount(): BelongsTo { return $this->belongsTo(Account::class, 'from_account_id'); }
    public function toAccount(): BelongsTo { return $this->belongsTo(Account::class, 'to_account_id'); }
    public function transactionOut(): BelongsTo { return $this->belongsTo(Transaction::class, 'trx_out_id'); }
    public function transactionIn(): BelongsTo { return $this->belongsTo(Transaction::class, 'trx_in_id'); }
}
