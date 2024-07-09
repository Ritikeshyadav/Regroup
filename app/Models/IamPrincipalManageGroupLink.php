<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalManageGroupLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_manage_group_links';

    protected $guarded = [];
    public function groupData()
    {
        return $this->hasOne(ManageGroup::class, 'id', 'manage_group_xid');
    }

    
}
