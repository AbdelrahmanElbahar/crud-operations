<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\LegacySchema;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use LegacySchema;

    public function test_user_blog_post_relationships(): void
    {
        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secret']);
        $blog = $user->blogs()->create(['title' => 'My blog', 'description' => 'About me']);
        $post = $blog->posts()->create(['title' => 'First', 'content' => 'Hello']);

        $this->assertTrue($user->blogs->first()->is($blog));
        $this->assertTrue($blog->user->is($user));
        $this->assertTrue($blog->posts->first()->is($post));
        $this->assertTrue($post->blog->is($blog));

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($blog->created_at);
        $this->assertNotNull($post->updated_at);
    }

    public function test_user_factory_matches_the_schema(): void
    {
        $this->assertDatabaseHas('users', ['email' => User::factory()->create()->email]);
    }

    public function test_password_is_hashed_and_hidden(): void
    {
        $user = User::create(['name' => 'Ana', 'email' => 'ana@example.com', 'password' => 'secret']);

        $this->assertTrue(Hash::check('secret', $user->password));
        $this->assertArrayNotHasKey('password', $user->toArray());
    }
}
