<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageCommunity extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_communities';

    protected $guarded = [];
    public function activityData()
    {
        return $this->hasOne(ManageActivities::class, 'id', 'activity_xid');
    }
    public function communityTypeData()
    {
        return $this->hasOne(ManageCommunityType::class, 'id', 'community_type_xid');
    }
}
