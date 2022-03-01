<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'subcategory_id', 'amount', 'customer_id', 'due', 'vat', 'is_vat_inclusive', 'status'];

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function subcategory(){
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function getStatus(){
        $paymentsSum = $this->payments->sum('amount');
        $today = Carbon::create(date("Y-m-d"));
        $dueDate = Carbon::create($this->due);
        if($paymentsSum >= $this->amount){
            $status = 'Paid';
        }
        else if($dueDate->gte($today)){
            $status = 'Outstanding';
        }
        else{
            $status = 'Overdue';
        }
        return $status;
    }

    public function isOverdue(){
        return $status == 'Overdue';
    }
}
