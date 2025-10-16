<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAccount extends Model
{
    protected $table = 'application_accounts';
    public $incrementing = false; // PK is application_id
    protected $fillable = ['id',    'application_id','requested_type','currency','initial_deposit'];

    public function application(): BelongsTo { return $this->belongsTo(CustomerApplication::class, 'application_id'); }
}
