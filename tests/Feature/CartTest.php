namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user, 'sanctum')
             ->postJson('/api/cart/add', [
                 'product_id' => $product->id,
                 'quantity' => 2,
             ])
             ->assertStatus(201)
             ->assertJson([
                 'user_id' => $user->id,
                 'product_id' => $product->id,
                 'quantity' => 2,
             ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $product->refresh();
        $this->assertEquals(8, $product->stock);
    }
}
