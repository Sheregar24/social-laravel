<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Likes;
use App\Models\Following;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts =Post::all();
        // foreach($posts as $prd){
        //     echo "<p style='padding-left:20px'>Post ID:$prd->id</p><br/>";
        //     echo "<p style='padding-left:20px'>Post Name:$prd->title</p><br/>";
        //     echo "<p style='padding-left:20px'>Post Description:$prd->description</p><br/>";
        //     echo "<p style='padding-left:20px'>Post Description:$prd->postcontent</p><br/>";
        //     echo "<p style='padding-left:20px'>user ID:$prd->user_id</p><br/>";
        // }
        return response()->json([
            'success' => true,
            'message' => 'Post discovered successfully',
            'data' => $posts,
        ], Response::HTTP_OK);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('title', 'description','postcontent','user_id');
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required',
            'postcontent'=> 'required|file',
            // 'user_id'=> 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        if ($request->hasFile('postcontent')) {
            // return "yes";
            $file=$request->postcontent;
            $extension=$file->getclientoriginalextension();
            $filename=date('mdYHis').'.'.$extension;
            $file->storeAs('uploads',$filename,'local');


            //Request is valid, create new product
            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'postcontent' => $request->postcontent,
                'user_id' => $request->user()->id
            ]);


            //Product created, return success response
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], Response::HTTP_OK);
        }
    }


    public function show()
    {
        $follower = Following::where('follower_user_id',Auth::user()->id)->get();
        //return $follower;
        foreach($follower as $f){
            $id=$f['user_id'];
            $post=Post::where('user_id','=',$id)->get();
        }
        return $post;


        // if (!$follower) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Sorry, posts not found.'
        //     ], 400);
        // }

        // return $post;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //Validate data
        $data = $request->only('title', 'description','user_id');
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required',
            'postcontent'=> 'required|file',
            'user_id'=> 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        if ($request->hasFile('postcontent')) {
            // return "yes";
            $file=$request->postcontent;
            $extension=$file->getclientoriginalextension();
            $filename=date('mdYHis').'.'.$extension;
            $file->storeAs('uploads',$filename,'local');


            //Request is valid, update product
            $data = $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'postcontent' => $request->postcontent,
                'user_id' => $request->user_id
            ]);

            //Product updated, return success response
            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => $data
            ], Response::HTTP_OK);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ], Response::HTTP_OK);
    }

    public function createfollower(Request $request)
    {
        //Validate data
        $data = $request->only('user_id', 'follower_user_id');
        $validator = Validator::make($data, [
            'user_id' => 'required',
            'follower_user_id' => 'required'

        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is valid, create new product
        $follower = Following::create([
            'user_id' => $request->user_id,
            'follower_user_id' => $request->follower_user_id
        ]);
        $countid=$follower->id;



        //Product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'follower created successfully',
            'data' => $follower,
            'followercount'=> $countid
        ], Response::HTTP_OK);
    }

    public function likePost(Request $req)
    {
        $logged_user=$req->user_id;
        $post = Post::find($req->id);
        if (!$post) {
            return response()->json(['success' => false, 'error' => 'Post does not exist.'], 400);
        }

        $check = Likes::where('post_id', $req->id)->where('user_id', $logged_user)->first();
        if ($check) {
            return response()->json('Post already liked!!');
        }
        $likes = new Likes(array('user_id' => $logged_user));
        $post->likes()->save($likes);
        $like_data=Likes::where('post_id', $req->id)->where('user_id', $req->user_id)->first();

        // $likelist = likes::where('id')->get();
        // $likecount = $likelist->count();
        $countid=$like_data->id;
        // return "likecount :".$countid;
        return response()->json([
            'success' => true,
            'likecount' => $countid
            ], 200);
    }
    public function getLikes( $req)
    {

        $data = Likes::join('users','users.id','=','likes.user_id')->get(['likes.*','users.name']);
        $count = Likes::where('post_id','=',$req)->count();
        return "Likecount :" .$count."</br>".$data;

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, post not found.'
            ], 400);
        }
    }
}
