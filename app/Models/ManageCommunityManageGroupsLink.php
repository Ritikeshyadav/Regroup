<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageCommunityManageGroupsLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_community_and_groups_links';

    protected $guarded = [];


    public function groupData()
    {
        return $this->hasOne(ManageGroup::class, 'id', 'manage_group_xid');
    }
    public function communityData()
    {
        return $this->hasOne(ManageCommunity::class, 'id', 'manage_community_xid');
    }
}

