<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends BaseRequest
{
    /**
     * Get the update validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function updateRules(): array
    {
        // dd('here');
        return [
            'image' => 'required|image|max:10240|mimes:jpeg,png,jpg,gif,svg', // 10MB max size
        ];
    }

      /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'image.required' => 'An image file is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image size must not exceed 10MB.',
        ];
    }
}
