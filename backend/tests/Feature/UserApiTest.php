<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\LegacySchema;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use LegacySchema;

    private function makeUser(string $email = 'ana@example.com'): User
    {
        return User::create(['name' => 'Ana', 'email' => $email, 'password' => 'secret']);
    }

    public function test_index_lists_users_newest_first_without_passwords(): void
    {
        $this->makeUser('a@example.com');
        $this->makeUser('b@example.com');

        $response = $this->get('/api/users')->assertOk();

        $response->assertJsonPath('success', true)
            ->assertJsonPath('data.0.email', 'b@example.com')
            ->assertJsonMissingPath('data.0.password');
        $this->assertMatchesRegularExpression('/^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d$/', $response->json('data.0.created_at'));
    }

    public function test_show_returns_one_user_or_404(): void
    {
        $user = $this->makeUser();

        $this->get("/api/users/{$user->id}")->assertOk()->assertJsonPath('data.name', 'Ana');
        $this->get('/api/users/999')->assertNotFound()->assertHeader('Content-Type', 'application/json');
    }

    public function test_store_creates_user_with_hashed_password_and_image(): void
    {
        Storage::fake('uploads');

        $this->post('/api/users', [
            'name' => '  Ana  ',
            'email' => 'ana@example.com',
            'password' => 'secret',
            'image' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        ])->assertCreated()->assertJson(['success' => true, 'message' => 'User created successfully.']);

        $user = User::first();
        $this->assertSame('Ana', $user->name);
        $this->assertTrue(Hash::check('secret', $user->password));
        Storage::disk('uploads')->assertExists("users/{$user->image}");
    }

    public function test_store_validation_errors_are_json_even_without_accept_header(): void
    {
        $this->makeUser();

        $this->post('/api/users', ['name' => '', 'email' => 'ana@example.com', 'password' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_store_rejects_non_image_upload(): void
    {
        $this->post('/api/users', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secret',
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['image']);
    }

    public function test_update_via_method_spoofing_keeps_password_and_image(): void
    {
        $user = $this->makeUser();
        $user->update(['image' => 'old.jpg']);

        // The frontend sends POST + _method=PUT because PHP can't read multipart PUT bodies.
        $this->post("/api/users/{$user->id}", ['_method' => 'PUT', 'name' => 'Ana B', 'email' => 'ana@example.com'])
            ->assertOk()->assertJson(['success' => true, 'message' => 'User updated successfully.']);

        $user->refresh();
        $this->assertSame('Ana B', $user->name);
        $this->assertSame('old.jpg', $user->image);
        $this->assertTrue(Hash::check('secret', $user->password));
    }

    public function test_update_rejects_email_taken_by_another_user(): void
    {
        $this->makeUser('taken@example.com');
        $user = $this->makeUser();

        $this->put("/api/users/{$user->id}", ['name' => 'Ana', 'email' => 'taken@example.com'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email']);
    }

    public function test_destroy_deletes_user_and_cascades_to_blogs(): void
    {
        $user = $this->makeUser();
        $user->blogs()->create(['title' => 'My blog']);

        $this->delete("/api/users/{$user->id}")
            ->assertOk()->assertJson(['success' => true, 'message' => 'User deleted successfully.']);

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('blogs', 0);
        $this->delete("/api/users/{$user->id}")->assertNotFound();
    }
}
