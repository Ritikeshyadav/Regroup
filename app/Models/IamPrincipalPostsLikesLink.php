<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalPostsLikesLink extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_posts_likes_links';
    protected $guarded = [];

    // protected $append = array('total_like');

    // public function getTotal_LikeAttribute($id)
    // {
    //     return $id;
    // }

    public function likeIcon()
    {
        return $this->belongsTo(LikeIcons::class,'like_icons_xid');
    }
}
