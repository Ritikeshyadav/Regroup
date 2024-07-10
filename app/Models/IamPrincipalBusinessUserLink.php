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

}
