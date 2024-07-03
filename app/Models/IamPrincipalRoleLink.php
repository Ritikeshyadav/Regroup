<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalRoleLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_role_link';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'principal_xid',
        'principal_group_xid',
        'role_xid',
        'is_active'
    ];
}
