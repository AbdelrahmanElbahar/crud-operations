<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private const RULES = [
        'blog_id' => 'required|integer|exists:blogs,id',
        'title' => 'required|string|max:200',
        'content' => 'required|string',
        'image' => self::IMAGE_RULES,
    ];

    // GET /api/posts
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Post::withBlogTitle()->orderByDesc('id')->get(),
        ]);
    }

    // POST /api/posts
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(self::RULES);
        $data['image'] = $this->storeImage($request, 'posts');

        Post::create($data);

        return response()->json(['success' => true, 'message' => 'Post created successfully.'], 201);
    }

    // GET /api/posts/{id} (queried by hand instead of route model binding so blog_title is included)
    public function show(string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Post::withBlogTitle()->findOrFail($id)]);
    }

    // PUT /api/posts/{post}
    public function update(Request $request, Post $post): JsonResponse
    {
        $data = $request->validate(self::RULES);
        $data['image'] = $this->storeImage($request, 'posts') ?? $post->image;

        $post->update($data);

        return response()->json(['success' => true, 'message' => 'Post updated successfully.']);
    }

    // DELETE /api/posts/{post}
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(['success' => true, 'message' => 'Post deleted successfully.']);
    }
}
