<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferItemStoreRequest;
use App\Http\Requests\TransferItemUpdateRequest;
use App\Models\TransferItem;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Item;
use App\Models\User;
use App\Models\ItemData;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTransferItem;
use App\Exports\ExportTransferItemParams;
use App\Imports\ImportTransferItem;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferItemController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $TransferItems = TransferItem::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $TransferItems->transform(function ($TransferItem) {
                $TransferItem->from_shop = $TransferItem->from_shop ? Shop::find($TransferItem->from_shop)->name : "Unknown";
                $TransferItem->to_shop = $TransferItem->to_shop ? Shop::find($TransferItem->to_shop)->name : "Unknown";
                $TransferItem->item_id = $TransferItem->item_id ? Item::find($TransferItem->item_id)->name : "Unknown";
                $TransferItem->created_by = $TransferItem->created_by ? User::find($TransferItem->created_by)->name : "Unknown";
                $TransferItem->updated_by = $TransferItem->updated_by ? User::find($TransferItem->updated_by)->name : "Unknown";
                $TransferItem->deleted_by = $TransferItem->deleted_by ? User::find($TransferItem->deleted_by)->name : "Unknown";
                
                return $TransferItem;
            });

            DB::commit();

            return $this->success('TransferItems retrived successfully', $TransferItems);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(TransferItemStoreRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $payload = collect($request->validated());
            $transferData = $payload->toArray();
    
            $fromShopItemData = ItemData::where('item_id', $transferData['item_id'])
                ->where('shop_id', $transferData['from_shop'])
                ->first();
    
            $toShopItemData = ItemData::where('item_id', $transferData['item_id'])
                ->where('shop_id', $transferData['to_shop'])
                ->first();
    
            if ($fromShopItemData) {
                $fromShopItemData->qty -= $transferData['qty'];
                $fromShopItemData->save();
    
                if ($toShopItemData) {
                    $toShopItemData->qty += $transferData['qty'];
                    $toShopItemData->save();
                } else {
                    $toShopItemData = new ItemData();
                    $toShopItemData->item_id = $transferData['item_id'];
                    $toShopItemData->shop_id = $transferData['to_shop'];
                    $toShopItemData->qty = $transferData['qty'];
                    $toShopItemData->save();
                }
    
                $TransferItem = TransferItem::create($transferData);
    
                DB::commit();
    
                return $this->success('TransferItem created successfully', $TransferItem);
            } else {
                DB::rollback();
                return $this->error('Item data not found for the from_shop', 404);
            }
    
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $TransferItem = TransferItem::findOrFail($id);
            DB::commit();

            return $this->success('TransferItem retrived successfully by id', $TransferItem);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(TransferItemUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $TransferItem = TransferItem::findOrFail($id);
            $TransferItem->update($payload->toArray());
            DB::commit();

            return $this->success('TransferItem updated successfully by id', $TransferItem);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $TransferItem = TransferItem::findOrFail($id);
            $TransferItem->forceDelete();

            DB::commit();

            return $this->success('TransferItem deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function exportexcel()
    {
        return Excel::download(new ExportTransferItem, 'TransferItems.xlsx');
    }

    public function exportparams(Request $request)
    {
        $filters = [
            'page' => $request->input('page'),
            'per_page' => $request->input('per_page'),
            'columns' => $request->input('columns'),
            'search' => $request->input('search'),
            'order' => $request->input('order'),
            'sort' => $request->input('sort'),
            'value' => $request->input('value'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
        return Excel::download(new ExportTransferItemParams($filters), 'TransferItems.xlsx');
    }

    public function exportpdf()
    {
        $data = TransferItem::all();
        $pdf = Pdf::loadView('TransferItemexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = TransferItem::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('TransferItemexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportTransferItem, request()->file('file'));

        return $this->success('TransferItem is imported successfully');
    }
}
