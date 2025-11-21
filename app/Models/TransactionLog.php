<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = ['transaction_id', 'status', 'log_data', 'notes'];

    protected $casts = [
        'log_data' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    } 
}