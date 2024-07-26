<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalPinnedLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_pinned_links';
    protected $guarded = [];

    // protected $appends = array('tag_detail','community_detail','pin_user_detail');

    public function tag()
    {
        return $this->belongsTo(ManageTags::class,'manage_tags_xid');
    }
    public function community()
    {
        return $this->belongsTo(ManageCommunity::class,'manage_communities_xid');
    }
    public function pin_user()
    {
        return $this->belongsTo(IamPrincipal::class,'pin_iam_principal_xid');
    }
}
