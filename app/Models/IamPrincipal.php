<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class IamPrincipal extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'iam_principal';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password_hash', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable =
    [
        'principal_type_xid',
        'principal_source_xid',
        'google_id',
        'apple_id',
        'facebook_id',
        'microsoft_id',
        'user_name',
        'password_hash',
        'pin',
        'full_name',
        'gender',
        'date_of_birth',
        'phone_number',
        'other_phone_number',
        'email_address',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'post_code',
        'last_login_datetime',
        'profile_photo',
        'referral_code',
        'description',
        'is_active',
        'is_profile_updated'
    ];
}
