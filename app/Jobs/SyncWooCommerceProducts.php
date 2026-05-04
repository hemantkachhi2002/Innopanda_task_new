<?php

namespace App\Jobs;

use App\Services\WooCommerceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncWooCommerceProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WooCommerceService $service): void
    {
        try {
            Log::info("Background Sync started for WooCommerce Products.");
            
            $page = 1;
            $allProducts = [];
            
            do {
                $products = $service->fetchProducts([
                    'page' => $page,
                    'per_page' => 100
                ]);
                
                if (!empty($products)) {
                    $allProducts = array_merge($allProducts, $products);
                    $page++;
                }
            } while (!empty($products));

            Log::info("Background Sync completed.", [
                'total_fetched' => count($allProducts)
            ]);
      

        } catch (\Exception $e) {
            Log::error("Sync Job Failed", ['error' => $e->getMessage()]);
        }
    }
}
