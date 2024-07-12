<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalBlockedProfile extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_blocked_profiles';

    protected $guarded = [];

    public function blockedProfile()
    {
        return $this->belongsTo(IamPrincipal::class,'blocked_iam_principal_xid');
    }
}
