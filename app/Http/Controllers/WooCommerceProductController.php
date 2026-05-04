<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\WooCommerceService;
use App\Jobs\SyncWooCommerceProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WooCommerceProductController extends Controller
{
    protected $wooService;

    public function __construct(WooCommerceService $wooService)
    {
        $this->wooService = $wooService;
    }

    /**
     * Create a new product.
     */
    public function createProduct(StoreProductRequest $request)
    {
        try {
            $data = [
                'name' => $request->name,
                'type' => 'simple',
                'regular_price' => (string) $request->price,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'sku' => $request->sku,
                'manage_stock' => true,
                'stock_quantity' => $request->quantity,
                'weight' => $request->weight,
                'categories' => array_map(function($id) {
                    return ['id' => $id];
                }, $request->woocommerce_category_id)
            ];

            $product = $this->wooService->createProduct($data);

            return response()->json([
                'status' => 'success',
                'woocommerce_product_id' => $product['id'],
                'message' => 'Product created successfully'
            ], 201);

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create product: " . $e->getMessage());
        }
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(UpdateProductRequest $request, $id)
    {
        try {
            $data = [];
            if ($request->has('name')) $data['name'] = $request->name;
            if ($request->has('price')) $data['regular_price'] = (string) $request->price;
            if ($request->has('description')) $data['description'] = $request->description;
            if ($request->has('short_description')) $data['short_description'] = $request->short_description;
            if ($request->has('sku')) $data['sku'] = $request->sku;
            if ($request->has('quantity')) $data['stock_quantity'] = $request->quantity;
            if ($request->has('weight')) $data['weight'] = $request->weight;
            if ($request->has('woocommerce_category_id')) {
                $data['categories'] = array_map(function($cid) {
                    return ['id' => $cid];
                }, $request->woocommerce_category_id);
            }

            $product = $this->wooService->updateProduct($id, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to update product: " . $e->getMessage());
        }
    }

    /**
     * Fetch all products with pagination and search.
     */
    public function getAllProducts(Request $request)
    {
        try {
            $params = [
                'page' => $request->get('page', 1),
                'per_page' => $request->get('per_page', 10),
                'search' => $request->get('search'),
                'sku' => $request->get('sku')
            ];

            $products = $this->wooService->fetchProducts(array_filter($params));

            return response()->json([
                'status' => 'success',
                'fetched' => count($products),
                'products' => $products
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to fetch products: " . $e->getMessage());
        }
    }

    /**
     * Delete a product.
     */
    public function deleteProduct($id)
    {
        try {
            $this->wooService->deleteProduct($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Product permanently deleted'
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to delete product: " . $e->getMessage());
        }
    }

    /**
     * Dispatch sync job.
     */
    public function syncProducts()
    {
        try {
            SyncWooCommerceProducts::dispatch();

            return response()->json([
                'status' => 'success',
                'message' => 'Product synchronization job dispatched successfully'
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse("Failed to dispatch sync job: " . $e->getMessage());
        }
    }

    /**
     * Helper for error responses.
     */
    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
