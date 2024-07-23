<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IamPrincipalPostsMasterComments extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'iam_principal_posts_master_comments';
    protected $guarded = [];

    public function repliedComment()
    {
        return $this->hasMany(PostMasterCommentsTreeLink::class, 'posts_master_comment_xid','id');
    }
}
