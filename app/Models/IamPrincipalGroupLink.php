<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalGroupLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_principal_group_link';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'principal_xid',
        'principal_group_xid',
        'is_active'
    ];
}
