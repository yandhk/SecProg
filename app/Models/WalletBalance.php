<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
    protected $fillable = ['learner_id','balance','last_updated'];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    public function learner()
    {
        return $this->belongsTo(User::class,'learner_id');
    }

    public function addFunds($amount)
    {
        $this->balance += $amount;
        $this->last_updated = now();
        $this->save();
    }

    public function deductFunds($amount)
    {
        if ($this->balance < $amount){
            return false;
        }
        $this->balance -= $amount;
        $this->last_updated = now();
        $this->save();
        return true;
    }
}