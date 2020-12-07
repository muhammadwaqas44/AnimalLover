<?php

namespace App\Services;

use App\Comment;
use App\GalleryImage;
use App\Models\Job;
use App\Notification;
use App\Post;
use App\ReportPost;
use App\User;
use App\UserLikePost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Support\Jsonable;

class PostService
{
    public function addcommentservice($request)
    {
        $errorMessage = [
            'comment.required' => 'Please add some comments',
            'post_id.required' => 'Post id is required',
            'user_id.required' => 'User id is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'comment' => 'required|string',
                'post_id' => 'required|numeric',
                'user_id' => 'required|numeric',
            ], $errorMessage
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }
        $post = Post::where('id', $request->post_id)->first();
        if ($post == null) {
            return Helper::jsonResponse(0, '', '', 'Post id does not exists');
        }
        $comment = new Comment();
        $comment->comment = $request->comment;
        $comment->user_id = Auth::user()->id;
        $comment->post_id = $request->post_id;
        $comment->save();
        $user = User::where('id', $request->user_id)->first();
        if($user){
            $notification =  new Notification();
            $notification->message = $user->first_name.' '.$user->last_name.' commented on your post';
            $notification->type = 'post_cooment';
            $notification->action_to = $request->user_id;
            $notification->action_by = Auth::user()->id;
            $notification->post_id = $request->post_id;
            $notification->save();

            $fcm = array($user->fcm_token);
            $dataBody = array();
            $dataBody['title'] = $notification->message;
            $dataBody['message'] = '';
            $dataBody['data'] = [];
            $dataNoti = array();
            $dataNoti['title'] = $notification->message;
            $dataNoti['body'] = '';
            $dataNoti['data'] = [];
            $no = Helper::sendPushNotification($fcm, $dataBody,$dataNoti);
//            dd($no);
            return Helper::jsonResponse(1, 'Comment added successfully');
        }else{
            return Helper::jsonResponse(0, '','','this user does not exists');
        }

    }

    public function viewpostservice($request)
    {
        $errorMessage = [
            'post_id.required' => 'Post id is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'post_id' => 'required|numeric',
            ], $errorMessage
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        $post = Post::with('createdBy', 'likes', 'comments', 'gallery')->find($request->post_id);
        if ($post) {
            $userLikePosts = UserLikePost::where("liked_by", Auth::id())->pluck("post_id")->toArray();
            $comments = [];
            $images = [];
            foreach ($post->gallery as $image) {
                $images[] = ['path' => asset($image->name)];
            }
            foreach ($post->comments as $comment) {
                $comments[] = [
                    'profile_image' => asset('/project-assets/images/'.$comment->user->profile_image),
                    'user_id' => $comment->user_id,
                    'username' => $comment->user->first_name . " " . $comment->user->last_name,
                    'comment' => $comment->comment,
                    'created_at' => Carbon::parse($comment->created_at)->timezone(Auth::user()->time_zone)->diffForHumans()
                ];
            }
            $data[] = [
                'user_id' => $post->created_by,
                'post_id' => $post->id,
                'profile_image' => asset('/project-assets/images/'.$post->createdBy->profile_image),
                'username' => $post->createdBy->first_name . ' ' . $post->createdBy->last_name,
                'created_at' =>  Carbon::parse($post->created_at)->timezone(Auth::user()->time_zone)->diffForHumans(),
                'location' => $post->location,
                'description' => $post->description,
                'likes' => (string)(($post->likes) ? $post->likes->count() : 0),
                'comments' => $comments,
                'comment_count' => (string)count($comments),
                'images' => $images,
                "like_by_me" => (in_array($post->id, $userLikePosts)) ? 1 : 0,
            ];
            return Helper::jsonResponse(1, '', $data);
        } else {
            return Helper::jsonResponse(1, 'No record found', [], '');
        }
    }

    public function wallpostservice($request)
    {
        $posts = Post::with('createdBy', 'comments','images')->latest("id");
        if($request->filter == 'show'){
            $errorMessage = [
//                'date.array' => 'date should be array',
                'topic.array' => 'Topic should be an array',
            ];
            $validator = Validator::make($request->all(),
                [
//                    'date' => 'array',
//                    'date.*' => 'string|distinct|in:today,yesterday,last_week,last_month,older',
                    'topic' => 'array|min:1',
                    'topic.*' => 'string|distinct|max:3',
                ], $errorMessage
            );
            if ($validator->fails()) {
                return Helper::jsonResponse(0, '', '', $validator->messages()->first());
            }

            if ($request->topic) {
                $posts = $posts->whereIn('topic', $request->topic);
            }

            if ($request->date) {
                $options = array('today', 'yesterday', 'last_week','last_month', 'older');
                $check = in_array($request->date, $options);
                if(!$check){
                    return Helper::jsonResponse(0, '', '', 'Invalid date');
                }
                    $m = $request->date;
                    if ($m == 'today') {
                        $today = date("Y-m-d", strtotime('now'));
                        $posts = $posts->whereDate('created_at', $today);

                    } elseif ($m == 'yesterday') {
                        $yesterday = date("Y-m-d", strtotime('-1 Days'));
                        $posts->whereDate('created_at', $yesterday);

                    } elseif ($m == 'last_week') {
                        $last_week = date("Y-m-d", strtotime('-7 Days'));
                        $now = date("Y-m-d", strtotime('now'));
                        $posts = $posts->whereBetween('created_at', [$last_week, $now]);

                    } elseif ($m == 'last_month') {
                        $last_month = date("Y-m-d", strtotime('-30 Days'));
                        $now = date("Y-m-d", strtotime('now'));
                        $posts = $posts->whereBetween('created_at', [$last_month, $now]);

                    } elseif ($m == 'older') {
                        $older = date("Y-m-d", strtotime('-30 Days'));
                        $posts = $posts->whereDate('created_at', '<', $older);
                    }
            }

            if($request->distance){
//                if(empty($request->distance['lat']) || empty($request->distance['long']) || empty($request->distance['mile'])){
//                    return Helper::jsonResponse(0, '', '', 'Invalid distance');
//                }
                if($request->distance['lat'] &&  $request->distance['long'] && $request->distance['mile']){
                    $lat = $request->distance['lat'];
                    $long = $request->distance['long'];
                    $mile = $request->distance['mile'];
//                $circle_radius = 6371;    //for KM
                    $circle_radius = 3958.748;  // for miles
                    $haverClause = '(' . $circle_radius . ' * acos(cos(radians(' . $lat . ')) * cos(radians(posts.lat)) * cos(radians(posts.long)- radians(' . $long . ') )+sin(radians(' . $lat . ')) * sin(radians(posts.lat))))';
                    $posts = $posts->select('posts.*', DB::raw($haverClause . ' AS distance'))
                        ->whereRaw($haverClause . ' <? ', [$mile]);
                }
            }
        }

        $userLikePosts = UserLikePost::where("liked_by", Auth::user()->id)->pluck('post_id')->toArray();

        $posts = $posts->paginate($request->size);
        $arr = [];
        foreach ($posts as $post){
            $liked_by_me = (in_array($post->id, $userLikePosts)) ? 1 : 0;
            $arr[] = $this->dataForWallPost($post, $liked_by_me);
        }

        $returnRes = [
            "collection" => $arr,
            "paginate" => Helper::paginateHelper($posts),
        ];

        return Helper::jsonResponse(1, 'Posts are here', $returnRes, '');
//        return Helper::jsonResponse(1, 'Posts are here', $posts, '');

    }
    private function dataForWallPost($post, $liked_by_me)
    {
        $images = array();
        foreach ($post->gallery as $image) {
            $images[] = ['path' => asset($image->name)];
        }
        $comments = $post->comments()->count();
        return [
            'user_id' => $post->createdBy->id,   // created by id
            'post_id' => $post->id,
            'profile_image' => asset('/project-assets/images/'.$post->createdBy->profile_image),
            'username' => $post->createdBy->first_name . ' ' . $post->createdBy->last_name,
            'created_at' => Carbon::parse($post->created_at)->timezone(Auth::user()->time_zone)->diffForHumans(),
            'location' => $post->location,
            'title' => $post->title,
            'description' => $post->description,
            'likes' => (string)(($post->likes) ? $post->likes->count() : 0),
            'comments' => $comments,
            'comment_count' => (string)$comments,
            'images' => $images,
            "like_by_me" => $liked_by_me,
        ];
    }

    public function getUserWallPostsService($request)
    {
        $userwallposts = Post::where('created_by', Auth::user()->id)->with('createdBy', 'likes', 'comments', 'gallery')->latest("id")->paginate($request->size);
        if ($userwallposts) {
            $userLikePosts = UserLikePost::where("liked_by", Auth::id())->pluck("post_id")->toArray();
            $userdata = array();
            foreach ($userwallposts as $post) {
                $liked_by_me = (in_array($post->id, $userLikePosts)) ? 1 : 0;
                $userdata[] = $this->USERdataForWallPost($post, $liked_by_me);
            }
            $returnRes = [
                "collection" => $userdata,
			    "paginate" => Helper::paginateHelper($userwallposts),
		    ];
            return Helper::jsonResponse(1, '', $returnRes);
        } else {
            return Helper::jsonResponse(1, 'No record found', [], '');
        }
    }
    private function USERdataForWallPost($post, $liked_by_me)
    {
        $images = array();
        foreach ($post->gallery as $image) {
            $images[] = ['path' => asset($image->name)];
        }
        $comments = $post->comments()->count();
        return [
            'post_id' => $post->id,
            'profile_image' => asset('project-assets/images/'.$post->createdBy->profile_image),
            'username' => $post->createdBy->first_name . ' ' . $post->createdBy->last_name,
            'created_at' => Carbon::parse($post->created_at)->timezone(Auth::user()->time_zone)->diffForHumans(),
            'location' => $post->location,
            'title' => $post->title,
            'description' => $post->description,
            'likes' => (string)(($post->likes) ? $post->likes->count() : 0),
            'comments' => $comments,
            'comment_count' => (string)$comments,
            'images' => $images,
            "like_by_me" => $liked_by_me,
        ];
    }

    public function addlikeservice($request)
    {
        $errorMessage = [
            'post_id.required' => 'Post id is required',
            'user_id.required' => 'User id is required',
        ];
        $validator = Validator::make($request->all(),
            [
                'post_id' => 'required|numeric',
                'user_id' => 'required|numeric',
            ], $errorMessage
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        $alreadyLiked = UserLikePost::where('liked_by', Auth::user()->id)->where('post_id', $request->post_id)->first();
        if ($alreadyLiked) {
            $alreadyLiked->delete();
            return Helper::jsonResponse(1, 'Post Unliked');
        }
        $userlike = new UserLikePost();
        $userlike->liked_by = Auth::user()->id;
        $userlike->post_id = $request->post_id;
        $userlike->save();

        $user = User::where('id', $request->user_id)->first();
        if($user){
            $notification =  new Notification();
            $notification->message = $user->first_name.' '.$user->last_name.' likes your post';
            $notification->type = 'post_like';
            $notification->action_to = $request->user_id;
            $notification->action_by = Auth::user()->id;
            $notification->post_id = $request->post_id;
            $notification->save();

            $fcm = array($user->fcm_token);
            $dataBody = array();
            $dataBody['title'] = $notification->message;
            $dataBody['message'] = '';
            $dataBody['data'] = [];
            $dataNoti = array();
            $dataNoti['title'] = $notification->message;
            $dataNoti['body'] = '';
            $dataNoti['data'] = [];
            $no = Helper::sendPushNotification($fcm, $dataBody,$dataNoti);
//            dd($no);
            return Helper::jsonResponse(1, 'Post liked');
        }else{
            return Helper::jsonResponse(0, '','','Post cannot be liked. This user does not exists');
        }
    }

    public function createpostservice($request)
    {
        $errorMessages = [
            'title.required' => 'Title is required',
            'location.required' => 'City/State is required',
            'lat.required' => 'Latitude is required',
            'long.required' => 'Longitude is required',
            'token.required' => 'User Token is required',
            'topic.required' => 'Topic is required',
            'images.max' => 'Maximum two picture  are required',
        ];
        $validator = Validator::make($request->all(),
            [
                'title' => 'required',
                'location' => 'required|string',
                'lat' => 'required|string',
                'long' => 'required|string',
                'token' => 'required|string',
                'topic' => 'required|numeric',
                'images' => 'max:2',
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', '', $validator->messages()->first());
        }

        $post = new Post();
        $post->title = $request->title;
        $post->location = $request->location;
        $post->lat = $request->lat;
        $post->long = $request->long;
        $post->description = $request->description;
        $post->topic = $request->topic;
        $post->created_by = Auth::user()->id;
        $post->save();
        if ($request->images != null) {
            foreach ($request->images as $image) {
                $galleryImage = new GalleryImage();
                $galleryImage->name = 'project-assets/images/' . $image;
                $galleryImage->post_id = $post->id;
                $galleryImage->save();
            }
        }

        return Helper::jsonResponse(1, 'Post added successfully', ['token' => Auth::user()->token, 'post_id' => $post->id]);
    }

    public function reportPost($request){
        $errorMessages = [
//            'post_id.required' => 'post_id is required',
            'message.required' => 'Message is required',
        ];
        $validator = Validator::make($request->all(),
            [
//                'post_id' => 'required',
                'message' => 'required|string',
            ], $errorMessages
        );
        if ($validator->fails()) {
            return Helper::jsonResponse(0, '', [], $validator->messages()->first());
        }

        if(!empty($request->post_id)){
            $post  = Post::find($request->post_id);
            if(!$post){
                return Helper::jsonResponse(0, '', [], 'Post does not exists...');
            }
            $c = ReportPost::where(['user_id' => Auth::id(), 'post_id' => $request->post_id])->first();
            if($c){
                return Helper::jsonResponse(0, '', [], 'You have already reported about this post');
            }
            $reported = new ReportPost();
            $reported->user_id = Auth::id();
            $reported->post_id = $request->post_id;
            $reported->message = $request->message;
            $reported->save();
            if($reported){
                return Helper::jsonResponse(1, 'Reported Successfully.', $reported, '');
            }else{
                return Helper::jsonResponse(0, '', [], 'Could not report');
            }
        }

        if(!empty($request->user_id)){
            $user = User::find($request->user_id);
            if(!$user){
                return Helper::jsonResponse(0, '', [], 'User does not exists...');
            }
            $c = ReportPost::where(['user_id' => Auth::id(), 'reported_user' => $request->user_id])->first();
            if($c){
                return Helper::jsonResponse(0, '', [], 'You have already reported about this user');
            }
            $reported = new ReportPost();
            $reported->user_id = Auth::id();
            $reported->reported_user = $request->user_id;
            $reported->message = $request->message;
            $reported->save();
            if($reported){
                return Helper::jsonResponse(1, 'Reported Successfully.', $reported, '');
            }else{
                return Helper::jsonResponse(0, '', [], 'Could not report');
            }

        }







    }





}
