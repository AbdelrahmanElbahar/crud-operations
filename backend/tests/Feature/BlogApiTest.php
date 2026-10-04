<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\LegacySchema;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use LegacySchema;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secret']);
    }

    public function test_index_and_show_include_user_name(): void
    {
        $this->user->blogs()->create(['title' => 'Older']);
        $blog = $this->user->blogs()->create(['title' => 'Newer']);

        $this->get('/api/blogs')->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.title', 'Newer')
            ->assertJsonPath('data.0.user_name', 'Ana');

        $this->get("/api/blogs/{$blog->id}")->assertOk()->assertJsonPath('data.user_name', 'Ana');
        $this->get('/api/blogs/999')->assertNotFound();
    }

    public function test_store_creates_blog_with_image(): void
    {
        Storage::fake('uploads');

        $this->post('/api/blogs', [
            'user_id' => $this->user->id,
            'title' => 'My blog',
            'description' => 'About me',
            'image' => UploadedFile::fake()->create('cover.png', 100, 'image/png'),
        ])->assertCreated()->assertJson(['success' => true, 'message' => 'Blog created successfully.']);

        $blog = Blog::first();
        $this->assertSame('My blog', $blog->title);
        Storage::disk('uploads')->assertExists("blogs/{$blog->image}");
    }

    public function test_store_requires_existing_user_and_title(): void
    {
        $this->post('/api/blogs', ['user_id' => 999, 'title' => ''])
            ->assertUnprocessable()->assertJsonValidationErrors(['user_id', 'title']);
    }

    public function test_update_keeps_image_when_none_uploaded(): void
    {
        $blog = $this->user->blogs()->create(['title' => 'Old', 'image' => 'old.png']);

        $this->post("/api/blogs/{$blog->id}", ['_method' => 'PUT', 'user_id' => $this->user->id, 'title' => 'New'])
            ->assertOk()->assertJson(['success' => true, 'message' => 'Blog updated successfully.']);

        $blog->refresh();
        $this->assertSame('New', $blog->title);
        $this->assertSame('old.png', $blog->image);
    }

    public function test_destroy_deletes_blog_and_cascades_to_posts(): void
    {
        $blog = $this->user->blogs()->create(['title' => 'My blog']);
        $blog->posts()->create(['title' => 'First', 'content' => 'Hello']);

        $this->delete("/api/blogs/{$blog->id}")
            ->assertOk()->assertJson(['success' => true, 'message' => 'Blog deleted successfully.']);

        $this->assertDatabaseCount('blogs', 0);
        $this->assertDatabaseCount('posts', 0);
    }
}
