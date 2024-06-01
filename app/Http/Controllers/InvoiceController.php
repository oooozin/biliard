<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Invoice;
use App\Models\ItemData;
use App\Exports\InvoicesExport;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $invoices = Invoice::with([
                'customer',
                'orders.orderItems',
                'orders.orderItems.user',
                'orders.orderItems.product',
                'orders.tableNumber',
            ])
                ->sortingQuery()
                ->searchQuery()
                ->paginationQuery();
            DB::commit();

            return $this->success('invoices retrived successfully', $invoices);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(InvoiceStoreRequest $request)
    {
        DB::beginTransaction();
    
        $payload = collect($request->validated());

    try {
        $existingInvoice = Invoice::where('item_id', $request->item_id)
                                  ->where('order_id', $request->order_id)
                                  ->first();

        if ($existingInvoice) {
            $newQty = $existingInvoice->qty + $request->qty;
            $existingInvoice->update(['qty' => $newQty]);
            $invoice = $existingInvoice;
        } else {
            $invoice = Invoice::create($payload->toArray());
        }
            $itemData = ItemData::where('item_id', $invoice->item_id)
                                ->where('shop_id', $request->shop_id)
                                ->first();
    
            if ($itemData) {
                $newQuantity = $itemData->qty - $request->qty;
                if ($newQuantity >= 0) {
                    $itemData->update(['qty' => $newQuantity]);
                } else {
                    throw new Exception('Not enough stock available');
                }
            } else {
                throw new Exception('Item data not found');
            }
    
            DB::commit();
            return $this->success('Invoice created and stock updated successfully', $invoice);
    
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError($e->getMessage());
        }
    }
    

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $invoice = Invoice::with([
                'customer',
                'orders.orderItems',
                'orders.orderItems.user',
                'orders.orderItems.product',
                'orders.tableNumber',
            ])->findOrFail($id);
            DB::commit();

            return $this->success('invoice retrived successfully by id', $invoice);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(InvoiceUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $invoice = Invoice::findOrFail($id);
            $invoice->update($payload->toArray());
            DB::commit();

            return $this->success('invoice updated successfully by id', $invoice);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
    
        try {
            $invoice = Invoice::findOrFail($id);
            $order = Order::findOrFail($invoice->order_id);
                    
            $itemData = ItemData::where('item_id', $invoice->item_id)
                                ->where('shop_id', $order->shop_id)
                                ->first();
    
            if ($itemData) {
                $newQuantity = $itemData->qty + $invoice->qty;
                $itemData->update(['qty' => $newQuantity]);    
            } 
            
            $invoice->forceDelete();
    
            DB::commit();
            return $this->success('Invoice deleted and stock returned successfully', []);
    
        } catch (Exception $e) {
            DB::rollback();
            return $this->internalServerError($e->getMessage());
        }
    }
    

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }

    // public function exportExcel(Request $request)
    // {
    //     $payload = collect($request);
    //     $columns = $payload['columns'];
    //     $columns = explode(',', $columns);

    //     return Excel::download(new InvoiceExport($columns), 'Invoices.xlsx');
    // }
}
