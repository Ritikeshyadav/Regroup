<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "notification_master";
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'iamprincipal_xid',
        'title',
        'description',
        'image',
        'is_read',
        'is_active',
    ];
}
