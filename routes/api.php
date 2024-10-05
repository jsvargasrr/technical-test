use App\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('products', [ProductController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('products', [ProductController::class, 'store']);
});

Route::middleware('auth:sanctum')->group(function () {

Route::post('cart/add', [CartController::class, 'addToCart']);
Route::get('cart', [CartController::class, 'viewCart']);
Route::delete('cart/{id}', [CartController::class, 'removeFromCart']);
});