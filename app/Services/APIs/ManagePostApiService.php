<?php

namespace App\Services\APIs;

use App\Models\IamPrincipalManageCommunityLink;
use App\Models\IamPrincipalPostsLikesLink;
use App\Models\LikeIcons;
use App\Models\ManageCommunity;
use App\Models\ManageTags;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ManagePost;
use App\Models\IamPrincipalSavedPost;
use App\Models\PostMasterCommentsTreeLink;
use App\Models\IamPrincipalPostsMasterComments;
use Exception;

class ManagePostApiService
{

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To fetch communities with tags
     */
    public function fetchCommunitiesWithTagsService()
    {
        try{
            $data = ManageCommunity::with(['tags'=>function($query){
                $query->select('id','manage_community_xid','name');
                $query->where('is_accepted',true);
            }])
            ->select('id','community_name')
            ->where('is_active',true)
            ->get();

            if($data == null)
            {
                Log::info('communities not found');
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),[],200);
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'),$data,200);
        }catch(Exception $e)
        {
            Log::error('Fetch communities with tags service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To store tags
     */
    public function storeTagsService($request)
    {
        try{
            DB::beginTransaction();
            $request['is_requested'] = 1;
            $request['is_active'] = 0;
            ManageTags::create($request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store Tags service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To store post
     */
    public function storePostService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;

            if($request->cta_link != null && $request->cta_title != null)
            {
                $request['is_uploaded_by_bussiness_user'] = 1;
            }

            if($request->hasFile('file'))
            {
                $fileName = saveSingleImageWithoutCrop($request->file,'post_image');
                $request['image'] = $fileName;

                // To remove file key and value from array 
                $newArray = \Illuminate\Support\Arr::except($request->all(),['file']);
            }
            // dd($newArray, $request->all());
            ManagePost::create($newArray ?? $request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'), [], 201);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store post service failed: '.$e);
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To fetch post
     */
    public function fetchPostService()
    {
        try{
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'),$this->getData(),200);
        }catch(Exception $e)
        {
            Log::error('Fetch post service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To fetch post
     */
    public function getData()
    {
        try{
            $followedCommunityId = IamPrincipalManageCommunityLink::where('iam_principal_xid',auth()->user()->id)->pluck('manage_community_xid');
            if($followedCommunityId == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),[],200);
            }
            $data = ManagePost::with(['iam_principal'=>function($q){
                $q->select('id','user_name','full_name','profile_photo');
            }])
            ->whereIn('post_in',$followedCommunityId)
            ->select('id','id as likecount','id as is_i_liked','id as total_comment','id as total_save','iam_principal_xid','caption','image','manage_tags_xids','post_as','cta_title','cta_link','created_at')
            ->get();
            if($data == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found'),[],200);
            }
            foreach($data as $post)
            {
                $tag_ids = json_decode($post->manage_tags_xids);
                $tag_names = [];
                if($tag_ids != null)
                {
                    foreach($tag_ids as $tagId)
                    {
                        array_push($tag_names,ManageTags::where('id',$tagId)->value('name'));
                    }
                }
                $post->tag_names = $tag_names;
            }

            return collect($data);
        }catch(Exception $e)
        {
            Log::error('Get data service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To store like post
     */
    public function storePostLikeService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;
            if(IamPrincipalPostsLikesLink::where(['iam_principal_xid'=>auth()->user()->id,'manage_posts_xid'=>$request->manage_posts_xid])->doesntExist())
            {
                IamPrincipalPostsLikesLink::create($request->all());
            }else{
                IamPrincipalPostsLikesLink::where(['iam_principal_xid'=>auth()->user()->id,'manage_posts_xid'=>$request->manage_posts_xid])->delete();
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),[],200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Store post like service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 22 july 2024
     * Use : To fetch like icons
     */
    public function fetchLikeIconsService()
    {
        try{
            $data = LikeIcons::select('id','image')
            ->where('is_active',true)
            ->get();
            
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully'),$data,200);
        }catch(Exception $e)
        {
            Log::error('fetch like icons service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To save post
     */
    public function savePostService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;
            if(IamPrincipalSavedPost::where($request->all())->doesntExist())
            {
                IamPrincipalSavedPost::create($request->all());
            }else{
                IamPrincipalSavedPost::where($request->all())->delete();
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),[],200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Save post service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To comment on post
     */
    public function commentOnPostService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;
            IamPrincipalPostsMasterComments::create($request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),[],200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Comment on post service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To reply on comment
     */
    public function replyOnCommentService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;
            PostMasterCommentsTreeLink::create($request->all());
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.save_data'),[],200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Comment on post service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To delete comment
     */
    public function deleteCommentService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;
            if(IamPrincipalPostsMasterComments::where($request->all())->exists())
            {
                IamPrincipalPostsMasterComments::where($request->all())->delete();
                if(PostMasterCommentsTreeLink::where('posts_master_comment_xid',$request->id)->exists())
                {
                    foreach(PostMasterCommentsTreeLink::where('posts_master_comment_xid',$request->id)->get() as $replyComment)
                    {
                        PostMasterCommentsTreeLink::where('posts_master_comment_xid',$request->id)->delete();
                    }
                }
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.delete'),[],200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Delete comment service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To delete reply on comment
     */
    public function deleteReplyOnCommentService($request)
    {
        try{
            DB::beginTransaction();
            $request['iam_principal_xid'] = auth()->user()->id;
            if(PostMasterCommentsTreeLink::where($request->all())->exists())
            {
                PostMasterCommentsTreeLink::where($request->all())->delete();
            }else{
                return jsonResponseWithErrorMessageApi('You cannot delete another replied comment',403);
            }
            DB::commit();
            return jsonResponseWithSuccessMessageApi(__('success.delete'),[],200);
        }catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Delete comment service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    /**
     * Created By : Ritikesh Yadav
     * Created At : 23 july 2024
     * Use : To fetch comment with replied comment
     */
    public function fetchCommentWithRepliedCommentService($request)
    {
        try{
            $Comments = IamPrincipalPostsMasterComments::with('repliedComment')->where($request->all())->get();
            if($Comments == null)
            {
                return jsonResponseWithSuccessMessageApi(__('success.data_not_found',[],200));
            }
            return jsonResponseWithSuccessMessageApi(__('success.data_fetched_successfully',$Comments,200));
        }catch(Exception $e)
        {
            Log::error('Fetch comment with replied comment service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchUserLikedListService($request)
    {
        try{
            $like_icon_id = $request->like_icons_xid;
            $like_list = IamPrincipalPostsLikesLink::with(['iam_principal'=>function($q){
                $q->select('id','user_name','full_name','profile_photo');
            },'likeIcon'=>function($q){
                $q->select('id','image');
            }])
            ->where('manage_posts_xid',$request->manage_posts_xid)
            ->when($like_icon_id != null, function($q) use ($like_icon_id){
                $q->where('like_icons_xid',$like_icon_id);
            })
            ->select('id','manage_posts_xid','iam_principal_xid','like_icons_xid')
            ->get();

            return jsonResponseWithSuccessMessageApi(__($like_list == null ? 'success.data_not_found' : 'success.data_fetched_successfully'), $like_list, 200);
        }catch(Exception $e)
        {
            Log::error('Fetch user like list service failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}