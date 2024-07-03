<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamAppAction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_app_action';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'action_name',
        'is_active'
    ];
}
