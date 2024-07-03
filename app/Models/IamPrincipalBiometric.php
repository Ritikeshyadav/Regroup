<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalBiometric extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_biometric';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'principal_xid',
        'biometric_type',
        'biometric_data',
        'is_active'
    ];
}
