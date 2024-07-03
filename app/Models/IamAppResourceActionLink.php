<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamAppResourceActionLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_app_resource_action_link';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'app_resource_xid',
        'app_action_xid',
        'is_active'
    ];
}
