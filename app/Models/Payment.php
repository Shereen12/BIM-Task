<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['transaction_id', 'amount', 'paid_date', 'method', 'details'];

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
