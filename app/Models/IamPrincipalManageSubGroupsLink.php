<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalManageSubGroupsLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_manage_sub_groups_links';

    protected $guarded = [];
    public function groupData()
    {
        return $this->hasOne(ManageGroup::class, 'id', 'manage_group_xid');
    }
    public function subGroupData()
    {
        return $this->hasOne(ManageSubGroups::class, 'id', 'manage_sub_group_xid');
    }

    
}
