<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialDataStoreRequest;
use App\Http\Requests\MaterialDataUpdateRequest;
use App\Models\MaterialData;
use App\Models\User;
use App\Models\Material;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialDataController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $materialDatas = MaterialData::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $materialDatas->transform(function ($materialData) {
                $materialData->material_id = $materialData->material_id ? Material::find($materialData->material_id)->name : "Unknown";
                $materialData->shop_id = $materialData->shop_id ? Shop::find($materialData->shop_id)->name : "Unknown";
                $materialData->created_by = $materialData->created_by ? User::find($materialData->created_by)->name : "Unknown";
                $materialData->updated_by = $materialData->updated_by ? User::find($materialData->updated_by)->name : "Unknown";
                $materialData->deleted_by = $materialData->deleted_by ? User::find($materialData->deleted_by)->name : "Unknown";
                
                return $materialData;
            });

            DB::commit();

            return $this->success('materialDatas retrived successfully', $materialDatas);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(MaterialDataStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $payload = collect($request->validated());
            $oldData = $payload->toArray();
    
            $oldMaterialData = MaterialData::where('material_id', $oldData['material_id'])
                ->where('shop_id', $oldData['shop_id'])
                ->first();
    
            if ($oldMaterialData) {
                $oldMaterialData->qty += $oldData['qty'];
                $oldMaterialData->save();
                DB::commit();
                return $this->success('materialData created successfully', $oldMaterialData);
            }else{
                $materialData = MaterialData::create($oldData);
                DB::commit();
                return $this->success('materialData created successfully', $materialData);
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

            $materialData = MaterialData::findOrFail($id);
            DB::commit();

            return $this->success('materialData retrived successfully by id', $materialData);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(MaterialDataUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $materialData = MaterialData::findOrFail($id);

            $materialData->update($payload->toArray());

            DB::commit();

            return $this->success('materialData updated successfully by id', $materialData);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $materialData = MaterialData::findOrFail($id);
            $materialData->forceDelete();

            DB::commit();

            return $this->success('materialData deleted successfully by id', []);

        } catch (Exception $e) {

            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function getImage($path)
    {
        $image = Storage::get($path);

        if ($image) {

            return response($image, 200)->header('Content-Type', Storage::mimeType($path));

        } else {

            return $this->notFound('Image Resource Not Found', []);

        }
    }
}
