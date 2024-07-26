<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class IamPrincipalBusinessUserLink extends Model
{
    use HasFactory;
   
    use SoftDeletes;

    protected $table = 'iam_principal_business_user_links';
    protected $dates = ['deleted_at'];

    protected $guarded =
    [ ];

    public function businessType()
    {
        return $this->belongsTo(BusinessTypes::class,'business_type_xid');
    }

    public function iamPrincipalData()
    {
        return $this->hasOne(IamPrincipal::class,'id','iam_principal_xid')->withDefault();
    }
}
