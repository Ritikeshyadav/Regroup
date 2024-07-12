<?php

namespace App\Models;

use App\Models\IamPrincipalType;
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

    protected $guarded =
    [
    ];

    public function interestsLink()
    {
        return $this->hasMany(IamPrincipalManageInterestLink::class,'iam_principal_xid','id');
    }

    public function iamPrincipalType()
    {
        return $this->belongsTo(IamPrincipalType::class,'principal_type_xid');
    }
}
