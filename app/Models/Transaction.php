<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'account_id','type','amount','currency',
        // you can keep 'reference_no' in fillable or omit it;
        // the creating hook below will populate it if empty:
        'reference_no','status','remarks',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_POSTED = 'posted';
    public const TYPE_BILL_PAYMENT = 'bill_payment';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $tx) {
            if (empty($tx->reference_no)) {
                $tx->reference_no = self::generateRef();   // <- always set
            }
            if (empty($tx->status)) {
                $tx->status = self::STATUS_POSTED;
            }
            if (! empty($tx->currency)) {
                $tx->currency = strtoupper($tx->currency);
            }
        });
    }

    public static function generateRef(string $prefix = 'TX'): string
    {
        // e.g. TX-20251024-8CH2Q9WL
        return sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(8)));
    }
}
