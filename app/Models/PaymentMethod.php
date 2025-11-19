<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name', 'type', 'provider', 'fee_percentage', 'fee_fixed', 'is_active', 'config'
    ];

    protected $casts = [
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function calculateFee($amount)
    {
        $percentageFee = $amount * ($this->fee_percentage / 100);
        return $percentageFee + $this->fee_fixed;
    }
}