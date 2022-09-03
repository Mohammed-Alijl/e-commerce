<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=['name','category_id','price','description'];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function images(){
        return $this->hasMany(Image::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'user_product');
    }
    public function colors(){
        return $this->hasMany(Color::class);
    }
    public function sizes(){
        return $this->hasMany(Size::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
    public function productCart(){
        return $this->hasMany(ProductCart::class);
    }
}
