<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $customers = Customer::sortingQuery()
                ->searchQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $customers->transform(function ($customer) {
                $customer->created_by = $customer->created_by ? User::find($customer->created_by)->name : "Unknown";
                $customer->updated_by = $customer->updated_by ? User::find($customer->updated_by)->name : "Unknown";
                $customer->deleted_by = $customer->deleted_by ? User::find($customer->deleted_by)->name : "Unknown";
                
                return $customer;
            });

            DB::commit();

            return $this->success('customers retrived successfully', $customers);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(CustomerStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        try {

            $customer = Customer::create($payload->toArray());

            DB::commit();

            return $this->success('customer created successfully', $customer);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $customer = Customer::findOrFail($id);
            DB::commit();

            return $this->success('customer retrived successfully by id', $customer);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(CustomerUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $customer = Customer::findOrFail($id);
            $customer->update($payload->toArray());
            DB::commit();

            return $this->success('customer updated successfully by id', $customer);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $customer = Customer::findOrFail($id);
            $customer->forceDelete();

            DB::commit();

            return $this->success('customer deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function exportexcel()
    {
        return Excel::download(new ExportShop, 'Shops.xlsx');
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
        return Excel::download(new ExportShopParams($filters), 'Shops.xlsx');
    }

    public function exportpdf()
    {
        $data = Category::all();
        $pdf = Pdf::loadView('categoryexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = Category::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('categoryexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportCategory, request()->file('file'));

        return $this->success('Category is imported successfully');
    }
}
