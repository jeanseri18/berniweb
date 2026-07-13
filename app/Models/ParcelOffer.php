<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'courier_id',
        'amount',
        'courier_amount',
        'sender_amount',
        'last_counter_by',
        'status',
        'turns_used',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'courier_amount' => 'decimal:2',
        'sender_amount' => 'decimal:2',
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }
}

