<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "manage_subscriptions";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'iam_principal_xid',
        'stripe_customer_id',
        'product_stripe_id',
        'stripe_price_id',
        'manage_product_xid',
        'manage_product_price_xid',
        'stripe_subscription_id',
        'amount',
        'subscription_date',
        'start_date',
        'end_date',
        'cancelled_at',
        'paused_at',
        'is_cancelled',
        'is_paused',
        'is_active',
    ];
}
