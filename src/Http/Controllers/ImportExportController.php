<?php

namespace FriendsOfBotble\MultiInventory\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use FriendsOfBotble\MultiInventory\Exports\InventoryExport;
use FriendsOfBotble\MultiInventory\Imports\InventoryImport;
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
        return view('plugins/multi-inventory::import');
    }
    
    public function import(Request $request, BaseHttpResponse $response)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
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
}