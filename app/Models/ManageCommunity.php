<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageCommunity extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_communities';

    protected $fillable = ['community_profile_photo','community_banner_image','community_name','community_location','community_description','community_type_xid','activity_xid','is_active','created_by','modified_by'];
}
