<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_group';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'principal_group_name',
        'is_active'
    ];
}
