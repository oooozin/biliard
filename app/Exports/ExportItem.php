<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportItem implements FromCollection, WithHeadings, WithMapping
{

    public function collection()
    {
        return Item::select('id', 'name', 'price', 'purchase_price', 'status', 'cateogry_id', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Price',
            'Purchase Price',
            'Status',
            'Category',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }

    public function map($post): array
    {
        $createdByUser = User::find($post->created_by);
        $updatedByUser = User::find($post->updated_by);
        $category = Category::find($post->category_id);

        return [
            $post->id,
            $post->name,
            $post->price,
            $post->purchase_price,
            $post->status,
            $category ? $category->name : 'Unknown',
            $createdByUser ? $createdByUser->name : 'Unknown',
            $updatedByUser ? $updatedByUser->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}