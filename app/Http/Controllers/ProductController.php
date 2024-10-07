<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $productQuery = Product::query()->orderBy('name');
        return ProductResource::collection($this->getPaginate($request, $productQuery));
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        // todo: write an exception file
        try {
            $imageFile = $request->file('image');

            //todo: media driver and media library (create polymorphic media table) // CloudinaryService

            $uploadedImage = Cloudinary::upload($imageFile->getRealPath(), [
                'public_id' => 'products/' . $product->id.'_product_image',
                'overwrite' => true, // Overwrite the existing image if it exists
            ]);

            //todo: make it more optimized (Separate base path and object key.)
            $product->image = $uploadedImage->getSecurePath();
            $product->save();

            return response()->json([
                'message' => 'Image uploaded successfully',
                'data' => ProductResource::make($product),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unable to upload image',
            ], 500); // You can use a different status code like 422 based on your preference
        }
    }
}
