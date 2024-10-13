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
use Illuminate\Support\Facades\Storage;

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

            //todo: media library (create polymorphic media table)

            $basePath = 'products/';
            $fileKey = $product->id . '_product_image.' . $imageFile->getClientOriginalExtension();

            // Combine base path and object key to form the full file path
            $filePath = $basePath . $fileKey;

            // Upload the image to Cloudinary using Storage facade
            // todo: we can move to file upload in background
            $uploadedImageUrl = Storage::disk('cloudinary')->put($filePath, file_get_contents($imageFile->getRealPath()));

            // Check if the image was uploaded successfully
            if ($uploadedImageUrl) {

                $product->attachMedia($request->file('image'));
                $product->save();

                return response()->json([
                    'message' => 'Image uploaded successfully',
                    'data' => ProductResource::make($product),
                ], 200);
            }

            return response()->json(['message' => 'Image upload failed'], 500);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500); // You can use a different status code like 422 based on your preference
        }
    }
}
