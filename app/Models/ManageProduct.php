<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'manage_product';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_name',
        'product_description',
        'image',
        'product_monthly_price',
        'product_yearly_price',
        'stripe_product_id',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'is_popular'
    ];
}
