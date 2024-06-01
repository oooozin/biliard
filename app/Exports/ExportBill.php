<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportCategory implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Category::select('id', 'name', 'amount', 'shop_id', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Amount',
            'Shop Id',
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

        return [
            $post->id,
            $post->name,
            $post->amount,
            $post->shop_id,
            $createdByUser ? $createdByUser->name : 'Unknown',
            $updatedByUser ? $updatedByUser->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}