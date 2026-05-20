<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CinetpayPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parcel_id',
        'amount',
        'currency',
        'status',
        'provider',
        'provider_payment_id',
        'checkout_url',
        'provider_payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'provider_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}

