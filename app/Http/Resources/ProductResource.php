<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sell_in' => $this->sell_in, // Number of days
            'quality' => $this->quality,
            'image' => $this->image,
            'details' => $this->details, // Decoding JSON details for better formatting
        ];
    }
}
