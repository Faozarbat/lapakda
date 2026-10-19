<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherUsage extends Model
{
    protected $fillable = ['voucher_id', 'user_id', 'store_id'];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
}