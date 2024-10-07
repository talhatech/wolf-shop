<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ApiInventoryService
{
    protected $apiEndpoint;

    public function __construct()
    {
        $this->apiEndpoint = config('constants.products_mock_api.endpoint');
    }

    /**
     * Fetch data from the API and return the response.
     *
     * @return array|null
     */
    public function fetchInventoryData():JsonResponse|\Exception
    {
        $response = Http::get($this->apiEndpoint);

    // todo: validate API response


        if ($response->successful()) {
            return $response->json(); // Return the JSON response as an array
        }

        // Log the error or handle it as needed
        throw new \Exception('Failed to fetch data from the API: ' . $response->status());
    }
}
