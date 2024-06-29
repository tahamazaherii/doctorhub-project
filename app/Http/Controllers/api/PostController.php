<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
       $posts= Post::with('user')->paginate(2);
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $post = Post::create([
            'user_id' =>  Auth::user()->id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        DB::commit();

        return response()->json(['message' => 'پست شما با موفقیت اضافه شد.', 'post' => $post ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
        $post->load('user');

        return response()->json(['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
        $post = Auth::user()->posts()->find($post->id);
        $post->load('user');
        if (!$post) {
            return response()->json(['message' => 'تسک یافت نشد.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:255',
        ]);


        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        DB::beginTransaction();

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        DB::commit();
        return response()->json(['message' => 'پست شما با موفقیت بروزرسانی شد.', 'post' => $post ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        $post = Auth::user()->posts()->find($post->id);

        if (!$post) {
            return response()->json(['message' => 'پست شما یافت نشد.'], 404);
        }

        // حذف post
        $post->delete();

        return response()->json(['message' => 'پست شما با موفقیت حذف شد.'],200);
    }
}
