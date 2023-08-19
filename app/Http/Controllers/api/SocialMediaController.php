<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Auth;
use Illuminate\Support\Facades\Validator;



class SocialMediaController extends Controller
{
    /* POST SECTION */
    public function postCreate(Request $request)
    {
        // Validate input
        $validator =  Validator::make($request->all(),[
            'content' => 'required|string',
        ]);
        
        if($validator->fails()){
            return response()->json([
                "error" => 'Content is required',
            ], 422);
        }

        // Create a new post
        $post = new Post();
        $post->content = $request->content;
        $post->image = $request->image;
        $post->user_id = 1;//auth()->user()->id;
        $post->save();

        return response()->json(['message' => 'Post has been created successfully'], 201);
    }

    public function postFetchAll()
    {
        // Fetch all posts with user likes and total likes count
        $posts = Post::with(['user', 'likes'])->get();

        return response()->json($posts);
    }

    /* COMMENT SECTION */
    public function commentCreate(Request $request)
    {
        // Validate input
        $validator =  Validator::make($request->all(),[
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                "error" => 'Content is required',
            ], 422);
        }
        $userId = 1;
        $existingComment = Comment::where('user_id', $userId)->first();

        if ($existingComment) {
            if ($existingComment->user_id === $userId) {
                return response()->json(['error' => 'You cannot comment your own post'], 400);
            }
            else{
                $existingComment->delete();
                return response()->json(['message' => 'Uncomment successfully']);
            }   
        }
        else{
            // Create a new comment
            $comment = new Comment();
            $comment->post_id = $request->post_id;
            $comment->user_id = 1;//auth()->user()->id;
            $comment->content = $request->content;
            $comment->save();

            return response()->json(['message' => 'Comment has been created successfully'], 201);
        }
    }

    public function commentFetchAll($postId)
    {
        $comments = Comment::with(['user', 'likes'])->where('post_id', $postId)->get();
        return response()->json($comments);
    }
    /* LIKE SECTION */
    public function likeOrUnlike(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'type' => 'required|in:0,1',
            'id' => 'required|exists:' . ($request->type === 0 ? 'posts' : 'comments') . ',id',
        ]);

        if($validator->fails()){
            return response()->json([
                "error" => 'type is required for ex.comment or post',
            ], 422);
        }

        $type = $request->type;
        $id = $request->id;
        $userId = 1;//auth()->user()->id;

        $existingLike = Like::where('user_id', $userId)->first();

        if ($existingLike) {
            if ($existingLike->user_id === $userId) {
                return response()->json(['error' => 'You cannot like your own post'], 400);
            }
            else{
                $existingLike->delete();
                return response()->json(['message' => 'Unliked successfully']);
            }
            
        }
        else{
            // Like the post or comment
            $like = new Like();
            $like->user_id = $userId;
            $like->likeable_id = $id;
            $like->likeable_type = $type;
            $like->save();

            return response()->json(['message' => 'Liked successfully'], 201);
        }

    }
}
