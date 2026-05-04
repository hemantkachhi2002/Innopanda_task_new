<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WooCommerceService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.woocommerce.url'), '/') . '/wp-json/wc/v3/';
        $this->client = new Client([
            'verify' => false, // Set to true in production with proper SSL
        ]);
    }

    /**
     * Create a new product in WooCommerce.
     *
     * @param array $data
     * @return array
     */
    public function createProduct(array $data)
    {
        return $this->request('POST', 'products', $data);
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateProduct($id, array $data)
    {
        return $this->request('PUT', "products/{$id}", $data);
    }

    /**
     * Fetch all products with pagination and filters.
     *
     * @param array $params
     * @return array
     */
    public function fetchProducts(array $params = [])
    {
        return $this->request('GET', 'products', [], $params);
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return array
     */
    public function deleteProduct($id)
    {
        return $this->request('DELETE', "products/{$id}", ['force' => true]);
    }

    /**
     * Common method to handle Guzzle requests.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @param array $params
     * @return array
     * @throws \Exception
     */
    protected function request($method, $endpoint, $data = [], $params = [])
    {
        $url = $this->baseUrl . $endpoint;

        try {
            Log::info("WooCommerce API Request started", [
                'method' => $method,
                'endpoint' => $endpoint,
                'payload' => $data,
                'query_params' => $params
            ]);

            $options = [];
            
            // Add authentication credentials to query parameters
            $params['consumer_key'] = config('services.woocommerce.key');
            $params['consumer_secret'] = config('services.woocommerce.secret');

            if (!empty($data)) {
                $options['json'] = $data;
            }
            
            $options['query'] = $params;

            $response = $this->client->request($method, $url, $options);
            $result = json_decode($response->getBody()->getContents(), true);

            Log::info("WooCommerce API Response received", [
                'status' => $response->getStatusCode(),
                'response_keys' => array_keys($result ?? [])
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error("WooCommerce API Request failed", [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
