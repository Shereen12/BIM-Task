<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'parent_id'];

    public function subcategories(){
        return $this->hasMany(Category::class, 'parent_id')->with('subcategories');
    }

    public function parentCategory(){
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
