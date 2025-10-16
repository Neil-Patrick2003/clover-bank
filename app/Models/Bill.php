<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $table = 'bills';
    protected $fillable = ['biller_code','biller_name','status'];

    public function payments(): HasMany { return $this->hasMany(BillPayment::class, 'biller_id'); }
}
