<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\LegacySchema;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use LegacySchema;

    private Blog $blog;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secret']);
        $this->blog = $user->blogs()->create(['title' => 'My blog']);
    }

    public function test_index_and_show_include_blog_title(): void
    {
        $this->blog->posts()->create(['title' => 'Older', 'content' => 'a']);
        $post = $this->blog->posts()->create(['title' => 'Newer', 'content' => 'b']);

        $this->get('/api/posts')->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.title', 'Newer')
            ->assertJsonPath('data.0.blog_title', 'My blog');

        $this->get("/api/posts/{$post->id}")->assertOk()->assertJsonPath('data.blog_title', 'My blog');
        $this->get('/api/posts/999')->assertNotFound();
    }

    public function test_store_creates_post_with_image(): void
    {
        Storage::fake('uploads');

        $this->post('/api/posts', [
            'blog_id' => $this->blog->id,
            'title' => 'First',
            'content' => 'Hello',
            'image' => UploadedFile::fake()->create('pic.webp', 100, 'image/webp'),
        ])->assertCreated()->assertJson(['success' => true, 'message' => 'Post created successfully.']);

        $post = Post::first();
        $this->assertSame('Hello', $post->content);
        Storage::disk('uploads')->assertExists("posts/{$post->image}");
    }

    public function test_store_requires_existing_blog_title_and_content(): void
    {
        $this->post('/api/posts', ['blog_id' => 999, 'title' => '', 'content' => ''])
            ->assertUnprocessable()->assertJsonValidationErrors(['blog_id', 'title', 'content']);
    }

    public function test_update_keeps_image_when_none_uploaded(): void
    {
        $post = $this->blog->posts()->create(['title' => 'Old', 'content' => 'a', 'image' => 'old.webp']);

        $this->post("/api/posts/{$post->id}", ['_method' => 'PUT', 'blog_id' => $this->blog->id, 'title' => 'New', 'content' => 'b'])
            ->assertOk()->assertJson(['success' => true, 'message' => 'Post updated successfully.']);

        $post->refresh();
        $this->assertSame('New', $post->title);
        $this->assertSame('old.webp', $post->image);
    }

    public function test_destroy_deletes_post(): void
    {
        $post = $this->blog->posts()->create(['title' => 'First', 'content' => 'Hello']);

        $this->delete("/api/posts/{$post->id}")
            ->assertOk()->assertJson(['success' => true, 'message' => 'Post deleted successfully.']);

        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('blogs', 1);
    }
}
