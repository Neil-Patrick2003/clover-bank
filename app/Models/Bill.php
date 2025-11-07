<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{


    protected $table = 'bills';
    protected $guarded =[];
    protected $casts = [
        'biller_name' => 'string',
    ];

    public function payments(): HasMany { return $this->hasMany(BillPayment::class, 'biller_id'); }
}
