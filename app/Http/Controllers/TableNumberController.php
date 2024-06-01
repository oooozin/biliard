<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableNumberStoreRequest;
use App\Http\Requests\TableNumberUpdateRequest;
use App\Models\TableNumber;
use App\Models\Shop;
use App\Models\User;
use App\Models\Cashier;
use Illuminate\Http\Request;
use App\Enums\TableStatusEnum;
use Illuminate\Support\Facades\DB;

class TableNumberController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $tableNumbers = TableNumber::with(['order'])
                ->searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $tableNumbers->transform(function ($tableNumber) {
                $tableNumber->shop_id = $tableNumber->shop_id ? Shop::find($tableNumber->shop_id)->name : "Unknown";
                $tableNumber->cashier_id = $tableNumber->cashier_id ? Cashier::find($tableNumber->cashier_id)->name : "Unknown";
                $tableNumber->created_by = $tableNumber->created_by ? User::find($tableNumber->created_by)->name : "Unknown";
                $tableNumber->updated_by = $tableNumber->updated_by ? User::find($tableNumber->updated_by)->name : "Unknown";
                $tableNumber->deleted_by = $tableNumber->deleted_by ? User::find($tableNumber->deleted_by)->name : "Unknown";
                
                return $tableNumber;
            });

            DB::commit();

            return $this->success('tableNumbers retrived successfully', $tableNumbers);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(TableNumberStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        $payload['status'] = TableStatusEnum::SUCCESS;

        try {

            $tableNumber = TableNumber::create($payload->toArray());

            DB::commit();

            return $this->success('tableNumber created successfully', $tableNumber);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $tableNumber = TableNumber::findOrFail($id);
            DB::commit();

            return $this->success('tableNumber retrived successfully by id', $tableNumber);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(TableNumberUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $tableNumber = TableNumber::findOrFail($id);
            $tableNumber->update($payload->toArray());
            DB::commit();

            return $this->success('tableNumber updated successfully by id', $tableNumber);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $tableNumber = TableNumber::findOrFail($id);
            $tableNumber->forceDelete();

            DB::commit();

            return $this->success('tableNumber deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }
}
