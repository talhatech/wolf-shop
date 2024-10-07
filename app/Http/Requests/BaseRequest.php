<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function expectsJson(): bool
    {
        return $this->isJson();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->isMethod('put')) {
            if ($this->isJson()) {
                return $this->apiUpdateRules();
            }

            return $this->updateRules();
        }

        return $this->createRules();
    }

    protected function createRules(): array
    {
        return [
            //
        ];
    }

    protected function updateRules(): array
    {
        return $this->createRules();
    }

    protected function apiUpdateRules(): array
    {
        return [
            //
        ];
    }
}
