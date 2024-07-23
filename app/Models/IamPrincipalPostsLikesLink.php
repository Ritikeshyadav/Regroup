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

    public function iam_principal()
    {
        return $this->belongsTo(IamPrincipal::class,'iam_principal_xid');
    }

    public function likeIcon()
    {
        return $this->belongsTo(LikeIcons::class,'like_icons_xid');
    }
}
