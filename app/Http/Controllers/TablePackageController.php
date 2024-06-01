<?php

namespace App\Http\Controllers;

use App\Http\Requests\TablePackageStoreRequest;
use App\Http\Requests\TablePackageUpdateRequest;
use App\Models\TablePackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTablePackage;
use App\Exports\ExportTablePackageParams;
use App\Imports\ImportTablePackage;
use Barryvdh\DomPDF\Facade\Pdf;

class TablePackageController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();

        try {
            $tablePackages = TablePackage::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $tablePackages->transform(function ($tablePackage) {
                $tablePackage->created_by = $tablePackage->created_by ? User::find($tablePackage->created_by)->name : "Unknown";
                $tablePackage->updated_by = $tablePackage->updated_by ? User::find($tablePackage->updated_by)->name : "Unknown";
                $tablePackage->deleted_by = $tablePackage->deleted_by ? User::find($tablePackage->deleted_by)->name : "Unknown";
                
                return $tablePackage;
            });

            DB::commit();

            return $this->success('TablePackages retrieved successfully', $tablePackages);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(TablePackageStoreRequest $request)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());

        try {

            $tablePackage = TablePackage::create($payload->toArray());

            DB::commit();

            return $this->success('tablePackage created successfully', $tablePackage);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $tablePackage = TablePackage::findOrFail($id);
            DB::commit();

            return $this->success('tablePackage retrived successfully by id', $tablePackage);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(TablePackageUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $tablePackage = TablePackage::findOrFail($id);
            $tablePackage->update($payload->toArray());
            DB::commit();

            return $this->success('tablePackage updated successfully by id', $tablePackage);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $tablePackage = TablePackage::findOrFail($id);
            $tablePackage->forceDelete();

            DB::commit();

            return $this->success('tablePackage deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function exportexcel()
    {
        return Excel::download(new ExportTablePackage, 'TablePackages.xlsx');
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
        return Excel::download(new ExportTablePackageParams($filters), 'TablePackages.xlsx');
    }

    public function exportpdf()
    {
        $data = TablePackage::all();
        $pdf = Pdf::loadView('tablePackageexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = TablePackage::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('tablePackageexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportTablePackage, request()->file('file'));

        return $this->success('TablePackage is imported successfully');
    }
}
