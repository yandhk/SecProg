<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'transaction_id', 'learner_id', 'course_id', 'payment_method_id',
        'amount', 'fee_amount', 'total_amount', 'status', 'payment_type',
        'payment_details', 'gateway_response', 'expires_at', 'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_details' => 'array',
        'gateway_response' => 'array',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    public function learner()
    {
        return $this->belongsTo(User::class, 'learner_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function logs()
    {
        return $this->hasMany(TransactionLog::class, 'transaction_id');
    }

    public function isExpired()
    {
        return now()->gt($this->expires_at);
    }

    public function canBePaid()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}