<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Models\TableNumber;
use App\Enums\TableStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $orders = Order::with('invoices') 
                ->searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->where('status', OrderStatusEnum::SUCCESS->value)
                ->paginationQuery();

            $orders->transform(function ($order) {
                $order->payment_id = $order->payment_id ? Payment::find($order->payment_id)->name : "Unknown";
                $order->customer_id = $order->customer_id ? Customer::find($order->customer_id)->name : "Unknown";
                $order->table_number_id = $order->table_number_id ? TableNumber::find($order->table_number_id)->name : "Unknown";
                $order->shop_id = $order->shop_id ? Shop::find($order->shop_id)->name : "Unknown";
                $order->created_by = $order->created_by ? User::find($order->created_by)->name : "Unknown";
                $order->updated_by = $order->updated_by ? User::find($order->updated_by)->name : "Unknown";
                $order->deleted_by = $order->deleted_by ? User::find($order->deleted_by)->name : "Unknown";
                
                return $order;
            });

            DB::commit();

            return $this->success('invoices retrived successfully', $orders);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }


    public function store(OrderStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        if ($request->has('endtime')) {
            $timeToAdd = explode(':', $request->endtime);
            $hours = $timeToAdd[0];
            $minutes = $timeToAdd[1] ?? 0;

            $payload['checkout'] = Carbon::now('Asia/Yangon')->addHours($hours)->addMinutes($minutes);
        }

        $payload['checkin'] = Carbon::now('Asia/Yangon');

        try {
            
            $order = Order::create($payload->toArray());
           
            if ($request->has('table_number_id')) {
                $tableNumber = TableNumber::findOrFail($request->table_number_id);
                $tableNumber->update([
                    'status' => TableStatusEnum::PENDING,
                    'order_id' => $order->id
                ]);
            }

            DB::commit();

            return $this->success('invoice created successfully', $order);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::with('invoices.item')->findOrFail($id);
            DB::commit();

            return $this->success('invoice retrived successfully by id', $order);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(OrderUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {

            $order = Order::findOrFail($id);
           
            if ($request->has('table_number_id')) {
                $tableNumber = TableNumber::findOrFail($request->table_number_id);
                $tableNumber->update([
                    'status' => TableStatusEnum::SUCCESS,
                    'order_id' => ''
                ]);
                DB::commit();
            }

            $order->update($payload->toArray());
            $order->update([
                'charge' => $request->charge,
                'refund' => $request->refund,
                'checkout' => Carbon::now('Asia/Yangon'),
                'status' => OrderStatusEnum::SUCCESS
            ]);
            DB::commit();

            return $this->success('invoice updated successfully by id', $order->checkout);

        } catch (Exception $e) {
            DB::rollback();
 
            return $this->internalServerError();
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $order = Order::findOrFail($id);
            $order->delete($id);

            DB::commit();

            return $this->success('invoice deleted successfully by id', []);

        } catch (Exception $e) {

            DB::rollback();

            return $this->internalServerError();
        }
    }
}
