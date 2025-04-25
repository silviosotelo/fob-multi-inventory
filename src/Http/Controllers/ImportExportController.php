<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use FriendsOfBotble\MultiInventory\Exports\InventoryExport;
use FriendsOfBotble\MultiInventory\Imports\InventoryImport;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends BaseController
{
    public function export(BaseHttpResponse $response)
    {
        return Excel::download(new InventoryExport, 'inventories-' . now()->format('Y-m-d') . '.xlsx');
    }
    
    public function getImportForm()
    {
        page_title()->setTitle(trans('plugins/multi-inventory::multi-inventory.import'));
        
        return view('plugins/multi-inventory::import');
    }
    
    public function import(Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
        
        try {
            Excel::import(new InventoryImport, $request->file('file'));
            
            return $response
                ->setMessage(trans('plugins/multi-inventory::multi-inventory.import_success'));
                
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage($e->getMessage());
        }
    }
    
    public function exportSample(BaseHttpResponse $response)
    {
        $headers = [
            'ID',
            'SKU',
            'Name',
        ];
        
        $inventories = Inventory::where('status', 'published')->get();
        
        foreach ($inventories as $inventory) {
            $headers[] = $inventory->name . ' (Stock)';
            
            if (setting('multi_inventory_inventoryPrices', false)) {
                $headers[] = $inventory->name . ' (Price)';
            }
        }
        
        $data = [];
        
        // Add sample product data
        $sampleProducts = Product::with('variations')->take(3)->get();
        
        foreach ($sampleProducts as $product) {
            $row = [
                'ID' => $product->id,
                'SKU' => $product->sku,
                'Name' => $product->name,
            ];
            
            foreach ($inventories as $inventory) {
                $row[$inventory->name . ' (Stock)'] = rand(5, 100);
                
                if (setting('multi_inventory_inventoryPrices', false)) {
                    // Sample price variations 
                    $price = $product->price * (rand(90, 110) / 100);
                    $row[$inventory->name . ' (Price)'] = round($price, 2);
                }
            }
            
            $data[] = $row;
        }
        
        return Excel::download(new class($headers, $data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $headers;
            protected $data;
            
            public function __construct($headers, $data)
            {
                $this->headers = $headers;
                $this->data = $data;
            }
            
            public function array(): array
            {
                return $this->data;
            }
            
            public function headings(): array
            {
                return $this->headers;
            }
        }, 'inventory-sample.xlsx');
    }
}