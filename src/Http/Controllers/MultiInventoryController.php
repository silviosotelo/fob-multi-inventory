<?php

namespace Botble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiInventoryController extends BaseController
{
    public function updateInventory(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        try {
            DB::beginTransaction();

            // Validate input
            $inventoryData = $request->input('inventories', []);

            foreach ($inventoryData as $inventoryId => $data) {
                // Find or create multi-inventory record
                $multiInventory = MultiInventory::firstOrNew([
                    'product_id' => $productId,
                    'id' => $inventoryId
                ]);

                // Update stock and price
                $multiInventory->fill([
                    'stock' => $data['stock'] ?? 0,
                    'price' => $data['price'] ?? $product->price
                ]);

                $multiInventory->save();
            }

            // Update main product quantity (optional, depends on your logic)
            $product->quantity = collect($inventoryData)->sum('stock');
            $product->save();

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans('plugins/multi-inventory::multi-inventory.update_success')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}