<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Agregar producto al carrito
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        // Verificar stock disponible
        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        // Calcular el precio total
        $totalPrice = $product->price * $request->quantity;

        // Crear o actualizar el ítem en el carrito
        $cartItem = Cart::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['quantity' => $request->quantity, 'total_price' => $totalPrice]
        );

        // Actualizar el stock del producto
        $product->decrement('stock', $request->quantity);

        return response()->json($cartItem, 201);
    }

    // Ver el carrito del usuario
    public function viewCart(Request $request)
    {
        $user = $request->user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        return response()->json($cartItems);
    }

    // Eliminar un ítem del carrito
    public function removeFromCart(Request $request, $id)
    {
        $user = $request->user();
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        // Reponer el stock
        $product = Product::findOrFail($cartItem->product_id);
        $product->increment('stock', $cartItem->quantity);

        $cartItem->delete();

        return response()->json(['message' => 'Producto eliminado del carrito']);
    }
}
