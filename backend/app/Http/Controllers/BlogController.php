<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private const RULES = [
        'user_id' => 'required|integer|exists:users,id',
        'title' => 'required|string|max:200',
        'description' => 'nullable|string',
        'image' => self::IMAGE_RULES,
    ];

    // GET /api/blogs
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Blog::withUserName()->orderByDesc('id')->get(),
        ]);
    }

    // POST /api/blogs
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(self::RULES);
        $data['image'] = $this->storeImage($request, 'blogs');

        Blog::create($data);

        return response()->json(['success' => true, 'message' => 'Blog created successfully.'], 201);
    }

    // GET /api/blogs/{id} (queried by hand instead of route model binding so user_name is included)
    public function show(string $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Blog::withUserName()->findOrFail($id)]);
    }

    // PUT /api/blogs/{blog}
    public function update(Request $request, Blog $blog): JsonResponse
    {
        $data = $request->validate(self::RULES);
        $data['image'] = $this->storeImage($request, 'blogs') ?? $blog->image;

        $blog->update($data);

        return response()->json(['success' => true, 'message' => 'Blog updated successfully.']);
    }

    // DELETE /api/blogs/{blog} (the database cascade removes the blog's posts)
    public function destroy(Blog $blog): JsonResponse
    {
        $blog->delete();

        return response()->json(['success' => true, 'message' => 'Blog deleted successfully.']);
    }
}
