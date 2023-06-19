<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProductPutRequest;
use App\Http\Requests\API\ProductStoreRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(private Product $product) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ProductCollection($this->product->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        if(!$request->user()->tokenCan('store')) abort(401, 'Unauthorized');

        return new ProductResource(\App\Models\Product::create($request->all()));
    }

    /**
     * Display the specified resource.
     */
    public function show($product)
    {
        $product = $this->product->find($product);
        if(!$product) abort(404, 'Produto não encontrado');

        return new ProductResource($product->load('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductPutRequest $request, Product $product)
    {
        if(!$request->user()->tokenCan('update')) abort(401, 'Unauthorized');

        $product->update($request->all());

       return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([], 204);
    }
}
