<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalSharedProfile extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_shared_profiles';
    protected $guarded = [];
}
