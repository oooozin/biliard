<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialStoreRequest;
use App\Http\Requests\MaterialUpdateRequest;
use App\Models\Material;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {

        DB::beginTransaction();

        try {

            $materials = Material::searchQuery()
                ->sortingQuery()
                ->filterQuery()
                ->filterDateQuery()
                ->paginationQuery();

            $materials->transform(function ($material) {
                $material->created_by = $material->created_by ? User::find($material->created_by)->name : "Unknown";
                $material->updated_by = $material->updated_by ? User::find($material->updated_by)->name : "Unknown";
                $material->deleted_by = $material->deleted_by ? User::find($material->deleted_by)->name : "Unknown";
                
                return $material;
            });

            DB::commit();

            return $this->success('materials retrived successfully', $materials);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function store(MaterialStoreRequest $request)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images');
            $image_url = Storage::url($path);
            $payload['image'] = $image_url;
        }

        try {

            $product = Material::create($payload->toArray());
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

            $product = Material::findOrFail($id);
            DB::commit();

            return $this->success('product retrived successfully by id', $product);

        } catch (Exception $e) {
            DB::rollback();

            return $this->internalServerError();
        }
    }

    public function update(MaterialUpdateRequest $request, $id)
    {
        DB::beginTransaction();

        $payload = collect($request->validated());

        try {

            $product = Material::findOrFail($id);

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

            $product = Material::findOrFail($id);
            $product->forceDelete();

            /****
             *
             * delete public image
             */
            // $old_image_url = $product->image;
            // $parsedUrl = parse_url($product->image);
            // $old_image_path = substr($parsedUrl['path'], 11);
            // Storage::delete($old_image_path);

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
