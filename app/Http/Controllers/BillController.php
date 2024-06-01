<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillStoreRequest;
use App\Http\Requests\BillUpdateRequest;
use App\Models\Bill;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportBill;
use App\Exports\ExportBillParams;
use App\Imports\ImportBill;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();

        try {
            $bills = Bill::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $bills->transform(function ($bill) {
                $bill->shop_id = $bill->shop_id ? Shop::find($bill->shop_id)->name : "Unknown";
                $bill->created_by = $bill->created_by ? User::find($bill->created_by)->name : "Unknown";
                $bill->updated_by = $bill->updated_by ? User::find($bill->updated_by)->name : "Unknown";
                $bill->deleted_by = $bill->deleted_by ? User::find($bill->deleted_by)->name : "Unknown";
                
                return $bill;
            });

            DB::commit();

            return $this->success('Bills retrieved successfully', $bills);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(BillStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        try {

            $bill = Bill::create($payload->toArray());

            DB::commit();

            return $this->success('bill created successfully', $bill);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $bill = Bill::findOrFail($id);
            DB::commit();

            return $this->success('bill retrived successfully by id', $bill);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(BillUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $bill = Bill::findOrFail($id);
            $bill->update($payload->toArray());
            DB::commit();

            return $this->success('bill updated successfully by id', $bill);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $bill = Bill::findOrFail($id);
            $bill->forceDelete();

            DB::commit();

            return $this->success('bill deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function exportexcel()
    {
        return Excel::download(new ExportBill, 'Bills.xlsx');
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
        return Excel::download(new ExportBillParams($filters), 'Bills.xlsx');
    }

    public function exportpdf()
    {
        $data = Bill::all();
        $pdf = Pdf::loadView('billexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = Bill::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('billexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportBill, request()->file('file'));

        return $this->success('Bill is imported successfully');
    }
}
