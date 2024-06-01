<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportShop;
use App\Exports\ExportShopParams;
use App\Imports\ImportShop;
use Barryvdh\DomPDF\Facade\Pdf;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();

        try {
            $shops = Shop::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $shops->transform(function ($shop) {
                $shop->created_by = $shop->created_by ? User::find($shop->created_by)->name : "Unknown";
                $shop->updated_by = $shop->updated_by ? User::find($shop->updated_by)->name : "Unknown";
                $shop->deleted_by = $shop->deleted_by ? User::find($shop->deleted_by)->name : "Unknown";
                
                return $shop;
            });

            DB::commit();

            return $this->success('Shops retrieved successfully', $shops);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(ShopStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        try {

            $shop = Shop::create($payload->toArray());

            DB::commit();

            return $this->success('shop created successfully', $shop);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $shop = Shop::findOrFail($id);
            DB::commit();

            return $this->success('shop retrived successfully by id', $shop);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(ShopUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $shop = Shop::findOrFail($id);
            $shop->update($payload->toArray());
            DB::commit();

            return $this->success('shop updated successfully by id', $shop);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $shop = Shop::findOrFail($id);
            $shop->forceDelete();

            DB::commit();

            return $this->success('shop deleted successfully by id', []);

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
        $data = Shop::all();
        $pdf = Pdf::loadView('shopexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = Shop::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('shopexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportShop, request()->file('file'));

        return $this->success('Shop is imported successfully');
    }
}
