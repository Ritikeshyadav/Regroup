<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalFollowers extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_followers';

    protected $fillable = ['iam_principal_xid','following_iam_principal_xid'];
}
