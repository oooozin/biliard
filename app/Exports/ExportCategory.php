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
        return Category::select('id', 'name', 'open_time', 'close_time', 'address', 'phone', 'created_by', 'updated_by', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Status',
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
            $post->status,
            $createdByUser ? $createdByUser->name : 'Unknown',
            $updatedByUser ? $updatedByUser->name : 'Unknown',
            $post->created_at,
            $post->updated_at
        ];
    }
}