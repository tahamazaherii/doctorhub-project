<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{

    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

      //ایجاد پست
      #[\PHPUnit\Framework\Attributes\Test]
      public function it_creates_a_post()
      {
          $this->actingAs($this->user);

          $response = $this->postJson('/api/posts', [
              'title' => 'Test post',
              'body' => 'Test body post',
          ]);


          $response->assertStatus(201);

          $this->assertDatabaseHas('posts', [
            'title' => 'Test post',
            'body' => 'Test body post',
            'user_id' => $this->user->id,
          ]);

      }

      public function it_can_update_a_post()
      {
          $this->actingAs($this->user);

          $response = $this->postJson('/api/posts', [
            'title' => 'Test post',
            'body' => 'Test body post',
          ]);

          $response->assertStatus(200); // Ensure todo is created successfully


          $post = Post::latest()->first(); // Get the latest created todo

          // Update the todo
          $updatedResponse = $this->putJson("/api/todos/{$post->id}", [
            'title' => 'Test update post',
            'body' => 'Test update body post',
          ]);

          $updatedResponse->assertStatus(200); // Ensure todo is updated successfully


          $this->assertDatabaseHas('posts', [
              'id' => $post->id,
              'title' => 'Test update post',
              'body' => 'Test update body post',
          ]);


      }


       //برای تست پاک کردن todo
       public function it_can_delete_a_post()
       {
         $this->actingAs($this->user);
           $post = Post::factory()->create();

           $post->delete();

           $this->assertDatabaseMissing('posts', ['id' => $post->id]);
       }


    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }
}
