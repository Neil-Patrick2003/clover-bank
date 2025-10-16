<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    protected $table = 'application_documents';
    protected $fillable = ['application_id','doc_type','file_url','verified_status'];

    public function application(): BelongsTo { return $this->belongsTo(CustomerApplication::class, 'application_id'); }
}
