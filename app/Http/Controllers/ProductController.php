<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Mostrar productos con filtros
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('price')) {
            $query->where('price', '<=', $request->price);
        }

        if ($request->has('ean')) {
            $query->where('ean', $request->ean);
        }

        if ($request->has('stock')) {
            $query->where('stock', '>=', $request->stock);
        }

        return response()->json($query->get());
    }

    // Crear producto
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'ean' => 'required|string|size:13|unique:products',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::create($request->all());

        return response()->json($product, 201);
    }
}
