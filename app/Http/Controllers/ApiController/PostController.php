<?php

namespace App\Http\Controllers\ApiController;

use App\Services\PostService;
use Illuminate\Http\Request;

class PostController
{
    public function createpost (Request $request, PostService $postService){
        $data = $postService->createpostservice($request);
        return $data;
    }

    public function addlike(Request $request, PostService $postService){
        $data = $postService->addlikeservice($request);
        return $data;
    }

    public function wallposts(Request $request,PostService $postService){
        $data = $postService->wallpostservice($request);
        return $data;
    }

    public function getUserWallPosts(PostService $postService,Request $request){
        $data = $postService->getUserWallPostsService($request);
        return $data;
    }

    public function viewpost(Request $request, PostService $postService){
        $data = $postService->viewpostservice($request);
        return $data;
    }

    public function addcomment(Request $request, PostService $postService){
        $data = $postService->addcommentservice($request);
        return $data;
    }

    public function reportPost(Request $request, PostService $postService){
        $data = $postService->reportPost($request);
        return $data;
    }

}
