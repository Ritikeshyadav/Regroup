<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\ManageTags;
use Illuminate\Http\Request;
use App\Services\APIs\ManagePostApiService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class ManagePostsApiController extends Controller
{
    protected $managePostApiService;
    public function __construct(ManagePostApiService $managePostApiService)
    {
        $this->managePostApiService = $managePostApiService;
    }

    public function fetchCommunitiesWithTags()
    {
        try{
            return $this->managePostApiService->fetchCommunitiesWithTagsService();
        }catch(Exception $e)
        {
            Log::error('Fetch communities with tags function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function storeTags(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'manage_community_xid' => 'required',
                // 'name' => 'required|exists:manage_tags,id,manage_community_xid,'.$request->manage_community_xid,
                'name' => ['required',function($attribute,$value,$fail) use ($request){
                    if(ManageTags::where(['manage_community_xid'=>$request->manage_community_xid,'name'=>$request->name])->exists())
                    {
                        $fail('Tag already exist');
                    }
                }],
            ]);
            if($validator->fails())
            {
                log::info('Store tags validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->storeTagsService($request);
        }catch(Exception $e)
        {
            Log::error('Store Tags function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function storePost(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'caption' => 'required',
                'file' => 'required',
                'post_in' => 'required|exists:manage_communities,id',
                'tag.*' => 'required',
                'post_as' => 'required',
                'cta_title' => 'sometimes|required',
                'cta_link' => 'sometimes|required',
            ]);
            if($validator->fails())
            {
                Log::info('Store post validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->storePostService($request);
        }catch(Exception $e)
        {
            Log::error('Store post function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchPost()
    {
        try{
            return $this->managePostApiService->fetchPostService();
        }catch(Exception $e)
        {
            Log::error('Fetch post function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchLatestPost()
    {
        try{
            return $this->managePostApiService->getData()->sortByDesc('created_at'); 
        }catch(Exception $e)
        {
            Log::error('Fetch post function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchLikeIcons()
    {
        try{
            return $this->managePostApiService->fetchLikeIconsService(); 
        }catch(Exception $e)
        {
            Log::error('Fetch like icons function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function storePostLike(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'manage_posts_xid' => 'required|exists:manage_posts,id',
                'like_icons_xid' => 'required|exists:like_icons,id',
            ]);
            if($validator->fails())
            {
                log::info('store post like validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->storePostLikeService($request);
        }catch(Exception $e)
        {
            Log::error('Store post like function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function savePost(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'manage_posts_xid' => 'required|exists:manage_posts,id',
            ]);
            if($validator->fails())
            {
                Log::info('Save post validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->savePostService($request);
        }catch(Exception $e)
        {
            Log::error('Save post function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function commentOnPost(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'manage_posts_xid' => 'required|exists:manage_posts,id',
                'comment' => 'required',
            ]);
            if($validator->fails())          
            {
                log::info('comment on post validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->commentOnPostService($request);
        }catch(Exception $e)
        {
            Log::error('Comment on post function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function replyOnComment(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'manage_posts_xid' => 'required|exists:manage_posts,id',
                'posts_master_comment_xid' => 'required|exists:iam_principal_posts_master_comments,id',
                'comment' => 'required',
            ]);
            if($validator->fails())          
            {
                log::info('Reply on comment validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->replyOnCommentService($request);
        }catch(Exception $e)
        {
            Log::error('Comment on post function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function deleteComment(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'id' => 'required|exists:iam_principal_posts_master_comments,id',
            ]);
            if($validator->fails())          
            {
                log::info('Delete comment validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->deleteCommentService($request);
        }catch(Exception $e)
        {
            Log::error('Delete comment function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function deleteReplyOnComment(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'id' => 'required|exists:post_master_comments_tree_links,id',
            ]);
            if($validator->fails())          
            {
                log::info('Delete comment validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->deleteReplyOnCommentService($request);
        }catch(Exception $e)
        {
            Log::error('Delete comment function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchCommentWithRepliedComment(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'manage_posts_xid' => 'required|exists:manage_posts,id',
            ]);
            if($validator->fails())          
            {
                log::info('Delete comment validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->fetchCommentWithRepliedCommentService($request);
        }catch(Exception $e)
        {
            Log::error('Fetch Comment with replied comment function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }

    public function fetchUserLikedList(Request $request)
    {
        try{
            $validator = validator::make($request->all(),[
                'manage_posts_xid' => 'required|exists:manage_posts,id',
            ]);
            if($validator->fails())          
            {
                log::info('fetch user like list validation failed: '.$validator->errors());
                return jsonResponseWithErrorMessageApi($validator->errors(),403);
            }
            return $this->managePostApiService->fetchUserLikedListService($request);
        }catch(Exception $e)
        {
            Log::error('Fetch Comment with replied comment function failed: '.$e->getMessage());
            return jsonResponseWithErrorMessageApi(__('auth.something_went_wrong'),500);
        }
    }
}
