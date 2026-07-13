<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'id_card_front',
        'id_card_back',
        'selfie',
        'selfie_with_id',
        'transport_type',
        'transport_mode',
        'transport_model',
        'transport_plate',
        'transport_photo',
        'zone_hint',
        'availability_hint',
        'payment_kind',
        'momo_number',
        'status',
        'admin_notes',
        'full_name',
        'phone',
        'email',
        'address',
        'payment_method',
        'payment_account'

    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
