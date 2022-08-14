<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PaymentTransaction extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;

    protected $table = 'payment_transactions';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user',
        'type',
        'purchase_code',
        'transaction_id',
        'store_product_id',
        'date_payment_start',
        'date_payment_end',
    ];

    /**
     * @return HasOne
     */
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }
}
