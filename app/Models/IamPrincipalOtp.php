<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalOtp extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal_otp';
    protected $dates = ['deleted_at'];

    protected $fillable =
    [
        'email_id',
        'principal_xid',
        'otp_code',
        'otp_purpose',
        'valid_till',
        'is_used',
        'is_active'
    ];
}
