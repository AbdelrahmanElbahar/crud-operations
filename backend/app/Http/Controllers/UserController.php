<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // GET /api/users
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => User::orderByDesc('id')->get(),
        ]);
    }

    // POST /api/users
    public function store(Request $request): JsonResponse
    {
        // On failure Laravel stops here and returns 422 JSON with the errors.
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string',
            'image' => self::IMAGE_RULES,
        ]);
        $data['image'] = $this->storeImage($request, 'users');

        // The model's 'hashed' cast bcrypts the password.
        User::create($data);

        return response()->json(['success' => true, 'message' => 'User created successfully.'], 201);
    }

    // GET /api/users/{user} (route model binding: Laravel loads the User or returns 404)
    public function show(User $user): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $user]);
    }

    // PUT /api/users/{user} (the frontend sends POST + _method=PUT for file uploads)
    public function update(Request $request, User $user): JsonResponse
    {
        // No password rule: like the old API, updating a user never changes the password.
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user)],
            'image' => self::IMAGE_RULES,
        ]);
        $data['image'] = $this->storeImage($request, 'users') ?? $user->image;

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'User updated successfully.']);
    }

    // DELETE /api/users/{user} (the database cascade removes the user's blogs and posts)
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
