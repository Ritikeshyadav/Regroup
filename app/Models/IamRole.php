<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamRole extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_role';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'role_name',
        'is_active'
    ];
}
