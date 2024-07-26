<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManagePost extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'manage_posts';
    protected $guarded = [];

    protected $appends = array('likecount','is_i_liked','likeIcon','total_comment','total_save','tags_xid');

    public function iam_principal()
    {
        return $this->belongsTo(IamPrincipal::class,'iam_principal_xid');
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To get 
     */
    // public function likeCount()
    // {
    //     return $this->hasMany(IamPrincipalPostsLikesLink::class,'manage_posts_xid','id');
    // }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : added attribute to get total like count 
     */
    public function getLikecountAttribute($id)
    {
        return IamPrincipalPostsLikesLink::where(['manage_posts_xid'=>$this->id])->count();
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : added attribute to get that i liked this post or not 
     */
    public function getIsILikedAttribute($id)
    {
        return IamPrincipalPostsLikesLink::with('likeIcon')->where(['manage_posts_xid'=>$this->id,'iam_principal_xid'=>auth()->user()->id])->exists();
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : added attribute to get like icon detail 
     */
    public function getLikeIconAttribute($id)
    {
        return IamPrincipalPostsLikesLink::with(['likeIcon'=>function($q){$q->select('id','image');}])->where(['manage_posts_xid'=>$this->id,'iam_principal_xid'=>auth()->user()->id])->select('like_icons_xid')->first();
    }
    
    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : added attribute to get total comment count 
     */
    public function getTotalCommentAttribute($id)
    {
        return IamPrincipalPostsMasterComments::where(['manage_posts_xid'=>$this->id])->count();
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : added attribute to get total save post count 
     */
    public function getTotalSaveAttribute($id)
    {
        return IamPrincipalSavedPost::where(['manage_posts_xid'=>$this->id])->count();
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 25 july 2024
     * Use : added attribute to get tags xid in array form 
     */
    public function getTagsXidAttribute($id)
    {
        return json_decode(ManagePost::where(['id'=>$this->id])->value('manage_tags_xids'));
    }
}
