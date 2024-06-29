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
 * @OA\Get(
 *     path="/api/posts",
 *     summary="Get a paginated list of posts with user info",
 *     tags={"post"},
 *     security={{ "bearerAuth":{} }},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="A paginated list of posts",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Post Title"),
 *                     @OA\Property(property="body", type="string", example="Post body content"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
 *                     @OA\Property(property="user", type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="John Doe"),
 *                         @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="links", type="object",
 *                 @OA\Property(property="first", type="string", example="http://localhost:8000/api/posts?page=1"),
 *                 @OA\Property(property="last", type="string", example="http://localhost:8000/api/posts?page=3"),
 *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
 *                 @OA\Property(property="next", type="string", example="http://localhost:8000/api/posts?page=2")
 *             ),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=3),
 *                 @OA\Property(property="path", type="string", example="http://localhost:8000/api/posts"),
 *                 @OA\Property(property="per_page", type="integer", example=2),
 *                 @OA\Property(property="to", type="integer", example=2),
 *                 @OA\Property(property="total", type="integer", example=5)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     )
 * )
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
     /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     tags={"post"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","body"},
     *             @OA\Property(property="title", type="string", example="Post Title"),
     *             @OA\Property(property="body", type="string", example="Post body content")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="پست شما با موفقیت اضافه شد."),
     *             @OA\Property(property="post", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Post Title"),
     *                 @OA\Property(property="body", type="string", example="Post body content"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title field is required.")),
     *                 @OA\Property(property="body", type="array", @OA\Items(type="string", example="The body field is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
     /**
     * @OA\Get(
     *     path="/api/posts/{post}",
     *     summary="Get a specific post by ID",
     *      tags={"post"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the post"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="post", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Post Title"),
     *                 @OA\Property(property="body", type="string", example="Post body content"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
    /**
     * @OA\Put(
     *     path="/api/posts/{post}",
     *     summary="Update a specific post by ID",
     *      tags={"post"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the post"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","body"},
     *             @OA\Property(property="title", type="string", example="Updated Post Title"),
     *             @OA\Property(property="body", type="string", example="Updated post body content")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="پست شما با موفقیت بروزرسانی شد."),
     *             @OA\Property(property="post", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Post Title"),
     *                 @OA\Property(property="body", type="string", example="Updated post body content"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="تسک یافت نشد.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title field is required.")),
     *                 @OA\Property(property="body", type="array", @OA\Items(type="string", example="The body field is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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

    /**
     * @OA\Delete(
     *     path="/api/posts/{post}",
     *     summary="Delete a specific post by ID",
     *     tags={"post"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the post"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="پست شما با موفقیت حذف شد.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="پست شما یافت نشد.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
