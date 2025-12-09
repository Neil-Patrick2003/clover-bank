<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $table = 'accounts';
    protected $guarded = [];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }

    public static function generateNumber($application = null): string
    {
        // Example format: YYMM + userId padded + random 3
        $prefix = now()->format('ym');
        $userId = str_pad((string)($application->user_id ?? 0), 6, '0', STR_PAD_LEFT);

        do {
            $candidate = "{$prefix}{$userId}".str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('account_number', $candidate)->exists());

        return $candidate;
    }

    // Transfers where this account is sender/receiver
    public function outgoingTransfers(): HasMany { return $this->hasMany(Transfer::class, 'from_account_id'); }
    public function incomingTransfers(): HasMany { return $this->hasMany(Transfer::class, 'to_account_id'); }
}
