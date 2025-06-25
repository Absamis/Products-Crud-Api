<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        "name",
        "category",
        "price",
        "user_id",
        "expiry_date",
        "discount_price",
        "description",
        "contact",
        "image",
        "status"
    ];

    public function imageUrl():Attribute{
        return Attribute::get(
            function(){
                return asset($this->image);
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    protected static function booted()
    {
        static::creating(function (Product $product) {
            $product->user_id = auth()->user()->id;
        });

        static::addGlobalScope('for-user', function (Builder $builder) {
            $builder->where("user_id", auth()->user()->id);
        });
    }
}
