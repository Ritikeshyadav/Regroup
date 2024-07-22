<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalManageCommunityLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_manage_community_links';

    protected $guarded = [];



    public function communityData()
    {
        return $this->hasOne(ManageCommunity::class, 'id', 'manage_community_xid');
    }
    
}
