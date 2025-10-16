<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycProfile extends Model
{
    protected $table = 'kyc_profiles';
    protected $fillable = ['user_id','kyc_level','id_type','id_number','id_expiry'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
