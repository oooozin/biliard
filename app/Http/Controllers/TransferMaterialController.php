<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferMaterialStoreRequest;
use App\Http\Requests\TransferMaterialUpdateRequest;
use App\Models\TransferMaterial;
use App\Models\Shop;
use App\Models\User;
use App\Models\Material;
use App\Models\MaterialData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTransferMaterial;
use App\Exports\ExportTransferMaterialParams;
use App\Imports\ImportTransferMaterial;
use Barryvdh\DomPDF\Facade\Pdf;

class TransferMaterialController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $TransferMaterials = TransferMaterial::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            DB::commit();

            $TransferMaterials->transform(function ($TransferMaterial) {
                $TransferMaterial->from_shop = $TransferMaterial->from_shop ? Shop::find($TransferMaterial->from_shop)->name : "Unknown";
                $TransferMaterial->to_shop = $TransferMaterial->to_shop ? Shop::find($TransferMaterial->to_shop)->name : "Unknown";
                $TransferMaterial->material_id = $TransferMaterial->material_id ? Material::find($TransferMaterial->material_id)->name : "Unknown";
                $TransferMaterial->created_by = $TransferMaterial->created_by ? User::find($TransferMaterial->created_by)->name : "Unknown";
                $TransferMaterial->updated_by = $TransferMaterial->updated_by ? User::find($TransferMaterial->updated_by)->name : "Unknown";
                $TransferMaterial->deleted_by = $TransferMaterial->deleted_by ? User::find($TransferMaterial->deleted_by)->name : "Unknown";
                
                return $TransferMaterial;
            });

            return $this->success('TransferMaterials retrived successfully', $TransferMaterials);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(TransferMaterialStoreRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $payload = collect($request->validated());
            $transferData = $payload->toArray();
    
            $fromShopMaterialData = MaterialData::where('material_id', $transferData['material_id'])
                ->where('shop_id', $transferData['from_shop'])
                ->first();
    
            $toShopMaterialData = MaterialData::where('material_id', $transferData['material_id'])
                ->where('shop_id', $transferData['to_shop'])
                ->first();
    
            if ($fromShopMaterialData) {
                $fromShopMaterialData->qty -= $transferData['qty'];
                $fromShopMaterialData->save();
    
                if ($toShopMaterialData) {
                    $toShopMaterialData->qty += $transferData['qty'];
                    $toShopMaterialData->save();
                } else {
                    $toShopMaterialData = new MaterialData();
                    $toShopMaterialData->material_id = $transferData['material_id'];
                    $toShopMaterialData->shop_id = $transferData['to_shop'];
                    $toShopMaterialData->qty = $transferData['qty'];
                    $toShopMaterialData->save();
                }
    
                $TransferItem = TransferMaterial::create($transferData);
    
                DB::commit();
    
                return $this->success('TransferMaterial created successfully', $TransferItem);
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

            $TransferMaterial = TransferMaterial::findOrFail($id);
            DB::commit();

            return $this->success('TransferMaterial retrived successfully by id', $TransferMaterial);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(TransferMaterialUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $TransferMaterial = TransferMaterial::findOrFail($id);
            $TransferMaterial->update($payload->toArray());
            DB::commit();

            return $this->success('TransferMaterial updated successfully by id', $TransferMaterial);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $TransferMaterial = TransferMaterial::findOrFail($id);
            $TransferMaterial->forceDelete();

            DB::commit();

            return $this->success('TransferMaterial deleted successfully by id', []);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function exportexcel()
    {
        return Excel::download(new ExportTransferMaterial, 'TransferMaterials.xlsx');
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
        return Excel::download(new ExportTransferMaterialParams($filters), 'TransferMaterials.xlsx');
    }

    public function exportpdf()
    {
        $data = TransferMaterial::all();
        $pdf = Pdf::loadView('TransferMaterialexport', ['data' => $data]);
        return $pdf->download();
    }

    public function exportpdfparams()
    {
        $data = TransferMaterial::searchQuery()
        ->sortingQuery()
        ->filterQuery()
        ->filterDateQuery()
        ->paginationQuery();
        
        $pdf = Pdf::loadView('TransferMaterialexport', ['data' => $data]);
        return $pdf->download();
    }

    public function import()
    {
        Excel::import(new ImportTransferMaterial, request()->file('file'));

        return $this->success('TransferMaterial is imported successfully');
    }
}
