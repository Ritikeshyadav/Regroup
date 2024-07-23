<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageCommunity extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_communities';

    // protected $fillable = ['community_profile_photo','community_banner_image','community_name','community_location','community_description','community_type_xid','activity_xid','is_active','created_by','modified_by'];

    protected $guarded = [];

    public function tags()
    {
        return $this->hasMany(ManageTags::class,'manage_community_xid','id');
    }

    public function activityData()
    {
        return $this->hasOne(ManageActivities::class, 'id', 'activity_xid');
    }
    
    public function communityTypeData()
    {
        return $this->hasOne(ManageCommunityType::class, 'id', 'community_type_xid');
    }
}
