<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportShop;
use App\Exports\ExportShopParams;
use App\Imports\ImportCategory;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $items = Item::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $items->transform(function ($item) {
                $item->category_id = $item->category_id ? Category::find($item->category_id)->name : "Unknown";
                $item->created_by = $item->created_by ? User::find($item->created_by)->name : "Unknown";
                $item->updated_by = $item->updated_by ? User::find($item->updated_by)->name : "Unknown";
                $item->deleted_by = $item->deleted_by ? User::find($item->deleted_by)->name : "Unknown";
                
                return $item;
            });

            DB::commit();

            return $this->success('items retrived successfully', $items);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(ItemStoreRequest $request)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images');
            $image_url = Storage::url($path);
            $payload['image'] = $image_url;
        }

        try {

            $product = Item::create($payload->toArray());
            DB::commit();

            return $this->success('product created successfully', $product);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function show($id)
    {
        DB::beginTransaction();

        try {

            $product = Item::findOrFail($id);
            DB::commit();

            return $this->success('product retrived successfully by id', $product);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(ItemUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $product = Item::findOrFail($id);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/images');
                $image_url = Storage::url($path);
                $payload['image'] = $image_url;
            }

            $product->update($payload->toArray());

            DB::commit();

            return $this->success('product updated successfully by id', $product);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $product = Item::findOrFail($id);
            $product->forceDelete();
            
            $parsedUrl = parse_url($product->image);
            $old_image_path = substr($parsedUrl['path'], 11);
            Storage::delete($old_image_path);

            DB::commit();

            return $this->success('product deleted successfully by id', []);

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
