<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemDataStoreRequest;
use App\Http\Requests\ItemDataUpdateRequest;
use App\Models\ItemData;
use App\Models\User;
use App\Models\Item;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemDataController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $itemDatas = ItemData::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $itemDatas->transform(function ($itemData) {
                $itemData->item_id = $itemData->item_id ? Item::find($itemData->item_id)->name : "Unknown";
                $itemData->shop_id = $itemData->shop_id ? Shop::find($itemData->shop_id)->name : "Unknown";
                $itemData->created_by = $itemData->created_by ? User::find($itemData->created_by)->name : "Unknown";
                $itemData->updated_by = $itemData->updated_by ? User::find($itemData->updated_by)->name : "Unknown";
                $itemData->deleted_by = $itemData->deleted_by ? User::find($itemData->deleted_by)->name : "Unknown";
                
                return $itemData;
            });

            DB::commit();

            return $this->success('itemDatas retrived successfully', $itemDatas);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(ItemDataStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $payload = collect($request->validated());
            $oldData = $payload->toArray();
    
            $oldItemData = ItemData::where('item_id', $oldData['item_id'])
                ->where('shop_id', $oldData['shop_id'])
                ->first();
    
            if ($oldItemData) {
                $oldItemData->qty += $oldData['qty'];
                $oldItemData->save();
                DB::commit();
                return $this->success('itemData created successfully', $oldItemData);
            }else{
                $itemData = ItemData::create($oldData);
                DB::commit();
                return $this->success('itemData created successfully', $itemData);
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

            $itemData = ItemData::findOrFail($id);
            DB::commit();

            return $this->success('itemData retrived successfully by id', $itemData);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(ItemDataUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $itemData = ItemData::findOrFail($id);

            $itemData->update($payload->toArray());

            DB::commit();

            return $this->success('itemData updated successfully by id', $itemData);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $itemData = ItemData::findOrFail($id);
            $itemData->forceDelete();

            /****
             *
             * delete public image
             */
            // $old_image_url = $itemData->image;
            // $parsedUrl = parse_url($itemData->image);
            // $old_image_path = substr($parsedUrl['path'], 11);
            // Storage::delete($old_image_path);

            DB::commit();

            return $this->success('itemData deleted successfully by id', []);

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
