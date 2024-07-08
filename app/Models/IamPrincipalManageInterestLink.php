<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalManageInterestLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_manage_interest_links';

    protected $fillable = ['iam_principal_xid', 'manage_interest_xid'];
}
