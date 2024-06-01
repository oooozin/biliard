<?php

namespace App\Http\Controllers;

use App\Enums\GeneralStatusEnum;
use App\Http\Requests\CashierStoreRequest;
use App\Http\Requests\CashierUpdateRequest;
use App\Models\Cashier;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $cashiers = Cashier::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $cashiers->transform(function ($cashier) {
                $cashier->shop_id = $cashier->shop_id ? Shop::find($cashier->shop_id)->name : "Unknown";
                $cashier->created_by = $cashier->created_by ? User::find($cashier->created_by)->name : "Unknown";
                $cashier->updated_by = $cashier->updated_by ? User::find($cashier->updated_by)->name : "Unknown";
                $cashier->deleted_by = $cashier->deleted_by ? User::find($cashier->deleted_by)->name : "Unknown";
                
                return $cashier;
            });

            DB::commit();

            return $this->success('cashiers retrived successfully', $cashiers);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(CashierStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        try {

            $cashier = Cashier::create($payload->toArray());

            DB::commit();

            return $this->success('cashier created successfully', $cashier);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $cashier = Cashier::findOrFail($id);
            DB::commit();

            return $this->success('cashier retrived successfully by id', $cashier);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(CashierUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $cashier = Cashier::findOrFail($id);
            $cashier->update($payload->toArray());
            DB::commit();

            return $this->success('cashier updated successfully by id', $cashier);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $cashier = Cashier::findOrFail($id);
            $cashier->forceDelete();

            DB::commit();

            return $this->success('cashier deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }
}
